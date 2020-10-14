<template>
  <div>
    <nav-breadcrumb :registrants="true">
      <btn
        :loading="loading"
        :labelBefore="registrantslabel.btnTitleBefore"
        :labelAfter="registrantslabel.btnTitleAfter"
        :type="'btn-primary btn-primary--admin modal-trigger'"
        @click="addRegistrants($event)"
      >
        <img :src="siteUrl + '/images/plus-white.svg'">
      </btn>
    </nav-breadcrumb>
    <main class="main">
      <div class="container">
        <span class="card-search employee-search">
          <div class="search-field">
            <label for="employee-search">
              <span hidden>Search</span>
              <img :src="siteUrl + '/images/search.svg'">
            </label>
            <input
              type="text"
              v-model="registrantsSearch"
              placeholder="Search Registrants"
              @keyup="search"
              id="employee-search"
            >
          </div>
        </span>
      </div>
      <section class="selection" data-view="list">
        <div class="container">
          <article
            v-for="user in users"
            v-if="user.users[0].role_id === 3"
            class="card-item admin-card-item employee-card"
          >
            <hgroup class="card-item-headlines">
              <h2
                class="card-item-headline"
              >{{ user.users[0].first_name }} {{ user.users[0].last_name }}</h2>
              <h3 class="card-item-subheadline">{{ user.users[0].email }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <span>{{ user.users[0].external_participants.company }}</span>
              </p>
              <p>
                <span>{{ user.users[0].external_participants.title }}</span>
              </p>
            </div>
            <div class>
              <label class="att-flex-box">
                Attendance
                <div class="attendance-checkbox">
                  <input
                    v-if="showEvent"
                    type="checkbox"
                    @change="attendance($event,user.users[0].id,user.events[0].attendance[0].id)"
                    :checked="user.events[0].attendance[0].joining_status"
                  >
           
                  <input
                    v-if="!showEvent"
                    type="checkbox"
                    @change="attendance($event,user.users[0].id,user.course_details[0].attendance[0].id)"
                    :checked="user.course_details[0].attendance[0].joining_status"
                  >
                </div>
              </label>
            </div>
            <div class="card-item-options-menu">
              <div @click="dropDown($event)" class="card-item-menu-trigger">
                <img :src="siteUrl + '/images/green-circle.svg'">
              </div>
              <ul class="card-item-menu">
                <li class="card-item-menu-item">
                  <a @click="deleteUser(user.id)" class="card-item-menu-link">Delete</a>
                </li>
              </ul>
            </div>
          </article>
          <article
            @click="addRegistrants($event)"
            class="card-item add-item modal-trigger"
            data-modal="registrants"
          >
            <img :src="siteUrl + '/images/plus-gray.svg'">
            <p>Add Registrants</p>
          </article>
          <div v-if="getPageCount > 1" class="card-item add-item">
            <paginate
              :page-count="getPageCount"
              :container-class="'pagination'"
              :prev-text="'prev'"
              :next-text="'next'"
              :click-handler="clickCallback"
            ></paginate>
          </div>
        </div>
      </section>
    </main>
    <modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="modalHeadercontent"
      :label="modalLabel"
      :deleteModal="true"
      @cancel="cancelModal"
      @update="register"
      :showModal="showModal"
      :registrant="true"
    >
      <add-form :form="form" :error="error"></add-form>
    </modal>
    <modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="''"
      :label="labelModal"
      @cancel="cancelModal"
      @update="register"
      :showModal="showDeleteModal"
      :deleteModal="false"
    >
      <section class="modal-card-data">
        <div class="row">
          <span>Are you sure you want to delete ?</span>
        </div>
        <div class="delete-content-right">
          <btn
            :loading="loading"
            @click="cancelModal()"
            :labelBefore="'Cancel'"
            :labelAfter="'Cancel'"
            :type="'btn-secondary'"
          ></btn>
          <btn
            :loading="loading"
            :labelBefore="'Delete'"
            :labelAfter="'Delete'"
            :type="'btn-primary'"
            @click="confirmDelete()"
          ></btn>
        </div>
      </section>
    </modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import Add from "./../registrants/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import Button from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import { required, email, minLength } from "vuelidate/lib/validators";
import moment from "moment";
export default {
  components: {
    modal: Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    btn: Button
  },
  data() {
    return {
      showCourse: false,
      showEvent: false,
      attendanceStatus: false,
      moment: moment,
      parPage: 10,
      getPageCount: 1,
      users: [],
      labelModal: [],
      showDeleteModal: false,
      registrantsSearch: "",
      siteUrl: siteUrl,
      showModal: false,
      modalHeaderTitle: "",
      modalHeaderDescription: "Add Registrant",
      modalHeadercontent: "Registrants Info",
      loading: false,
      siteUrl: siteUrl,
      importlabel: {
        btnTitleBefore: "Import CSV",
        btnTitleAfter: "Import CSV"
      },
      registrantslabel: {
        btnTitleBefore: "Add Registrants",
        btnTitleAfter: "Add Registrants"
      },
      modalLabel: {
        btnTitleBefore: "Add Registrants",
        btnTitleAfter: "Add Registrants"
      },
      form: {
        id: "",
        name: "",
        email: ""
      },
      error: {
        name: "",
        email: ""
      }
    };
  },
  methods: {
    clickCallback: function(pageNum) {
      this.currentPage = Number(pageNum);
      this.search();
    },
    confirmDelete() {
      let url = "";
      let data = "";
      switch (this.$route.name) {
        case "courseRegistrants":
          url = api.deleteRegisterCourseInternalUser;
          break;
        case "eventRegistrants":
          url = api.deleteRegisterEventInternalUser;
          break;
      }
      axios.delete(url + this.deleteId).then(res => {
        this.$noty.success("Registrants Delete Successfully!");
        this.getRegistrants();
      });
      this.cancelModal();
    },
    search() {
      let search = "";
      switch (this.$route.name) {
        case "courseRegistrants":
          search = {
            search_value: this.registrantsSearch,
            page: this.currentPage,
            per_page: this.parPage,
            course_detail_id: this.$route.params.id
          };
          break;
        case "eventRegistrants":
          search = {
            search_value: this.registrantsSearch,
            page: this.currentPage,
            per_page: this.parPage,
            event_id: this.$route.params.id
          };
          break;
      }

      if (this.registrantsSearch) {
        axios.post(api.employeeExternalUserSearch, search).then(res => {
          this.users = res.data.data;
          if (res.data.current_page !== null) {
            this.getPageCount = res.data.current_page;
          }
        });
      } else {
        this.getRegistrants();
      }
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    getRegistrants() {
      let url = "";
      let data = "";
      switch (this.$route.name) {
        case "courseRegistrants":
          this.showEvent = false;
          data = {
            page: this.currentPage,
            per_page: this.parPage,
            course_detail_id: this.$route.params.id
          };
          url = api.getCoursesexternaleUsers;
          break;
        case "eventRegistrants":
          this.showEvent = true;
          data = {
            page: this.currentPage,
            per_page: this.parPage,
            event_id: this.$route.params.id
          };
          url = api.getEventsexternaleUsers;
          break;
      }

      axios.post(url, data).then(response => {
        this.users = response.data.data;
        if (response.data.current_page !== null) {
          this.getPageCount = response.data.current_page;
        }
      });
    },
    register() {
      let errname = false;
      let erremail = false;

      if (this.form.name == "") {
        this.error.name = "Name is required.";
        errname = false;
      } else {
        this.error.name = "";
        errname = true;
      }

      if (this.form.email == "") {
        this.error.email = "Email is required.";
        erremail = false;
      } else {
        this.error.email = "";
        erremail = true;
      }

      if (errname && erremail) {
        this.form.joining_status = 1;
        if (!this.form.id) {
          let url = "";
          let data = "";
          switch (this.$route.name) {
            case "courseRegistrants":
              this.form.course_detail_id = this.$route.params.id;
              url = api.registerCourseInternalUser;
              break;
            case "eventRegistrants":
              this.form.event_id = this.$route.params.id;
              url = api.registerEventInternalUser;
              break;
          }

          axios
            .post(url, this.form)
            .then(res => {
              if (res.data.error) {
                this.$noty.error("Registrant Already Exist !");
              } else {
                this.loading = false;
                this.getRegistrants();
                this.cancelModal();
                this.$noty.success("Registrant Created Successfully !");
              }
            })
            .catch(err => {
              err.response.data.error &&
                this.$noty.error(err.response.data.error);
              err.response.data.errors
                ? this.setErrors(err.response.data.errors)
                : this.clearErrors();
              this.loading = false;
            });
        } else {
          axios
            .post(api.updateUser, this.form)
            .then(res => {
              this.loading = false;
              this.getRegistrants();
              this.cancelModal();
              this.$noty.success("Registrant Updated Successfully !");
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
    attendance(e, user_id, attendance_id) {
      let dateTime = new Date();
      dateTime = moment(dateTime).format("YYYY-MM-DD HH:mm:ss");
      let data = {
        user_id: user_id,
        attendance_id: attendance_id,
        joining_status: e.target.checked,
        att_date_time: dateTime
      };
      axios.post(api.markAttendance, data).then(res => {
        if (e.target.checked) {
          this.$noty.success("Attendance Marked Successfully");
        } else {
          this.$noty.warning("Attendance Unmarked Successfully");
        }
      });
    },
    edit(id) {
      this.clearForm();
      this.editModal = true;
      this.showModal = true;
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Update Registrants";
      this.modalHeadercontent = "Registrants Info";
      this.registrantslabel.btnTitleBefore = "Update Registrants";
      this.registrantslabel.btnTitleAftere = "Update Registrants";
      this.modalLabel.btnTitleBefore = "Update Registrants";
      this.modalLabel.btnTitleAftere = "Update Registrants";
      this.form.user_id = id;
      axios.get(api.internalUsers + "/" + id).then(res => {
        this.form.id = res.data.id;
        this.form.name = res.data.first_name + " " + res.data.last_name;
        this.form.email = res.data.email;
      });
    },
    deleteUser(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Registrants";
      this.labelModal.btnTitleBefor = "Delete Registrants";
      this.labelModal.btnTitleAfter = "Delete Registrants";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    addRegistrants: function(e) {
      this.modalHeaderDescription = "Add Registrants";
      this.showModal = true;
      this.registrantslabel.btnTitleBefore = "Add Registrants";
      this.registrantslabel.btnTitleAftere = "Add Registrants";
      this.modalLabel.btnTitleBefore = "Add Registrants";
      this.modalLabel.btnTitleAftere = "Add Registrants";
    },
    cancelModal() {
      this.showModal = false;
      this.showDeleteModal = false;
      this.clearForm();
    },
    setErrors(errors) {
      this.error.email = errors.email ? errors.email[0] : null;
      this.error.name = errors.name ? errors.name[0] : null;
    },
    clearErrors() {
      this.error.email = null;
      this.error.name = null;
    },
    clearForm() {
      this.form.id = "";
      this.form.name = "";
      this.form.email = "";
    }
  },
  created() {
    this.getRegistrants();
  }
};
</script>
<style>
.pagination {
  display: inline-block;
}

.pagination li {
  color: #02082f;
  text-transform: capitalize;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  list-style: none;
}

.pagination li.active {
  background-color: #1b998b;
  color: white;
  border-radius: 5px;
}

.pagination li a:hover:not(.active) {
  border-radius: 5px;
}

.attendance-checkbox {
  padding-top: 2px;
}
.att-flex-box {
  display: flex;
}
</style>
