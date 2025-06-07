<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class ClientUnblockedMailer
{
    public static function send($client)
    {
        $subject = 'Tin vui! Tài khoản cửa hàng của bạn đã được mở lại';

        $body = view('emails.client_unblocked', [
            'name' => $client->name,
        ])->render();

        Mail::send([], [], function ($message) use ($client, $subject, $body) {
            $message->to($client->email)
                ->subject($subject)
                ->html($body);
        });
    }
}
