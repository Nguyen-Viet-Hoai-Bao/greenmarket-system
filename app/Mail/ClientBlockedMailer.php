<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class ClientBlockedMailer
{
    public static function send($client)
    {
        $subject = 'Thông báo quan trọng: Tài khoản cửa hàng của bạn đã bị khóa';

        $body = view('emails.client_blocked', [
            'name' => $client->name,
        ])->render();

        Mail::send([], [], function ($message) use ($client, $subject, $body) {
            $message->to($client->email)
                ->subject($subject)
                ->html($body);
        });
    }
}
