<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancelRequested extends Notification
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
            'message' => "Yêu cầu hủy đơn hàng {$this->orderCode} của bạn đã được ghi nhận.",
        ];
    }
}
