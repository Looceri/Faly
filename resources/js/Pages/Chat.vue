<template>

    <Head title="Chat" />
    <AuthenticatedLayout>
        <div class="chat-container bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="chat-header bg-white dark:bg-gray-900">
                <h2 class="text-gray-900 dark:text-white">Chat com Gemini</h2>
            </div>
            <div class="chat-messages bg-white dark:bg-gray-800" id="chat-messages">
                <div v-for="message in messages" :key="message.id"
                    :class="{ 'user-message': message.user === userName, 'chatgpt-message': message.user !== userName }"
                    class="message-container">
                    <div class="message-content" :class="{ 'flex-row-reverse': message.user === userName }">
                        <div class="message-avatar">
                            <svg v-if="message.user === userName"  width="40px" height="40px"
                                viewBox="0 0 24 24" id="user" data-name="Flat Color" xmlns="http://www.w3.org/2000/svg"
                                class=" dark:fill-white fill-black icon flat-color">
                                <path id="primary"
                                    d="M21,20a2,2,0,0,1-2,2H5a2,2,0,0,1-2-2,6,6,0,0,1,6-6h6A6,6,0,0,1,21,20Zm-9-8A5,5,0,1,0,7,7,5,5,0,0,0,12,12Z"
                                    ></path>
                            </svg>
                            <div v-else class="bot-avatar bg-gray-200 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </div>
                        </div>
                        <div class="message-text bg-gray-100 dark:bg-gray-700">
                            <div v-html="message.text" :class="{ 'text-right': message.user === userName }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-input bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
                <form @submit.prevent="sendMessage">
                    <input type="text" v-model="newMessage" placeholder="Digite sua mensagem..." autocomplete="off"
                        class="input-message bg-gray-100 dark:bg-gray-700 dark:text-white">
                    <button type="submit" :disabled="newMessage.trim() === ''" class="send-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'; // Importa ref para criar variáveis reativas e onMounted para manipular eventos após a montagem
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3'; // Importa usePage do Inertia.js
import axios from 'axios';

const user = usePage().props.auth.user; // Obtém o usuário autenticado

const messages = ref([]); // Variável reativa para armazenar mensagens
const newMessage = ref(''); // Variável reativa para armazenar nova mensagem digitada
const userName = user.name; // Nome do usuário atual

const sendMessage = () => {
    if (newMessage.value.trim() === '') return;

    const userMessage = {
        id: Date.now(),
        user: userName,
        text: newMessage.value,
    };
    messages.value.push(userMessage); // Adiciona a mensagem do usuário à lista de mensagens
    newMessage.value = ''; // Limpa o campo de entrada após enviar a mensagem

    axios.post('/chat', { message: userMessage.text })
        .then(response => {
            const geminiMessage = {
                id: Date.now(),
                user: 'Gemini',
                text: response.data.text?.trim() ?? 'Resposta não encontrado',
            };
            geminiMessage.text = geminiMessage.text.replace(/<br>/g, '\n') // Replace <br> with newlines for better handling
                .replace(/(\*\*|\_\_)(.+?)\1/g, '<strong>$2</strong>') // Convert bold text to HTML tags
                .replace(/^\s*-\s*/g, '<li>') // Convert bullet points to HTML list items
                .replace(/\n\s*<\/li>/g, '</li>\n') // Add newlines after closing list items
                .replace(/^\s*<\/ul>\s*$/g, '</ul>') // Add newline after closing unordered list
                .replace(/\n/g, '<br>'); // Convert newlines to HTML line breaks
            console.log(geminiMessage.text);
            console.log("");
            geminiMessage.text = "<p>" + geminiMessage.text + "</p>";
            console.log(geminiMessage.text);
            messages.value.push(geminiMessage); // Adiciona a mensagem do chatbot à lista de mensagens
            scrollToBottom(); // Rolamento automático para a última mensagem
        })
        .catch(error => {
            console.error('Error sending message:', error);
            const errorMessage = {
                id: Date.now(),
                user: 'Gemini',
                text: 'Um erro ocorreu enquanto processava o seu request.',
            };
            messages.value.push(errorMessage); // Adiciona mensagem de erro à lista de mensagens
            scrollToBottom(); // Rolamento automático para a última mensagem
        });
};

const scrollToBottom = () => {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
};

onMounted(() => {
    scrollToBottom(); // Rolamento automático para a última mensagem após a montagem do componente
});
</script>


<style scoped>
.chat-container {
    display: flex;
    flex-direction: column;
    height: 93vh;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.chat-header {
    background-color: #007bff;
    color: white;
    padding: 10px;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.message-container {
    margin-bottom: 15px;
}

.message-content {
    display: flex;
    align-items: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.chatgpt-message {
    justify-content: flex-start;
}

.message-avatar {
    width: 40px;
    height: 40px;
    margin-right: 10px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-message .message-avatar {
    margin-left: 10px;
    margin-right: 0;
}

.message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bot-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background-color: #e0e0e0;
    color: #757575;
}

.bot-avatar svg {
    width: 60%;
    height: 60%;
}

.message-text {
    background-color: #f0f0f0;
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 70%;
    word-wrap: break-word;
}

.user-message .message-text {
    background-color: #007bff;
    color: white;
}

.text-right {
    text-align: right;
}

.chat-input {
    padding: 10px;
    border-top: 1px solid #e0e0e0;
}

.chat-input form {
    display: flex;
    align-items: center;
}

.input-message {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    margin-right: 10px;
}

.send-button {
    background-color: transparent;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.send-button svg {
    fill: #007bff;
    stroke: #007bff;
}

.dark .chat-container {
    background-color: #1a202c;
    border-color: #2d3748;
}

.dark .chat-header {
    background-color: #1a202c;
}

.dark .chat-messages {
    background-color: #1a202c;
}

.dark .message-text {
    background-color: #2d3748;
}

.dark .bot-avatar {
    background-color: #2d3748;
}

.dark .chat-input {
    background-color: #1a202c;
    border-color: #2d3748;
}

.dark .input-message {
    background-color: #2d3748;
    color: white;
}
</style>
