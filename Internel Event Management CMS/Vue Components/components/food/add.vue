<template>
  <div class="row">
    <div class="col-2">
      <div class="row">
        <label class="form-block">
          <span>Caterer</span>
          <input type="text" v-model="form.name" placeholder="Name of Caterer">
          <span
            class="invalid-feedback form-error"
            v-show="v.foodForm.name.$error"
          >Caterer is required.</span>
        </label>
      </div>
      <div class="row">
        <div class="col-2">
          <label class="form-block">
            <span>Phone Number</span>
            <input
              type="tel"
              v-model="form.phone"
              @keypress="isNumber($event)"
              placeholder="(000) 000-0000"
            >
            <span
              class="invalid-feedback form-error"
              v-show="v.foodForm.phone.$error"
            >Phone Number is required.</span>
          </label>
        </div>
        <div class="col-2">
          <label class="form-block">
            <span>Cater Date</span>
            <date-picker :class="'date-input'" v-model="form.start_date" placeholder="mm/dd/yyyy"></date-picker>
            <div
              class="invalid-feedback form-error"
              v-show="v.foodForm.start_date.$error"
            >Start Date is required.</div>
          </label>
        </div>
      </div>
    </div>
    <div class="col-2">
      <div class="row">
        <div class="col-2">
          <label class="form-block">
            <span>Address</span>
            <gmap-autocomplete
              @place_changed="setPlace"
              :value="form.address"
              placeholder="123 Main Street"
            ></gmap-autocomplete>
            <div
              class="invalid-feedback form-error"
              v-show="v.foodForm.address.$error"
            >Address is required.</div>
          </label>
        </div>
        <div class="col-2">
          <label class="form-block" for="event-city">
            <span>City</span>
            <select v-model="form.city_id">
              <option value>Select City</option>
              <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
            </select>
            <div
              class="invalid-feedback form-error"
              v-show="v.foodForm.city_id.$error"
            >City is required.</div>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="col-2">
          <label class="form-block" for="event-state">
            <span>State</span>
            <select v-model="form.state_id">
              <option value>Select State</option>
              <option v-for="state in states" :value="state.id">{{ state.name }}</option>
            </select>
            <div
              class="invalid-feedback form-error"
              v-show="v.foodForm.state_id.$error"
            >State is required.</div>
          </label>
        </div>
        <div class="col-2">
          <label class="form-block">
            <span>Zip</span>
            <input type="text" @keypress="isNumber($event)" v-model="form.zip" placeholder="79401">
            <div class="invalid-feedback form-error" v-show="v.foodForm.zip.$error">Zip is required.</div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import moment from "moment";
import { mapGetters } from "vuex";
export default {
  props: ["view", "form", "v", "editModal"],
  data() {
    return {
      siteUrl: siteUrl,
      moment: moment,
      states: "",
      cities: "",
      currentPlace: null
    };
  },
  computed: mapGetters(["hasEventData"]),
  methods: {
    setPlace(place) {
      this.currentPlace = place;
      this.addMarker();
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
    addMarker() {
      if (this.currentPlace) {
        this.form.zip = "";
        this.form.city_id = "";
        this.form.state_id = "";

        let address = this.placeToAddress(this.currentPlace);
        if (address.Zip) {
          if (address.Zip.long_name) {
            this.form.zip = address.Zip.long_name;
          }
        }

        if (address.City) {
          if (address.City.long_name) {
            axios
              .post(api.searchCities, { name: address.City.long_name })
              .then(res => {
                if (res.data) {
                  if (res.data.length > 0) {
                    this.cities = res.data;
                    this.form.city_id = res.data[0].id;
                  }
                }
              });
          }
        }

        if (address.State) {
          if (address.State.long_name) {
            axios
              .post(api.searchStates, { name: address.State.long_name })
              .then(res => {
                if (res.data) {
                  if (res.data.length > 0) {
                    this.states = res.data;
                    this.form.state_id = res.data[0].id;
                  }
                }
              });
          }
        }

        this.form.address = this.currentPlace.formatted_address;
        this.currentPlace = null;
      }
    },
    setFood(food) {
      this.cities = [food[0].city];
      this.states = [food[0].states];
    },
    placeToAddress(place) {
      var address = {};
      place.address_components.forEach(function(c) {
        switch (c.types[0]) {
          case "street_number":
            address.StreetNumber = c;
            break;
          case "route":
            address.StreetName = c;
            break;
          case "neighborhood":
          case "locality":
            address.City = c;
            break;
          case "administrative_area_level_1":
            address.State = c;
            break;
          case "postal_code":
            address.Zip = c;
            break;
          case "country":
            address.Country = c;
            break;
        }
      });
      return address;
    }
  },
  mounted() {
    this.$eventHub.$on("foods", this.setFood);
  },
  created() {},
  beforeDestroy() {
    this.$eventHub.$off("foods");
  }
};
</script>
<style>
.time-management {
  display: flex;
}
.mg-10 {
  margin-right: 10px;
}
</style>

