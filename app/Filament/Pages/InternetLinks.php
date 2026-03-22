<?php

namespace App\Filament\Pages;

use App\Models\InternetGroup;
use App\Models\InternetLink;
use Filament\Schemas\Schema;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;


class InternetLinks extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;
    protected static ?string $navigationLabel = 'Internetlinks';
    protected string $view = 'filament.pages.internet-links';

    protected string | Width | null $maxContentWidth = Width::Full;


    public ?int $activeGroupId = null;
    public ?int $activeLinkId = null;
    public ?string $link_title = '';
    public ?string $url = '';
    public ?int $activeGroupEditId = null;
    public ?string $groupName = null;

    public function mount(): void
    {
        $this->activeGroupId = InternetGroup::orderBy('order')->value('id');
        $this->form->fill();
        $this->groupForm->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('link_title')
                    ->label('Omschrijving')
                    ->required(),

                TextInput::make('url')
                    ->label('URL-link')
                    ->required(),
            ]);
    }

    public function selectGroup(int $groupId): void
    {
        $this->activeGroupId = $groupId;
        $this->resetForm();
    }

    public function editLink(int $linkId): void
    {
        $this->activeLinkId = $linkId;
        $link = InternetLink::findOrFail($linkId);

        $this->form->fill([
            'link_title' => $link->link_title,
            'url'   => $link->url,
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (!$this->activeGroupId) {
            Notification::make()
                ->title('Geen groep geselecteerd')
                ->danger()
                ->send();
            return;
        }

        if ($this->activeLinkId) {
            InternetLink::findOrFail($this->activeLinkId)->update([
                'link_title' => $data['link_title'],
                'url'   => $data['url'],
            ]);
        } else {
            InternetLink::create([
                'internet_group_id' => $this->activeGroupId,
                'link_title' => $data['link_title'],
                'url'   => $data['url'],
                'order' => 0,
            ]);
        }

        Notification::make()
            ->title('Opgeslagen')
            ->success()
            ->send();

        $this->resetForm();
    }

    public function deleteLink(int $linkId): void
    {
        InternetLink::findOrFail($linkId)->delete();

        Notification::make()
            ->title('Link verwijderd')
            ->success()
            ->send();

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->activeLinkId = null;
        $this->form->fill([
            'link_title' => '',
            'url'   => '',
        ]);
    }

    public function getGroups()
    {
        return InternetGroup::orderBy('order')->get();
    }

    public function getLinks()
    {
        return InternetLink::where('internet_group_id', $this->activeGroupId)
            ->orderBy('order')
            ->get();
    }
    #[On('links-reordered')]
    public function reorderLinks(array $order): void
    {
        logger('reorderLinks aangeroepen', ['order' => $order]);

        foreach ($order as $index => $id) {
            InternetLink::where('id', $id)->update(['order' => $index + 1]);
        }

        Notification::make()
            ->title('Volgorde opgeslagen')
            ->success()
            ->send();
    }


    public function groupForm(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('groupName')
                    ->label('Groepsnaam')
                    ->required(),
            ])
            ->statePath('');
    }

    public function editGroup(int $groupId): void
    {
        $this->activeGroupEditId = $groupId;
        $group = InternetGroup::findOrFail($groupId);
        $this->groupName = $group->name;
        $this->groupForm->fill(['groupName' => $group->name]);
    }

    public function saveGroup(): void
    {
        $this->validate(['groupName' => 'required|string|max:255']);

        if ($this->activeGroupEditId) {
            InternetGroup::findOrFail($this->activeGroupEditId)->update(['name' => $this->groupName]);
        } else {
            InternetGroup::create([
                'name' => $this->groupName,
                'order' => InternetGroup::max('order') + 1,
            ]);
        }

        Notification::make()->title('Groep opgeslagen')->success()->send();
        $this->resetGroupForm();
    }

    public function deleteGroup(int $groupId): void
    {
        InternetGroup::findOrFail($groupId)->delete();

        if ($this->activeGroupId === $groupId) {
            $this->activeGroupId = InternetGroup::orderBy('order')->value('id');
        }

        Notification::make()->title('Groep verwijderd')->success()->send();
        $this->resetGroupForm();
    }

    public function resetGroupForm(): void
    {
        $this->activeGroupEditId = null;
        $this->groupName = '';
        $this->groupForm->fill(['groupName' => '']);
    }

    protected function getForms(): array
    {
        return ['form', 'groupForm'];
    }

    #[On('move-link')]
    public function moveLink(int $linkId, int $groupId): void
    {
        InternetLink::findOrFail($linkId)->update(['internet_group_id' => $groupId]);

        Notification::make()->title('Link verplaatst')->success()->send();
    }

    #[On('groups-reordered')]
    public function reorderGroups(array $order): void
    {
        foreach ($order as $index => $id) {
            InternetGroup::where('id', $id)->update(['order' => $index + 1]);
        }

        Notification::make()->title('Volgorde opgeslagen')->success()->send();
    }
}
