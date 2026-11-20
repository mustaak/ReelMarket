//

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
import 'emoji-picker-element';

document.addEventListener('alpine:init', () => {
    Alpine.data('chatGifPicker', (apiKey) => ({
        apiKey,
        isOpen: false,
        isLoading: false,
        hasLoadedTrending: false,
        query: '',
        gifs: [],
        error: '',
        requestController: null,

        toggle() {
            this.isOpen = !this.isOpen;

            if (this.isOpen && !this.hasLoadedTrending) {
                this.loadGifs();
            }
        },

        async loadGifs() {
            if (!this.apiKey) {
                this.gifs = [];
                this.error = 'GIF search needs a GIPHY API key in the server .env file.';

                return;
            }

            this.isLoading = true;
            this.error = '';
            this.requestController?.abort();
            const requestController = new AbortController();
            this.requestController = requestController;

            const endpoint = this.query.trim()
                ? 'https://api.giphy.com/v1/gifs/search'
                : 'https://api.giphy.com/v1/gifs/trending';
            const params = new URLSearchParams({
                api_key: this.apiKey,
                limit: '18',
                rating: 'g',
                bundle: 'messaging_non_clips',
            });

            if (this.query.trim()) {
                params.set('q', this.query.trim());
            }

            try {
                const response = await fetch(`${endpoint}?${params.toString()}`, {
                    credentials: 'omit',
                    referrerPolicy: 'no-referrer',
                    signal: requestController.signal,
                });

                if (requestController.signal.aborted) {
                    return;
                }

                if (!response.ok) {
                    this.gifs = [];
                    this.error = `GIPHY could not load GIFs (HTTP ${response.status}).`;

                    return;
                }

                const payload = await response.json();

                if (requestController.signal.aborted) {
                    return;
                }

                if (!Array.isArray(payload.data)) {
                    this.gifs = [];
                    this.error = 'GIPHY returned an invalid response.';

                    return;
                }

                this.gifs = payload.data.flatMap((gif) => {
                    const preview = gif.images?.fixed_width_small?.url;
                    const url = gif.images?.fixed_width?.url ?? gif.images?.original?.url;
                    const mediaUrlPattern = /^https:\/\/media\d*\.giphy\.com\/.+\.gif(?:\?.*)?$/i;

                    return typeof preview === 'string'
                        && typeof url === 'string'
                        && mediaUrlPattern.test(preview)
                        && mediaUrlPattern.test(url)
                        ? [{ id: gif.id, title: gif.title || 'GIF', preview, url }]
                        : [];
                });
                this.hasLoadedTrending = true;
            } catch (error) {
                if (error instanceof DOMException && error.name === 'AbortError') {
                    return;
                }

                if (!(error instanceof TypeError || error instanceof SyntaxError)) {
                    throw error;
                }

                console.error('Unable to load GIPHY results.', error);
                this.gifs = [];
                this.error = 'Could not load GIFs. Check your connection and try again.';
            } finally {
                if (this.requestController === requestController) {
                    this.isLoading = false;
                }
            }
        },

        selectGif(gif) {
            this.$wire.selectGif(gif.url);
            this.isOpen = false;
        },
    }));
});
