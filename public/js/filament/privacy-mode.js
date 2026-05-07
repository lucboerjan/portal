/* if (window.Alpine) {
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
} */


const initPrivacyStore = () => {
    Alpine.store('privacyMode', {
        enabled: localStorage.getItem('privacy-mode') === 'true',
        showPinModal: false,
        pinInput: '',
        pinError: false,
        loading: false,

        init() {
            this.apply();
        },

        toggle() {
            if (this.enabled) {
                // Geblurd → onthullen → pin vragen
                this.pinInput = '';
                this.pinError = false;
                this.showPinModal = true;
            } else {
                // Zichtbaar → blurren → direct
                this.enabled = true;
                localStorage.setItem('privacy-mode', true);
                this.apply();
            }
        },

        async submitPin() {
            this.loading = true;
            this.pinError = false;

            try {
                const response = await fetch('/privacy/verify-pin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ pin: this.pinInput }),
                });

                if (response.ok) {
                    this.enabled = false;
                    localStorage.setItem('privacy-mode', false);
                    this.apply();
                    this.showPinModal = false;
                } else {
                    this.pinError = true;
                }
            } catch (e) {
                this.pinError = true;
            } finally {
                this.loading = false;
                this.pinInput = '';
            }
        },

        apply() {
            document.body.classList.toggle('privacy-mode', this.enabled);
        },
    });
};

if (window.Alpine) {
    initPrivacyStore();
} else {
    document.addEventListener('alpine:init', initPrivacyStore);
}    