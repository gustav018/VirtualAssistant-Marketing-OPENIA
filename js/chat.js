// Esperar a que se cargue el contenido de la página
document.addEventListener('DOMContentLoaded', function () {
    // Obteniendo elementos del DOM
    const chatForm = document.getElementById('chat-form'); // Obtener el formulario de chat
    const chatInput = document.getElementById('chat-input'); // Obtener el campo de entrada de chat
    const chatLog = document.getElementById('chat-log'); // Obtener el registro de chat

    // Historial de chat
    const chatHistory = [];

    // Agregando evento de envío de formulario
    chatForm.addEventListener('submit', function (event) {
        event.preventDefault();
        // Obteniendo el mensaje del campo de entrada y eliminando los espacios en blanco al inicio y al final
        const message = chatInput.value.trim();

        // Verificando si el mensaje no está vacío
        if (message !== '') {
            // Agregando el mensaje al historial de chat
            chatHistory.push(message);
            // Agregando el mensaje al registro de chat en el cliente
            appendMessage('Tu', message);
            // Limpiando el campo de entrada
            chatInput.value = '';

            // Mostrando la barra de carga
            $("#barra").show();

            // Obteniendo respuesta del chatbot
            $.ajax({
                type: "POST",
                url: "chat.php",
                dataType: "json",
                data: { chatHistory: chatHistory }, // Pasando el historial de chat a PHP
                success: function (data) {
                    if (data.error == 1) {
                        console.error('Error al obtener la respuesta del chatbot:', data.response);
                        // Mostrando un mensaje de error en caso de que ocurra un error
                        const errorMessage = document.getElementById('error-message');
                        errorMessage.innerHTML = 'Ha ocurrido un error. Por favor, intenta de nuevo más tarde.';
                    } else if (data.exito == 1) {
                        if (data.response) {
                            // Ocultando la descripción y mostrando el registro de chat
                            $("#descripcion").hide();
                            $("#chat-log").show();
                            $("#barra").hide();
                            // Agregando la respuesta del chatbot al historial de chat en el cliente
                            appendMessage('Bot', data.response);
                        }
                    }
                },
            });
        }
    });

    // Función para agregar un mensaje al registro de chat
    function appendMessage(sender, content) {
        const messageContainer = document.createElement('div');
        messageContainer.classList.add('message');
        messageContainer.classList.add(sender === 'Bot' ? 'bot-message' : 'user-message');

        const senderElement = document.createElement('strong');
        if (sender === 'Tu') {
            senderElement.innerHTML = 'Tu 😎: ';
        } else if (sender === 'Bot') {
            senderElement.innerHTML = 'Bot 🤖: ';
        }

        messageContainer.appendChild(senderElement);

        const contentElement = document.createElement('span');
        contentElement.innerText = content;
        messageContainer.appendChild(contentElement);

        chatLog.appendChild(messageContainer);
        // Desplazarse al final del contenedor de chat
        chatLog.scrollTop = chatLog.scrollHeight;
    }
});
