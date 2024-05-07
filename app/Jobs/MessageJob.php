<?php

namespace App\Jobs;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'superadmin']);
        })->get();
        foreach($users as $user){
            broadcast(new MessageEvent($this->message,$user));
        }
    }
}
