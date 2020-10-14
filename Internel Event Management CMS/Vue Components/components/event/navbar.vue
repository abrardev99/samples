<template>
  <nav class="sub-nav">
    <div class="container">
      <ul v-show="event" class="menu">
        <li class="menu-item">
          <a
            @click="active($event),toggleState('active')"
            :class="{ 'active-link' : isStateActive == 'active' }"
            class="menu-link"
          >Active</a>
        </li>
        <li class="menu-item">
          <a
            @click="inactive($event),toggleState('inactive')"
            :class="{ 'active-link' : isStateActive == 'inactive' }"
            class="menu-link"
          >Inactive</a>
        </li>
      </ul>
      <ul v-show="!event" class="menu">

        <router-link
          v-if="id"
          :to="{ name: 'eventcourse', params: { id: id }}"
          tag="li"
          class="menu-item"
        >
          <a href="/course/" :class="'menu-link active-link'">
            <span>Courses</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'eventhotel'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Hotels</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'eventFood'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Foods</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'eventGiveaways'}" tag="li" class="menu-item">
          <a href="/hotel" :class="'menu-link'">
            <span>Give Aways</span>
          </a>
        </router-link>

        <router-link v-if="id" :to="{name: 'eventSeating'}" tag="li" class="menu-item">
          <a href="/seating" :class="'menu-link'">
            <span>Seating</span>
          </a>
        </router-link>

      </ul>
      <ul v-show="changemode" class="view-selection">
        <li class="view-selection-item">
          <a
            @click="cards($event),toggle('cards')"
            class="view-selection-link"
            :class="{ 'active-link' : isActive == 'cards' }"
          >
            <img :src="siteUrl + '/images/cube.svg'">
          </a>
        </li>
        <li class="view-selection-item">
          <a
            @click="list($event),toggle('list')"
            class="view-selection-link"
            :class="{ 'active-link' : isActive == 'list' }"
          >
            <img :src="siteUrl + '/images/hamburger.svg'">
          </a>
        </li>
      </ul>
    </div>
  </nav>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";

export default {
  props: ["event", "changemode"],
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
    cards: function(e) {
      this.$emit("cards", e);
    },
    list: function(e) {
      this.$emit("list", e);
    },
    toggle(value) {
      this.isActive = value;
    },
    active: function(e) {
      this.$emit("active", e);
    },
    inactive: function(e) {
      this.$emit("inactive", e);
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
