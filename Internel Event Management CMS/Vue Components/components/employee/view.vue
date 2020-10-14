<template>
  <div>
    <nav-breadcrumb :employee="true">
      <btn
        :loading="loading"
        :labelBefore="importlabel.btnTitleBefore"
        :labelAfter="importlabel.btnTitleAfter"
        :type="'btn-secondary'"
      >
        <img :src="siteUrl + '/images/top-black-arrow.svg'">
      </btn>
      <btn
        :loading="loading"
        :labelBefore="employeelabel.btnTitleBefore"
        :labelAfter="employeelabel.btnTitleAfter"
        :type="'btn-primary btn-primary--admin modal-trigger'"
        @click="addEmployee($event)"
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
              v-model="employeeSearch"
              placeholder="Search employees"
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
            v-if="user.role_id === 2"
            class="card-item admin-card-item employee-card"
          >
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ user.first_name }} {{ user.last_name }}</h2>
              <h3 class="card-item-subheadline">{{ user.internal_participants.job_title }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <span>{{ user.internal_participants.sector }}</span>
              </p>
              <p>
                <span>{{ user.internal_participants.states.name }}, {{ user.internal_participants.cities.name }}</span>
              </p>
            </div>
            <div class="card-item-options-menu">
              <div @click="dropDown($event)" class="card-item-menu-trigger">
                <img :src="siteUrl + '/images/green-circle.svg'">
              </div>
              <ul class="card-item-menu">
                <li class="card-item-menu-item">
                  <a @click="edit(user.id)" class="card-item-menu-link">Edit</a>
                </li>
                <li class="card-item-menu-item">
                  <a @click="deleteUser(user.id)" class="card-item-menu-link">Delete</a>
                </li>
              </ul>
            </div>
          </article>
          <article
            @click="addEmployee($event)"
            class="card-item add-item modal-trigger"
            data-modal="employee"
          >
            <img :src="siteUrl + '/images/plus-gray.svg'">
            <p>Add Employee</p>
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
      :employee="true"
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
import Add from "./../employee/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import Button from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import { required, email, minLength } from "vuelidate/lib/validators";
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
      parPage: 10,
      getPageCount: 1,
      users: [],
      labelModal: [],
      showDeleteModal: false,
      employeeSearch: "",
      siteUrl: siteUrl,
      showModal: false,
      modalHeaderTitle: "",
      modalHeaderDescription: "Add Employee",
      modalHeadercontent: "Employee Info",
      loading: false,
      siteUrl: siteUrl,
      importlabel: {
        btnTitleBefore: "Import CSV",
        btnTitleAfter: "Import CSV"
      },
      employeelabel: {
        btnTitleBefore: "Add Employee",
        btnTitleAfter: "Add Employee"
      },
      modalLabel: {
        btnTitleBefore: "Add Employee",
        btnTitleAfter: "Add Employee"
      },
      form: {
        id: "",
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        role_id: 2,
        password: "",
        password_confirmation: "",
        state_id: "",
        city_id: "",
        job_title: "",
        marketing_title: "",
        sector: "",
        status: "activated"
      },
      error: {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        role_id: "",
        password: "",
        password_confirmation: "",
        state_id: "",
        city_id: "",
        job_title: "",
        marketing_title: "",
        sector: ""
      }
    };
  },
  methods: {
    clickCallback: function(pageNum) {
      this.currentPage = Number(pageNum);
      this.search();
    },
    confirmDelete() {
      axios.delete(api.deleteUser + this.deleteId).then(res => {
        this.$noty.success("Employee Delete Successfully!");
      });
      this.cancelModal();
      this.getEmployees();
    },
    search() {
      let search = {
        search_value: this.employeeSearch,
        page: this.currentPage,
        per_page: this.parPage
      };

      if (this.employeeSearch) {
        axios.post(api.employeeInternalSearch, search).then(res => {
          this.users = res.data.data;
          if (res.data.current_page !== null) {
            this.getPageCount = res.data.current_page;
          }
        });
      } else {
        this.getEmployees();
      }
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    getEmployees() {
      axios
        .post(api.internalUsers, {
          page: this.currentPage,
          per_page: this.parPage
        })
        .then(response => {
          this.users = response.data.data;
          if (response.data.current_page !== null) {
            this.getPageCount = response.data.current_page;
          }
        });
    },
    register() {
      let errFname = false;
      let errLname = false;
      let errEmail = false;
      let errPhone = false;
      let errPassword = false;
      let errPasswordConfirmation = false;
      let errState_id = false;
      let errCity_id = false;
      let errJob_title = false;
      let errMarketing_title = false;
      let errSector = false;
      let errStatus = false;

      if (this.form.first_name == "") {
        this.error.first_name = "first name is required.";
        errFname = false;
      } else {
        this.error.first_name = "";
        errFname = true;
      }

      if (this.form.last_name == "") {
        this.error.last_name = "last name is required.";
        errLname = false;
      } else {
        this.error.last_name = "";
        errLname = true;
      }

      if (this.form.email == "") {
        this.error.email = "email is required.";
        errEmail = false;
      } else {
        this.error.email = "";
        errEmail = true;
      }

      if (this.form.phone == "") {
        this.error.phone = "phone is required.";
        errPhone = false;
      } else {
        this.error.phone = "";
        errPhone = true;
      }

      if (!this.form.id) {
        if (this.form.password == "") {
          this.error.password = "password is required.";
          errPassword = false;
        } else {
          this.error.password = "";
          errPassword = true;
        }

        if (this.form.password_confirmation == "") {
          this.error.password_confirmation = "Confirm password is required.";
          errPasswordConfirmation = false;
        } else {
          this.error.password_confirmation = "";
          errPasswordConfirmation = true;
        }
      } else {
        errPassword = true;
        errPasswordConfirmation = true;
      }

      if (this.form.state_id == "") {
        this.error.state_id = "state is required.";
        errState_id = false;
      } else {
        this.error.state_id = "";
        errState_id = true;
      }

      if (this.form.city_id == "") {
        this.error.city_id = "city is required.";
        errCity_id = false;
      } else {
        this.error.city_id = "";
        errCity_id = true;
      }

      if (this.form.job_title == "") {
        this.error.job_title = "job title is required.";
        errJob_title = false;
      } else {
        this.error.job_title = "";
        errJob_title = true;
      }

      if (this.form.marketing_title == "") {
        this.error.marketing_title = "marketing title is required.";
        errMarketing_title = false;
      } else {
        this.error.marketing_title = "";
        errMarketing_title = true;
      }

      if (this.form.sector == "") {
        this.error.sector = "sector title is required.";
        errSector = false;
      } else {
        this.error.sector = "";
        errSector = true;
      }

      if (this.form.status == "") {
        this.error.status = "status title is required.";
        errStatus = false;
      } else {
        this.error.status = "";
        errStatus = true;
      }

      if (
        errFname &&
        errLname &&
        errEmail &&
        errPhone &&
        errPassword &&
        errPasswordConfirmation &&
        errState_id &&
        errCity_id &&
        errJob_title &&
        errMarketing_title &&
        errSector &&
        errStatus
      ) {
        if (!this.form.id) {
          axios
            .post(api.register, this.form)
            .then(res => {
              this.loading = false;
              this.getEmployees();
              this.cancelModal();
              this.$noty.success("Employee Created Successfully!");
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
              this.getEmployees();
              this.cancelModal();
              this.$noty.success("Employee Updated Successfully!");
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
    edit(id) {
      this.clearForm();
      this.editModal = true;
      this.showModal = true;
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Update Employee";
      this.modalHeadercontent = "Employee Info";
      this.employeelabel.btnTitleBefore = "Update Employee";
      this.employeelabel.btnTitleAftere = "Update Employee";
      this.modalLabel.btnTitleBefore = "Update Employee";
      this.modalLabel.btnTitleAftere = "Update Employee";
      this.form.user_id = id;
      axios.get(api.internalUsers + "/" + id).then(res => {
        this.form.id = res.data.id;
        this.form.first_name = res.data.first_name;
        this.form.last_name = res.data.last_name;
        this.form.email = res.data.email;
        this.form.phone = res.data.phone;
        this.form.role_id = 2;
        this.form.state_id = res.data.state_id;
        this.form.city_id = res.data.city_id;
        this.form.job_title = res.data.job_title;
        this.form.marketing_title = res.data.marketing_title;
        this.form.sector = res.data.sector;
        this.form.password = res.data.password;
        this.form.password_confirmation = res.data.password_confirmation;
        this.form.status = "activated";
      });
    },
    deleteUser(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete  Employee";
      this.labelModal.btnTitleBefor = "Delete Employee";
      this.labelModal.btnTitleAfter = "Delete Employee";
      this.showDeleteModal = true;
      this.deleteId = id;
    },
    addEmployee: function(e) {
      this.modalHeaderDescription = "Add Employee";
      this.showModal = true;
      this.employeelabel.btnTitleBefore = "Add Employee";
      this.employeelabel.btnTitleAftere = "Add Employee";
      this.modalLabel.btnTitleBefore = "Add Employee";
      this.modalLabel.btnTitleAftere = "Add Employee";
    },
    cancelModal() {
      this.showModal = false;
      this.showDeleteModal = false;
      this.clearForm();
    },
    setErrors(errors) {
      this.error.first_name = errors.first_name ? errors.first_name[0] : null;
      this.error.last_name = errors.last_name ? errors.last_name[0] : null;
      this.error.email = errors.email ? errors.email[0] : null;
      this.error.phone = errors.phone ? errors.phone[0] : null;
      this.error.password = errors.password ? errors.password[0] : null;
      this.error.password_confirmation = errors.password_confirmation
        ? errors.password_confirmation[0]
        : null;
    },
    clearErrors() {
      this.error.first_name = null;
      this.error.last_name = null;
      this.error.email = null;
      this.error.phone = null;
      this.error.password = null;
      this.error.password_confirmation = null;
    },
    clearForm() {
      this.form.id = "";
      this.form.first_name = "";
      this.form.last_name = "";
      this.form.email = "";
      this.form.phone = "";
      this.form.role_id = "";
      this.form.password = "";
      this.form.password_confirmation = "";
      this.form.state_id = "";
      this.form.city_id = "";
      this.form.job_title = "";
      this.form.marketing_title = "";
      this.form.sector = "";
      this.form.status = "activated";

      this.error.first_name = "";
      this.error.last_name = "";
      this.error.email = "";
      this.error.phone = "";
      this.error.role_id = "";
      this.error.password = "";
      this.error.password_confirmation = "";
      this.error.state_id = "";
      this.error.city_id = "";
      this.error.job_title = "";
      this.error.marketing_title = "";
      this.error.sector = "";
    }
  },
  created() {
    this.getEmployees();
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
  /* background-color: white; */
  border-radius: 5px;
}
</style>
