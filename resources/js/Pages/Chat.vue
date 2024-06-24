<template>

    <Head title="Chat" />

    <AuthenticatedLayout>
      <div class="chat-container">
        <div class="chat-messages">
          <div v-for="message in messages" :key="message.id"
            :class="{ 'user-message': message.user === userName, 'chatgpt-message': message.user === 'ChatGPT' }">
            <span class="user">{{ message.user }}:</span> {{ message.text }}
          </div>
        </div>
        <div class="chat-input">
          <textarea type="text" v-model="newMessage" @keyup.enter="sendMessage"></textarea>
          <button @click="sendMessage">Enviar</button>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';

  export default {
    components: {
      AuthenticatedLayout,
      Head
    },
    data() {
      return {
        messages: [],
        newMessage: '',
        userName: '' // <-- Adicionado para armazenar o nome do usuário
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
          user: this.userName, // <-- Usando o nome do usuário aqui
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
    height: 400px;
    /* Ajuste a altura conforme necessário */
    border: 1px solid #ccc;
    padding: 10px;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 10px;
}

.message {
    margin-bottom: 5px;
    padding: 8px;
    border-radius: 5px;
}

.user-message {
    background-color: #eee;
    text-align: right;
}

.chatgpt-message {
    background-color: #ccf;
}
</style>
