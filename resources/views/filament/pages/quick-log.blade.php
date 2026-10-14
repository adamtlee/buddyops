<x-filament-panels::page>
    @if ($this->dogs->count() > 1)
        <x-filament::section>
            <label for="quick-log-dog" class="mb-2 flex items-center gap-2 text-base font-semibold text-gray-950 dark:text-white">
                <x-filament::icon icon="heroicon-o-heart" class="h-5 w-5 text-primary-600" />
                Logging for
            </label>
            <select id="quick-log-dog" wire:model.live="selectedDogId" class="w-full rounded-lg border-2 border-primary-500 bg-white p-4 text-lg font-semibold text-gray-950 shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 dark:text-white">
                @foreach ($this->dogs as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </x-filament::section>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        {{ $this->fedAction }}
        {{ $this->walkedAction }}
        {{ $this->playedAction }}
    </div>

    <x-filament::section heading="Recent activity">
        <ul class="divide-y divide-gray-200 dark:divide-white/10">
            @forelse ($this->recentActivity as $entry)
                <li class="flex items-center gap-3 py-3">
                    <x-filament::icon :icon="$entry['icon']" class="h-6 w-6 shrink-0 text-gray-500" />
                    <span class="flex-1 text-sm">{{ $entry['text'] }}</span>
                    <span class="text-xs text-gray-500">{{ $entry['at']->diffForHumans() }}</span>
                </li>
            @empty
                <li class="py-3 text-sm text-gray-500">No activity yet.</li>
            @endforelse
        </ul>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
