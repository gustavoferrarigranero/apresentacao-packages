document.addEventListener('alpine:init', () => {
    Alpine.data('feedbackWidget', () => ({
        open: false,
    }));
});
