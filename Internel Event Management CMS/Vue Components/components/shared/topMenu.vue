<template>
  <header>
    <nav v-if="isLoggedIn" class="main-navigation">
      <div class="container">
        <div class="logo">
          <a href="/">
            <img :src="siteUrl + '/images/nav-logo.svg'" />
          </a>
        </div>
        <ul v-if="$user.get().role === 'admin'" class="menu">
          <router-link
            @click.native="toggle('dashboard')"
            :to="{name: 'dashboard'}"
            tag="li"
            class="menu-item"
          >
            <a
              href="/"
              :class="{ 'active-link' : isActive == 'home' }"
              class="menu-link dashboard-link"
            >
              <img :src="siteUrl + '/images/home.svg'" />
              <span>Dashboard</span>
            </a>
          </router-link>
          <router-link
            @click.native="toggle('event')"
            v-show="isLoggedIn"
            :to="{name: 'event'}"
            tag="li"
            class="menu-item"
          >
            <a
              href="/events"
              :class="{ 'active-link' : isActive == 'event' }"
              class="menu-link events-link"
            >
              <img :src="siteUrl + '/images/events.svg'" />
              <span>Events</span>
            </a>
          </router-link>
          <router-link
            @click.native="toggle('courses')"
            v-show="isLoggedIn"
            :to="{name: 'courses'}"
            tag="li"
            class="menu-item"
          >
            <a
              href="/courses"
              :class="{ 'active-link' : isActive == 'course' }"
              class="menu-link admin-link"
            >
              <img :src="siteUrl + '/images/reports.svg'" />
              <span>Courses</span>
            </a>
          </router-link>
          <router-link
            @click.native="toggle('employee')"
            v-show="isLoggedIn"
            :to="{name: 'employee'}"
            tag="li"
            class="menu-item"
          >
            <a
              href="/employee"
              :class="{ 'active-link' : isActive == 'users' }"
              class="menu-link admin-link"
            >
              <img :src="siteUrl + '/images/settings.svg'" />
              <span>Employees</span>
            </a>
          </router-link>
        </ul>
        <ul
          v-if="$user.get().role === 'internal_user' || $user.get().role === 'external_user'"
          class="menu"
        >
          <router-link
            @click.native="toggle('eventInvitation')"
            :to="{ name: 'eventInvitation' , params: { id : user_id  }}"
            tag="li"
            class="menu-item"
          >
            <a
              href="/"
              :class="{ 'active-link' : isActive == 'eventInvitation' }"
              class="menu-link dashboard-link"
            >
              <img :src="siteUrl + '/images/home.svg'" />
              <span>Events</span>
            </a>
          </router-link>

          <router-link
            @click.native="toggle('courseInvitation')"
            :to="{ name: 'courseInvitation' , params: { id: user_id  } }"
            tag="li"
            class="menu-item"
          >
            <a
              href="/"
              :class="{ 'active-link' : isActive == 'courseInvitation' }"
              class="menu-link dashboard-link"
            >
              <img :src="siteUrl + '/images/reports.svg'" />
              <span>Courses</span>
            </a>
          </router-link>
        </ul>
        <form v-if="$user.get().role === 'admin'" class="search">
          <div class="search-field">
            <img :src="siteUrl + '/images/search.svg'" />
            <label for="search" hidden>Search</label>
            <input type="text" placeholder="Search" id="search" />
          </div>
        </form>
        <div class="user-actions">
          <div class="dropdown">
            <div class="avatar-img">
              <img :src="siteUrl + '/images/nav-profile.svg'" />
            </div>
            <div v-show="isLoggedIn" class="dropdown-content">
              <a href="#" @click.prevent="logout">Logout</a>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <slot></slot>
  </header>
</template>
<script>
import { siteUrl } from "./../../config";
import { siteName } from "./../../config";
import { mapGetters } from "vuex";
import jwtToken from "../../helpers/jwt-token";
export default {
  data() {
    return {
      siteName: siteName,
      siteUrl: siteUrl,
      isActive: "home",
      user_id : null
    };
  },
  computed: mapGetters(["isLoggedIn", "userData"]),
  methods: {
    logout() {
      jwtToken.removeToken();
      this.$store.dispatch("unsetAuthUser").then(() => {
        localStorage.removeItem("role");
        this.$user.set({ role: "guest" });
        this.$router.push({ name: "login" });
        localStorage.getItem("role");
      });
    },
    toggle(value) {
      this.isActive = value;
    }
  },
  mounted() {
        this.user_id = this.userData.id;
  }
};
</script>
