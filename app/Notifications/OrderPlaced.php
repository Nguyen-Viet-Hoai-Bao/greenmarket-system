<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
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
            'message' => "Bạn đã đặt hàng thành công. Mã đơn hàng: {$this->orderCode}",
        ];
    }
}
