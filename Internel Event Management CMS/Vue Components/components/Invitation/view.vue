<template>
  <div>
    <nav-breadcrumb :inviteUser="title"></nav-breadcrumb>
    <main class="main">
      <div class="container">
        <span class="card-search employee-search">
          <div class="search-field">
            <label for="employee-search">
              <span hidden>Search</span>
              <img :src="siteUrl + '/images/search.svg'" />
            </label>
            <input
              type="text"
              v-model="employeeSearch"
              placeholder="Search"
              @keyup="search"
              id="employee-search"
            />
          </div>
          <ul class="user-dropdown prefix-options" v-if="isActive">
            <li v-for="(usersResult,key) in usersResults">
              <span>{{ usersResult.first_name }} {{ usersResult.last_name }}</span>
              <span @click="selectUser(usersResult)" class="invite-btn">Invite User</span>
            </li>
          </ul>
          <ul class="user-dropdown prefix-options" v-if="notFound">
            <li>
              <span @click="addEmployee($event)" class="new-invite-btn">Invite New User</span>
            </li>
          </ul>
        </span>
      </div>
      <section class="selection" data-view="list">
        <div class="container">
          <article v-for="user in usersData" class="card-item admin-card-item employee-card">
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ user.first_name }} {{ user.last_name }}</h2>
              <h3 class="card-item-subheadline">{{ user.email }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <span>{{ user.inviation_type }}</span>
              </p>
              <p>
                <span v-if="user.user_id">Existing User</span>
                <span v-else>New User</span>
              </p>
            </div>
          </article>
        </div>
      </section>
      <section class="selection" data-view="list">
        <div class="container">
          <article v-for="user in users" class="card-item admin-card-item employee-card">
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ user.first_name }} {{ user.last_name }}</h2>
              <h3 class="card-item-subheadline">{{ user.email }}</h3>
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
                <img :src="siteUrl + '/images/green-circle.svg'" />
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
            <img :src="siteUrl + '/images/plus-gray.svg'" />
            <p>Invite New User</p>
          </article>
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
      <add-form :form="form" :v="$v"></add-form>
    </modal>
    <custom-loader></custom-loader>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import Add from "./../Invitation/add.vue";
import Button from "./../shared/button/button.vue";
import Loader from "./../shared/loader/loader.vue";
import moment from "moment";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
  components: {
    modal: Modal,
    "nav-breadcrumb": breadCrumbNavigation,
    "add-form": Add,
    btn: Button,
    "custom-loader": Loader
  },
  data() {
    return {
      parPage: 10,
      getPageCount: 1,
      usersResults: "",
      usersData: "",
      isActive: false,
      notFound: false,
      labelModal: [],
      showDeleteModal: false,
      loading: false,
      moment: moment,
      userData: [],
      siteUrl: siteUrl,
      title: true,
      showModal: false,
      employeeSearch: "",
      users: [],
      modalHeaderTitle: "",
      modalHeaderDescription: "",
      modalHeadercontent: "Employee Info",
      modalLabel: {
        btnTitleBefore: "Invite New User",
        btnTitleAfter: "Invite New User"
      },
      employeelabel: {
        btnTitleBefore: "Invite New User",
        btnTitleAfter: "Invite New User"
      },
      form: {
        first_name: "",
        last_name: "",
        email: "",
        employee: true
      },
      error: {}
    };
  },
  validations: {
    form: {
      first_name: { required },
      last_name: { required },
      email: { required, email }
    }
  },
  methods: {
    clickCallback: function(pageNum) {
      this.currentPage = Number(pageNum);
      this.search();
    },
    search() {
      let search = {
        search: this.employeeSearch
      };
      if (this.employeeSearch) {
        axios.post(api.userInviteSearch, search).then(res => {
          this.usersResults = res.data;
          if (res.data.length > 0) {
            this.isActive = true;
          } else {
            this.isActive = false;
          }

          if (res.data.length === 0) {
            this.notFound = true;
          } else {
            this.notFound = false;
          }
        });
      }
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
      this.modalHeaderDescription = "Invite New User";
      this.showModal = true;
      this.employeelabel.btnTitleBefore = "Invite New User";
      this.employeelabel.btnTitleAftere = "Invite New User";
      this.modalLabel.btnTitleBefore = "Invite New User";
      this.modalLabel.btnTitleAftere = "Invite New User";
    },
    cancelModal() {
      this.showModal = false;
      this.showDeleteModal = false;
      this.form.first_name = "";
      this.form.last_name = "";
      this.form.email = "";
      this.form.employee = "";
    },
    selectUser(user) {
      this.$eventHub.$emit("show-modal", "general-modal");
      this.isActive = false;
      this.notFound = false;
      this.employeeSearch = user.first_name + " " + user.last_name;

      let data = {
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        user_id: user.id,
        status: "pending"
      };

      if (user.role_id === 2) {
        data.user_type = "internal_user";
      } else if (user.role_id === 3) {
        data.user_type = "external_user";
      }

      let routeName = this.$route.name;
      if (routeName === "inviteEvent") {
        data.event_id = this.$route.params.id;
        data.inviation_type = "events";
      } else {
        data.course_detail_id = this.$route.params.id;
        data.inviation_type = "courses";
      }

      axios.post(api.storeInviteUser, data).then(res => {
        if (res.data.error) {
          this.$eventHub.$emit("hide-modal", "general-modal");
          this.$noty.warning("User already Invited!");
          return;
        }
        this.usersResults = res.data;
        this.$eventHub.$emit("hide-modal", "general-modal");
        this.$noty.success("User Invited Successfully!");
        this.getUsers();
        this.cancelModal();
      });
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    register() {
      this.$v.form.$touch();
      if (this.$v.form.$error) return;

      this.$eventHub.$emit("show-modal", "general-modal");

      let data = {
        first_name: this.form.first_name,
        last_name: this.form.last_name,
        email: this.form.email,
        user_id: null,
        status: "pending"
      };

      if (this.form.employee) {
        data.user_type = "internal_user";
      } else {
        data.user_type = "external_user";
      }

      let routeName = this.$route.name;
      if (routeName === "inviteEvent") {
        data.event_id = this.$route.params.id;
        data.inviation_type = "events";
      } else {
        data.course_detail_id = this.$route.params.id;
        data.inviation_type = "courses";
      }
      this.cancelModal();
      axios.post(api.storeInviteUser, data).then(res => {
        this.usersResults = res.data;
        this.$eventHub.$emit("hide-modal", "general-modal");
        this.$noty.success("User Invited Successfully!");
        this.getUsers();
      });
    },
    getUsers() {
      let data = { id: this.$route.params.id };
      let url = "";

      let routeName = this.$route.name;
      if (routeName === "inviteEvent") {
        url = api.eventInviteUser;
      } else {
        url = api.courseInviteUser;
      }

      axios.post(url, data).then(res => {
        this.usersData = res.data;
      });
    }
  },
  created() {
    this.getUsers();
  }
};
</script>
<style>
.new-invite-btn {
  background-color: #4caf50;
  color: white;
  padding: 3px 5px;
  font-size: 11px;
  margin: 20px 0px;
  cursor: pointer;
}

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
ul.user-dropdown.prefix-options {
  overflow: hidden;
  border-radius: 50px;
}
ul.user-dropdown.prefix-options li {
  margin-left: 15px;
}
.invite-btn {
  background-color: #4caf50;
  color: white;
  padding: 1px 4px;
  font-size: 11px;
  margin: 5px 45px;
  cursor: pointer;
  display: block;
  float: right;
}
</style>