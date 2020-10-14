<template>
  <div>
    <nav-breadcrumb :inviteUser="title"></nav-breadcrumb>
    <main class="main">
      <nav class="sub-nav">
        <div class="container">
          <ul v-if="invitations.length > 0" class="menu">
            <li class="menu-item">
              <span class="menu-link active-link" @click="registered()">Registered</span>
            </li>
            <li class="menu-item">
              <span @click="notRegistered()" class="menu-link">Not Registered</span>
            </li>
          </ul>
          <ul class="view-selection">
            <li class="view-selection-item">
              <a
                @click="changeView('cards')"
                class="view-selection-link active-link"
                data-view="cards"
              >
                <img :src="siteUrl + '/images/cube.svg'" />
              </a>
            </li>
            <li class="view-selection-item">
              <a @click="changeView('list')" class="view-selection-link" data-view="list">
                <img :src="siteUrl + '/images/hamburger.svg'" />
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <section class="selection register show" data-view="cards">
        <div class="container">
          <article
            v-for="invitation in invitations"
            v-if="invitation.status === 'approved'"
            class="card-item events-card-item"
            data-card="event"
          >
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ invitation.event_name }}</h2>
              <h3
                class="card-item-subheadline"
              >{{ invitation.city_name }} , {{ invitation.state_name }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <span>
                  <b>{{ invitation.total_courses }}</b> courses offered
                </span>
              </p>
            </div>
            <div class="card-item-date-info">
              <div class="row">
                <div class="start">
                  <p class="card-item-date-label">Start Date</p>
                  <p class="card-item-date">{{ moment(invitation.start_date).format("YYYY/MM/DD") }}</p>
                </div>
                <div class="end">
                  <p class="card-item-date-label">End Date</p>
                  <p class="card-item-date">{{ moment(invitation.end_date).format("YYYY/MM/DD") }}</p>
                </div>
              </div>
              <div class="row">
                <div class="start">
                  <p class="card-item-date-label">Reg. Start Date</p>
                  <p
                    class="card-item-date"
                  >{{ moment(invitation.reg_start_date).format("YYYY/MM/DD") }}</p>
                </div>
                <div class="end">
                  <p class="card-item-date-label">Reg. End Date</p>
                  <p
                    class="card-item-date"
                  >{{ moment(invitation.reg_end_date).format("YYYY/MM/DD") }}</p>
                </div>
              </div>
            </div>
            <div class="actions">
              <button
                class="btn-danger-secondary modal-trigger withdraw"
                @click="withDrawn(invitation)"
                data-modal="withdraw"
              >Withdraw</button>
              <button
                class="btn-primary modal-trigger"
                @click="preView(invitation)"
                data-modal="user-event"
              >Edit</button>
            </div>
          </article>
        </div>
      </section>
      <section class="not-register selection" data-view="cards">
        <div class="container">
          <article
            class="card-item events-card-item"
            v-for="invitation in invitations"
            v-if="invitation.status === 'pending'"
            data-card="event"
          >
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ invitation.event_name }}</h2>
              <h3
                class="card-item-subheadline"
              >{{ invitation.city_name }} , {{ invitation.state_name }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <span>
                  Booked at
                  <br />
                  <b>{{ invitation.hotel_name }}</b>
                </span>
              </p>
              <p>
                <span>
                  Enrolled in
                  <br />
                  <b>{{ invitation.total_courses }} courses</b>
                </span>
              </p>
            </div>
            <div class="card-item-date-info">
              <div class="row">
                <div class="start">
                  <p class="card-item-date-label">Start Date</p>
                  <p class="card-item-date">{{ moment(invitation.start_date).format("YYYY/MM/DD") }}</p>
                </div>
                <div class="end">
                  <p class="card-item-date-label">End Date</p>
                  <p class="card-item-date">{{ moment(invitation.end_date).format("YYYY/MM/DD") }}</p>
                </div>
              </div>
              <div class="row">
                <div class="start">
                  <p class="card-item-date-label">Reg. Start Date</p>
                  <p
                    class="card-item-date"
                  >{{ moment(invitation.reg_start_date).format("YYYY/MM/DD") }}</p>
                </div>
                <div class="end">
                  <p class="card-item-date-label">Reg. End Date</p>
                  <p
                    class="card-item-date"
                  >{{ moment(invitation.reg_end_date).format("YYYY/MM/DD") }}</p>
                </div>
              </div>
            </div>
            <div class="actions">
              <button
                class="btn-secondary modal-trigger"
                @click="preView(invitation)"
                data-action="view"
                data-modal="user-event"
              >View</button>
              <button
                class="btn-primary modal-trigger"
                @click="registerEvent(invitation)"
                data-modal="register"
              >Register</button>
            </div>
          </article>
        </div>
      </section>
    </main>

    <div class="modal register-modal" data-action="cancel" data-modal="register">
      <div class="modal-content">
        <h3>Register for {{ eventName }}?</h3>
        <button
          class="btn-secondary modal-action"
          data-modal="register"
          @click="cancel('cancel','register')"
          data-action="cancel"
        >Cancel</button>
        <button @click="registerUserEvent(register_event_id)" class="btn-primary">Register</button>
      </div>
    </div>

    <div class="modal delete-modal withdrawn" data-modal="withdraw">
      <div class="modal-content">
        <h3 class="h3">Withdraw from {{withdraw.name}} ?</h3>
        <p>You will be removed from any courses you've enrolled in for this event.</p>
        <button
          class="btn-secondary modal-action"
          @click="cancel('cancel','withdraw')"
          data-modal="withdraw"
          data-action="cancel"
        >Cancel</button>
        <button @click="withDrawInvitation(withdraw.id)" class="btn-primary">Withdraw</button>
      </div>
    </div>

   <div class="modal add-edit-modal event-modal view" data-modal="user-event" data-action="add">
    <div class="modal-content">
      <header class="modal-header">
        <div class="header-info">
          <p class="current-action">View Event</p>
          <h3 class="headline">{{ events.name }}</h3>
        </div>
        <div class="modal-header-ctas">
          <button class="btn-secondary modal-action" @click="cancel('cancel','user-event')" data-action="cancel" data-modal="user-event">Close</button>
          <button class="btn-primary modal-action modal-trigger" data-modal="register">Update</button>
        </div>
      </header>
      <section class="modal-cards">
        <article   @click="modalSubactions($event,0,'main')" class="modal-card active-card" data-card="main" data-number="0">
          <header class="modal-card-header">
            <h3 class="small-headline">Event Information</h3>
          </header>
          <section class="modal-card-data">
            <div class="row stretch-height">
              <div class="col-2">
                <div class="row">
                  <div class="col-2">
                    <h3>Event</h3>
                    <p class="no-margin">{{ events.name }}</p>
                  </div>
                  <div class="col-2">
                    <h3 class="no-bottom-margin">Location</h3>
                    <p class="no-margin">{{ events.location }}</p>
                  </div>
                </div>
                <section class="form-section course-schedule">
                  <div class="row">
                    <div class="col-2">
                      <div class="form-block">
                        <span>Start Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">{{ moment(events.start_date).format("YYYY/MM/DD") }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="col-2">
                      <div class="form-block">
                        <span>End Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">{{ moment(events.end_date).format("YYYY/MM/DD") }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <div class="form-block">
                        <span>Reg. Start Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">{{ moment(events.reg_start_date).format("YYYY/MM/DD") }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="col-2">
                      <div class="form-block">
                        <span>Reg. End Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">{{ moment(events.reg_end_date).format("YYYY/MM/DD") }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </section>
              </div>
              <div class="col-2">
                <div>
                    <h3>Details</h3>
                    <p>{{ events.details }}</p>
                  </div>
              </div>
            </div>
           </section>
        </article>
        <article  @click="modalSubactions($event,1,'courses')" class="modal-card" data-card="courses" data-number="1">
          <header class="modal-card-header">
            <h3 class="small-headline"><a class="close-course">Course List</a><span>Course List</span></h3>
          </header>
          <section class="modal-card-data">
            <i style="margin: -0.5rem 0 2rem; display: block">You will be enrolled in the courses you select below
              once you register for this event.</i>
            <div class="row">
              <div class="course-list">
                <div v-for="course in courses"
                   class="course-listing">
                  <p> {{ course.course_detail_id }} {{ course.course.prefix }} : {{ course.course.name }}</p>
                  <div class="course-actions">
                    <p>View</p>
                    <p @click="enroll(course.id)" class="selected">Enrolled</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="course-info">
              <section class="modal-card-data">
                <div class="row">
                  <div class="col-2">
                    <h2>PSC383: Aviation</h2>
                  </div>
                  <div class="col-2">
                    <button class="btn-secondary selected">Enrolled</button>
                  </div>
                </div>
                <div class="row stretch-height">
                  <div class="col-2">
                    <div class="row">
                      <div class="col-2">
                        <h3>Code</h3>
                        <p class="no-margin">PSC383</p>
                      </div>
                      <div class="col-2">
                        <h3>Name</h3>
                        <p class="no-margin">Aviation</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-2">
                        <h3>Instructor</h3>
                        <p class="no-margin">Mike Ramirez</p>
                      </div>
                      <div class="col-2">
                        <h3>Seats Available</h3>
                        <p class="no-margin">48/50</p>
                      </div>
                    </div>
                    <section class="form-section course-schedule">
                      <div class="row">
                        <div class="col-2">
                          <div class="form-block">
                            <span>Start Date</span>
                            <p class="bold-font course-dates">
                              <span class="course-start-date">5/12/19</span>
                            </p>
                          </div>
                        </div>
                        <div class="col-2">
                          <div class="form-block">
                            <span>End Date</span>
                            <p class="bold-font course-dates">
                              <span class="course-start-date">5/14/19</span>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-2">
                          <div class="form-block">
                            <span>Start Time</span>
                            <p class="bold-font course-dates">
                              <span class="course-start-date">1:00pm</span>
                            </p>
                          </div>
                        </div>
                        <div class="col-2">
                          <div class="form-block">
                            <span>End Time</span>
                            <p class="bold-font course-dates">
                              <span class="course-start-date">2:00pm</span>
                            </p>
                          </div>
                        </div>
                      </div>
                    </section>
                  </div>
                  <div class="col-2">
                    <div class="row vertical">
                      <h3>Location</h3>
                      <p class="no-margin">Facility Name, Room #300</p>
                      <p class="no-margin">905 Ave K, Lubbock, TX 79401</p>
                    </div>
                    <div
                      style="width: 100%; height: 70.5%; background: #f4f5f6; display: flex; align-items: center; justify-content: center">
                      <p>Map Placeholder</p>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="user-event-details">
                    <h3>Details</h3>
                    <p>Cras mattis consectetur purus sit amet fermentum. Donec ullamcorper nulla non metus
                      auctor fringilla. Nullam id dolor id nibh ultricies vehicula ut id elit. Nullam quis
                      risus eget urna mollis ornare vel eu leo.</p>
                  </div>
                </div>
              </section>
            </div>
          </section>
        </article>
        <article @click="modalSubactions($event,2,'hotel')"  class="modal-card" data-card="hotel" data-number="2">
          <header class="modal-card-header">
            <h3 class="small-headline">Hotel Information</h3>
          </header>
          <section class="modal-card-data">
            <div class="row stretch-height">
              <div class="col-2">
                <div class="row">
                  <div class="col-2">
                    <h3>Hotel</h3>
                    <p class="no-margin">Hyatt Regency</p>
                  </div>
                  <div class="col-2">
                    <h3>Phone Number</h3 class="no-bottom-margin">
                    <p class="no-margin">000-000-0000</p>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <h3>Location</h3 class="no-bottom-margin">
                    <p class="no-margin">12345 N. Fancy Hotel Place</p>
                    <p class="no-margin">Lubbock, TX 79401</p>
                  </div>
                </div>
                <section class="form-section course-schedule">
                  <div class="row">
                    <div class="col-2">
                      <div class="form-block">
                        <span>Check-In Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">5/12/19</span>
                        </p>
                      </div>
                    </div>
                    <div class="col-2">
                      <div class="form-block">
                        <span>Check-Out Date</span>
                        <p class="bold-font course-dates">
                          <span class="course-start-date">5/14/19</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </section>
              </div>
              <div class="col-2">
                <div
                  style="width: 100%; height: 100%; background: #f4f5f6; display: flex; align-items: center; justify-content: center">
                  <p>Map Placeholder</p>
                </div>
              </div>
            </div>
          </section>
        </article>
      </section>
    </div>
    <section class="modal-subactions">
      <article @click="modalSubactions($event,1,'courses')" class="modal-subaction" data-number="1" data-card="courses">
        <div class="modal-subaction-icon">
           <img :src="siteUrl + '/images/modal_subaction-hotels.png'">
        </div>
        <p>Course List</p>
      </article>
      <article @click="modalSubactions($event,2,'hotel')" class="modal-subaction" data-number="2" data-card="hotel">
        <div class="modal-subaction-icon">
            <img :src="siteUrl + '/images/modal_subaction-giveaways.png'">   
        </div>
        <p>Hotel Information</p>
      </article>
    </section>
  </div>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import moment from "moment";
import { mapGetters } from "vuex";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
export default {
  props: ["view", "form", "v", "editModal"],
  components: {
    "nav-breadcrumb": breadCrumbNavigation
  },
  data() {
    return {
      siteUrl: siteUrl,
      moment: moment,
      title: true,
      eventTitle: "",
      api: api,
      invitations: "",
      course_prefix: "",
      course_name: "",
      enrollment_id: "",
      hideWithDraw: false,
      eventName: "",
      register_event_id: "",
      courses : [],
      withdraw: {
        id: null,
        name: ""
      },
      events : {
        id: null,
        name : "",
        location : "",
        start_date : "",
        end_date : "",
        reg_start_date: "",
        reg_end_date: "",
        details: "",
        event_id : ""
      }
    };
  },
  computed: mapGetters(["hasEventData", "userData"]),
  watch: {},
  methods: {
    enroll(id){
      console.log(id)
    },
    changeView(dataView) {
      var elements = document.getElementsByClassName("view-selection-link");
      elements[0].classList.remove("active-link");
      $(".selection").attr("data-view", `${dataView}`);
    },
    withDrawn(withDraw) {
      var elements = document.getElementsByClassName("withdrawn");
      elements[0].classList.add("active-modal");
      this.withdraw.id = withDraw.id;
      this.withdraw.name = withDraw.event_name;
    },
    preView(invitationData) {
      var elements = document.getElementsByClassName("view");
      elements[0].classList.add("active-modal");
      this.events.id = invitationData.id;
      this.events.name = invitationData.event_name;
      this.events.location = invitationData.city_name + ' , ' +  invitationData.state_name;
      this.events.start_date = invitationData.start_date;
      this.events.end_date = invitationData.end_date;
      this.events.reg_start_date = invitationData.reg_start_date;
      this.events.reg_end_date = invitationData.reg_end_date;
      this.events.details = invitationData.event_details;
      this.events.event_id = invitationData.event_id;
    },
    withDrawInvitation(id) {
      window.axios.post(api.withDrawCourse, { id: id }).then(response => {
        this.invitations = response.data;
        this.cancel("cancel", "withdraw");
        this.getEventsInvitation();
        this.notRegistered();
      });
    },
    cancel(dataAction, dataModal) {
      var action = dataAction,
        modal = dataModal;
      if (action == "cancel" || action == "close") {
        $('.modal[data-modal="' + modal + '"]').removeClass("active-modal");
      }
    },
    notRegistered() {
      var elements = document.getElementsByClassName("register")[0];
      elements.classList.add("hide");
      elements.classList.remove("show");
      var element = document.getElementsByClassName("not-register");
      element[0].classList.remove("hide");
      element[0].classList.add("show");
      this.hideWithDraw = false;
    },
    registered() {
      var elements = document.getElementsByClassName("not-register");
      elements[0].classList.add("hide");
      elements[0].classList.remove("show");
      var element = document.getElementsByClassName("register")[0];
      element.classList.remove("hide");
      element.classList.add("show");
      this.hideWithDraw = true;
    },
    registerEvent(invitation) {
      var elements = document.getElementsByClassName("register-modal");
      elements[0].classList.add("active-modal");
      this.eventName = invitation.event_name;
      this.register_event_id = invitation.id;
    },
    getEventsInvitation() {
      let user_id = this.userData.id;
      window.axios
        .post(api.eventInvitedUserList, { id: user_id })
        .then(response => {
          if (response.data.length > 0) {
            this.invitations = response.data;
          }
        });
    },
    registerUserEvent(id) {
      window.axios.post(api.enrollUser, { id: id }).then(response => {
        this.invitations = response.data;
        this.cancel("cancel", "register");
        this.getEventsInvitation();
        this.registered();
      });
    },
    modalSubactions(event, num, dataAttr) {
       switch (dataAttr) {
          case 'courses':
             this.getEventCourses(this.events.event_id);
          break;
          case 'hotel':
              console.log('hotel')
          break;
       } 

        let amount = num * 70;
        this.moveModalCards(event, num, amount, dataAttr);
    },
    getEventCourses(eventId) {
      axios
        .get(api.editEvent + eventId)
        .then(res => {
          const courseArr = [];
          if (res.data.course_details.length > 0) {
            for (let key in res.data.course_details) {
              res.data.course_details[key].av_needs = JSON.parse(
                res.data.course_details[key].av_needs
              );
              res.data.course_details[key].av_pro = JSON.parse(
                res.data.course_details[key].av_pro
              );

              res.data.course_details[key].instructor_name = JSON.parse(
                res.data.course_details[key].instructor_name
              );
              courseArr.push(res.data.course_details[key]);
            }
          }
          this.courses = courseArr;
        })
        .catch(err => {});
    },
    moveModalCards(card, num, amount, dataAttr) {
      this.tabAttr = dataAttr;
      let cards = document.querySelector(".modal-cards"),
        cardNumber = num;
      $(".active-card")
        .prevAll()
        .unbind();
      $(cards).css(
        "transform",
        `translate(calc(-${amount}vw - (${cardNumber} * 1.85rem)`
      );
      [].forEach.call(cards.children, function(c) {
        c.getAttribute("data-card") == dataAttr
          ? c.classList.add("active-card")
          : c.classList.remove("active-card");
      });
      document.querySelectorAll(".modal-subaction").forEach(sub => {
        if (sub.getAttribute("data-card") == dataAttr) {
          sub.classList.add("current-subaction");
          sub.classList.remove("inactive-subaction");
        } else {
          sub.classList.remove("current-subaction");
        }
      });
      if ($(".current-subaction").length > 0) {
        $(".modal-subaction:not(.current-subaction)").addClass(
          "inactive-subaction"
        );
      } else {
        $(".modal-subaction").removeClass("inactive-subaction");
      }
    }
  },
  mounted() {
    this.getEventsInvitation();
    this.hideWithDraw = true;
  }
};
</script>
<style scoped>
.show {
  display: block;
}
.hide {
  display: none;
}
</style>


