<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2">
        <label class="form-block">
          <span>Name</span>
          <input
            type="text"
            class="form-control"
            :class="{'is-invalid' : error.name}"
            id="first_name"
            v-model="form.name"
            autocomplete="off"
            :disabled="loading"
            @keyup="search"
          >
          <ul class="prefix-options" v-if="users.length > 0">
            <li
              v-for="(user,key) in users"
              v-if="user.role_id === 3"
              @click="seletcedUser(user)"
            >{{ user.first_name }} {{ user.last_name }}</li>
          </ul>
          <ul class="prefix-options" v-if="noUsreExist">
            <li>No Result Found</li>
          </ul>
          <div class="invalid-feedback" v-show="error.name">{{ error.name }}</div>
        </label>
      </div>
      <div class="col-2">
        <label class="form-block">
          <span>Email</span>
          <input
            type="text"
            class="form-control"
            :class="{'is-invalid' : error.email}"
            id="last_name"
            v-model="form.email"
            autocomplete="off"
            :disabled="loading"
          >
          <div class="invalid-feedback" v-show="error.email">{{ error.email }}</div>
        </label>
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
export default {
  props: ["view", "form", "error", "loading"],
  data() {
    return {
      siteUrl: siteUrl,
      api: api,
      noUsreExist: false,
      users: ""
    };
  },
  methods: {
    search() {
      let search = { search_value: this.form.name };
      axios.post(api.employeeExternalSearch, search).then(res => {
        if (res.data.length > 0) {
          this.noUsreExist = false;
          this.users = res.data;
        } else {
          this.noUsreExist = true;
          this.users = [];
        }
      });
    },
    seletcedUser(user) {
      this.form.user_id = user.id;
      this.noUsreExist = false;
      this.users = [];
      this.form.name = user.first_name + " " + user.last_name;
      this.form.email = user.email;
    }
  },
  mounted() {},
  created() {}
};
</script>
