<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCancelledBySystem extends Notification
{
    use Queueable;
    protected $orderCode;
    protected $cancelReason;

    public function __construct(string $orderCode, $cancelReason)
    {
        $this->orderCode = $orderCode;
        $this->cancelReason = $cancelReason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Đơn hàng {$this->orderCode} của bạn đã bị hủy. LÝ DO {$this->cancelReason}.",
        ];
    }
}
