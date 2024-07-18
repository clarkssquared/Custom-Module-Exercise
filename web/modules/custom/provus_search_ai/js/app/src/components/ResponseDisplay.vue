<template>
  <div
      :class='["message"]'
  >
    <div class="text">
      <div class="agent">{{ agent_name }}</div>
      <div v-html="text"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResponseDisplay',
  props: [
    'text',
    'nickname',
    'agent_name',
    'agent',
  ]
}
</script>
