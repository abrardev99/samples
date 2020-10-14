<template>
  <div>
    <nav-breadcrumb v-if="!course" :editEvent="true" :eventTitle="eventTitle"></nav-breadcrumb>
    <nav-breadcrumb v-else :course="true" :eventTitle="eventTitle"></nav-breadcrumb>
    <event-sub-nav v-if="!course" @cards="cards" @list="list"></event-sub-nav>
    <course-sub-nav v-else></course-sub-nav>
    <section class="selection" :data-view="view">
      <div class="container">
        <article class="card-item events-card-item" v-for="hotel in hotels">
          <hgroup class="card-item-headlines">
            <h2 class="card-item-headline">{{ hotel.name }}</h2>
          </hgroup>
          <div class="card-item-secondary-info">
            <div class="stacked-paragraphs">
              <img :src="siteUrl + '/images/location.svg'">
              <p>
                <span>{{ hotel.address }}</span>
                <span>{{ hotel.city.name }}, {{ hotel.states.name }} {{ hotel.zip }}</span>
              </p>
            </div>
          </div>
          <div class="card-item-date-info">
            <div class="start">
              <p class="card-item-date-label">Start</p>
              <p class="card-item-date">{{ moment(hotel.start).format("YYYY-MM-DD")}}</p>
            </div>
            <div class="end">
              <p class="card-item-date-label">End</p>
              <p class="card-item-date">{{ moment(hotel.end).format("YYYY-MM-DD") }}</p>
            </div>
          </div>
          <div class="card-item-options-menu">
            <div @click="dropDown($event)" class="card-item-menu-trigger events-card-item">
              <img :src="siteUrl + '/images/card-item.svg'">
            </div>
            <ul class="card-item-menu events-card-menu">
              <li @click="edit(hotel.id)" class="card-item-menu-item">
                <a class="card-item-menu-link">Edit</a>
              </li>
              <li class="card-item-menu-item">
                <a @click="deleteCourse(hotel.id)" class="card-item-menu-link">Delete</a>
              </li>
            </ul>
          </div>
        </article>
        <article
          @click="addHotel($event)"
          class="card-item add-item modal-trigger"
          data-modal="courses"
        >
          <img :src="siteUrl + '/images/plus.svg'">
          <p>Add Hotel</p>
        </article>
      </div>
    </section>
    <hotel-modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="modalHeadercontent"
      :course="true"
      :label="label"
      :deleteModal="true"
      @cancel="cancelModal"
      @update="addModal"
      :showModal="showModal"
    >
      <add-form :form="hotelForm" :v="$v" :editModal="editModal"></add-form>
    </hotel-modal>
    <hotel-modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="''"
      :label="labelModal"
      @cancel="cancelModal"
      @update="addModal"
      :showModal="showDeleteModal"
      :deleteModal="false"
    >
      <section class="modal-card-data">
        <div class="row">
          <span>Are you sure you want to delete ?</span>
        </div>
        <div class="delete-content-right">
          <edit-button
            :loading="loading"
            @click="cancelModal()"
            :labelBefore="'Cancel'"
            :labelAfter="'Cancel'"
            :type="'btn-secondary'"
          ></edit-button>
          <edit-button
            :loading="loading"
            :labelBefore="'Delete'"
            :labelAfter="'Delete'"
            :type="'btn-primary'"
            @click="confirmDelete()"
          ></edit-button>
        </div>
      </section>
    </hotel-modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import Add from "./../hotel/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import EditButton from "./../shared/button/button.vue";
