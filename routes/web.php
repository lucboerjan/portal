<?php

use App\Http\Controllers\PrivacyPinController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect('/admin');
    }
    return redirect('/admin/login');
});

Route::post('/privacy/verify-pin', [PrivacyPinController::class, 'verify'])
    ->name('privacy.verify-pin')
    ->middleware('auth');

Route::post('/filament/logviewer/delete-lines', function () {
    $file = \Opcodes\LogViewer\Facades\LogViewer::getFile(request('file'));
    $linesToDelete = json_decode(request('lines'), true);
    $lines = file($file->path());
    $newLines = [];
    foreach ($lines as $index => $line) {
        if (!in_array($index, $linesToDelete)) {
            $newLines[] = $line;
        }
    }
    file_put_contents($file->path(), implode('', $newLines));
    return back()->with('success', 'Geselecteerde regels verwijderd.');
})->name('filament.logviewer.delete-lines');
