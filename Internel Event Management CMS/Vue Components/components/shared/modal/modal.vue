<template>
  <div
    class="modal add-edit-modal event-modal"
    :class="{ 'active-modal' : showModal , 'admin-modal' : employee }"
    :data-modal="modalAction"
    data-action="add"
  >
    <div class="modal-content">
      <header class="modal-header">
        <div class="header-info">
          <p class="current-action">{{ headerTitle }}</p>
          <h3 class="headline">{{ headerDescription }}</h3>
        </div>
        <div v-show="deleteModal" class="modal-header-ctas">
          <modal-button
            @click="cancelbtn($event)"
            :cancelButtonLoading="cancelButtonLoading"
            :labelBefore="cancelLabel.btnTitleBefore"
            :labelAfter="cancelLabel.btnTitleAfter"
            :type="'btn-secondary'"
          ></modal-button>

          <modal-button
            v-show="course"
            @click="updateBtnClick($event)"
            :loading="updateButtonLoading"
            :labelBefore="label.btnTitleBefore"
            :labelAfter="label.btnTitleAfter"
            :type="'btn-primary modal-action'"
          ></modal-button>

          <modal-button
            v-show="editEvent"
            @click="updateBtnClick($event)"
            :loading="updateButtonLoading"
            :labelBefore="updateLabel.btnTitleBefore"
            :labelAfter="updateLabel.btnTitleAfter"
            :type="'btn-primary'"
          ></modal-button>

          <modal-button
            v-show="event"
            @click="updateBtnClick($event)"
            :loading="updateButtonLoading"
            :labelBefore="label.btnTitleBefore"
            :labelAfter="label.btnTitleAfter"
            :type="'btn-primary'"
          ></modal-button>

          <modal-button
            v-show="employee"
            @click="updateBtnClick($event)"
            :loading="updateButtonLoading"
            :labelBefore="label.btnTitleBefore"
            :labelAfter="label.btnTitleBefore"
            :type="'btn-primary modal-action'"
          ></modal-button>

       <modal-button
            v-show="registrant"
            @click="updateBtnClick($event)"
            :loading="updateButtonLoading"
            :labelBefore="label.btnTitleBefore"
            :labelAfter="label.btnTitleBefore"
            :type="'btn-primary modal-action'"
          ></modal-button>


        </div>
      </header>
      <section class="modal-cards">
        <article
          @click="modalSubactions($event,0,'main')"
          class="modal-card"
          :class="{ 'active-card' : event }"
          data-number="0"
          data-card="main"
        >
          <header class="modal-card-header">
            <h3 class="small-headline">{{ contentTitle }}</h3>
          </header>
          <slot></slot>
        </article>
        <article
          v-show="event"
          @click="modalSubactions($event,1,'courses')"
          class="modal-card"
          data-number="1"
          data-card="courses"
        >
          <header class="modal-card-header">
            <h3 class="small-headline">Add Course</h3>
          </header>
          <course-from :form="courseForm" :v="$v"></course-from>
        </article>
        <article
          v-show="event"
          @click="modalSubactions($event,2,'food')"
          class="modal-card"
          data-number="2"
          data-card="food"
        >
          <header class="modal-card-header">
            <h3 class="small-headline">Add Food</h3>
          </header>
          <section class="modal-card-data">
            <food-form :form="foodForm" :v="$v"></food-form>
          </section>
        </article>
        <article
          v-show="event"
          @click="modalSubactions($event,3,'hotels')"
          class="modal-card"
          data-number="3"
          data-card="hotels"
        >
          <header class="modal-card-header">
            <h3 class="small-headline">Add Hotel</h3>
          </header>
          <hotel-from :form="hotelForm" :v="$v"></hotel-from>
        </article>
        <article
          v-show="event"
          @click="modalSubactions($event,4,'giveaways')"
          class="modal-card"
          data-number="4"
          data-card="giveaways"
        >
          <header class="modal-card-header">
            <h3 class="small-headline">Add Giveaway Item(s)</h3>
          </header>
          <give-away :form="giveAwayForm" :v="$v"></give-away>
        </article>
      </section>

      <section v-show="editEvents" class="modal-subactions">
        <article
          @click="modalSubactions($event,1,'courses')"
          class="modal-subaction"
          data-number="1"
          data-card="courses"
        >
          <div class="modal-subaction-icon">
            <img :src="siteUrl + '/images/modal_subaction-courses.png'">
          </div>
          <p>Add Courses</p>
        </article>
        <article
          @click="modalSubactions($event,2,'food')"
          class="modal-subaction"
          data-number="2"
          data-card="food"
        >
          <div class="modal-subaction-icon">
            <img :src="siteUrl + '/images/modal_subaction-food.png'">
          </div>
          <p>Add Food</p>
        </article>
        <article
          @click="modalSubactions($event,3,'hotels')"
          class="modal-subaction"
          data-number="3"
          data-card="hotels"
        >
          <div class="modal-subaction-icon">
            <img :src="siteUrl + '/images/modal_subaction-hotels.png'">
          </div>
          <p>Add Hotels</p>
        </article>
        <article
          @click="modalSubactions($event,4,'giveaways')"
          class="modal-subaction"
          data-number="4"
          data-card="giveaways"
        >
          <div class="modal-subaction-icon">
            <img :src="siteUrl + '/images/modal_subaction-giveaways.png'">
          </div>
          <p>Add Giveaways</p>
        </article>
      </section>
    </div>
  </div>
