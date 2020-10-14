<template>
  <div>
    <nav-breadcrumb v-if="!event" :eventTitle="eventTitle" :editEvent="true"></nav-breadcrumb>
    <nav-breadcrumb v-else :course="true" :eventTitle="eventTitle"></nav-breadcrumb>
    <event-sub-nav v-if="!event" @cards="cards" @list="list"></event-sub-nav>
    <course-sub-nav v-else></course-sub-nav>
    <section class="selection" data-view="cards">
      <div class="container">
        <div class="row">
          <div class="col-1">
            <article id="seating-chart" class="empty">
              <button @click="addSeat" class="btn-add">
                <svg
                  width="28px"
                  height="28px"
                  viewBox="0 0 28 28"
                  version="1.1"
                  xmlns="http://www.w3.org/2000/svg"
                  xmlns:xlink="http://www.w3.org/1999/xlink"
                >
                  <g
                    id="Page-1"
                    stroke="none"
                    stroke-width="1"
                    fill="none"
                    fill-rule="evenodd"
                    stroke-linecap="square"
                  >
                    <g
                      class="add-icon svg-icon"
                      transform="translate(-630.000000, -547.000000)"
                      stroke="#fff"
                      stroke-width="3.33473684"
                    >
                      <g id="Group-6" transform="translate(30.000000, 517.000000)">
                        <g id="Group-8" transform="translate(602.000000, 32.000000)">
                          <path d="M12,-7.02216063e-15 L12,24" id="Line" />
                          <path
                            d="M12,0.48 L12,23.52"
                            id="Line-Copy"
                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                          />
                        </g>
                      </g>
                    </g>
                  </g>
                </svg>
              </button>
              <div id="tables"></div>
            </article>
          </div>
          <div class="col-3">
            <article id="selected-table-info" class="empty">
              <header>
                <h3>
                  Table #
                  <span></span>
                </h3>
              </header>
              <div class="row">
                <p>Number of Seats</p>
                <div class="row">
                  <button class="js-changeNumberOfSeats" @click="changeSeat(-1)" data-number="-1">
                    <img :src="siteUrl + '/images/arrow_down.svg'" />
                  </button>
                  <input type="text" class="js-numberOfSeats" />
                  <button class="js-changeNumberOfSeats" @click="changeSeat(1)" data-number="1">
                    <img :src="siteUrl + '/images/arrow_top.svg'" />
                  </button>
                </div>
              </div>
              <div class="row js-tableRegistrants">
                <div
                  :class="['table-input table-'+table.id , { 'show' : table.isActive }]"
                  v-for="(table, table_index) in tables"
                >
                  <div :id="'myModal-'+table_index" :class="'modal'">
                    <div class="modal-content">
                      <span class="close" @click="modalClose('myModal-'+table_index)">&times;</span>
                      <seating-chart :seating="table.totalSeats"></seating-chart>
                    </div>
                  </div>
                  <div
                    v-for="(totalSeat, index) in table.totalSeats"
                    v-bind:key="index"
                    v-bind:class="['course-flex1', 'input-'+index]"
                  >
                    <div class="row v-center course">
                      <span class="manage-text">Seat Number {{index + 1}}</span>
                      <input
                        type="text"
                        class="js-tableRegistrantEntry"
                        placeholder="Start typing name"
                        v-on:keyup="getUsers($event.target.value,index,table_index)"
                        v-model="totalSeat.name"
                      />
                      <ul
                        v-if="!toogleShow"
                        class="users-options"
                        :class="'table-'+table_index+'-toogle-option-'+index+ ' hide'"
                      >
                        <li
                          v-for="(usersResult,key) in usersResults"
                          @click="selectUser(usersResult.users[0], index ,table_index)"
                        >{{ usersResult.users[0].first_name }} {{ usersResult.users[0].last_name }}</li>
                      </ul>
                      <ul
                        :class="'table-'+table_index+'-toogle-option-'+index+ ' hide'"
                        class="users-options"
                        v-else
                      >
                        <li>No Record Found</li>
                      </ul>
                    </div>

                    <div class="mange-sticky">
                      <div class="stick">
                        <button
                          @click="deleteSeat(totalSeat.seat_id)"
                          class="btn-secondary size"
                        >Delete Seat</button>
                      </div>
                    </div>
                  </div>
                  <div class="mange-sticki">
                    <div class="stick">
                      <span
                        class="btn-secondary m-20"
                        @click="deleteTable(table.tableable_id)"
                      >Delete Table</span>
                      <button @click="submitSeatData()" class="btn-primary size">Update</button>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
    <div class="modal add-edit-modal event-modal" data-modal="courses" data-action="add">
      <div class="modal-content">
        <header class="modal-header">
          <div class="header-info">
            <p class="current-action">Edit Event</p>
            <h3 class="headline">Spring 2020</h3>
          </div>
          <div class="modal-header-ctas">
            <button
              class="btn-secondary modal-action"
              data-action="cancel"
              data-modal="courses"
            >Cancel</button>
            <button class="btn-primary modal-action">Update</button>
          </div>
        </header>
        <section class="modal-add-actions">
          <article class="modal-action current-action" data-add="course"></article>
          <article class="modal-action" data-add="food"></article>
          <article class="modal-action" data-add="hotel"></article>
          <article class="modal-action" data-add="giveaway"></article>
        </section>
      </div>
    </div>
    <modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="''"
      :label="labelModal"
      @cancel="cancelModal"
      @update="addModal"
      :showModal="showDeleteModal"
      :deleteModal="false"
    >
      <section class="modal-card-data">
        <div class="row">
          <span>Are you sure you want to delete ?</span>
        </div>
        <div class="delete-content-right">
          <add-button
            :loading="loading"
            @click="cancelModal()"
            :labelBefore="'Cancel'"
            :labelAfter="'Cancel'"
            :type="'btn-secondary'"
          ></add-button>
          <add-button
            :loading="loading"
            :labelBefore="'Delete'"
            :labelAfter="'Delete'"
            :type="'btn-primary'"
            @click="confirmDelete()"
          ></add-button>
        </div>
      </section>
    </modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import EditButton from "./../shared/button/button.vue";
