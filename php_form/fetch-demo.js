const form = document.querySelector('#demo-form');
const result = document.querySelector('#result');

form.addEventListener('submit', async (event) => {
    event.preventDefault();                       // 1. stay on this page

    const formData = new FormData(form);          // 2. collect every input that has a name

    const response = await fetch('echo.php', {    // 3. send a POST request to PHP
        method: 'POST',
        body: formData
    });

    const data = await response.json();           // 4. JSON text -> JavaScript object

    result.textContent = data.method + ': ' + data.displayName;   // 5. show it (as text)
});
