<?php

namespace App\Jobs;

use App\Mail\UserInvitationMail;
use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendUserInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 60; // seconds between retries

    public function __construct(
        public UserInvitation $invitation
    ) {}

    public function handle(): void
    {
        // Don't bother sending if invitation was cancelled or already accepted
        if (!$invitation = UserInvitation::find($this->invitation->id)) {
            return;
        }

        if ($invitation->accepted_at || $invitation->expires_at < now()) {
            return;
        }

        Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send invitation email after all retries.', [
            'email' => $this->invitation->email,
            'error' => $exception->getMessage(),
        ]);
    }
}