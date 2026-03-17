<?php

namespace App\Http\Controllers;

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = UserInvitation::with('inviter')
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return response()->json($invitations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email|unique:user_invitations,email',
        ]);

        // Delete any expired invitations for this email
        UserInvitation::where('email', $request->email)
            ->where('expires_at', '<', now())
            ->delete();

        $invitation = UserInvitation::createInvitation(
            $request->email,
            auth()->id()
        );

        Mail::to($request->email)->send(new UserInvitationMail($invitation));

        return response()->json([
            'message' => 'Invitation sent successfully',
            'invitation' => $invitation->load('inviter'),
        ], 201);
    }

    public function show(string $token)
    {
        Log::info('invitation show called');
        Log::info('Showing invitation for token', ['token' => $token]);
        $invitation = UserInvitation::where('token', $token)->firstOrFail();
        
        Log::info('Invitation found', ['invitation' => $invitation]);

        if (!$invitation->isValid()) {
            return Inertia::render('InvitationExpired');
        }

        return Inertia::render('AcceptInvitation', [
            'token' => $token,
            'email' => $invitation->email,
        ]);
    }

    public function accept(Request $request, string $token)
    {
        Log::info('invitation accept called');
        Log::info('Accepting invitation for token', ['token' => $token]);
        $invitation = UserInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return response()->json([
                'message' => 'This invitation has expired or has already been used.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
        ]);

        Log::info('User created successfully', ['user' => $user]);

        $invitation->markAsAccepted();

        auth()->login($user);

        return response()->json([
            'message' => 'Account created successfully',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    public function destroy(UserInvitation $invitation)
    {
        $invitation->delete();

        return response()->json([
            'message' => 'Invitation cancelled successfully',
        ]);
    }
}