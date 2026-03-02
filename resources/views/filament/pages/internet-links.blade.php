<x-filament::page>

    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">

        {{-- Linkerkolom: groepen --}}
        <div style="width: 400px; flex-shrink: 0;">

            {{-- Groep lijst --}}
            <div id="sortable-groups" style="display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 1rem;">
                @foreach ($this->getGroups() as $group)
                    <div data-id="{{ $group->id }}" style="display: flex; align-items: center; gap: 0.25rem;">
                        <span class="drag-handle-group" style="color: #9ca3af; cursor: grab; padding: 0.25rem;">
                            <x-filament::icon icon="heroicon-o-bars-3" class="h-4 w-4" />
                        </span>
                        <button wire:click="selectGroup({{ $group->id }})"
                            style="flex: 1; text-align: left; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; border: none;
                    background: {{ $activeGroupId === $group->id ? '#6366f1' : '#f3f4f6' }};
                    color: {{ $activeGroupId === $group->id ? 'white' : 'inherit' }};">
                            {{ $group->name }}
                        </button>
                        <button wire:click="editGroup({{ $group->id }})"
                            style="color: #4f46e5; background: none; border: none; cursor: pointer; padding: 0.25rem;">
                            <x-filament::icon icon="heroicon-o-pencil" class="h-4 w-4" />
                        </button>
                        <button wire:click="deleteGroup({{ $group->id }})"
                            style="color: #dc2626; background: none; border: none; cursor: pointer; padding: 0.25rem;">
                            <x-filament::icon icon="heroicon-o-trash" class="h-4 w-4" />
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Groep formulier --}}
            <div
                style="background: white; border-radius: 0.75rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.75rem;">
                    {{ $activeGroupEditId ? 'Groep bewerken' : 'Nieuwe groep' }}
                </p>

                {{ $this->groupForm }}

                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                    <x-filament::button wire:click="saveGroup" type="button" size="sm">
                        Bewaren
                    </x-filament::button>
                    @if ($activeGroupEditId)
                        <x-filament::button color="gray" wire:click="resetGroupForm" type="button" size="sm">
                            Annuleren
                        </x-filament::button>
                    @endif
                </div>
            </div>

        </div>

        {{-- Middenkolom: links --}}
        <div style="flex: 1;">
            <div id="sortable-container" style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach ($this->getLinks() as $link)
                    <div data-id="{{ $link->id }}"
                        style="display: flex; align-items: center; justify-content: space-between; background: white; padding: 0.75rem 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="drag-handle" style="color: #9ca3af; cursor: grab;">
                                <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5" />
                            </span>
                           {{-- maak link --}}
                            <a href="{{ $link->url }}" target="_blank"><span style="font-size: 0.875rem; font-weight: 500;">{{ $link->link_title }}</span> </a>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <a href="{{ $link->url }}" target="_blank" style="color: #16a34a;">
                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="h-5 w-5" />
                            </a>
                            <button wire:click="editLink({{ $link->id }})"
                                style="color: #4f46e5; background: none; border: none; cursor: pointer;">
                                <x-filament::icon icon="heroicon-o-pencil" class="h-5 w-5" />
                            </button>
                            <button wire:click="deleteLink({{ $link->id }})"
                                style="color: #dc2626; background: none; border: none; cursor: pointer;">
                                <x-filament::icon icon="heroicon-o-trash" class="h-5 w-5" />
                            </button>
                            <select
                                onchange="Livewire.dispatch('move-link', { linkId: {{ $link->id }}, groupId: parseInt(this.value) })"
                                style="font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.25rem 0.5rem; color: #6b7280; cursor: pointer;">
                                @foreach ($this->getGroups() as $group)
                                    <option value="{{ $group->id }}"
                                        {{ $link->internet_group_id === $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach

                @if ($this->getLinks()->isEmpty())
                    <p style="font-size: 0.875rem; color: #9ca3af;">Geen links in deze groep.</p>
                @endif
            </div>
        </div>

        {{-- Rechterkolom: formulier --}}
        <div
            style="width: 400px; flex-shrink: 0; background: white; border-radius: 0.75rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; flex-direction: column; gap: 1rem;">

                {{ $this->form }}

                <div style="display: flex; gap: 0.75rem;">
                    <x-filament::button wire:click="save" type="button">
                        Bewaren
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="resetForm" type="button">
                        Leegmaken
                    </x-filament::button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function initSortable() {
            const container = document.getElementById('sortable-container');
            if (!container) return;

            if (container._sortable) {
                container._sortable.destroy();
                container._sortable = null;
            }

            container._sortable = Sortable.create(container, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: () => {
                    const ids = [...container.querySelectorAll('[data-id]')]
                        .map(el => parseInt(el.dataset.id));
                    Livewire.dispatch('links-reordered', {
                        order: ids
                    });
                }
            });
        }

        /*         document.addEventListener('DOMContentLoaded', initSortable);
                document.addEventListener('livewire:updated', initSortable); */


        function initGroupSortable() {
            const container = document.getElementById('sortable-groups');
            if (!container) return;

            if (container._sortable) {
                container._sortable.destroy();
                container._sortable = null;
            }

            container._sortable = Sortable.create(container, {
                animation: 150,
                handle: '.drag-handle-group',
                onEnd: () => {
                    const ids = [...container.querySelectorAll('[data-id]')]
                        .map(el => parseInt(el.dataset.id));
                    Livewire.dispatch('groups-reordered', {
                        order: ids
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSortable();
            initGroupSortable();
        });

        document.addEventListener('livewire:updated', () => {
            initSortable();
            initGroupSortable();
        });

    </script>

</x-filament::page>
