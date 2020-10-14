<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2">
        <label class="form-block">
          <span>Hotel</span>
          <input type="text" v-model="form.name" placeholder="Name of Hotel">
          <span
            class="invalid-feedback form-error"
            v-show="v.hotelForm.name.$error"
          >Hotel Name is required.</span>
        </label>
      </div>
      <div class="col-2">
        <div class="row">
          <div class="col-2--lg">
            <label class="form-block">
              <span>Dates</span>
              <div class="row time-management">
                <div class="col-2--lg mg-10">
                  <date-picker
                    :class="'date-input'"
                    v-model="form.start_date"
                    placeholder="mm/dd/yyyy"
                  ></date-picker>
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.hotelForm.start_date.$error"
                  >Start Date is required.</div>
                </div>
                <div class="col-2--lg">
                  <date-picker
                    :class="'date-input'"
                    v-model="form.end_date"
                    placeholder="mm/dd/yyyy"
                  ></date-picker>
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.hotelForm.end_date.$error"
                  >End Date is required.</div>
                </div>
              </div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block">
              <span>Phone Number</span>
              <input
                type="tel"
                v-model="form.phone"
                @keypress="isNumber($event)"
                placeholder="(000) 000-0000"
              >
              <div
                class="invalid-feedback form-error"
                v-show="v.hotelForm.phone.$error"
              >Phone Number is required.</div>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="row stretch-height">
      <div class="col-2">
        <div class="row">
          <div class="col-1">
            <label class="form-block">
              <span>Address</span>
              <gmap-autocomplete
                @place_changed="setPlace"
                :value="form.address"
                placeholder="123 Main Street"
              ></gmap-autocomplete>
              <!-- <input type="text" v-model="form.address" placeholder="123 Main Street"> -->
              <div
                class="invalid-feedback form-error"
                v-show="v.hotelForm.address.$error"
              >Address is required.</div>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-2">
            <label class="form-block" for="event-city">
              <span>City</span>
              <select v-model="form.city_id">
                <option value>Select City</option>
                <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
              </select>
              <div
                class="invalid-feedback form-error"
                v-show="v.hotelForm.city_id.$error"
              >City is required.</div>
            </label>
          </div>
          <div class="col-1">
            <div class="row">
              <div class="col-2--lg mg-10">
                <label class="form-block" for="event-state">
                  <span>State</span>
                  <select v-model="form.state_id">
                    <option value>Select State</option>
                    <option v-for="state in states" :value="state.id">{{ state.name }}</option>
                  </select>
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.hotelForm.state_id.$error"
                  >State is required.</div>
                </label>
              </div>
              <div class="col-2--lg">
                <label class="form-block">
                  <span>Zip</span>
                  <input
                    type="text"
                    @keypress="isNumber($event)"
                    v-model="form.zip"
                    placeholder="79401"
                  >
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.hotelForm.zip.$error"
                  >Zip is required.</div>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-2">
        <section class="form-section course-schedule">
          <gmap-map :center="center" :zoom="12" style="width:100%;  height: 400px;">
            <gmap-marker
              :key="index"
              v-for="(m, index) in markers"
              :position="m.position"
              @click="center=m.position"
            ></gmap-marker>
          </gmap-map>
        </section>
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import { mapGetters } from "vuex";
import moment from "moment";
export default {
  props: ["view", "form", "v", "editModal"],
  data() {
    return {
      siteUrl: siteUrl,
      moment: moment,
      states: "",
      cities: "",
      center: { lat: 45.508, lng: -73.587 },
      markers: [],
      places: [],
      currentPlace: null
    };
  },
  computed: mapGetters(["hasEventData"]),
  watch: {
    $props: {
      handler() {
        if (this.editModal) {
          if (this.form) {
            if (this.form.lat && this.form.long) {
              this.editMarker(Number(this.form.lat), Number(this.form.long));
            }
          }
        }
      },
      deep: true,
      immediate: true
    }
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
    setPlace(place) {
      this.currentPlace = place;
      this.addMarker();
    },
    editMarker(lat, long) {
      const marker = {
        lat: lat,
        lng: long
      };
      this.markers.push({ position: marker });
      this.center = marker;
      this.currentPlace = null;
    },
    setHotel(hotel) {
      this.cities = [hotel[0].city];
      this.states = [hotel[0].states];
    },
    addMarker() {
      var componentForm = {
        street_number: "short_name",
        route: "long_name",
        locality: "long_name",
        administrative_area_level_1: "short_name",
        country: "long_name",
        postal_code: "short_name"
      };

      if (this.currentPlace) {
        const marker = {
          lat: this.currentPlace.geometry.location.lat(),
          lng: this.currentPlace.geometry.location.lng()
        };

        this.form.lat = marker.lat;
        this.form.long = marker.lng;

        this.markers.push({ position: marker });
        this.places.push(this.currentPlace);

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
        this.center = marker;
        this.currentPlace = null;
      }
    },
    geolocate: function() {
      navigator.geolocation.getCurrentPosition(position => {
        this.center = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
      });
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
          case "locality": // North Hollywood or Los Angeles?
            address.City = c;
            break;
          case "administrative_area_level_1": //  Note some countries don't have states
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
    this.$eventHub.$on("hotels", this.setHotel);
  },
  created() {},
  beforeDestroy() {
    this.$eventHub.$off("hotels");
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

