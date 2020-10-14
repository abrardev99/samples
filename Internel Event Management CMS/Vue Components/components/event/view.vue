<template>
  <div>
    <nav-breadcrumb :event="true">
      <add-button
        :loading="loading"
        :labelBefore="label.btnTitleBefore"
        :labelAfter="label.btnTitleAfter"
        :type="'btn-primary'"
        @click="addEvent($event)"
      >
        <img :src="siteUrl + '/images/plus-white.svg'" />
      </add-button>
    </nav-breadcrumb>
    <event-sub-nav
      :event="true"
      @cards="cards"
      @list="list"
      @active="getEvents('active')"
      @inactive="getEvents('inactive')"
    ></event-sub-nav>
    <section class="selection" :data-view="view">
      <div class="container">
        <article v-for="event in events" class="card-item events-card-item" data-card="event">
          <hgroup class="card-item-headlines">
            <h2 class="card-item-headline">{{ event.name }}</h2>
            <h3 class="card-item-subheadline">{{ event.state.name }}, {{ event.city.name }}</h3>
          </hgroup>
          <div class="card-item-secondary-info">
            <p>
              <span>
                <b>{{ event.registrants }}</b> registrations
              </span>
            </p>
            <p>
              <span>
                <b>{{ event.course_details.length }}</b> courses
              </span>
            </p>
          </div>
          <div class="card-item-date-info">
            <div class="row">
              <div class="start">
                <p class="card-item-date-label">Start Date</p>
                <p class="card-item-date">{{ moment(event.start_date).format("YYYY/MM/DD") }}</p>
              </div>
              <div class="end">
                <p class="card-item-date-label">End Date</p>
                <p class="card-item-date">{{ moment(event.end_date).format("YYYY/MM/DD") }}</p>
              </div>
            </div>
            <div class="row">
              <div class="start">
                <p class="card-item-date-label">Reg. Start Date</p>
                <p class="card-item-date">{{ moment(event.reg_start_date).format("YYYY/MM/DD") }}</p>
              </div>
              <div class="end">
                <p class="card-item-date-label">Reg. End Date</p>
                <p class="card-item-date">{{ moment(event.reg_end_date).format("YYYY/MM/DD") }}</p>
              </div>
            </div>
          </div>
          <div class="card-item-options-menu">
            <div @click="dropDown($event)" class="card-item-menu-trigger events-card-item">
              <img :src="siteUrl + '/images/card-item.svg'" />
            </div>
            <ul class="card-item-menu events-card-menu" :class="{ 'open' : dropClass }">
              <li @click="edit(event.id)" class="card-item-menu-item">
                <a class="card-item-menu-link">Edit</a>
              </li>
              <router-link
                :to="{name: 'eventRegistrants', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Registrations</span>
                </a>
              </router-link>
              <router-link
                :to="{name: 'eventcourse', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Courses</span>
                </a>
              </router-link>
              <router-link
                :to="{name: 'eventFood', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Foods</span>
                </a>
              </router-link>
              <router-link
                :to="{name: 'eventhotel', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Hotels</span>
                </a>
              </router-link>
              <router-link :to="{name: 'eventSeating', params: { id: event.id  }}">
                <a href="/seating" :class="'card-item-menu-link'">
                  <span>View Seating</span>
                </a>
              </router-link>

              <router-link
                :to="{name: 'inviteEvent', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Invitations</span>
                </a>
              </router-link>

              <router-link
                :to="{name: 'eventGiveaways', params: { id: event.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Give Away</span>
                </a>
              </router-link>

              <li class="card-item-menu-item">
                <a @click="deleteEvent(event.id)" class="card-item-menu-link">Delete</a>
              </li>
            </ul>
          </div>
        </article>
        <article
          @click="addEvent($event)"
          class="card-item add-item modal-trigger"
          data-modal="event"
        >
          <img :src="siteUrl + '/images/plus.svg'" />
          <p>Add Event</p>
        </article>
      </div>
    </section>
    <modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="modalHeadercontent"
      :event="true"
      :modalAction="'event'"
      :label="labelModal"
      @cancel="cancelModal"
      @update="addModalEvent"
      :eventId="form.modal_event_id"
      :showModal="showModal"
      :deleteModal="true"
      :editEvents="editEvents"
    >
      <add-form :form="form" :error="error"></add-form>
    </modal>

    <modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="''"
      :label="labelModal"
      @cancel="cancelModal"
      @update="addModalEvent"
      :showModal="showDeleteModal"
      :deleteModal="false"
    >
      <section class="modal-card-data">
        <div class="row">
          <span>Are you sure you want to delete ?</span>
        </div>
        <div class="delete-content-right">
          <add-button
            :loading="loading"
            @click="cancelModal()"
            :labelBefore="'Cancel'"
            :labelAfter="'Cancel'"
            :type="'btn-secondary'"
          ></add-button>
          <add-button
            :loading="loading"
            :labelBefore="'Delete'"
            :labelAfter="'Delete'"
            :type="'btn-primary'"
            @click="confirmDelete()"
          ></add-button>
        </div>
      </section>
    </modal>
  </div>