import AddButton from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import CourseSubNav from "./../course/navbar.vue";
import SeatingChart from "./../seating/chart.vue";
import Modal from "./../shared/modal/modal.vue";
export default {
  components: {
    modal: Modal,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    btn: EditButton,
    "course-sub-nav": CourseSubNav,
    "seating-chart": SeatingChart,
    "add-button": AddButton
  },
  data() {
    return {
      courses: [],
      courseDetail: [],
      noResultExist: false,
      toogleShow: false,
      showDeleteModal: false,
      tablesData: "",
      eventTitle: "",
      usersResults: "",
      isTable: false,
      selectedUser: [],
      NumberOftables: [],
      tables: [],
      deleteId: "",
      selUser: {
        name: []
      },
      labelModal: {
        btnTitleBefore: "",
        btnTitleAftere: ""
      },
      totalSeats: [],
      event: false,
      siteUrl: siteUrl,
      showModal: false,
      seats: "",
      modalHeaderTitle: "Edit Event",
      modalHeaderDescription: "Spring 2020",
      modalHeadercontent: "Add Course",
      loading: false,
      siteUrl: siteUrl,
      seatData: "",
      label: {
        btnTitleBefor: "Edit Event",
        btnTitleAfter: "Edit Event"
      },
      view: "cards"
    };
  },
  methods: {
    addModal() {},
    deleteSeat(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Seat";
      this.labelModal.btnTitleBefor = "Delete Seat";
      this.labelModal.btnTitleAfter = "Delete Seat";
      this.showDeleteModal = true;
      this.deleteId = id;
      this.isTable = false;
    },
    deleteTable(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Table";
      this.labelModal.btnTitleBefor = "Delete Table";
      this.labelModal.btnTitleAfter = "Delete Table";
      this.showDeleteModal = true;
      this.deleteId = id;
      this.isTable = true;
    },
    cancelModal() {
      this.showDeleteModal = false;
    },
    confirmDelete() {
      let apiUrl = "";
      if (this.isTable) {
        apiUrl = api.deleteTables;
      } else {
        apiUrl = api.deleteSeat;
      }
      axios.delete(apiUrl + this.deleteId).then(res => {
        this.$noty.success("Record Delete Successfully!");
        document.getElementById("tables").innerHTML = "";
        this.tables = [];
        this.tablesData = [];
        this.getSeating(this.$route.params.id);
      });
      this.cancelModal();
    },
    cards() {
      this.view = "cards";
    },
    list() {
      this.view = "list";
    },
    selectTable(table) {
      let tableInfo = document.getElementById("selected-table-info"),
        tableNumber = tableInfo.querySelector("header span"),
        numberOfSeats = tableInfo.querySelector(".js-numberOfSeats");

      document
        .getElementById("tables")
        .querySelectorAll(".table")
        .forEach(table => {
          table.classList.remove("selected-table");
        });
      table[0].classList.add("selected-table");

      tableNumber.innerHTML = table[0].getAttribute("data-table");
      numberOfSeats.value = table[0].getAttribute("data-total-seats");
    },
    chooseTable(table, index) {
      let tableInfo = document.getElementById("selected-table-info"),
        tableNumber = tableInfo.querySelector("header span"),
        numberOfSeats = tableInfo.querySelector(".js-numberOfSeats");
      document
        .getElementById("tables")
        .querySelectorAll(".table")
        .forEach(table => {
          table.classList.remove("selected-table");
        });
      table[0].classList.add("table-content-" + index);
      tableNumber.innerHTML = table[0].getAttribute("data-table");
      numberOfSeats.value = table[0].getAttribute("data-total-seats");
    },
    showSeat(tableData) {
      var seatingChart = document.getElementById("seating-chart");
      var tables = document.getElementById("tables");
      if (tableData.length > 0) {
        for (var k = 0; k < tableData.length; k++) {
          var numberOfExistingTables = k;
          var table = document.createElement("div");
          var tableSection = document.createElement("div");
          var tableInfo = document.createElement("p");
          var tableNumber = document.createElement("p");
          var tableSeats = document.createElement("p");
          var availableSeats = document.createElement("span");
          var totalSeats = document.createElement("span");
          table.classList.add("table");
          tableSection.classList.add("table-section");
          tableInfo.classList.add("table-info");
          tableInfo.classList.add("table-seat-" + k);
          tableInfo.setAttribute("info-seat", k);
          tableNumber.innerHTML = "#" + (numberOfExistingTables + 1);
          availableSeats.classList.add("open-seats");
          availableSeats.innerHTML = 0;
          if (tableData[k].seats.length > 0) {
            var availableSeatsCounter = 0;
            for (var m = 0; m < tableData[k].seats.length; m++) {
              if (tableData[k].seats[m].user_id === null) {
                availableSeatsCounter++;
              }
            }
            availableSeats.innerHTML = availableSeatsCounter;
            table.setAttribute("data-open-seats", availableSeatsCounter);
          }
          totalSeats.classList.add("total-seats");
          var total_seats = "";
          if (tableData[k].seats) {
            total_seats = tableData[k].seats.length;
            totalSeats.innerHTML = tableData[k].seats.length;
          } else {
            total_seats = 0;
            totalSeats.innerHTML = 0;
          }
          tableSeats.appendChild(availableSeats);
          tableSeats.innerHTML += "/";
          tableSeats.appendChild(totalSeats);
          tableSeats.innerHTML += " seats available";
          tableInfo.appendChild(tableNumber);
          tableInfo.appendChild(tableSeats);
          table.appendChild(tableSection);
          table.appendChild(tableInfo);
          var currtableNumber = numberOfExistingTables + 1;
          if (total_seats > 0) {
            for (var j = 0; j < total_seats; j++) {
              this.NumberOftables.push({ seats: j });
            }
          }
          table.setAttribute("data-table", numberOfExistingTables + 1);
          table.setAttribute("data-total-seats", total_seats);
          tables.appendChild(table);
          seatingChart.classList.remove("empty");
          $(seatingChart).animate({
            scrollTop: $(table).offset().top - $(seatingChart).scrollTop()
          });
          var elemRemoveClass = document.getElementsByClassName("table-input");
          if (elemRemoveClass.length > 0) {
            for (var i = 0; i < elemRemoveClass.length; i++) {
              elemRemoveClass[i].classList.remove("show");
            }
          }
          this.tables.push({
            id: currtableNumber,
            totalSeats: [],
            isActive: false,
            tableable_id: tableData[k].id
          });

          $(".table-info").click(function() {
            var table_id = $(this).attr("info-seat");
            var modal = document.getElementById("myModal-" + table_id);
            modal.style.display = "block";
            var btn = document.getElementsByClassName("selected-table");
            window.onclick = function(event) {
              if (event.target == modal) {
                modal.style.display = "none";
              }
            };
          });

          $(table).click(function() {
            let activeTable = $(this);
            let tableInfo = document.getElementById("selected-table-info"),
              tableNumber = tableInfo.querySelector("header span"),
              numberOfSeats = tableInfo.querySelector(".js-numberOfSeats");
            document
              .getElementById("tables")
              .querySelectorAll(".table")
              .forEach(activeTable => {
                activeTable.classList.remove("selected-table");
              });
            activeTable[0].classList.add("selected-table");
            tableNumber.innerHTML = activeTable[0].getAttribute("data-table");
            numberOfSeats.value = activeTable[0].getAttribute(
              "data-total-seats"
            );

            var elemRemoveClass = document.getElementsByClassName(
              "table-input"
            );
            if (elemRemoveClass.length > 0) {
              for (var i = 0; i < elemRemoveClass.length; i++) {
                elemRemoveClass[i].classList.remove("show");
              }
            }
            var elemAddClass = document.getElementsByClassName(
              "table-" + activeTable[0].getAttribute("data-table")
            );
            elemAddClass[0].classList.add("show");
          });
          $("#selected-table-info").removeClass("empty");
          this.seatData = "";
          this.chooseTable($(table), k);
          this.showSeats(k, "", tableData);
          $(".table-section").click(function(e) {
            let elemRemoveClass = document.getElementsByClassName(
              "course-flex1"
            );
            if (elemRemoveClass.length > 0) {
              for (let n = 0; n < elemRemoveClass.length; n++) {
                elemRemoveClass[n].classList.remove("show");
              }
            }
            let seat_num = $(this).attr("seat-id");
            let table_id = $(this).attr("table-seat-id");
            if (e.offsetX > e.target.offsetLeft) {
              $(".table-" + table_id + " > .input-" + seat_num).addClass(
                "show"
              );
            } else {
              seat_num = parseInt(seat_num) - 1;
              $(".table-" + table_id + " > .input-" + seat_num).addClass(
                "show"
              );
            }
          });
          var forwadStep = k + 1;
          if (forwadStep === tableData.length) {
            this.tables[k].isActive = true;
            var element = document.getElementsByClassName("table-content-" + k);
            element[0].classList.add("selected-table");
          }
        }
      }
    },
    showSeats(k, button = false, tableData) {
      var selectedTable = document.querySelector(".table-content-" + k);
      var currentNumberOfSeats = selectedTable.getAttribute("data-total-seats");
      var arrayOfRegistrants = [];
      var totalSeats = "";
      var tableNumber = selectedTable.getAttribute("data-table");
      var tableNum = parseInt(tableNumber) - 1;
      var amount = 1;
      if (amount < 0) {
        if (currentNumberOfSeats <= 0) {
          var someVariable = "";
          return someVariable ? true : false;
        } else {
          const indexToRemove = currentNumberOfSeats - 1;
          const index = this.tables[tableNum].totalSeats.indexOf(indexToRemove);
          this.tables[tableNum].totalSeats.splice(index, 1);
          selectedTable.setAttribute("data-total-seats", indexToRemove);
          document.querySelector(".js-numberOfSeats").value = indexToRemove;
          this.updateTable(selectedTable);
          return false;
        }
      }
      button
        ? (totalSeats = parseInt(currentNumberOfSeats) + parseInt(amount))
        : (totalSeats = parseInt(amount));
      this.NumberOftables[tableNum].seats = totalSeats;
      var sum;
      if (tableData[k].seats.length > 0) {
        sum = tableData[k].seats.length;
        if (sum > 0) {
          for (var l = 0; l < sum; l++) {
            this.tables[tableNum].totalSeats.push({
              seat_id: tableData[k].seats[l].id,
              name: tableData[k].seats[l].name,
              id: tableData[k].seats[l].user_id,
              index_ittr: l + 1
            });
          }
        }

        var elemRemoveClass = document.getElementsByClassName("table-input");
        if (elemRemoveClass.length > 0) {
          for (var i = 0; i < elemRemoveClass.length; i++) {
            elemRemoveClass[i].classList.remove("show");
          }
        }
        this.updateTable(selectedTable, this.tables);
      }
    },
    addSeat() {
      let seatingChart = document.getElementById("seating-chart");
      let tables = document.getElementById("tables");
      let table = document.createElement("div");
      let tableSection = document.createElement("div");
      let tableInfo = document.createElement("p");
      let tableNumber = document.createElement("p");
      let tableSeats = document.createElement("p");
      let availableSeats = document.createElement("span");
      let totalSeats = document.createElement("span");
      let numberOfExistingTables = 0;
      table.classList.add("table");
      tableSection.classList.add("table-section");
      tableInfo.classList.add("table-info");
      tables.querySelectorAll(".table").forEach(table => {
        numberOfExistingTables++;
      });
      tableNumber.innerHTML = "#" + (numberOfExistingTables + 1);
      availableSeats.classList.add("open-seats");
      availableSeats.innerHTML = 0;
      totalSeats.classList.add("total-seats");
      totalSeats.innerHTML = 0;
      tableSeats.appendChild(availableSeats);
      tableSeats.innerHTML += "/";
      tableSeats.appendChild(totalSeats);
      tableSeats.innerHTML += " seats available";
      tableInfo.appendChild(tableNumber);
      tableInfo.appendChild(tableSeats);
      table.appendChild(tableSection);
      table.appendChild(tableInfo);
      let currtableNumber = numberOfExistingTables + 1;
      this.NumberOftables.push({ seats: 0 });
      table.setAttribute("data-table", numberOfExistingTables + 1);
      table.setAttribute("data-total-seats", 0);
      table.setAttribute("data-open-seats", 0);
      tables.appendChild(table);

      seatingChart.classList.remove("empty");
      $(seatingChart).animate({
        scrollTop: $(table).offset().top - $(seatingChart).scrollTop()
      });

      let elemRemoveClass = document.getElementsByClassName("table-input");
      if (elemRemoveClass.length > 0) {
        for (var i = 0; i < elemRemoveClass.length; i++) {
          elemRemoveClass[i].classList.remove("show");
        }
      }
      this.tables.push({
        id: currtableNumber,
        totalSeats: [],
        isActive: true
      });

      $(table).click(function() {
        let activeTable = $(this);
        let tableInfo = document.getElementById("selected-table-info"),
          tableNumber = tableInfo.querySelector("header span"),
          numberOfSeats = tableInfo.querySelector(".js-numberOfSeats");
        document
          .getElementById("tables")
          .querySelectorAll(".table")
          .forEach(activeTable => {
            activeTable.classList.remove("selected-table");
          });
        activeTable[0].classList.add("selected-table");
        tableNumber.innerHTML = activeTable[0].getAttribute("data-table");
        numberOfSeats.value = activeTable[0].getAttribute("data-total-seats");

        let elemRemoveClass = document.getElementsByClassName("table-input");
        if (elemRemoveClass.length > 0) {
          for (var i = 0; i < elemRemoveClass.length; i++) {
            elemRemoveClass[i].classList.remove("show");
          }
        }
        let elemAddClass = document.getElementsByClassName(
          "table-" + activeTable[0].getAttribute("data-table")
        );
        elemAddClass[0].classList.add("show");
        var modal = document.getElementById("myModal");
        var btn = document.getElementsByClassName("selected-table");
        window.onclick = function(event) {
          if (event.target == modal) {
            modal.style.display = "none";
          }
        };
      });
      $("#selected-table-info").removeClass("empty");
      this.selectTable($(table));
    },
    changeNumberOfSeats(amount, button = false) {
      let selectedTable = document.querySelector(".selected-table"),
        currentNumberOfSeats = selectedTable.getAttribute("data-total-seats"),
        arrayOfRegistrants = [],
        totalSeats;
      let tableNumber = selectedTable.getAttribute("data-table");
      let tableNum = parseInt(tableNumber) - 1;
      if (amount < 0) {
        if (currentNumberOfSeats <= 0) {
          let someVariable = "";
          return someVariable ? true : false;
        } else {
          const indexToRemove = currentNumberOfSeats - 1;
          const index = this.tables[tableNum].totalSeats.indexOf(indexToRemove);
          this.tables[tableNum].totalSeats.splice(index, 1);
          selectedTable.setAttribute("data-total-seats", indexToRemove);
          document.querySelector(".js-numberOfSeats").value = indexToRemove;
          this.updateTable(selectedTable, "");
          return false;
        }
      }
      button
        ? (totalSeats = parseInt(currentNumberOfSeats) + parseInt(amount))
        : (totalSeats = parseInt(amount));

      this.NumberOftables[tableNum].seats = totalSeats;
      if (this.tables.length > 0) {
        var sum = 0;
        for (var i = 0; i < this.tables.length; i++) {
          let elemGetLength = document.getElementsByClassName(
            "table-" + parseInt(i + 1)
          )[0];
          sum += parseInt(elemGetLength.getElementsByTagName("input").length);
        }
      }

      sum = sum + 1;
      if (sum > this.seats) {
        this.$noty.warning("Seat Limit Exceed.");
        return false;
      } else {
        this.tables[tableNum].totalSeats.push({
          name: null,
          id: null
        });
      }
      document.querySelector(".js-numberOfSeats").value = totalSeats;
      selectedTable.setAttribute("data-total-seats", totalSeats);
      let elemRemoveClass = document.getElementsByClassName("table-input");
      if (elemRemoveClass.length > 0) {
        for (var i = 0; i < elemRemoveClass.length; i++) {
          elemRemoveClass[i].classList.remove("show");
        }
      }
      let elemAddClass = document.getElementsByClassName(
        "table-" + parseInt(tableNumber)
      );
      elemAddClass[0].classList.add("show");
      this.updateTable(selectedTable, this.tables);

      $(".table-section").click(function(e) {
        let elemRemoveClass = document.getElementsByClassName("course-flex1");
        if (elemRemoveClass.length > 0) {
          for (let n = 0; n < elemRemoveClass.length; n++) {
            elemRemoveClass[n].classList.remove("show");
          }
        }
        let seat_num = $(this).attr("seat-id");
        let table_id = $(this).attr("table-seat-id");
        if (e.offsetX > e.target.offsetLeft) {
          $(".table-" + table_id + " > .input-" + seat_num).addClass("show");
        } else {
          seat_num = parseInt(seat_num) - 1;
          $(".table-" + table_id + " > .input-" + seat_num).addClass("show");
        }
      });

      let elemRemoveClasses = document.getElementsByClassName("course-flex1");
      if (elemRemoveClasses.length > 0) {
        for (let n = 0; n < elemRemoveClasses.length; n++) {
          elemRemoveClasses[n].classList.remove("show");
        }
      }
    },
    modalClose(modal) {
      var span = document.getElementById(modal);
      span.style.display = "none";
    },
    updateTable(table, table_data) {
      let openSeats = table.querySelector(".open-seats"),
        totalSeats = table.querySelector(".total-seats"),
        tableSection;
      openSeats.innerHTML = table.getAttribute("data-open-seats");
      totalSeats.innerHTML = table.getAttribute("data-total-seats");
      let table_id = table.getAttribute("data-table");
      table.querySelectorAll(".table-section").forEach(section => {
        section.remove();
      });

      let s_id = 1;
      let storeSeatIndex = [];
      for (
        let i = 1;
        i <= Math.ceil(table.getAttribute("data-total-seats") / 2);
        i++
      ) {
        tableSection = document.createElement("div");
        tableSection.classList.add("table-section");
        tableSection.classList.add("seat-" + s_id);
        tableSection.setAttribute("seat-id", s_id);
        tableSection.setAttribute("table-seat-id", table_id);

        table.appendChild(tableSection);
        storeSeatIndex.push(s_id);
        s_id = s_id + 2;
      }

      let arr = [];
      if (table_data.length > 0) {
        if (table_data[table_id - 1].totalSeats.length > 0) {
          for (var l = 0; l < table_data[table_id - 1].totalSeats.length; l++) {
            if (table_data[table_id - 1].totalSeats[l].id !== null) {
              arr[l] = 1;
            } else {
              arr[l] = 0;
            }
          }
        }
      }

      if (arr.length > 0) {
        let j = 0;
        for (let k = 0; k < storeSeatIndex.length; k++) {
          let sel_index = storeSeatIndex[k];
          let index = j + 1;
          let next = arr[j + 1];
          if (next === null) {
            next = 0;
          }

          let pre = arr[j];

          let constant = pre + next;
          if (constant === 2) {
            $(".seat-" + sel_index).addClass("both-booked");
          } else if (arr[j] === 1) {
            $(".seat-" + sel_index).addClass("before-booked");
          } else if (arr[j + 1] === 1) {
            $(".seat-" + sel_index).addClass("after-booked");
          }
          j = j + 2;
        }
      }

      parseInt(table.getAttribute("data-total-seats")) % 2 !== 0
        ? table.classList.add("odd-number-seats")
        : table.classList.remove("odd-number-seats");
    },
    count(array) {
      var c = 0;
      for (i in array) if (array[i] != undefined) c++;
      return c;
    },
    getSeating(eventId) {
      let url = "";
      let event = false;
      switch (this.$route.name) {
        case "eventSeating":
          url = api.editEvent;
          event = true;
          this.event = false;
          break;
        case "courseSeating":
          url = api.viewCourse;
          event = false;
          this.event = true;
          break;
      }
      axios.get(url + eventId).then(res => {
        if (event) {
          this.eventTitle = res.data.name;
          this.tablesData = res.data.tables;
          this.seats = res.data.seats;
          this.showSeat(this.tablesData);
        } else {
          this.eventTitle = res.data.course.name;
          this.tablesData = res.data.tables;
          this.seats = res.data.seats;
          this.showSeat(this.tablesData);
        }
      });
    },
    changeSeat(amount) {
      this.changeNumberOfSeats(amount, true);
    },
    submitSeatData() {
      let tableable_id = this.$route.params.id;
      let type = "";
      switch (this.$route.name) {
        case "eventSeating":
          type = "event";
          break;
        case "courseSeating":
          type = "course";
          break;
      }
      let data = {
        tableable_id: tableable_id,
        tableable_type: type,
        status: 1,
        seats: this.tables
      };
      axios.post(api.storeTable, data).then(res => {
        this.usersResults = res.data.data;
        this.$noty.success("Seats Added Successfully!");
        document.getElementById("tables").innerHTML = "";
        this.tables = [];
        this.tablesData = [];
        this.getSeating(this.$route.params.id);
      });
    },
    getUsers(value, index, table_id) {
      let search = "";
      switch (this.$route.name) {
        case "courseSeating":
          search = {
            search_value: value,
            course_detail_id: this.$route.params.id,
            page: 1,
            per_page: 50
          };
          break;
        case "eventSeating":
          search = {
            search_value: value,
            event_id: this.$route.params.id,
            page: 1,
            per_page: 50
          };
          break;
      }
      if (value) {
        axios.post(api.employeeExternalUserSearch, search).then(res => {
          if (res.data.data.length > 0) {
            this.usersResults = res.data.data;
            this.toogleShow = false;
          } else {
            this.toogleShow = true;
          }
        });

        let user_data_lenght = this.tables[table_id].totalSeats.length;
        if (user_data_lenght > 0) {
          for (var i = 0; i < user_data_lenght; i++) {
            let toogle = "table-" + table_id + "-toogle-option-" + parseInt(i);
            let elemRemoveClass = document.getElementsByClassName(toogle);
            elemRemoveClass[0].classList.remove("show");
          }
        }
        let toogle = "table-" + table_id + "-toogle-option-" + index;
        let elemRemoveClass = document.getElementsByClassName(toogle);
        elemRemoveClass[0].classList.add("show");
      }
    },
    selectUser(user, index, table_id) {
      let toogle = "table-" + table_id + "-toogle-option-" + index;
      let elemRemoveClass = document.getElementsByClassName(toogle);
      elemRemoveClass[0].classList.remove("show");
      this.tables[table_id].totalSeats[index].name =
        user.first_name + " " + user.last_name;
      this.tables[table_id].totalSeats[index].id = user.id;
    }
  },
  created() {
    this.getSeating(this.$route.params.id);
  }
};
</script>
<style>
.table-input {
  display: none;
}
.show {
  display: block !important;
}
.users-options {
  list-style-type: none;
  padding: 0px;
  margin-top: 0px;
  display: block;
  border: 1px solid;
  border: 0.5px solid #cddbe3;
  background: #e6eef1;
  width: 100%;
  padding: 0p;
}
.row.v-center.course {
  width: 100% !important;
  display: flex;
  flex-direction: column;
}

