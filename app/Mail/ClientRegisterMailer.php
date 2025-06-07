<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class ClientRegisterMailer
{
    public static function send($client)
    {
        $subject = 'Cửa hàng của bạn đang chờ phê duyệt';

        $body = view('emails.client_register', [
            'name' => $client->name
        ])->render();

        Mail::send([], [], function ($message) use ($client, $subject, $body) {
            $message->to($client->email)
                ->subject($subject)
                ->html($body);
        });
    }
}
