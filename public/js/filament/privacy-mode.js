if (window.Alpine) {
    // Alpine is al gestart, registreer store direct
    Alpine.store('privacyMode', {
        enabled: localStorage.getItem('privacy-mode') === 'true',
        init() { this.apply(); },
        toggle() {
            this.enabled = !this.enabled;
            localStorage.setItem('privacy-mode', this.enabled);
            this.apply();
        },
        apply() {
            document.body.classList.toggle('privacy-mode', this.enabled);
        },
    });
} else {
    // Alpine nog niet gestart, wacht op event
    document.addEventListener('alpine:init', () => {
        Alpine.store('privacyMode', {
            enabled: localStorage.getItem('privacy-mode') === 'true',
            init() { this.apply(); },
            toggle() {
                this.enabled = !this.enabled;
                localStorage.setItem('privacy-mode', this.enabled);
                this.apply();
            },
            apply() {
                document.body.classList.toggle('privacy-mode', this.enabled);
            },
        });
    });
}