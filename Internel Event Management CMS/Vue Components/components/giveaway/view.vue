<template>
  <div>
    <nav-breadcrumb v-if="!course" :editEvent="true" :eventTitle="eventTitle">
    
    </nav-breadcrumb>
    <nav-breadcrumb v-else :course="true" :eventTitle="eventTitle"> </nav-breadcrumb>
    <event-sub-nav v-if="!course" @cards="cards" @list="list"></event-sub-nav>
    <course-sub-nav v-else></course-sub-nav>
    <section class="selection" :data-view="view">
      <div class="container">
        <article class="card-item events-card-item" v-for="giveAway in giveAways">
          <hgroup class="card-item-headlines">
            <h2 class="card-item-headline">{{ giveAway.items }}</h2>
          </hgroup>
          <div class="card-item-secondary-info">
            <div class="stacked-paragraphs">
              <img :src="siteUrl + '/images/location.svg'">
              <p>
                <span>{{ giveAway.address }}</span>
                <span>{{ giveAway.city.name }}, {{ giveAway.states.name }} {{ giveAway.zip }}</span>
              </p>
            </div>
          </div>
          <div class="card-item-date-info">
            <div class="start">
              <p class="card-item-date-label">CREATED DATE</p>
              <p class="card-item-date">{{ moment(giveAway.created_at).format("YYYY-MM-DD")}}</p>
            </div>
          </div>
          <div class="card-item-options-menu">
            <div @click="dropDown($event)" class="card-item-menu-trigger events-card-item">
              <img :src="siteUrl + '/images/card-item.svg'">
            </div>
            <ul class="card-item-menu events-card-menu">
              <li @click="edit(giveAway.id)" class="card-item-menu-item">
                <a class="card-item-menu-link">Edit</a>
              </li>
              <li class="card-item-menu-item">
                <a @click="deleteGiveAway(giveAway.id)" class="card-item-menu-link">Delete</a>
              </li>
            </ul>
          </div>
        </article>
        <article
          @click="addGiveAway($event)"
          class="card-item add-item modal-trigger"
          data-modal="courses"
        >
          <img :src="siteUrl + '/images/plus.svg'">
          <p>Add Give Away</p>
        </article>
      </div>
    </section>
    <giveaway-modal
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
      <add-form :form="giveAwayForm" :v="$v" :editModal="editModal"></add-form>
    </giveaway-modal>
    <giveaway-modal
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
    </giveaway-modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import Add from "./../giveaway/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import EditButton from "./../shared/button/button.vue";
