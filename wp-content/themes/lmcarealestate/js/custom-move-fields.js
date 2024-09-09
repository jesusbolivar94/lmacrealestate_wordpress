document.addEventListener('DOMContentLoaded', () => {
    const [form1, form2] = [
        document.querySelector('#search-1 form'),
        document.querySelector('#search-2 form')
    ]

    form1.addEventListener('submit', (event) => {
        event.preventDefault()

        form1.querySelectorAll('.dynamic-hidden-field')
            .forEach(hiddenField => hiddenField.remove());

        [...form2.querySelectorAll('input, select, textarea')].forEach((field) => {
            if (field.type === 'radio') {
                return;
            }

            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = field.name;
            hiddenField.value = field.value;
            hiddenField.classList.add('dynamic-hidden-field');
            form1.appendChild(hiddenField);
        });

        form1.submit()
    })
})