@php
use Opcodes\LogViewer\Facades\LogViewer;
@endphp

<x-filament-panels::page>
    <div 
        x-data="{
            selected: [],
            toggleAll(logs) {
                if (this.selected.length === logs.length) {
                    this.selected = [];
                } else {
                    this.selected = logs.map(l => l.index);
                }
            }
        }"
        class="flex flex-col lg:flex-row gap-4"
    >

        {{-- Sidebar --}}
        <div class="lg:w-64 w-full bg-white dark:bg-gray-900 rounded-lg border p-4 h-auto lg:h-[80vh] overflow-y-auto shadow-sm">
            <h3 class="text-lg font-semibold mb-3">Logbestanden</h3>

            @foreach(LogViewer::getFiles() as $fileItem)
                <a 
                    href="?file={{ $fileItem->identifier }}"
                    class="block px-3 py-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 
                           {{ request('file') === $fileItem->identifier ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-200' : '' }}"
                >
                    {{ $fileItem->name }}
                </a>
            @endforeach
        </div>

        {{-- Main content --}}
        <div class="flex-1 bg-white dark:bg-gray-900 rounded-lg border p-4 shadow-sm">

            @php
                $file = request('file')
                    ? LogViewer::getFile(request('file'))
                    : LogViewer::getFiles()->first();

                $logs = collect();

                if ($file) {
                    // Veilig pad reconstrueren
                    $path = storage_path('logs/' . $file->name);

                    if (file_exists($path)) {
                        $lines = file($path, FILE_IGNORE_NEW_LINES);

                        foreach ($lines as $i => $line) {
                            // Regex die ALLE Laravel logs matcht
                            if (preg_match('/^\[(.*?)\]\s+([a-zA-Z0-9_-]+)\.([A-Z]+):\s+(.*)$/', $line, $m)) {
                                $logs->push((object)[
                                    'index' => $i,
                                    'datetime' => $m[1],
                                    'env' => $m[2],
                                    'level' => strtolower($m[3]),
                                    'message' => $m[4],
                                    'context' => null,
                                ]);
                            }
                        }

                        $logs = $logs->reverse()->values();
                    }
                }
            @endphp

            @if (!$file)
                <p class="text-gray-500">Geen logbestand geselecteerd.</p>
            @else

                {{-- Toolbar --}}
                <div class="sticky top-0 bg-white dark:bg-gray-900 pb-3 mb-4 z-10 border-b">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold">{{ $file->name }}</h3>

                        <div class="flex flex-wrap items-center gap-3">
                            <span x-text="selected.length + ' geselecteerde regel(s)'"></span>

                            <form 
                                method="POST" 
                                action="{{ route('filament.logviewer.delete-lines') }}"
                                x-show="selected.length > 0"
                            >
                                @csrf
                                <input type="hidden" name="file" value="{{ $file->identifier }}">
                                <input type="hidden" name="lines" x-model="selected">

                                <x-filament::button 
                                    color="danger"
                                    class="whitespace-nowrap"
                                    onclick="return confirm('Weet je zeker dat je deze regels wil verwijderen?')"
                                >
                                    Verwijder geselecteerde regels
                                </x-filament::button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Logs --}}
                <div class="space-y-2 h-[75vh] overflow-y-auto font-mono text-sm">

                    {{-- Select all --}}
                    <div class="flex items-center gap-2 mb-2">
                        <input type="checkbox" @click="toggleAll({{ $logs->toJson() }})">
                        <span class="text-gray-600">Selecteer alles</span>
                    </div>

                    @foreach ($logs as $log)
                        <div
                            class="p-3 rounded border
                            @if ($log->level === 'error') border-red-400 bg-red-50 dark:bg-red-900/20
                            @elseif($log->level === 'warning') border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20
                            @elseif($log->level === 'info') border-blue-400 bg-blue-50 dark:bg-blue-900/20
                            @else border-gray-300 bg-gray-50 dark:bg-gray-800 @endif
                        ">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" :value="{{ $log->index }}" x-model="selected">
                                    <span class="font-semibold">{{ strtoupper($log->level) }}</span>
                                </div>

                                <span class="text-gray-500">{{ $log->datetime }}</span>
                            </div>

                            <pre class="whitespace-pre-wrap mt-2">{{ $log->message }}</pre>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>