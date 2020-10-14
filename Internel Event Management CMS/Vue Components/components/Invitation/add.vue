<template>
  <section class="modal-card-data">
    <div class="adjust-center">
      <div class="row">
        <div class="max-size">
          <label class="form-block">
            <span>First Name</span>
            <input
              type="text"
              class="form-control"
              id="first_name"
              v-model="form.first_name"
              autocomplete="off"
              :disabled="loading"
            />
            <span
              class="invalid-feedback form-error"
              v-show="v.form.first_name.$error"
            >First Name is required.</span>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="max-size">
          <label class="form-block">
            <span>Last Name</span>
            <input
              type="text"
              class="form-control"
              id="last_name"
              v-model="form.last_name"
              autocomplete="off"
              :disabled="loading"
            />
            <span
              class="invalid-feedback form-error"
              v-show="v.form.last_name.$error"
            >Last Name is required.</span>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="max-size">
          <label class="form-block">
            <span>Email Address</span>
            <input
              type="email"
              class="form-control"
              id="email"
              v-model="form.email"
              autocomplete="off"
              :disabled="loading"
            />
            <span
              class="invalid-feedback form-error"
              v-show="v.form.email.$error"
            >Email is required.</span>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="max-size p-20">
          <label class="toogle-switch-box form-block">
            <span class="psc-label">PSC Employee</span>
            <label class="switch">
              <input type="checkbox" v-model="form.employee" />
              <span class="slider round"></span>
            </label>
          </label>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
export default {
  props: ["view", "form", "error", "loading", "v"],
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
<style scoped>
.adjust-center {
  margin: auto;
  width: 60%;
  padding: 10px;
}
.max-size {
  width: 100%;
}
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #98e69b;
}

input:focus + .slider {
  box-shadow: 0 0 1px #98e69b;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.psc-label {
  color: #636981;
}
.toogle-switch-box {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.p-20 {
  padding-top: 20px;
}
</style>
