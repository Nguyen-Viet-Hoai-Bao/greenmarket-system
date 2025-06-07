<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class ClientApprovedMailer
{
    public static function send($client)
    {
        $subject = 'Chúc mừng! Cửa hàng của bạn đã được duyệt';

        $body = view('emails.client_approved', [
            'name' => $client->name,
        ])->render();

        Mail::send([], [], function ($message) use ($client, $subject, $body) {
            $message->to($client->email)
                ->subject($subject)
                ->html($body);
        });
    }
}
