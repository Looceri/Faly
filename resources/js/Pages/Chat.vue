<template>
  <Head title="Chat" />

  <AuthenticatedLayout>
    <div class="chat-container">
      <!-- Header -->
      <div class="top">
        <img src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">
        <div>
          <p>{{ userName }}</p>
          <small>Online</small>
        </div>
      </div>
      <!-- End Header -->

      <!-- Chat -->
      <div class="chat-messages">
        <div v-for="message in messages" :key="message.id"
          :class="{ 'user-message': message.user === userName, 'chatgpt-message': message.user === 'ChatGPT' }">
          <img v-if="message.user !== userName" src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">
          <span class="user">{{ message.user }}:</span> {{ message.text }}
          <img v-if="message.user === userName" src="https://assets.edlin.app/images/rossedlin/03/rossedlin-03-100.jpg" alt="Avatar">
        </div>
      </div>
      <!-- End Chat -->

      <!-- Footer -->
      <div class="chat-input bottom">
        <form @submit.prevent="sendMessage">
          <input type="text" v-model="newMessage" placeholder="Enter message..." autocomplete="off">
          <button type="submit">Enviar</button>
        </form>
      </div>
      <!-- End Footer -->
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  components: {
    AuthenticatedLayout,
    Head
  },
  data() {
    return {
      messages: [],
      newMessage: '',
      userName: ''
    };
  },
  mounted() {
    // Obtém o nome do usuário autenticado (ajuste conforme necessário)
    this.userName = this.$page.props.auth.user.name;
  },
  methods: {
    sendMessage() {
      if (this.newMessage.trim() === '') return;

      const userMessage = {
        id: Date.now(),
        user: this.userName,
        text: this.newMessage,
      };
      this.messages.push(userMessage);

      axios.post('/chatgpt', { message: this.newMessage })
        .then(response => {
          const chatGptMessage = {
            id: Date.now(),
            user: 'ChatGPT',
            text: response.data.choices && response.data.choices.length > 0
              ? response.data.choices[0].text.trim()
              : 'Resposta não encontrada',
          };
          this.messages.push(chatGptMessage);
        })
        .catch(error => {
          console.error('Erro ao enviar mensagem:', error);
          const errorMessage = {
            id: Date.now(),
            user: 'ChatGPT',
            text: 'Ocorreu um erro ao processar sua solicitação.',
          };
          this.messages.push(errorMessage);
        });

      this.newMessage = '';
    },
  },
};
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  border: 1px solid #ccc;
}

.top {
  display: flex;
  align-items: center;
  padding: 10px;
  border-bottom: 1px solid #ccc;
}

.top img {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

.top div p {
  margin: 0;
  font-weight: bold;
}

.top div small {
  color: green;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 10px;
}

.message {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.user-message {
  justify-content: flex-end;
  background-color: #eee;
  text-align: right;
}

.chatgpt-message {
  justify-content: flex-start;
  background-color: #ccf;
}

.message img {
  border-radius: 50%;
  width: 30px;
  height: 30px;
  margin: 0 10px;
}

.chat-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ccc;
}

.chat-input form {
  display: flex;
  width: 100%;
}

.chat-input input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.chat-input button {
  padding: 10px;
  background-color: #007bff;
  border: none;
  border-radius: 5px;
  color: white;
  cursor: pointer;
}

.chat-input button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}
</style>
