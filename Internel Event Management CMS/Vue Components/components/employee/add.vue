<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2">
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
      </div>
      <div class="col-2">
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
      </div>
    </div>
    <div class="row">
      <div class="col-2">
        <label class="form-block">
          <span>Email Address</span>
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
      </div>
      <div class="col-2">
        <label class="form-block">
          <span>Phone number</span>
          <input
            type="text"
            placeholder="000-000-0000"
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
      </div>
    </div>
    <div class="row">
      <div class="col-2">
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
      </div>
      <div class="col-2">
        <div class="row">
          <div class="col-2">
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
          </div>
          <div class="col-2">
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
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-2">
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
      </div>
      <div class="col-2">
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
      </div>
    </div>
    <div class="row">
      <div class="col-2">
        <label class="form-block">
          <span>Create Password</span>
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
      </div>
      <div class="col-2">
        <label class="form-block">
          <span>Repeat Password</span>
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
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
export default {
  props: ["view", "form", "error", "loading"],
  data() {
    return {
      siteUrl: siteUrl,
      states: "",
      cities: ""
    };
  },
  methods: {
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
    }
  },
  mounted() {
    this.stateList();
    this.cityList();
  },
  created() {}
};
</script>
