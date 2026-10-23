document.addEventListener('alpine:init', () => {
    Alpine.data('feedbackWidget', ({ endpoint, csrfToken, successMessage }) => ({
        open: false,
        submitting: false,
        success: false,
        message: '',
        email: '',
        rating: null,
        errors: {},
        endpoint,
        csrfToken,
        successMessage,

        reset() {
            this.message = '';
            this.email = '';
            this.rating = null;
            this.errors = {};
            this.success = false;
        },

        close() {
            this.open = false;
            this.reset();
        },

        async submit() {
            this.submitting = true;
            this.errors = {};

            const response = await fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    message: this.message,
                    email: this.email || null,
                    rating: this.rating,
                }),
            });

            const payload = await response.json();
            this.submitting = false;

            if (! response.ok) {
                this.errors = payload.errors ?? { form: [payload.message] };
                return;
            }

            this.success = true;
            this.message = '';
            this.email = '';
            this.rating = null;
        },
    }));
});
