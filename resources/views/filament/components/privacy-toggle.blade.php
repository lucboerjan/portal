<div x-data class="flex items-center">

    {{-- Toggle button --}}
    <button
        x-on:click="$store.privacyMode.toggle()"
        class="privacy-toggle"
        :title="$store.privacyMode.enabled ? 'Disable Privacy Mode' : 'Enable Privacy Mode'"
    >
        <svg x-show="!$store.privacyMode.enabled" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <svg x-show="$store.privacyMode.enabled" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
        </svg>
    </button>

    {{-- Pin modal --}}
    <div
        x-show="$store.privacyMode.showPinModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        x-on:keydown.escape.window="$store.privacyMode.showPinModal = false"
    >
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-80" x-on:click.stop>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Voer je pincode in
            </h2>

            <input
                type="password"
                inputmode="numeric"
                maxlength="10"
                x-model="$store.privacyMode.pinInput"
                x-on:keydown.enter="$store.privacyMode.submitPin()"
                x-ref="pinField"
                x-init="$watch('$store.privacyMode.showPinModal', val => val && $nextTick(() => $refs.pinField.focus()))"
                class="w-full border rounded-lg px-3 py-2 text-center text-xl tracking-widest dark:bg-gray-700 dark:text-white"
                :class="$store.privacyMode.pinError ? 'border-red-500' : 'border-gray-300'"
                placeholder="••••"
            />

            <p x-show="$store.privacyMode.pinError" class="text-red-500 text-sm mt-2">
                Ongeldige pincode
            </p>

            <div class="flex gap-2 mt-4">
                <button
                    x-on:click="$store.privacyMode.showPinModal = false"
                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    Annuleren
                </button>
                <button
                    x-on:click="$store.privacyMode.submitPin()"
                    :disabled="$store.privacyMode.loading"
                    class="flex-1 px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50"
                >
                    <span x-show="!$store.privacyMode.loading">Bevestigen</span>
                    <span x-show="$store.privacyMode.loading">...</span>
                </button>
            </div>
        </div>
    </div>

</div>