</template>
<script>
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import AddButton from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import Add from "./../event/add.vue";
import Modal from "./../shared/modal/modal.vue";
import { api } from "../../config";
import { siteUrl } from "../../config";
import moment from "moment";
import { mapActions } from "vuex";
export default {
  components: {
    modal: Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    "add-button": AddButton
  },
  data() {
    return {
      events: [],
      loading: false,
      editEvents: false,
      deleteId: "",
      event: {
        data: ""
      },
      siteUrl: siteUrl,
      moment: moment,
      showModal: false,
      showDeleteModal: false,
      modalHeaderTitle: "",
      modalHeaderDescription: "Add Event",
      modalHeadercontent: "Event Info",
      labelModal: {
        btnTitleBefore: "Add Event",
        btnTitleAftere: "Add Event"
      },
      label: {
        btnTitleBefore: "Add Event",
        btnTitleAftere: "Add Event"
      },
      view: "cards",
      dropClass: false,
      form: {
        name: "",
        start_date: "",
        end_date: "",
        city_id: "",
        state_id: "",
        reg_start_date: "",
        reg_end_date: "",
        details: "",
        modal_event_id: "",
        status: 1
      },
      error: {
        name: "",
        start_date: "",
        end_date: "",
        city_id: "",
        state_id: "",
        reg_start_date: "",
        reg_end_date: "",
        details: ""
      }
    };
  },
  computed: {},
  methods: {
    ...mapActions(["setEventsData"]),
    edit(id) {
      this.$eventHub.$emit("addEvent");
      this.cancelModal();
      this.showModal = true;
      this.modalHeaderDescription = "Update Event";
      this.labelModal.btnTitleBefore = "Update Event";
      this.labelModal.btnTitleAftere = "Update Event";
      axios.get(api.editEvent + id).then(response => {
        this.form.name = response.data.name;
        this.form.start_date = moment(response.data.start_date).format(
          "D	 MMM YY"
        );
        this.form.end_date = moment(response.data.end_date).format("D	 MMM YY");
        this.form.reg_start_date = moment(response.data.reg_start_date).format(
          "D	 MMM YY"
        );
        this.form.reg_end_date = moment(response.data.reg_end_date).format(
          "D	 MMM YY"
        );
        this.form.city_id = response.data.city_id;
        this.form.state_id = response.data.state_id;
        this.form.details = response.data.details;
        this.form.status = response.data.status;
        this.form.event_id = response.data.id;
        this.editEvents = false;
      });
    },
    getEventRegistrants(id) {
      axios.get(api.registrantsCount + id).then(res => {
        if (res.data.registrants) {
          return res.data.registrants.users.length;
        } else {
          return 0;
        }
      });
    },
    getEvents(status) {
      this.events = "";
      let resultVal = "";
      switch (status) {
        case "active":
          resultVal = 1;
          break;
        case "inactive":
          resultVal = 0;
          break;
      }

      axios
        .get(api.event + resultVal)
        .then(res => {
          this.events = res.data;
        })
        .catch(err => {
          err.response.data.error && this.$noty.error(err.response.data.error);
          err.response.data.errors
            ? this.setErrors(err.response.data.errors)
            : this.clearErrors();
        });
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    addEvent() {
      this.editEvents = true;
      this.cancelModal();
      this.modalHeaderDescription = "Add Event";
      this.labelModal.btnTitleBefor = "Add Event";
      this.labelModal.btnTitleAfter = "Add Event";
      this.showModal = true;
      this.$eventHub.$emit("addEvent");
    },
    deleteEvent(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Event";
      this.labelModal.btnTitleBefor = "Delete Event";
      this.labelModal.btnTitleAfter = "Delete Event";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    confirmDelete() {
      axios.delete(api.deleteEvent + this.deleteId).then(res => {
        this.$noty.success("Event Delete Successfully!");
        this.getEvents("active");
      });
      this.cancelModal();
      this.getEvents("active");
    },
    addModalEvent() {
      let error = false;

      if (this.form.name == "") {
        this.error.name = "Name is required.";
        error = false;
      } else {
        this.error.name = "";
        error = true;
      }

      if (this.form.start_date == "") {
        this.error.start_date = "Start Date is required.";
        error = false;
      } else {
        this.error.start_date = "";
        error = true;
      }

      if (this.form.end_date == "") {
        this.error.end_date = "End Date is required.";
        error = false;
      } else {
        this.error.end_date = "";
        error = true;
      }

      if (this.form.city_id == "") {
        this.error.city_id = "City is required.";
        error = false;
      } else {
        this.error.city_id = "";
        error = true;
      }

      if (this.form.state_id == "") {
        this.error.state_id = "State is required.";
        error = false;
      } else {
        this.error.state_id = "";
        error = true;
      }

      if (this.form.reg_start_date == "") {
        this.error.reg_start_date = "Registration Start Date is required.";
        error = false;
      } else {
        this.error.reg_start_date = "";
        error = true;
      }

      if (this.form.reg_end_date == "") {
        this.error.reg_end_date = "Registration End Date is required.";
        error = false;
      } else {
        this.error.reg_end_date = "";
        error = true;
      }

      if (this.form.details == "") {
        this.error.details = "Details is required.";
        error = false;
      } else {
        this.error.details = "";
        error = true;
      }

      if (error) {
        this.form.start_date = moment(this.form.start_date).format(
          "YYYY-MM-DD"
        );
        this.form.end_date = moment(this.form.end_date).format("YYYY-MM-DD");
        this.form.reg_start_date = moment(this.form.reg_start_date).format(
          "YYYY-MM-DD"
        );
        this.form.reg_end_date = moment(this.form.reg_end_date).format(
          "YYYY-MM-DD"
        );
        this.form.event_name = this.form.name;
        if (!this.form.event_id) {
          if (this.form.modal_event_id === "") {
            axios
              .post(api.addNewEvent, this.form)
              .then(res => {
                this.loading = false;
                this.form.modal_event_id = res.data.response.event_id;
                this.$noty.success("Event Added Successfully !");
                this.getEvents("active");
                this.$eventHub.$emit("eventAdded", res.data.response.event_id);
              })
              .catch(err => {
                err.response.data.error &&
                  this.$noty.error(err.response.data.error);
                err.response.data.errors
                  ? this.setErrors(err.response.data.errors)
                  : this.clearErrors();
                this.loading = false;
              });
          }
        } else {
          axios
            .post(api.updateEvent, this.form)
            .then(res => {
              this.loading = false;
              this.showModal = false;
              this.$noty.success("Event Updated Successfully!");
              this.getEvents("active");
            })
            .catch(err => {
              err.response.data.error &&
                this.$noty.error(err.response.data.error);
              err.response.data.errors
                ? this.setErrors(err.response.data.errors)
                : this.clearErrors();
              this.loading = false;
            });
        }
      }
    },
    setErrors(errors) {
      this.error.name = errors.name ? errors.name[0] : null;
      this.error.start_date = errors.start_date ? errors.start_date[0] : null;
      this.error.end_date = errors.end_date ? errors.end_date[0] : null;
      this.error.city_id = errors.city_id ? errors.city_id[0] : null;
      this.error.state_id = errors.state_id ? errors.state_id[0] : null;
      this.error.reg_start_date = errors.reg_start_date
        ? errors.reg_start_date[0]
        : null;
      this.error.reg_end_date = errors.reg_end_date
        ? errors.reg_end_date[0]
        : null;
      this.error.details = errors.details ? errors.details[0] : null;
    },
    clearErrors() {
      this.error.name = null;
      this.error.start_date = null;
      this.error.end_date = null;
      this.error.city_id = null;
      this.error.state_id = null;
      this.error.reg_start_date = null;
      this.error.reg_end_date = null;
      this.error.details = null;
    },
    editEvent(id) {
      this.$router.push({ name: "editEvent", params: { id } });
    },
    eventAddedSuccess(date) {
      this.$router.push({ name: "event" });
    },
    cards() {
      this.view = "cards";
    },
    list() {
      this.view = "list";
    },
    cancelModal() {
      this.form.name = "";
      this.form.start_date = "";
      this.form.end_date = "";
      this.form.city_id = "";
      this.form.state_id = "";
      this.form.reg_start_date = "";
      this.form.reg_end_date = "";
      this.form.details = "";
      this.form.status = 1;
      this.clearErrors();
      this.showModal = false;
      this.showDeleteModal = false;
      this.form.event_id = "";
      this.form.modal_event_id = "";
    }
  },
  mounted() {
    this.getEvents("active");
    this.$eventHub.$on("giveAwayAddedd", id => {
      this.Modal = false;
      this.cancelModal();
    });
  },
  created() {}
};
</script>
<style>
.card-item-menu-link {
  font-size: 12px;
  padding: 0.5rem 1rem;
  color: #02082f;
}

.card-item-menu {
  right: 0;
}
</style>

