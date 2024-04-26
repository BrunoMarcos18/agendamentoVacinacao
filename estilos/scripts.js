document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Envia os dados do formulário para login.php usando fetch
        fetch('../backend/login.php', { // Ajuste no caminho para backend
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `username=${username}&password=${password}`
        })
        .then(response => response.text())
        .then(data => {
            // Exibe a resposta do servidor
            alert(data);
        })
        .catch(error => {
            console.error('Erro:', error);
        });

        // Limpa os campos após enviar
        document.getElementById('username').value = '';
        document.getElementById('password').value = '';
    });
});
