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
    <p class="mb-2 text-3xl font-semibold">
        Users
    </p>

    <p class="mb-6 text-sm text-gray-600">
        Click on a user to impersonate them.
    </p>

    <table class="w-full text-left">
        <thead>
            <tr class="border-b">
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Unread</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $user)
                <tr
                    wire:click="impersonate({{ $user->id }})"
                    title="Impersonate"
                    class="cursor-pointer border-b hover:bg-black hover:text-white"
                >
                    <td class="px-4 py-4 font-medium">
                        {{ $user->name }}
                    </td>

                    <td class="px-4 py-4">
                        {{ $user->email }}
                    </td>

                    <td class="px-4 py-4">
                        {{ $user->unread_notifications_count }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>