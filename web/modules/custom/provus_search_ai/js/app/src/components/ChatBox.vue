<template>
  <form class='chat-box' @submit='onSubmit($event)'>
    <input
        v-model='text'
        placeholder='Type your question...'
        type='text'
    />
    <button :disabled='text === ""'>Send</button>
  </form>
</template>

<script>
export default {
  name: 'ChatBox',
  data: () => ({
    text: '',
  }),
  methods: {
    onSubmit(event) {
      this.$emit("submit", event, this.text);
      this.text = '';
    }
  }
}
</script>

<style scoped>
.chat-box {
  width: 100%;
  display: flex;
}

input {
  width: min(100%, 20rem);
  flex-grow: 1;
}

button:disabled {
  opacity: 0.5;
}
</style>
