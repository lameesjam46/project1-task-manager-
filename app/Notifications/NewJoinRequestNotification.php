<?php

namespace App\Notifications;

use App\Models\CompanyJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewJoinRequestNotification extends Notification
{
    use Queueable;

    public $joinRequest;

    public function __construct(CompanyJoinRequest $joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->joinRequest->user_id,
            'user_name' => $this->joinRequest->user->name,
            'company_id' => $this->joinRequest->company_id,
            'message' => 'طلب انضمام جديد من ' . $this->joinRequest->user->name,
        ];
    }
} 