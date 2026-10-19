(() => {
    const keyPrefix = 'fixdesk-form-draft:';
    const pendingKey = 'fixdesk-form-pending';
    const forms = document.querySelectorAll('form.card[method="POST"]');

    if (document.querySelector('.alert-success')) {
        const submittedDraftKey = sessionStorage.getItem(pendingKey);

        if (submittedDraftKey?.startsWith(keyPrefix)) {
            localStorage.removeItem(submittedDraftKey);
        }

        sessionStorage.removeItem(pendingKey);
    } else if (document.querySelector('.alert-danger')) {
        sessionStorage.removeItem(pendingKey);
    }

    forms.forEach((form) => {
        const draftKey = `${keyPrefix}${form.action}`;
        const fields = Array.from(form.elements).filter((field) => {
            return field.name && !['hidden', 'submit', 'button', 'reset', 'file'].includes(field.type);
        });

        const saveDraft = () => {
            const draft = fields.map((field) => ({
                name: field.name,
                type: field.type,
                value: field.type === 'select-multiple'
                    ? Array.from(field.selectedOptions, (option) => option.value)
                    : field.value,
                checked: ['checkbox', 'radio'].includes(field.type) ? field.checked : undefined,
            }));

            localStorage.setItem(draftKey, JSON.stringify(draft));
        };

        const legacyDraft = sessionStorage.getItem(draftKey);
        const savedDraft = localStorage.getItem(draftKey) ?? legacyDraft;

        if (legacyDraft && !localStorage.getItem(draftKey)) {
            localStorage.setItem(draftKey, legacyDraft);
            sessionStorage.removeItem(draftKey);
        }

        if (savedDraft) {
            try {
                const draft = JSON.parse(savedDraft);
                const usedFields = new Set();

                draft.forEach((savedField) => {
                    const field = fields.find((candidate, index) => {
                        return candidate.name === savedField.name && !usedFields.has(index);
                    });

                    if (!field) {
                        return;
                    }

                    const fieldIndex = fields.indexOf(field);
                    usedFields.add(fieldIndex);

                    if (['checkbox', 'radio'].includes(field.type)) {
                        field.checked = savedField.checked;
                    } else if (field.type === 'select-multiple') {
                        Array.from(field.options).forEach((option) => {
                            option.selected = savedField.value.includes(option.value);
                        });
                    } else {
                        field.value = savedField.value;
                    }
                });
            } catch {
                localStorage.removeItem(draftKey);
            }
        }

        form.addEventListener('input', saveDraft);
        form.addEventListener('change', saveDraft);
        form.addEventListener('submit', () => {
            saveDraft();
            sessionStorage.setItem(pendingKey, draftKey);
        });
    });
})();
