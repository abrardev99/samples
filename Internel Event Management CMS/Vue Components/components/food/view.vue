<template>
  <div>
    <nav-breadcrumb v-if="!course" :editEvent="true" :eventTitle="eventTitle">
   
    </nav-breadcrumb>
    <nav-breadcrumb v-else :course="true" :eventTitle="eventTitle"></nav-breadcrumb>
    <event-sub-nav v-if="!course" @cards="cards" @list="list"></event-sub-nav>
    <course-sub-nav v-else=""></course-sub-nav>
    <section class="selection" :data-view="view">
      <div class="container">
        <article class="card-item events-card-item" v-for="food in foods">
          <hgroup class="card-item-headlines">
            <h2 class="card-item-headline">{{ food.caterer }}</h2>
          </hgroup>
          <div class="card-item-secondary-info">
            <div class="stacked-paragraphs">
              <img :src="siteUrl + '/images/location.svg'">
              <p>
                <span>{{ food.address }}</span>
                <span>{{ food.city.name }}, {{ food.states.name }} {{ food.zip }}</span>
              </p>
            </div>
          </div>
          <div class="card-item-date-info">
            <div class="start">
              <p class="card-item-date-label">CATER DATE</p>
              <p class="card-item-date">{{ moment(food.date).format("YYYY-MM-DD")}}</p>
            </div>
          </div>
          <div class="card-item-options-menu">
            <div @click="dropDown($event)" class="card-item-menu-trigger events-card-item">
              <img :src="siteUrl + '/images/card-item.svg'">
            </div>
            <ul class="card-item-menu events-card-menu">
              <li @click="edit(food.id)" class="card-item-menu-item">
                <a class="card-item-menu-link">Edit</a>
              </li>
              <li class="card-item-menu-item">
                <a @click="deleteFood(food.id)" class="card-item-menu-link">Delete</a>
              </li>
            </ul>
          </div>
        </article>
        <article
          @click="addFood($event)"
          class="card-item add-item modal-trigger"
          data-modal="courses"
        >
          <img :src="siteUrl + '/images/plus.svg'">
          <p>Add Food</p>
        </article>
      </div>
    </section>
    <food-modal
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
      <section class="modal-card-data">
        <add-form :form="foodForm" :v="$v" :editModal="editModal"></add-form>
      </section>
    </food-modal>
    <food-modal
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
    </food-modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import Add from "./../food/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import EditButton from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import CourseSubNav from "./../course/navbar.vue";
import moment from "moment";
import { required, email, minLength } from "vuelidate/lib/validators";
import { mapGetters } from "vuex";
export default {
  components: {
    "food-modal": Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "course-sub-nav": CourseSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    "edit-button": EditButton
  },
  data() {
    return {
      courses: [],
      foods: "",
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
      modalHeadercontent: "Add Food",
      loading: false,
      siteUrl: siteUrl,
      navlabel: {
        btnTitleBefore: "Edit Food",
        btnTitleAfter: "Edit Food"
      },
      labelModal: {
        btnTitleBefore: "Delete Food",
        btnTitleAftere: "Delete Food"
      },
      label: {
        btnTitleBefore: "Add Food",
        btnTitleAfter: "Add Food"
      },
      view: "cards",
      foodForm: {
        food_id: "",
        name: "",
        phone: "",
        start_date: "",
        address: "",
        city_id: "",
        state_id: "",
        zip: ""
      }
    };
  },
  computed: mapGetters(["hasEventData"]),
  validations: {
    foodForm: {
      name: { required },
      phone: { required },
      start_date: { required },
      address: { required },
      city_id: { required },
      state_id: { required },
      zip: { required }
    }
  },
  methods: {
    deleteFood(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Food";
      this.labelModal.btnTitleBefor = "Delete Food";
      this.labelModal.btnTitleAfter = "Delete Food";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    getEventFoods(eventId) {
      this.foodForm.event_id = eventId;
      let url = "";
      let event = false;
      switch (this.$route.name) {
        case "eventFood":
          url = api.editEvent;
          event = true;
          this.course = false;
          break;
        case "courseFood":
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

        this.foods = res.data.foods;
      });
    },
    addFood: function(e) {
      this.modalHeaderTitle = "Add Food";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Add Food";
      this.label.btnTitleBefore = "Add Food";
      this.label.btnTitleAftere = "Add Food";
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
      this.$v.foodForm.$touch();
      if (this.$v.foodForm.$error) return;
      this.foodForm.id = this.foodForm.food_id;
      this.foodForm.caterer = this.foodForm.name;
      this.foodForm.date = moment(this.foodForm.start_date).format(
        "YYYY-MM-DD"
      );
      this.foodForm.foodable_id = this.$route.params.id;
      switch (this.$route.name) {
        case "eventFood":
          this.foodForm.foodable_type = "event";
          break;
        case "courseFood":
          this.foodForm.foodable_type = "course";
          break;
      }
      if (!this.foodForm.food_id) {
        axios.post(api.addFood, this.foodForm).then(res => {
          this.loading = false;
          this.showModal = false;
          this.getEventFoods(this.$route.params.id);
          this.$noty.success("Food Added Successfully!");
        });
      } else {
        axios.post(api.updateFood, this.foodForm).then(res => {
          this.loading = false;
          this.showModal = false;
          this.getEventFoods(this.$route.params.id);
          this.$noty.success("Food Updated Successfully!");
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
      this.modalHeaderTitle = "Edit Food";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Edit Food";
      this.label.btnTitleBefore = "Update Food";
      this.label.btnTitleAftere = "Update Food";
      axios.get(api.getEventFood + "/" + id).then(response => {
        this.$eventHub.$emit("foods", response.data);
        this.foodForm.food_id = response.data[0].id;
        this.foodForm.name = response.data[0].caterer;
        this.foodForm.phone = response.data[0].phone;
        this.foodForm.start_date = moment(response.data[0].date).format(
          "YYYY-MM-DD"
        );
        this.foodForm.address = response.data[0].address;
        this.foodForm.city_id = response.data[0].city_id;
        this.foodForm.state_id = response.data[0].state_id;
        this.foodForm.zip = response.data[0].zip;
      });
    },
    confirmDelete() {
      axios.delete(api.deleteFood + this.deleteId).then(res => {
        this.$noty.success("Food Delete Successfully!");
      });
      this.cancelModal();
      this.getEventFoods(this.$route.params.id);
    },
    clearForm() {
      this.$v.$reset();
      this.foodForm.food_id = "";
      this.foodForm.name = "";
      this.foodForm.phone = "";
      this.foodForm.start_date = "";
      this.foodForm.address = "";
      this.foodForm.city_id = "";
      this.foodForm.state_id = "";
      this.foodForm.zip = "";
    }
  },
  created() {
    this.getEventFoods(this.$route.params.id);
  }
};
</script>
