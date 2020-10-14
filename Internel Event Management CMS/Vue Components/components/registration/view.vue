<template>
  <div class="login-page">
    <header class="header">
      <nav class="main-navigation">
        <div class="container">
          <div class="logo login-logo">
            <a href="/">
              <img :src="siteUrl + '/images/logo.svg'">
            </a>
          </div>
        </div>
      </nav>
    </header>
    <div class="registration-content">
      <section class="login-info">
        <div class="container">
          <div class="user-info">
            <h1>{{ title }}</h1>
            <form @submit.prevent="register">
              <label class="form-block">
                <span>First Name</span>
                <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid' : error.first_name}"
                  id="first_name"
                  v-model="form.first_name"
                  autocomplete="off"
                  :disabled="loading"
                >
                <div class="invalid-feedback" v-show="error.first_name">{{ error.first_name }}</div>
              </label>

              <label class="form-block">
                <span>Last Name</span>
                <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid' : error.last_name}"
                  id="last_name"
                  v-model="form.last_name"
                  autocomplete="off"
                  :disabled="loading"
                >
                <div class="invalid-feedback" v-show="error.last_name">{{ error.last_name }}</div>
              </label>

              <label class="form-block">
                <span>Email</span>
                <input
                  type="email"
                  class="form-control"
                  :class="{'is-invalid' : error.email}"
                  id="email"
                  v-model="form.email"
                  autocomplete="off"
                  :disabled="loading"
                >
                <div class="invalid-feedback" v-show="error.email">{{ error.email }}</div>
              </label>

              <label class="form-block">
                <span>Phone</span>
                <input
                  type="text"
                  @keypress="isNumber($event)"
                  class="form-control"
                  :class="{'is-invalid' : error.phone}"
                  id="phone"
                  v-model="form.phone"
                  autocomplete="off"
                  :disabled="loading"
                >
                <div class="invalid-feedback" v-show="error.phone">{{ error.phone }}</div>
              </label>

              <div v-if="internal">
                <label class="form-block">
                  <span>State</span>
                  <select
                    :class="{'is-invalid' : error.state_id}"
                    v-model="form.state_id"
                    :disabled="loading"
                  >
                    <option value>Select State</option>
                    <option v-for="state in states" :value="state.id">{{ state.name }}</option>
                  </select>
                  <div class="invalid-feedback" v-show="error.state_id">{{ error.state_id }}</div>
                </label>

                <label class="form-block">
                  <span>City</span>
                  <select
                    :class="{'is-invalid' : error.city_id}"
                    v-model="form.city_id"
                    :disabled="loading"
                  >
                    <option value>Select State</option>
                    <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
                  </select>
                  <div class="invalid-feedback" v-show="error.city_id">{{ error.city_id }}</div>
                </label>

                <label class="form-block">
                  <span>Job Title</span>
                  <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid' : error.job_title}"
                    id="job_title"
                    v-model="form.job_title"
                    autocomplete="off"
                    :disabled="loading"
                  >
                  <div class="invalid-feedback" v-show="error.job_title">{{ error.job_title }}</div>
                </label>

                <label class="form-block">
                  <span>Marketing Title</span>
                  <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid' : error.marketing_title}"
                    id="marketing_title"
                    v-model="form.marketing_title"
                    autocomplete="off"
                    :disabled="loading"
                  >
                  <div
                    class="invalid-feedback"
                    v-show="error.marketing_title"
                  >{{ error.marketing_title }}</div>
                </label>

                <label class="form-block">
                  <span>Sector</span>
                  <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid' : error.sector}"
                    id="sector"
                    v-model="form.sector"
                    autocomplete="off"
                    :disabled="loading"
                  >
                  <div class="invalid-feedback" v-show="error.sector">{{ error.sector }}</div>
                </label>
              </div>

              <div v-else>
                <label class="form-block">
                  <span>Company</span>
                  <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid' : error.company}"
                    id="company"
                    v-model="form.company"
                    autocomplete="off"
                    :disabled="loading"
                  >
                  <div class="invalid-feedback" v-show="error.company">{{ error.company }}</div>
                </label>

                <label class="form-block">
                  <span>Title</span>
                  <input
                    type="text"
                    class="form-control"
                    :class="{'is-invalid' : error.title}"
                    id="title"
                    v-model="form.title"
                    autocomplete="off"
                    :disabled="loading"
                  >
                  <div class="invalid-feedback" v-show="error.title">{{ error.title }}</div>
                </label>
              </div>

              <label class="form-block">
                <span>Password</span>
                <input
                  type="password"
                  class="form-control"
                  :class="{'is-invalid' : error.password}"
                  id="password"
                  v-model="form.password"
                  :disabled="loading"
                >
                <div class="invalid-feedback" v-show="error.password">{{ error.password }}</div>
              </label>

              <label class="form-block">
                <span>Confirm Password</span>
                <input
                  type="password"
                  class="form-control"
                  :class="{'is-invalid' : error.password_confirmation}"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :disabled="loading"
                >

                <div
                  class="invalid-feedback"
                  v-show="error.password_confirmation"
                >{{ error.password_confirmation }}</div>
              </label>

              <submit-btn
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
      <loading :active.sync="loading" :can-cancel="false" :loader="'dots'" :is-full-page="fullPage"></loading>
      <div class="pattern-img-top">
        <img :src="siteUrl + '/images/login-pattern-top.png'">
      </div>
      <div class="pattern-img-bottom">
        <img :src="siteUrl + '/images/login-pattern-bottom.png'">
      </div>
      <div class="bg-img"></div>
    </div>
  </div>
</template>

