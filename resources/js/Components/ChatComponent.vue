<template>
    <div class="chat-container">
      <div class="chat-messages">
        <div v-for="message in messages" :key="message.id" :class="{ 'user-message': message.user === 'Você', 'chatgpt-message': message.user === 'ChatGPT' }">
          <span class="user">{{ message.user }}:</span> {{ message.text }}
        </div>
      </div>
      <div class="chat-input">
        <input type="text" v-model="newMessage" @keyup.enter="sendMessage">
        <button @click="sendMessage">Enviar</button>
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        messages: [],
        newMessage: '',
      };
    },
    methods: {
      sendMessage() {
        if (this.newMessage.trim() === '') return;

        this.messages.push({
          id: Date.now(),
          user: 'Você',
          text: this.newMessage,
        });

        axios.post('/chatgpt', { message: this.newMessage })
          .then(response => {
            this.messages.push({
              id: Date.now(),
              user: 'ChatGPT',
              text: response.data.choices[0].text.trim(),
            });
          })
          .catch(error => {
            console.error(error);
            this.messages.push({
              id: Date.now(),
              user: 'ChatGPT',
              text: 'Ocorreu um erro ao processar sua solicitação.',
            });
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
    height: 400px; /* Ajuste a altura conforme necessário */
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
