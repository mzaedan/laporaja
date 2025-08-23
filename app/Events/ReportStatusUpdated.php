<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReportStatus;

class ReportStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reportStatus;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(ReportStatus $reportStatus, $userId)
    {
        $this->reportStatus = $reportStatus;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Log the channel being broadcast to
        \Log::info('Broadcasting to channel: report-status.' . $this->userId, [
            'report_id' => $this->reportStatus->report_id,
            'user_id' => $this->userId,
            'status' => $this->reportStatus->status
        ]);

        return [
            new PrivateChannel('report-status.' . $this->userId)
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'report.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->reportStatus->report_id,
            'status' => $this->reportStatus->status,
            'description' => $this->reportStatus->description,
            'created_at' => $this->reportStatus->created_at->toDateTimeString(),
            'report' => [
                'id' => $this->reportStatus->report->id,
                'title' => $this->reportStatus->report->title,
                'code' => $this->reportStatus->report->code
            ]
        ];
    }
}
