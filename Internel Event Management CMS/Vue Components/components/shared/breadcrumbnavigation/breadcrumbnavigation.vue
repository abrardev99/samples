<template>
  <section class="topbar">
    <div class="container">
      <div v-show="editEvent" class="topbar-left">
        <p class="breadcrumbs">
          <span class="breadcrumb">Events</span>
          <span class="breadcrumb-divider">></span>
          <span class="breadcrumb">{{ eventTitle }}</span>
        </p>
        <h1 class="headline">{{ eventTitle }}</h1>
      </div>
      <div class="topbar-left">
        <h1 v-show="event" class="headline">Events</h1>
        <h1 v-show="employee" class="headline">Employees</h1>
        <h1 v-show="course" class="headline">Course</h1>
        <h1 v-show="registrants" class="headline">Registrants</h1>
        <h1 v-show="inviteUser" class="headline">Invite User</h1>
      </div>
      <div class="topbar-right">
        <slot></slot>
      </div>
    </div>
  </section>
</template>

<script>
import { siteUrl } from "./../../../config";

export default {
  props: [
    "event",
    "editEvent",
    "employee",
    "eventTitle",
    "course",
    "registrants",
    "inviteUser"
  ],
  data() {
    return {
      siteUrl: siteUrl
    };
  },
  components: {},
  methods: {}
};
</script>
