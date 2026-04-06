<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;

class LogViewer extends Page


{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;
    protected static string | UnitEnum | null $navigationGroup  = 'System';
    protected static ?string $title = 'Log Viewer';
    protected string $view = 'filament.pages.log-viewer';
    protected string | Width | null $maxContentWidth = Width::Full;
}
