<?php

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Collection $users;

    public function mount(NotificationService $notificationService): void
    {
        $this->users = User::query()
            ->orderBy('name')
            ->get()
            ->each(function (User $user) use ($notificationService) {
                $user->unread_notifications_count = $notificationService->getUnreadCount($user);
            });
    }
};
?>

<div>
    <h1>Users</h1>

    <ul>
        @foreach ($users as $user)
            <li>
                <strong>{{ $user->name }}</strong>
                — {{ $user->email }}
                — {{ $user->unread_notifications_count }} unread
            </li>
        @endforeach
    </ul>
</div>