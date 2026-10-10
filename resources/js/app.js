import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import 'trix';
import 'trix/dist/trix.css';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

function isBlankHtml(html) {
    return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim() === '';
}

/*
 * Trix 2 registers its custom elements from a setTimeout(0) in its module body,
 * so <trix-editor> may still be an un-upgraded HTMLElement when Alpine boots.
 * Binding is therefore deferred to the trix-initialize event Trix fires once the
 * element is upgraded.
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('richTextEditor', (property, initialValue) => ({
        isBound: false,

        init() {
            const editor = this.$refs.editor;

            if (editor.editor) {
                this.bind(editor, initialValue);
            } else {
                editor.addEventListener('trix-initialize', () => this.bind(editor, initialValue), { once: true });
            }
        },

        bind(editor, initialValue) {
            if (this.isBound) {
                return;
            }

            this.isBound = true;

            if (initialValue) {
                editor.value = initialValue;
            }

            editor.dataset.empty = String(isBlankHtml(editor.value));

            editor.addEventListener('trix-change', () => {
                const value = editor.value;

                editor.dataset.empty = String(isBlankHtml(value));

                // Deferred: assigning a $wire property does not send a request,
                // the value travels with the next commit (the form submit).
                this.$wire.$set(property, value);
            });
        },
    }));
});

function initRevealAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal:not(.revealed)').forEach(el => observer.observe(el));

    document.querySelectorAll('.stagger-children').forEach(parent => {
        const children = parent.children;
        Array.from(children).forEach((child, index) => {
            child.style.animationDelay = `${index * 0.1}s`;
        });
    });
}

window.Echo.private('App.Models.User.' + window.userId)
    .notification((notification) => {
        if(notification.type === 'notification.created'){
            window.Livewire.dispatch('new-notification', {notification: notification})
        }
    });

document.addEventListener('DOMContentLoaded', initRevealAnimations);

document.addEventListener('livewire:navigated', initRevealAnimations);
