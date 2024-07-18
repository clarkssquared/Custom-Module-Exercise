<template>
  <div class='app'>
    <div ref='messages' class='chat-form-response'>
      <ResponseDisplay
          v-for='message in messages'
          :key='message.id'
          :text='message.text'
          :nickname="message.nickname"
          :agent="message.agent"
          :agent_name="message.agent_name"
      />
      <template v-if="messages.length < 1">
        
      </template>
      <img src="./assets/loader.svg" class="loader" v-if="showLoader"/>
    </div>
    <ChatBox
        class='chat-box'
        @submit='onSubmit'
    />
  </div>
</template>

<script>
import ChatBox from "@/components/ChatBox";
import ResponseDisplay from "@/components/ResponseDisplay";
import axios from 'axios';
import { uuid } from 'vue-uuid'; 

export default {
  name: 'App',
  components: {
    ChatBox,
    ResponseDisplay
  },
  created() {
    this.getChat();
  },
  methods: {
    onRegister(event) {
      event.preventDefault();
    },
    getChat() {
    },

    onSubmit(event, text) {
      event.preventDefault();
      if (!text) return;

      this.messages.push({
        id: 'query',
        text: text,
        agent: 'guest',
        nickname: 'G',
        agent_name: 'You'
      })
      
      this.fetch(text)
    },
    async fetch(text) {
      this.showLoader = true;
      const searchResult = await axios
        .get(this.queryUrl + '?query=' + text + '&uuid=' + this.uuid, {
          headers: {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Headers': '*',
            'Accept': 'application/json'
          }
        })

      this.showLoader = false;
      this.response = searchResult.data;
      if (this.response.data) {
        this.messages.push({
          id: this.response.entity_id,
          text: this.response.data,
          agent: 'agent',
          nickname: 'P',
          agent_name: 'Search Result'
        })
      }
    },
  },
  data: () => ({
    messages: [],
    showLoader: false,
    // queryUrl: '/result-test.php', 
    queryUrl: '/search-query',
    uuid: uuid.v1(),
  })
}
</script>

<style>
  * {
    box-sizing: border-box;
  }
  
  html {
    font-family: 'Georama', sans-serif;
  }
  
  body {
    margin: 0;
    padding: 1rem;
  }
  
  button {
    border: 0;
    background: #2a60ff;
    color: white;
    cursor: pointer;
    padding: 1rem;
  }
  
  input {
    border: 0;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.1);
  }
  </style>
  
  <style scoped>
  .app {
    height: 100vh;
    display: flex;
    flex-direction: column;
  }
  
  .messages {
    flex-grow: 1;
    overflow: auto;
    padding: 1rem;
  }

  .message {
    display: flex;
    align-items: start;
    column-gap: 10px;
    margin-bottom: 20px;
  }
  
  .avatar {
    width: 1.5rem;
    height: 1.5rem;
    text-align: center;
    background: gray;
    border-radius: 50%;
    padding: .2rem;
    color: white;
  }

  .loader {
    max-width: 16px;
  }
  </style>
  