<template>
  <button @click="callback($event)" :class="type" :disabled="loading">
    <slot></slot>
    <span v-if="labelBefore" v-show="!loading">{{ labelBefore }}</span>
    <span v-if="labelAfter" v-show="loading">{{ labelAfter }}</span>
  </button>
</template>
<script>
export default {
  props: ["loading", "labelBefore", "labelAfter", "type"],
  data() {
    return {};
  },
  components: {},
  methods: {
    callback: function(e) {
      this.$emit("click", e);
    }
  }
};
</script>
    