.users-options li {
  padding: 10px;
  color: #636981;
  font-size: 0.75rem;
  font-weight: 700;
  font-size: 15px;
  border-bottom: 0.5px solid #cddbe3;
}
#selected-table-info > div:nth-of-type(2) input {
  margin-bottom: unset;
}
.hide {
  display: none;
}
.show {
  display: block;
}
.js-tableRegistrants {
  margin-bottom: 40px;
}
.sticky {
  position: sticky !important;
}

.modal {
  display: none;
  position: fixed;
  z-index: 999999;
  padding-top: 100px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0, 0, 0);
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  /* background-color: #fefefe; */
  margin: auto;
  padding: 5px;
  /* border: 1px solid #888; */
  width: 80%;
}

.close {
  color: #ffffff;
  float: right;
  margin-right: 7px;
  font-size: 32px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.both-booked::before {
  background: #e6eef1;
}
.both-booked::after {
  background: #e6eef1;
}
.before-booked::before {
  background: #e6eef1;
}
.after-booked::after {
  background: #e6eef1;
}
.delete-table {
  font-size: 12px;
  color: #385898;
  display: block;
  float: right;
  padding-top: 5px;
  margin-right: 5px;
}
input.js-tableRegistrantEntry {
  border: 2px solid #e6eef1 !important;
}
.mange-sticky {
  margin-top: 35px;
}
.stick {
  display: block;
}

.course-flex1 {
  display: none;
}
.course-flex {
  display: flex;
}
.manage-text {
  font-size: 13px;
  font-weight: bold;
}
#selected-table-info > div:nth-of-type(2) {
  display: block;
  padding: 1rem 0;
  margin: 1px 27.5px;
  max-height: 32vh;
  overflow: auto;
}
.mange-sticki {
  display: flex;
  flex-direction: inherit;
  flex-direction: row;
  justify-content: space-between;
  font-size: -3px;
  justify-content: space-between;
  margin-top: 20px;
}
.m-20 {
  margin-top: 20px;
  font-size: 12px;
}
.size {
  font-size: 12px;
}
</style>

