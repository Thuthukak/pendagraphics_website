<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'is_current_user' => $user->id === Auth::id(),
                ];
            });

        return response()->json($users);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        // Optional: Add authorization check
        // $this->authorize('delete', $user);

        try {
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user.'
            ], 500);
        }
    }
}