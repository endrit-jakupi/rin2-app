<?php

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public $notifications;

    public function mount(NotificationService $notificationService): void
    {
        $this->notifications = $notificationService->getForUser(
            Auth::user()
        );
    }
};
?>

<div>
    <h1>Notifications</h1>

    @forelse ($notifications as $notification)
        <div>
            <strong>{{ $notification->type }}</strong>
            <p>{{ $notification->message }}</p>
            <small>
                Expires: {{ $notification->expires_at->format('Y-m-d H:i') }}
            </small>
        </div>
    @empty
        <p>No notifications.</p>
    @endforelse
</div>