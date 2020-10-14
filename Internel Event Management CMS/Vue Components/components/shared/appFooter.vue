<template>
  <footer v-show="hide">
  </footer>
</template>

<script>
import { siteName } from "../../config";
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      hide: true,
      siteName: siteName,
      currentYear: new Date().getFullYear()
    };
  },
  computed: mapGetters(["isLoggedIn"])
};
</script>
