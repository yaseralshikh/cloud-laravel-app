document.addEventListener('submit', (event) => {
    const form = event.target;

    if (event.defaultPrevented || !form.matches('form[data-submit-loading]')) {
        return;
    }

    if (form.getAttribute('aria-busy') === 'true') {
        event.preventDefault();
        return;
    }

    const button = event.submitter ?? form.querySelector('button[type="submit"]');

    if (!button) {
        return;
    }

    form.setAttribute('aria-busy', 'true');
    button.setAttribute('data-pending-submit', '');
    button.disabled = true;
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submit-loading][aria-busy="true"]').forEach((form) => {
        form.removeAttribute('aria-busy');

        form.querySelectorAll('[data-pending-submit]').forEach((button) => {
            button.removeAttribute('data-pending-submit');
            button.disabled = false;
        });
    });
});
