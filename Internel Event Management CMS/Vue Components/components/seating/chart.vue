<template>
  <div class="containers-list">
    <div class="rows" id="row1">
      <div v-for="i in Math.ceil(seating.length / 3)" :class="'columns c1'">
        <a v-for="seat in seating.slice((i-1)*3, i*3)"   :class="{ 'booked' : seat.id !== null }">
          <p> Seat {{seat.index_ittr}}</p>
          <span v-if="seat.name" class="s-name">{{seat.name}}</span>
          <span v-else="" class="s-name">Available</span>
        </a>
      </div>
    </div>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
export default {
  props: ["seating"],
  components: {},
  data() {
    return {
      api: api,
      siteUrl: siteUrl
    };
  },
  methods: {}
};
</script>
<style>
.containers-list {
  margin: 0 auto;
  background: #e1e1e1;
  padding: 2em;
}

.rows {
  width: 100%;
  text-align: center;
}
.columns {
  width: 49.5%;
  display: inline-block;
}
.columns a {
  width: 25%;
  display: inline-block;
  text-align: center;
  padding: 20px 0;
  background: #ffffff;
  margin: 7px 5px;
  cursor: pointer;
}
.columns a p {
  display: block;
  text-align: center;
  transition: ease 0.35s;
  opacity: 1;
  width: 100% !important;
}
.columns a:hover p {
  opacity: 1;
}
.columns a span {
  display: block;
  text-align: center;
  transition: ease 0.35s;
  opacity: 1;
  text-transform: capitalize;
}

.booked {
  background: #f0f5fa !important;
}
</style>

