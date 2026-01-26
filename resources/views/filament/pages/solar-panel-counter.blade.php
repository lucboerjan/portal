<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">

        <form wire:submit="create">
            <div class="space-y-2"> {{ $this->form }}

                <div style="margin-top: 6px; margin-bottom: 6px;">
                    <x-filament::button type="submit">
                        Save
                    </x-filament::button>
                </div>
            </div>
        </form>

        <div class="md:col-span-2">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
