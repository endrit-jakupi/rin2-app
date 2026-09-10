<?php

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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

    public function impersonate(User $user): void
    {
        session(['impersonating_from' => auth()->id()]);
    
        Auth::login($user);
    
        request()->session()->regenerate();
    
        $this->redirect('/');
    }
};
?>

<div>
    <h1>Users</h1>

    <ul>
        @foreach ($users as $user)
            <li>
                <button type="button" wire:click="impersonate({{ $user->id }})">
                    {{ $user->name }}
                </button>
                — {{ $user->email }}
                — {{ $user->unread_notifications_count }} unread
            </li>
        @endforeach
    </ul>
</div>