import CourseSubNav from "./../course/navbar.vue";
import EventSubNav from "./../event/navbar.vue";
import moment from "moment";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
  components: {
    "giveaway-modal": Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    "edit-button": EditButton,
    "course-sub-nav": CourseSubNav
  },
  data() {
    return {
      courses: [],
      giveAways: "",
      giveAwayId: "",
      course: false,
      showDeleteModal: false,
      editModal: false,
      courseDetail: [],
      moment: moment,
      siteUrl: siteUrl,
      showModal: false,
      eventTitle: "",
      modalHeaderTitle: "Edit Event",
      modalHeaderDescription: "Spring 2020",
      modalHeadercontent: "Add Give Away",
      loading: false,
      siteUrl: siteUrl,
      navlabel: {
        btnTitleBefore: "Edit Give Away",
        btnTitleAfter: "Edit Give Away"
      },
      labelModal: {
        btnTitleBefore: "Delete Give Away",
        btnTitleAftere: "Delete Give Away"
      },
      label: {
        btnTitleBefore: "Add Give Away",
        btnTitleAfter: "Add Give Away"
      },
      view: "cards",
      giveAwayForm: {
        giveaway_id: "",
        item: "",
        quantity: "",
        vendor: "",
        phone: "",
        address: "",
        city_id: "",
        state_id: "",
        zip: "",
        extra_small: "",
        small: "",
        medium: "",
        large: "",
        extra_large: "",
        double_exel: "",
        triple_exel: "",
        sizes: false
      }
    };
  },
  validations: {
    giveAwayForm: {
      item: { required },
      quantity: { required },
      vendor: { required },
      phone: { required },
      address: { required },
      city_id: { required },
      state_id: { required },
      zip: { required }
    }
  },
  methods: {
    deleteGiveAway(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Give Away";
      this.labelModal.btnTitleBefor = "Delete Give Away";
      this.labelModal.btnTitleAfter = "Delete Give Away";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    getEventGiveAway(eventId) {
      this.giveAwayForm.event_id = eventId;

      let url = "";
      let event = false;
      switch (this.$route.name) {
        case "eventGiveaways":
          url = api.editEvent;
          event = true;
          this.course = false;
          break;
        case "courseGiveaways":
          url = api.viewCourse;
          event = false;
          this.course = true;
          break;
      }

      axios.get(url + eventId).then(res => {
        if (event) {
          this.eventTitle = res.data.name;
        } else {
          this.eventTitle = res.data.course.name;
        }
        this.giveAways = res.data.giveaways;
      });
    },
    addGiveAway: function(e) {
      this.modalHeaderTitle = "Add Give Away";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Add Give Away";
      this.label.btnTitleBefore = "Add Give Away";
      this.label.btnTitleAftere = "Add Give Away";
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
      this.$v.giveAwayForm.$touch();
      if (this.$v.giveAwayForm.$error) return;
      let form_data = new FormData();
      if (this.giveAwayForm.image) {
        form_data.append("media", this.giveAwayForm.image);
      }
      form_data.append("give_away_id", this.giveAwayForm.give_away_id);
      form_data.append("items", this.giveAwayForm.item);
      form_data.append("quantity", this.giveAwayForm.quantity);
      form_data.append("phone", this.giveAwayForm.phone);
      form_data.append("vendor", this.giveAwayForm.vendor);
      form_data.append("address", this.giveAwayForm.address);
      form_data.append("city_id", this.giveAwayForm.city_id);
      form_data.append("state_id", this.giveAwayForm.state_id);
      form_data.append("event_id", this.$route.params.id);
      form_data.append("zip", this.giveAwayForm.zip);
      if (this.giveAwayForm.sizes) {
        form_data.append("sizes", 1);
      } else {
        form_data.append("sizes", 0);
      }

      if (this.giveAwayForm.extra_small !== null) {
        form_data.append("x_small", this.giveAwayForm.extra_small);
      }

      if (this.giveAwayForm.small !== null) {
        form_data.append("small", this.giveAwayForm.small);
      }

      if (this.giveAwayForm.medium !== null) {
        form_data.append("medium", this.giveAwayForm.medium);
      }

      if (this.giveAwayForm.large !== null) {
        form_data.append("large", this.giveAwayForm.large);
      }

      if (this.giveAwayForm.extra_large !== null) {
        form_data.append("x_large", this.giveAwayForm.extra_large);
      }

      if (this.giveAwayForm.double_exel !== null) {
        form_data.append("two_x_large", this.giveAwayForm.double_exel);
      }

      if (this.giveAwayForm.triple_exel !== null) {
        form_data.append("three_x_large", this.giveAwayForm.triple_exel);
      }

      form_data.append("giveawayable_id", this.$route.params.id);

      switch (this.$route.name) {
        case "eventGiveaways":
          form_data.append("giveawayable_type", "event");
          break;
        case "courseGiveaways":
          form_data.append("giveawayable_type", "course");
          break;
      }

      if (!this.giveAwayForm.giveaway_id) {
        axios.post(api.addGiveAway, form_data).then(res => {
          this.giveAwayId = res.data.give_away.id;
          form_data.append("giveaway_id", res.data.give_away.id);
          axios.post(api.addGiveAwaySizes, form_data).then(res => {});
          this.$noty.success("GiveAway Added Successfully!");
          this.$eventHub.$emit("giveAwayAddedd");
          this.showModal = false;
          this.getEventGiveAway(this.$route.params.id);
          this.clearForm();
        });
      } else {
        axios.post(api.updateGiveAway, form_data).then(res => {
          form_data.append("giveaway_id", this.giveAwayForm.give_away_id);
          form_data.append(
            "giveaway_size_id",
            this.giveAwayForm.giveaway_size_id
          );
          axios.post(api.editGiveAwaySizes, form_data).then(res => {});
          this.$noty.success("GiveAway Updated Successfully!");
          this.showModal = false;
          this.getEventGiveAway(this.$route.params.id);
          this.clearForm();
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
      this.modalHeaderTitle = "Edit Give Away";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Edit Give Away";
      this.label.btnTitleBefore = "Update Give Away";
      this.label.btnTitleAftere = "Update Give Away";
      axios.get(api.getGiveAway + "/" + id).then(response => {
        this.$eventHub.$emit("addGiveAway", response.data);
        this.giveAwayForm.giveaway_id = response.data[0].id;
        this.giveAwayForm.give_away_id = response.data[0].id;
        this.giveAwayForm.event_id = response.data[0].event_id;
        this.giveAwayForm.item = response.data[0].items;
        this.giveAwayForm.quantity = response.data[0].quantity;
        this.giveAwayForm.vendor = response.data[0].vendor;
        this.giveAwayForm.phone = response.data[0].phone;
        this.giveAwayForm.address = response.data[0].address;
        this.giveAwayForm.city_id = response.data[0].city_id;
        this.giveAwayForm.state_id = response.data[0].state_id;
        this.giveAwayForm.zip = response.data[0].zip;
        if (response.data[0].sizes) {
          if (response.data[0].sizes.length > 0) {
            this.giveAwayForm.sizes = false;
            this.giveAwayForm.giveaway_size_id = response.data[0].sizes[0].id;
            this.giveAwayForm.extra_small = response.data[0].sizes[0].x_small;
            this.giveAwayForm.small = response.data[0].sizes[0].small;
            this.giveAwayForm.medium = response.data[0].sizes[0].medium;
            this.giveAwayForm.large = response.data[0].sizes[0].large;
            this.giveAwayForm.extra_large = response.data[0].sizes[0].x_large;
            this.giveAwayForm.double_exel =
              response.data[0].sizes[0].two_x_large;
            this.giveAwayForm.triple_exel =
              response.data[0].sizes[0].three_x_large;
            this.giveAwayForm.image = response.data[0].sizes[0].media;
          }
        }
      });
    },
    confirmDelete() {
      axios.delete(api.deleteGiveAway + this.deleteId).then(res => {
        this.$noty.success("Give Away Delete Successfully!");
      });
      this.cancelModal();
      this.getEventGiveAway(this.$route.params.id);
    },
    clearForm() {
      this.$v.$reset();
      this.giveAwayForm.giveaway_id = "";
      this.giveAwayForm.item = "";
      this.giveAwayForm.quantity = "";
      this.giveAwayForm.vendor = "";
      this.giveAwayForm.phone = "";
      this.giveAwayForm.address = "";
      this.giveAwayForm.city_id = "";
      this.giveAwayForm.state_id = "";
      this.giveAwayForm.zip = "";
      this.giveAwayForm.extra_small = "";
      this.giveAwayForm.small = "";
      this.giveAwayForm.medium = "";
      this.giveAwayForm.large = "";
      this.giveAwayForm.extra_large = "";
      this.giveAwayForm.double_exel = "";
      this.giveAwayForm.triple_exel = "";
      this.giveAwayForm.image = "";
      this.giveAwayForm.sizes = false;
      this.giveAwayForm.give_away_id = "";
      this.giveAwayForm.giveaway_size_id = "";
      this.$eventHub.$emit("clearGiveAway");
    }
  },
  created() {
    this.getEventGiveAway(this.$route.params.id);
  }
};
</script>
<style>
.upload-box {
  background: rgba(230, 238, 241, 0.15);
  border: 1px dashed rgba(99, 105, 129, 0.6);
  text-align: center;
  height: calc(20rem + 1vh);
  width: 100%;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  cursor: pointer;
}
</style>

