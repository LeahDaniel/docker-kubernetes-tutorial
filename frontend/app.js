const form = document.getElementById('shorten-form');
const urlInput = document.getElementById('url');
const submitBtn = document.getElementById('submit-btn');
const result = document.getElementById('result');

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    submitBtn.disabled = true;
    result.className = 'result visible';
    result.innerHTML = 'Creating link…';

    try {
        const response = await fetch('/api/links', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ url: urlInput.value }),
        });

        const body = await response.json();

        if (!response.ok) {
            const message = body.message || 'Something went wrong.';
            const errors = body.errors?.url?.join(' ') || '';
            throw new Error([message, errors].filter(Boolean).join(' '));
        }

        const link = body.data;
        result.innerHTML = `
            <p><strong>Short link:</strong> <a href="/${link.code}">${link.short_url}</a></p>
            <p><strong>Original:</strong> ${link.url}</p>
        `;
        urlInput.value = '';
    } catch (error) {
        result.innerHTML = `<p class="error">${error.message}</p>`;
    } finally {
        submitBtn.disabled = false;
    }
});