</template>
<script>
import Button from "./../button/button.vue";
import { siteUrl } from "../../../config";
import courseForm from "./../../course/add.vue";
import hotelForm from "./../../hotel/add.vue";
import foodForm from "./../../food/add.vue";
import giveAwayForm from "./../../giveaway/add.vue";
import moment from "moment";
import { api } from "./../../../config";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
  props: [
    "showModal",
    "course",
    "editEvent",
    "headerTitle",
    "headerDescription",
    "btnTitle",
    "contentTitle",
    "event",
    "employee",
    "modalAction",
    "label",
    "deleteModal",
    "eventId",
    "editEvents",
    "registrant"
  ],
  components: {
    "modal-button": Button,
    "course-from": courseForm,
    "hotel-from": hotelForm,
    "food-form": foodForm,
    "give-away": giveAwayForm
  },
  watch: {
    $props: {
      handler() {},
      deep: true,
      immediate: true
    }
  },
  data() {
    return {
      api: api,
      siteUrl: siteUrl,
      updateButtonLoading: false,
      cancelButtonLoading: false,
      courseId: "",
      foodId: "",
      hotelId: "",
      giveAwayId: "",
      events: "",
      tabAttr: "main",
      updateLabel: {
        btnTitleBefore: "Update",
        btnTitleAfter: "Update"
      },
      cancelLabel: {
        btnTitleBefore: "Cancel",
        btnTitleAfter: "Cancel"
      },
      courseForm: {
        course_detail_id: "",
        courses: [],
        id: "",
        prefix: "",
        number: "",
        name: "",
        semester: "",
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
        reg_start_date: moment().format("YYYY-MM-DD"),
        reg_end_date: moment().format("YYYY-MM-DD"),
        start_time: "",
        end_time: "",
        details: "",
        room_number: "",
        seats: "",
        prerequisites: "",
        instructor: "",
        av_need: "",
        av_pro: "",
        status: 1
      },
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
      },
      foodForm: {
        food_id: "",
        name: "",
        phone: "",
        start_date: "",
        address: "",
        city_id: "",
        state_id: "",
        zip: ""
      },
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
    courseForm: {
      prefix: { required },
      number: { required },
      name: { required },
      start_date: { required },
      end_date: { required },
      reg_start_date: { required },
      reg_end_date: { required },
      details: { required },
      start_time: { required },
      end_time: { required },
      room_number: { required },
      seats: { required },
      prerequisites: { required }
    },
    hotelForm: {
      name: { required },
      start_date: { required },
      end_date: { required },
      phone: { required },
      address: { required },
      city_id: { required },
      state_id: { required },
      zip: { required }
    },
    foodForm: {
      name: { required },
      phone: { required },
      start_date: { required },
      address: { required },
      city_id: { required },
      state_id: { required },
      zip: { required }
    },
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
    cancelbtn(e) {
      this.$emit("cancel", e);
      this.courseForm.course_detail_id = "";
      this.courseForm.courses = [];
      this.courseForm.id = "";
      this.courseForm.prefix = "";
      this.courseForm.number = "";
      this.courseForm.name = "";
      this.courseForm.semester = "";
      this.courseForm.start_date = moment().format("YYYY-MM-DD");
      this.courseForm.end_date = moment().format("YYYY-MM-DD");
      this.courseForm.reg_start_date = moment().format("YYYY-MM-DD");
      this.courseForm.reg_end_date = moment().format("YYYY-MM-DD");
      this.courseForm.start_time = "";
      this.courseForm.end_time = "";
      this.courseForm.details = "";
      this.courseForm.room_number = "";
      this.courseForm.seats = "";
      this.courseForm.prerequisites = "";
      this.courseForm.instructor = "";
      this.courseForm.av_need = "";
      this.courseForm.av_pro = "";
      this.courseForm.status = 1;
      this.hotelForm.hotel_id = "";
      this.hotelForm.name = "";
      this.hotelForm.start_date = "";
      this.hotelForm.end_date = "";
      this.hotelForm.phone = "";
      this.hotelForm.address = "";
      this.hotelForm.city_id = "";
      this.hotelForm.state_id = "";
      this.hotelForm.zip = "";
      this.foodForm.food_id = "";
      this.foodForm.name = "";
      this.foodForm.phone = "";
      this.foodForm.start_date = "";
      this.foodForm.address = "";
      this.foodForm.city_id = "";
      this.foodForm.state_id = "";
      this.foodForm.zip = "";
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
      this.giveAwayForm.sizes = false;
      this.courseId = "";
      this.foodId = "";
      this.hotelId = "";
      this.giveAwayId = "";
      this.events = "";
      this.$v.$reset();
    },
    updateBtnClick(e) {
      this.$emit("update", e);
      if (this.event) {
        switch (this.tabAttr) {
          case "courses":
            this.manageCourses();
            break;
          case "food":
            this.manageFood();
            break;
          case "hotels":
            this.manageHotels();
            break;
          case "giveaways":
            this.manageGiveAways();
            break;
        }
      }
    },
    moveModalCards(card, num, amount, dataAttr) {
      this.tabAttr = dataAttr;
      let cards = document.querySelector(".modal-cards"),
        cardNumber = num;
      $(".active-card")
        .prevAll()
        .unbind();
      $(cards).css(
        "transform",
        `translate(calc(-${amount}vw - (${cardNumber} * 1.85rem)`
      );
      [].forEach.call(cards.children, function(c) {
        c.getAttribute("data-card") == dataAttr
          ? c.classList.add("active-card")
          : c.classList.remove("active-card");
      });
      document.querySelectorAll(".modal-subaction").forEach(sub => {
        if (sub.getAttribute("data-card") == dataAttr) {
          sub.classList.add("current-subaction");
          sub.classList.remove("inactive-subaction");
        } else {
          sub.classList.remove("current-subaction");
        }
      });
      if ($(".current-subaction").length > 0) {
        $(".modal-subaction:not(.current-subaction)").addClass(
          "inactive-subaction"
        );
      } else {
        $(".modal-subaction").removeClass("inactive-subaction");
      }
    },
    modalSubactions(event, num, dataAttr) {
      if (this.events) {
        let amount = num * 70;
        switch (dataAttr) {
          case "courses":
            this.label.btnTitleBefore = "Add Course";
            this.label.btnTitleAfter = "Add Course";
            break;
          case "food":
            this.label.btnTitleBefore = "Add Food";
            this.label.btnTitleAfter = "Add Food";
            break;
          case "hotels":
            this.label.btnTitleBefore = "Add Hotel";
            this.label.btnTitleAfter = "Add Hotel";
            break;
          case "giveaways":
            this.label.btnTitleBefore = "Add Give Away";
            this.label.btnTitleAfter = "Add Give Away";
            break;
          case "main":
            this.label.btnTitleBefore = "Add Event";
            this.label.btnTitleAfter = "Add Event";
            break;
        }

        this.moveModalCards(event, num, amount, dataAttr);
      }
    },
    manageCourses() {
      let avProsError = false;
      let avNeedsError = false;
      let instructorsError = false;

      let avProsErrorCount = 0;
      let avNeedsErrorCount = 0;
      let instructorsErrorCount = 0;

      if (this.editModal) {
        this.courseForm.avPros = this.courseForm.courses.av_pro;
        this.courseForm.avNeeds = this.courseForm.courses.av_needs;
        this.courseForm.instructors = this.courseForm.courses.instructor_name;
      }

      var i;
      for (i = 0; i < this.courseForm.avPros.length; i++) {
        if (
          this.courseForm.avPros[i].Name === null ||
          this.courseForm.avPros[i].Name === ""
        ) {
          this.courseForm.avPros[i].error = true;
          avProsErrorCount++;
        } else {
          this.courseForm.avPros[i].error = false;
        }
      }

      var i;
      for (i = 0; i < this.courseForm.avNeeds.length; i++) {
        if (
          this.courseForm.avNeeds[i].Name === null ||
          this.courseForm.avNeeds[i].Name === ""
        ) {
          this.courseForm.avNeeds[i].error = true;
          avNeedsErrorCount++;
        } else {
          this.courseForm.avNeeds[i].error = false;
        }
      }

      var i;
      for (i = 0; i < this.courseForm.instructors.length; i++) {
        if (
          this.courseForm.instructors[i].instructorName === null ||
          this.courseForm.instructors[i].instructorName === ""
        ) {
          this.courseForm.instructors[i].error = true;
          instructorsErrorCount++;
        } else {
          this.courseForm.instructors[i].error = false;
        }
      }

      if (avProsErrorCount > 0) {
        avProsError = true;
      } else {
        avProsError = false;
      }

      if (avNeedsErrorCount > 0) {
        avNeedsError = true;
      } else {
        avNeedsError = false;
      }

      if (instructorsErrorCount > 0) {
        instructorsError = true;
      } else {
        instructorsError = false;
      }

      this.$v.courseForm.$touch();
      if (this.$v.courseForm.$error) return;
      if (!avProsError && !instructorsError && !avNeedsError) {
        this.courseForm.facility_name = "  ";
        this.courseForm.address = "";
        this.courseForm.city_id = "";
        this.courseForm.state_id = "";
        this.courseForm.zip = "";
        this.courseForm.instructor_name = this.courseForm.instructors;
        this.courseForm.av_needs = this.courseForm.avNeeds;
        this.courseForm.av_pro = this.courseForm.avPros;
        this.courseForm.credit_type = "";
        this.courseForm.duration_type = "";

        this.courseForm.start_date = moment(this.courseForm.start_date).format(
          "YYYY-MM-DD"
        );
        this.courseForm.end_date = moment(this.courseForm.end_date).format(
          "YYYY-MM-DD"
        );

        this.courseForm.reg_start_date = moment(
          this.courseForm.reg_start_date
        ).format("YYYY-MM-DD");
        this.courseForm.reg_end_date = moment(
          this.courseForm.reg_end_date
        ).format("YYYY-MM-DD");

        this.courseForm.start_time = moment(
          this.courseForm.start_time,
          "h:mm a"
        ).format("HH:mm:ss");
        this.courseForm.end_time = moment(
          this.courseForm.end_time,
          "h:mm a"
        ).format("HH:mm:ss");

        if (this.courseId === "") {
          axios.post(api.addCoursesDetails, this.courseForm).then(res => {
            let data = {
              course_detail_id: res.data.course.id,
              event_id: this.eventId
            };
            this.courseId = res.data.course.id;
            axios
              .post(api.registerEventCourse, data)
              .then(res => {
                this.modalSubactions("", 2, "food");
                this.$noty.success("Food Added Successfully!");
              })
              .catch(err => {});
          });
        } else {
          this.modalSubactions("", 2, "food");
          this.$noty.warning("Food Already Added!");
        }
      }
    },
    manageGiveAways() {
      this.$v.giveAwayForm.$touch();
      if (this.$v.giveAwayForm.$error) return;
      let form_data = new FormData();
      if (this.giveAwayForm.image) {
        form_data.append("media", this.giveAwayForm.image);
      }
      if (this.giveAwayId === "") {
        form_data.append("items", this.giveAwayForm.item);
        form_data.append("quantity", this.giveAwayForm.quantity);
        form_data.append("phone", this.giveAwayForm.phone);
        form_data.append("vendor", this.giveAwayForm.vendor);
        form_data.append("address", this.giveAwayForm.address);
        form_data.append("city_id", this.giveAwayForm.city_id);
        form_data.append("state_id", this.giveAwayForm.state_id);
        form_data.append("event_id", this.eventId);
        form_data.append("zip", this.giveAwayForm.zip);
        form_data.append("sizes", this.giveAwayForm.sizes);

        axios.post(api.addGiveAway, form_data).then(res => {
          this.giveAwayId = res.data.give_away.id;
          form_data.append("giveaway_id", res.data.give_away.id);
          form_data.append("x_small", this.giveAwayForm.extra_small);
          form_data.append("small", this.giveAwayForm.small);
          form_data.append("medium", this.giveAwayForm.medium);
          form_data.append("large", this.giveAwayForm.large);
          form_data.append("x_large", this.giveAwayForm.extra_large);
          form_data.append("two_x_large", this.giveAwayForm.double_exel);
          form_data.append("three_x_large", this.giveAwayForm.triple_exel);
          axios.post(api.addGiveAwaySizes, form_data).then(res => {});
          this.$noty.success("GiveAway Added Successfully!");
          this.$eventHub.$emit("giveAwayAddedd");
          this.cancelbtn();
        });
      } else {
        this.$noty.warning("GiveAway Already Added!");
      }
    },
    manageHotels() {
      this.$v.hotelForm.$touch();
      if (this.$v.hotelForm.$error) return;
      this.hotelForm.event_id = this.eventId;
      this.hotelForm.start = moment(this.hotelForm.start_date).format(
        "YYYY-MM-DD"
      );
      this.hotelForm.end = moment(this.hotelForm.end_date).format("YYYY-MM-DD");
      if (!this.hotelForm.hotel_id) {
        if (this.hotelId === "") {
          axios.post(api.addHotel, this.hotelForm).then(res => {
            this.hotelId = res.data.id;
            this.$noty.success("Hotel Added Successfully!");
            this.modalSubactions("", 4, "giveaways");
          });
        } else {
          this.modalSubactions("", 4, "giveaways");
          this.$noty.warning("Hotel Already Added!");
        }
      } else {
        axios.post(api.updateHotel, this.hotelForm).then(res => {
          this.loading = false;
        });
      }
    },
    manageFood() {
      this.$v.foodForm.$touch();
      if (this.$v.foodForm.$error) return;
      this.foodForm.event_id = this.eventId;
      this.foodForm.caterer = this.foodForm.name;
      this.foodForm.date = moment(this.foodForm.start_date).format(
        "YYYY-MM-DD"
      );
      if (this.foodId === "") {
        axios.post(api.addFood, this.foodForm).then(res => {
          this.foodId = res.data.id;
          this.$noty.success("Food Added Successfully!");
          this.modalSubactions("", 3, "hotels");
        });
      } else {
        this.$noty.warning("Food Already Added!");
        this.modalSubactions("", 3, "hotels");
      }
    }
  },
  mounted() {
    this.$eventHub.$on("eventAdded", id => {
      this.events = id;
      this.modalSubactions("", 1, "courses");
    });

    this.$eventHub.$on("addEvent", () => {
      let num = 0;
      let amount = num * 70;
      this.moveModalCards("", num, amount, "main");
    });
  }
};
</script>

