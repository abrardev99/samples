<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2">
        <div class="row">
          <label class="form-block" for="event-name">
            <span>Event Name</span>
            <input v-model="form.name" type="text" placeholder="Spring 2020">
            <div class="invalid-feedback form-error" v-show="error.name">{{ error.name }}</div>
          </label>
        </div>
        <div class="row">
          <div class="col-2">
            <label class="form-block" for="event-city">
              <span>City</span>
              <select v-model="form.city_id">
                <option value>Select City</option>
                <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
              </select>
              <div class="invalid-feedback form-error" v-show="error.state_id">{{ error.state_id }}</div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block" for="event-state">
              <span>State</span>
              <select v-model="form.state_id">
                <option value>Select State</option>
                <option v-for="state in states" :value="state.id">{{ state.name }}</option>
              </select>
              <div class="invalid-feedback form-error" v-show="error.city_id">{{ error.city_id }}</div>
            </label>
          </div>
        </div>
      </div>
      <div class="col-2">
        <div class="row">
          <div class="col-2 no-margin">
            <label class="form-block" for="start-date">
              <span>Start Date</span>
              <date-picker v-model="form.start_date" placeholder="mm/dd/yyyy"></date-picker>
              <div
                class="invalid-feedback form-error"
                v-show="error.start_date"
              >{{ error.start_date }}</div>
            </label>
          </div>
          <div class="col-2 no-margin">
            <label class="form-block" for="end-date">
              <span>End Date</span>
              <date-picker v-model="form.end_date" placeholder="mm/dd/yyyy"></date-picker>
              <div class="invalid-feedback form-error" v-show="error.end_date">{{ error.end_date }}</div>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-2 no-margin">
            <label class="form-block" for="registration-start-date">
              <span>Registration Start Date</span>
              <date-picker v-model="form.reg_start_date" placeholder="mm/dd/yyyy"></date-picker>
              <div
                class="invalid-feedback form-error"
                v-show="error.reg_start_date"
              >{{ error.reg_start_date }}</div>
            </label>
          </div>
          <div class="col-2 no-margin">
            <label class="form-block" for="registration-end-date">
              <span>Registration End Date</span>
              <date-picker v-model="form.reg_end_date" placeholder="mm/dd/yyyy"></date-picker>
              <div
                class="invalid-feedback form-error"
                v-show="error.reg_end_date"
              >{{ error.reg_end_date }}</div>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <label class="form-block" for="event-details">
        <span>Event Details</span>
        <textarea v-model="form.details" placeholder="Type notes here..." style="min-height: 8rem"></textarea>
        <div class="invalid-feedback form-error" v-show="error.details">{{ error.details }}</div>
      </label>
    </div>
  </section>
</template>

<script>
import { api } from "../../config";
import { mapGetters } from "vuex";
export default {
  props: ["error", "form"],
  data() {
    return {
      states: "",
      cities: ""
    };
  },
  computed: mapGetters(["hasEventData"]),
  components: {},
  methods: {},
  mounted() {
    if (this.hasEventData.state !== null) {
      this.states = this.hasEventData.state;
    }

    if (this.hasEventData.city !== null) {
      this.cities = this.hasEventData.city;
    }
  }
};
</script>

<style scoped>
</style>