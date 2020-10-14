<template>
  <nav class="sub-nav">
    <div class="container">
      <ul class="menu">
        <router-link v-if="id" :to="{name: 'coursehotel'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Hotels</span>
          </a>
        </router-link>
        <router-link v-if="id" :to="{name: 'courseFood'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Foods</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'courseGiveaways'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Give Aways</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'courseSeating'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Seating</span>
          </a>
        </router-link>
      </ul>
    </div>
  </nav>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";

export default {
  data() {
    return {
      courses: [],
      courseDetail: [],
      siteUrl: siteUrl,
      activeLink: "active-link",
      isStateActive: "active",
      isActive: "cards"
    };
  },
  methods: {
    toggle(value) {
      this.isActive = value;
    },
    toggleState(value) {
      this.isStateActive = value;
    }
  },
  created() {
    this.id = this.$route.params.id;
  }
};
</script>
