<template>
  <modal name="general-modal" :width="'94%'" :height="'auto'" :scrollable="false">
    <div id="customer-modal-content">
      <i id="modal-content-loading" class="fa fa-5x fa-circle-o-notch fa-spin fa-fw view-circle"></i>
    </div>
  </modal>
</template>
<script>
export default {
  data() {
    return {};
  },
  methods: {
    globalModalShow(name, params = null) {
      this.$modal.show(name, params);
    },
    globalModalHide(name) {
      this.$modal.hide(name);
    }
  },
  created() {
    this.$eventHub.$on("show-modal", this.globalModalShow);
    this.$eventHub.$on("hide-modal", this.globalModalHide);
  },
  beforeDestroy() {
    this.$eventHub.$off("show-modal");
    this.$eventHub.$off("hide-modal");
  }
};
</script>
<style>
.v--modal-box.v--modal {
  background-color: unset !important;
  color: white;
  box-shadow: unset !important;
  display: block;
  text-align: center;
}
</style>



