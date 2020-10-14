<template>
  <div class="login-page">
    <header class="header">
      <nav class="main-navigation">
        <div class="container">
          <div class="logo login-logo">
            <a href="/">
              <img :src="siteUrl + '/images/logo.svg'" />
            </a>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <login-form @loginSuccess="loginSuccess"></login-form>
      <div class="pattern-img-top">
        <img :src="siteUrl + '/images/login-pattern-top.png'" />
      </div>
      <div class="pattern-img-bottom">
        <img :src="siteUrl + '/images/login-pattern-bottom.png'" />
      </div>
      <div class="bg-img"></div>
    </main>
  </div>
</template>
<script>
import jwtToken from "../../helpers/jwt-token";
import { siteUrl } from "./../../config";
import { mapActions } from "vuex";
import Form from "./../login/Form.vue";
import { api } from "../../config";
export default {
  components: {
    "login-form": Form
  },
  data() {
    return {
      siteUrl: siteUrl,
      event: {
        city: "",
        state: "",
        start_time: "",
        end_time: ""
      }
    };
  },
  methods: {
    ...mapActions(["setAuthUser", "setEventsData"]),
    loginSuccess(data) {
      jwtToken.setToken(data.token);
      this.setAuthUser(data.user);
      this.stateList();
      this.cityList();
      this.starttimeInterval();
      this.endtimeInterval();
      let roles = "";
      let u_id = data.user.id;
      switch (data.user.role_id) {
        case 1:
          roles = "admin";
          this.$router.push({ name: "dashboard" });
          break;
        case 2:
          roles = "internal_user";
          this.$router.push({ name: "eventInvitation", params: { id : u_id }});
          break;
        case 3:
          roles = "external_user";
          this.$router.push({ name: "eventInvitation", params: { id : u_id }});
          break;
      }
      localStorage.setItem("role", roles);
      this.$user.set(Object.assign(data.user, { role: roles }));
    },
    stateList() {
      axios.post(api.states).then(res => {
        this.event.state = res.data;
        this.setEventsData(this.event);
      });
    },
    cityList() {
      axios.post(api.cities).then(res => {
        this.event.city = res.data;
        this.setEventsData(this.event);
      });
    },
    starttimeInterval: function() {
      window.axios.get(api.startTimeInterval).then(response => {
        this.event.start_time = response.data;
        this.setEventsData(this.event);
      });
    },
    endtimeInterval: function() {
      window.axios.get(api.endTimeInterval).then(response => {
        this.event.end_time = response.data;
        this.setEventsData(this.event);
      });
    }
  }
};
</script>