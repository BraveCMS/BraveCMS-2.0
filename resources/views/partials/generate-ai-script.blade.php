<script>
    document.getElementById('generateArticleBtn').addEventListener('click', async function() {
        const prompt = document.getElementById('aiPrompt').value.trim();
        if (!prompt) return alert('Please enter a prompt for the AI.');

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Generating...`;

        try {
            const response = await fetch('{{ route('dashboard.articles.ai-generate') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt
                })
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error('Non-JSON response:', text);
                throw new Error('AI returned invalid response.');
            }

            if (data.error) {
                alert('AI generation failed: ' + data.error);
            } else {
                const titleField = document.getElementById('title');
                const shortDescField = document.getElementById('short_description');
                const contentField = CKEDITOR.instances.content;

                if (titleField) titleField.value = data.title || '';
                if (shortDescField) shortDescField.value = data.short_description || '';
                if (contentField) contentField.setData(data.content || '');

                const modalEl = document.getElementById('aiWriterModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance.hide();
            }
        } catch (err) {
            console.error(err);
            alert('AI generation failed: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
