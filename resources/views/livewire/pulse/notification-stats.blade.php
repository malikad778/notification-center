<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Notifications Sent"
        title="Notifications Sent"
        details="count"
    >
        <x-slot:icon>
            <x-pulse::icons.sparkles />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($stats->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Channel</x-pulse::th>
                        <x-pulse::th class="text-right">Count</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($stats as $stat)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $stat->key }}">
                            <x-pulse::td>
                                {{ ucfirst($stat->key) }}
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-900 font-bold dark:text-gray-100">
                                {{ number_format($stat->count) }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
