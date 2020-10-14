<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2--lg">
        <div class="row">
          <div class="col-2--lg">
            <label class="form-block">
              <span>Item</span>
              <input type="text" v-model="form.item" placeholder="Giveaway item">
              <span
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.item.$error"
              >Item is required.</span>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block">
              <span>Quantity</span>
              <input type="text" v-model="form.quantity" placeholder="eg. 100">
              <span
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.quantity.$error"
              >Item is required.</span>
            </label>
          </div>
        </div>
        <hr style="opacity: 0.2; margin: 2.25rem 0">
        <div class="row">
          <div class="col-2--lg">
            <label class="form-block">
              <span>Vendor</span>
              <input type="text" v-model="form.vendor" placeholder="Name of Vendor">
              <span
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.vendor.$error"
              >Item is required.</span>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block">
              <span>Phone</span>
              <input
                type="tel"
                v-model="form.phone"
                @keypress="isNumber($event)"
                placeholder="(000) 000-0000"
              >
              <div
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.phone.$error"
              >Phone Number is required.</div>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-2--lg">
            <label class="form-block">
              <span>Address</span>
              <gmap-autocomplete
                @place_changed="setPlace"
                :value="form.address"
                placeholder="123 Main Street"
              ></gmap-autocomplete>
              <div
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.address.$error"
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
                v-show="v.giveAwayForm.city_id.$error"
              >City is required.</div>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="col-2--lg">
            <label class="form-block" for="event-state">
              <span>State</span>
              <select v-model="form.state_id">
                <option value>Select State</option>
                <option v-for="state in states" :value="state.id">{{ state.name }}</option>
              </select>
              <div
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.state_id.$error"
              >State is required.</div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block">
              <span>ZIP CODE</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.zip"
                placeholder="79401"
              >
              <div
                class="invalid-feedback form-error"
                v-show="v.giveAwayForm.zip.$error"
              >Zip is required.</div>
            </label>
          </div>
        </div>
      </div>
      <div class="col-2">
        <label class="form-block form-block--checkbox">
          <span>Sizes</span>
          <input
            type="checkbox"
            :class="{ 'checked' : disableSize }"
            @change="changeQuantity($event)"
            v-model="form.sizes"
          >
        </label>
        <div id="giveaway-sizes" class="row text-center space-cols">
          <div class="col-7">
            <label class="form-block">
              <span>XS</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.extra_small"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>S</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.small"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>M</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.medium"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>L</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.large"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>XL</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.extra_large"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>2XL</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.double_exel"
                :disabled="disableSize"
              >
            </label>
          </div>
          <div class="col-7">
            <label class="form-block">
              <span>3XL</span>
              <input
                type="text"
                @keypress="isNumber($event)"
                v-model="form.triple_exel"
                :disabled="disableSize"
              >
            </label>
          </div>
        </div>
        <label class="row">
          <div class="upload-box">
            <input type="file" class="hide-placeholder" @change="previewImage" accept="image/*">
            <div v-if="!form.image">
              <img
                v-if="imageData.length === 0"
                :src="siteUrl + '/images/modal-card-upload-image.png'"
              >
            </div>
            <div v-else>
              <img v-if="imageData.length === 0" :src="siteUrl + '/images/' + form.image">
            </div>
            <div class="image-preview" v-if="imageData.length > 0">
              <img class="preview" :src="imageData">
            </div>
            <p v-if="imageData.length === 0">
              Drag &amp; drop or
              <a>click to upload image</a>
            </p>
          </div>
        </label>
      </div>
    </div>
  </section>
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
      center: { lat: 45.508, lng: -73.587 },
      markers: [],
      places: [],
      currentPlace: null,
      imageData: "",
      disableSize: true
    };
  },
  computed: mapGetters(["hasEventData"]),
  watch: {},
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
    },
    changeQuantity(e) {
      if (e.target.checked) {
        this.disableSize = false;
      } else {
        this.disableSize = true;
      }
    },
    previewImage: function(event) {
      var input = event.target;
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = e => {
          this.imageData = e.target.result;
        };
        this.form.image = input.files[0];

        reader.readAsDataURL(input.files[0]);
      }
    },
    setGiveAway(giveaway) {
      this.cities = [giveaway[0].city];
      this.states = [giveaway[0].states];
    }
  },
  mounted() {
    this.$eventHub.$on("clearGiveAway", id => {
      this.imageData = "";
    });

    this.$eventHub.$on("addGiveAway", this.setGiveAway);
  },
  beforeDestroy() {
    this.$eventHub.$off("addGiveAway");
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
.hide-placeholder {
  display: none;
}
</style>

