<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancelled extends Notification
{
    use Queueable;

    public function __construct(private string $orderCode)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Đơn hàng {$this->orderCode} của bạn đã bị hủy.",
        ];
    }
}
