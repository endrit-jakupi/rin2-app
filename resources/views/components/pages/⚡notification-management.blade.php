<?php

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Collection $notifications;

    public function mount(): void
    {
        $this->notifications = Notification::query()
            ->with('user')
            ->latest()
            ->get();
    }
};
?>

<div>
    <p class="mb-6 text-3xl font-semibold">
        Notification Management
    </p>

    <div class="w-full">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Destination</th>
                    <th class="px-4 py-3">Expires</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($notifications as $notification)
                    <tr class="border-b">
                        <td class="px-4 py-4 font-medium">
                            {{ $notification->type }}
                        </td>

                        <td class="px-4 py-4 text-gray-600">
                            {{ $notification->message }}
                        </td>

                        <td class="px-4 py-4 text-gray-600">
                            {{ $notification->user?->name ?? 'All users' }}
                        </td>

                        <td class="px-4 py-4 text-gray-600">
                            {{ $notification->expires_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="4"
                            class="px-4 py-4 text-gray-600"
                        >
                            No notifications have been posted.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>