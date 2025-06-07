<?php

namespace App\Mail;

use Illuminate\Support\Facades\Mail;

class ClientRejectedMailer
{
    public static function send($client)
    {
        $subject = 'Thông báo: Yêu cầu đăng ký cửa hàng của bạn không được duyệt';

        $body = view('emails.client_rejected', [
            'name' => $client->name,
        ])->render();

        Mail::send([], [], function ($message) use ($client, $subject, $body) {
            $message->to($client->email)
                ->subject($subject)
                ->html($body);
        });
    }
}
