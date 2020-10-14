<template>
  <section class="login-info">
    <div class="container">
      <div class="user-info">
        <h1>{{ title }}</h1>
        <form @submit.prevent="login">
          <label class="form-block">
            <span>{{ label.email }}</span>
            <input
              type="email"
              placeholder="name@email.com"
              v-model="form.email"
              autocomplete="off"
              :disabled="loading"
              :class="{'is-invalid' : error.email}"
            >
          </label>
          <label class="form-block">
            <span>{{ label.password }}</span>
            <input
              type="password"
              placeholder="password"
              id="password"
              v-model="form.password"
              :disabled="loading"
            >
          </label>
          <submit-btn
            @click="btnClick"
            :loading="loading"
            :labelBefore="label.btnTitleBefor"
            :labelAfter="label.btnTitleAfter"
            :type="'btn-login'"
          >
           <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
          </submit-btn>
        </form>
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import Loading from "vue-loading-overlay";
import Button from "./../shared/button/button.vue";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
export default {
  data() {
    return {
      fullPage: true,
      loading: false,
      title: "Log In",
      label: {
        email: "Email Address",
        password: "Password",
        btnTitleBefor: "Login",
        btnTitleAfter: "Logging in"
      },
      form: {
        email: "admin@example.com",
        password: "admin"
      },
      error: {
        email: null,
        password: null
      }
    };
  },
  components: {
    Loading,
    "submit-btn": Button,
  },
  methods: {
    btnClick() {},
    login() {
      this.loading = true;
      axios
        .post(api.login, this.form)
        .then(res => {
          this.loading = false;
          this.$noty.success("Welcome back!");
          this.$emit("loginSuccess", res.data);
        })
        .catch(err => {
          err.response.data.error && this.$noty.error(err.response.data.error);
          err.response.data.errors
            ? this.setErrors(err.response.data.errors)
            : this.clearErrors();
          this.loading = false;
        });
    },
    setErrors(errors) {
      this.error.email = errors.email ? errors.email[0] : null;
      this.error.password = errors.password ? errors.password[0] : null;
    },
    clearErrors() {
      this.error.email = null;
      this.error.password = null;
    }
  }
};
</script>