<script>
import { api } from "../../config";
import { siteUrl } from "../../config";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";
import Button from "./../shared/button/button.vue";
export default {
  data() {
    return {
      type: this.$route.params.type,
      fullPage: true,
      loading: false,
      internal: false,
      siteUrl: siteUrl,
      states: "",
      cities: "",
      title: "External User",
      label: {
        btnTitleBefor: "Sign Up",
        btnTitleAfter: "Logging in"
      },
      form: {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        role_id: 3,
        password: "",
        password_confirmation: "",

        //external user data
        state_id: "",
        city_id: "",
        job_title: "",
        marketing_title: "",
        sector: "",
        status: "activated",

        //internal user data
        company: "",
        title: ""
      },
      error: {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        role_id: "",
        password: "",
        state_id: "",
        city_id: "",
        job_title: "",
        password_confirmation: "",

        marketing_title: "",
        sector: "",
        status: "",
        //    internal user data
        company: "",
        title: ""
      }
    };
  },
  components: {
    Loading,
    "submit-btn": Button
  },
  methods: {
    register() {
      let error = false;

      if (this.form.first_name == "") {
        this.error.first_name = "first name is required.";
        error = false;
      } else {
        this.error.first_name = "";
        error = true;
      }

      if (this.form.last_name == "") {
        this.error.last_name = "last name is required.";
        error = false;
      } else {
        this.error.last_name = "";
        error = true;
      }

      if (this.form.email == "") {
        this.error.email = "email is required.";
        error = false;
      } else {
        this.error.email = "";
        error = true;
      }

      if (this.form.phone == "") {
        this.error.phone = "phone is required.";
        error = false;
      } else {
        this.error.phone = "";
        error = true;
      }

      if (this.form.password == "") {
        this.error.password = "password is required.";
        error = false;
      } else {
        this.error.password = "";
        error = true;
      }

      if (this.form.password_confirmation == "") {
        this.error.password_confirmation = "Confirm password is required.";
        error = false;
      } else {
        this.error.password_confirmation = "";
        error = true;
      }

      if (this.type == "internal_user") {
        if (this.form.state_id == "") {
          this.error.state_id = "state is required.";
          error = false;
        } else {
          this.error.state_id = "";
          error = true;
        }

        if (this.form.city_id == "") {
          this.error.city_id = "city is required.";
          error = false;
        } else {
          this.error.city_id = "";
          error = true;
        }

        if (this.form.job_title == "") {
          this.error.job_title = "job title is required.";
          error = false;
        } else {
          this.error.job_title = "";
          error = true;
        }

        if (this.form.marketing_title == "") {
          this.error.marketing_title = "marketing title is required.";
          error = false;
        } else {
          this.error.marketing_title = "";
          error = true;
        }

        if (this.form.sector == "") {
          this.error.sector = "sector title is required.";
          error = false;
        } else {
          this.error.sector = "";
          error = true;
        }

        if (this.form.status == "") {
          this.error.status = "status title is required.";
          error = false;
        } else {
          this.error.status = "";
          error = true;
        }
      } else {
        if (this.form.company == "") {
          this.error.company = "company is required.";
          error = false;
        } else {
          this.error.company = "";
          error = true;
        }

        if (this.form.title == "") {
          this.error.title = "title is required.";
          error = false;
        } else {
          this.error.title = "";
          error = true;
        }
      }

      if (error) {
        axios
          .post(api.register, this.form)
          .then(res => {
            this.loading = false;
            this.$noty.success("Registration Completed Successfully!");
            // this.$router.push({ name: "login" });
          })
          .catch(err => {
            err.response.data.error &&
              this.$noty.error(err.response.data.error);
            err.response.data.errors
              ? this.setErrors(err.response.data.errors)
              : this.clearErrors();
            this.loading = false;
          });
      }
    },
    isNumber: function(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;
      if (
        charCode > 31 &&
        (charCode < 48 || charCode > 57) &&
        charCode !== 46
      ) {
        evt.preventDefault();
      } else {
        return true;
      }
    },
    stateList() {
      axios
        .post(api.states)
        .then(res => {
          this.states = res.data;
        })
        .catch(err => {});
    },
    cityList() {
      axios
        .post(api.cities)
        .then(res => {
          this.cities = res.data;
        })
        .catch(err => {});
    },
    countryList() {
      axios
        .post(api.countries)
        .then(res => {})
        .catch(err => {});
    },
    setErrors(errors) {
      this.error.first_name = errors.first_name ? errors.first_name[0] : null;
      this.error.last_name = errors.last_name ? errors.last_name[0] : null;
      this.error.email = errors.email ? errors.email[0] : null;
      if (this.error.email !== null) {
        this.$noty.error(errors.email[0]);
      }
      this.error.phone = errors.phone ? errors.phone[0] : null;
      this.error.password = errors.password ? errors.password[0] : null;
      this.error.password_confirmation = errors.password_confirmation
        ? errors.password_confirmation[0]
        : null;
    },
    clearErrors() {
      this.error.first_name = null;
      this.error.last_name = null;
      this.error.email = null;
      this.error.phone = null;
      this.error.password = null;
      this.error.password_confirmation = null;
    }
  },
  mounted() {
    this.stateList();
    this.cityList();
  },
  created() {
    if (this.type == "internal_user") {
      this.internal = true;
      this.form.role_id = 2;
      this.title = "Internal User";
    }
  }
};
</script>

<style scoped>
.login-page {
  height: unset;
}
.login-page .bg-img {
  position: fixed;
}
.registration-content {
  overflow-x: hidden;
}

.login-page .user-info {
  margin-bottom: 20px;
}
.form-block > span:first-of-type {
  margin: 0 0 0.2rem;
}
.btn-login {
  margin-top: 5px;
}
</style>