import CourseSubNav from "./../course/navbar.vue";
import EventSubNav from "./../event/navbar.vue";
import moment from "moment";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
  components: {
    "hotel-modal": Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    "edit-button": EditButton,
    "course-sub-nav": CourseSubNav
  },
  data() {
    return {
      courses: [],
      hotels: "",
      showDeleteModal: false,
      course: false,
      editModal: false,
      courseDetail: [],
      moment: moment,
      siteUrl: siteUrl,
      showModal: false,
      eventTitle: "",
      modalHeaderTitle: "Edit Event",
      modalHeaderDescription: "Spring 2020",
      modalHeadercontent: "Add Hotel",
      loading: false,
      siteUrl: siteUrl,
      navlabel: {
        btnTitleBefore: "Edit Hotel",
        btnTitleAfter: "Edit Hotel"
      },
      labelModal: {
        btnTitleBefore: "Delete Hotel",
        btnTitleAftere: "Delete Hotel"
      },
      label: {
        btnTitleBefore: "Add Hotel",
        btnTitleAfter: "Add Hotel"
      },
      view: "cards",
      hotelForm: {
        hotel_id: "",
        name: "",
        start_date: "",
        end_date: "",
        phone: "",
        address: "",
        city_id: "",
        state_id: "",
        zip: ""
      }
    };
  },
  validations: {
    hotelForm: {
      name: { required },
      start_date: { required },
      end_date: { required },
      phone: { required },
      address: { required },
      city_id: { required },
      state_id: { required },
      zip: { required }
    }
  },
  methods: {
    deleteCourse(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Hotel";
      this.labelModal.btnTitleBefor = "Delete Hotel";
      this.labelModal.btnTitleAfter = "Delete Hotel";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    getEventHotels(eventId) {
      let url = "";
      let event = false;
      switch (this.$route.name) {
        case "eventhotel":
          url = api.editEvent;
          event = true;
          this.course = false;
          break;
        case "coursehotel":
          url = api.viewCourse;
          event = false;
          this.course = true;
          break;
      }
      this.hotelForm.event_id = eventId;
      axios.get(url + eventId).then(res => {
        if (event) {
          this.eventTitle = res.data.name;
        } else {
          this.eventTitle = res.data.course.name;
        }
        this.hotels = res.data.hotels;
      });
    },
    addHotel: function(e) {
      this.modalHeaderTitle = "Add Hotel";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Add Hotel";
      this.label.btnTitleBefore = "Add Hotel";
      this.label.btnTitleAftere = "Add Hotel";
      this.showModal = true;
      this.clearForm();
      this.editModal = false;
    },
    cancelModal() {
      this.showModal = false;
      this.showDeleteModal = false;
      this.clearForm();
    },
    updateModal() {
      console.log("update");
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    addModal() {
      this.$v.hotelForm.$touch();
      if (this.$v.hotelForm.$error) return;
      this.hotelForm.start = moment(this.hotelForm.start_date).format(
        "YYYY-MM-DD"
      );
      this.hotelForm.end = moment(this.hotelForm.end_date).format("YYYY-MM-DD");

      this.hotelForm.hotelable_id = this.$route.params.id;
      switch (this.$route.name) {
        case "eventhotel":
          this.hotelForm.hotelable_type = "event";
          break;
        case "coursehotel":
          this.hotelForm.hotelable_type = "course";
          break;
      }

      if (!this.hotelForm.hotel_id) {
        axios.post(api.addHotel, this.hotelForm).then(res => {
          this.loading = false;
          this.showModal = false;
          this.getEventHotels(this.$route.params.id);
          this.$noty.success("Hotel Added Successfully!");
        });
      } else {
        axios.post(api.updateHotel, this.hotelForm).then(res => {
          this.loading = false;
          this.showModal = false;
          this.getEventHotels(this.$route.params.id);
          this.$noty.success("Hotel Updated Successfully!");
        });
      }
    },
    cards() {
      this.view = "cards";
    },
    list() {
      this.view = "list";
    },
    edit(id) {
      this.clearForm();
      this.editModal = true;
      this.showModal = true;
      this.modalHeaderTitle = "Edit Hotel";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Edit Hotel";
      this.label.btnTitleBefore = "Update Hotel";
      this.label.btnTitleAftere = "Update Hotel";
      axios.get(api.getEventHotels + "/" + id).then(response => {
        this.$eventHub.$emit("hotels", response.data);
        this.hotelForm.hotel_id = response.data[0].id;
        this.hotelForm.name = response.data[0].name;
        this.hotelForm.start_date = response.data[0].start;
        this.hotelForm.end_date = response.data[0].end;
        this.hotelForm.phone = response.data[0].phone;
        this.hotelForm.address = response.data[0].address;
        this.hotelForm.city_id = response.data[0].city_id;
        this.hotelForm.state_id = response.data[0].state_id;
        this.hotelForm.zip = response.data[0].zip;
        this.hotelForm.lat = response.data[0].lat;
        this.hotelForm.long = response.data[0].long;

        this.hotelForm.start_date = moment(response.data[0].start_date).format(
          "YYYY-MM-DD"
        );
        this.hotelForm.end_date = moment(response.data[0].end_date).format(
          "YYYY-MM-DD"
        );
      });
    },
    confirmDelete() {
      axios.delete(api.deleteHotel + this.deleteId).then(res => {
        this.$noty.success("Hotel Delete Successfully!");
      });
      this.cancelModal();
      this.getEventHotels(this.$route.params.id);
    },
    clearForm() {
      this.$v.$reset();
      this.hotelForm.hotel_id = "";
      this.hotelForm.start = "";
      this.hotelForm.end = "";
      this.hotelForm.hotel_id = "";
      this.hotelForm.name = "";
      this.hotelForm.start_date = "";
      this.hotelForm.end_date = "";
      this.hotelForm.phone = "";
      this.hotelForm.address = "";
      this.hotelForm.city_id = "";
      this.hotelForm.state_id = "";
      this.hotelForm.zip = "";
    }
  },
  created() {
    this.getEventHotels(this.$route.params.id);
  }
};
</script>
