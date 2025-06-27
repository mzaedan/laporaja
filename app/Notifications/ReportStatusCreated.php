<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\ReportStatus;

class ReportStatusCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $reportStatus;

    public function __construct(ReportStatus $reportStatus)
    {
        $this->reportStatus = $reportStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->reportStatus->id,
            'status' => $this->reportStatus->status,
            'message' => 'Status laporan baru: ' . $this->reportStatus->status,
            'created_at' => $this->reportStatus->created_at,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
