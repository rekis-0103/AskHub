import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('markdownEditor', (initialValue = '') => ({
    body: initialValue,
    html: '',
    loading: false,
    async preview() {
        this.loading = true;
        const response = await fetch('/markdown/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ body: this.body }),
        });
        const data = await response.json();
        this.html = data.html ?? '';
        this.loading = false;
    },
}));

Alpine.data('questionComposer', (initialTitle = '') => ({
    title: initialTitle,
    suggestions: [],
    timer: null,
    lookup() {
        clearTimeout(this.timer);
        if (this.title.trim().length < 4) {
            this.suggestions = [];
            return;
        }
        this.timer = setTimeout(async () => {
            const response = await fetch(`/questions/suggestions?q=${encodeURIComponent(this.title)}`, {
                headers: { 'Accept': 'application/json' },
            });
            this.suggestions = response.ok ? await response.json() : [];
        }, 300);
    },
}));

Alpine.start();
