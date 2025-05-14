<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;


class ContactController extends Controller
{
    public function contactForm(Request $request)
{
    $validated = $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'subject' => 'required',
        'message' => 'required',
    ]);

    Contact::create($validated);

    return response()->json([
        'message' => 'Message sent successfully',
    ], 201);
}

}
