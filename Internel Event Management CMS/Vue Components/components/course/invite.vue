<template>
  <div>
    <nav-breadcrumb :inviteUser="title"></nav-breadcrumb>
    <main class="main">
      <nav class="sub-nav">
        <div class="container">
          <ul v-if="invitations.length > 0" class="menu">
            <li class="menu-item">
              <span class="menu-link active-link" @click="enrolled('enrolled')">Enrolled</span>
            </li>
            <li class="menu-item">
              <span href @click="notEnrolled('notenrolled')" class="menu-link">Not Enrolled</span>
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
      <section class="selection enroll show" data-view="cards">
        <div class="container">
          <article
            v-for="invitation in invitations"
            v-if="invitation.status === 'approved'"
            class="card-item events-card-item course-card-item"
          >
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ invitation.course_prefix }}</h2>
              <h3 class="card-item-subheadline">{{ invitation.course_name }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <img :src="siteUrl + '/images/user-icon.svg'" />
                <span>{{ invitation.facility_name }}</span>
              </p>
              <p v-if="invitation.state_name !== null && invitation.city_name !== null">
                <img :src="siteUrl + '/images/location.svg'" />
                <span>{{invitation.state_name}} , {{invitation.city_name}}</span>
              </p>
            </div>
            <div class="card-item-date-info">
              <div class="start">
                <p class="card-item-date-label">Start</p>
                <p class="card-item-date">{{ moment(invitation.start_date).format("YYYY/MM/DD") }}</p>
                <p
                  class="card-item-time"
                >{{ moment(invitation.start_time, "h:mm a").format("HH:mm:ss") }}</p>
              </div>
              <div class="end">
                <p class="card-item-date-label">End</p>
                <p class="card-item-date">{{ moment(invitation.end_date).format("YYYY/MM/DD") }}</p>
                <p
                  class="card-item-time"
                >{{ moment(invitation.end_time, "h:mm a").format("HH:mm:ss") }}</p>
              </div>
            </div>
            <div class="actions">
              <button
                class="btn-danger-secondary modal-trigger"
                @click="withDraw(invitation)"
                data-modal="withdraw"
              >Withdraw</button>
              <button
                class="btn-primary modal-trigger"
                data-modal="user-course"
                @click="preView(invitation)"
                data-action="view"
              >View</button>
            </div>
          </article>
        </div>
      </section>
      <section class="selection not-enroll hide" data-view="cards">
        <div class="container">
          <article
            v-for="invitation in invitations"
            v-if="invitation.status === 'pending'"
            class="card-item events-card-item course-card-item"
          >
            <hgroup class="card-item-headlines">
              <h2 class="card-item-headline">{{ invitation.course_prefix }}</h2>
              <h3 class="card-item-subheadline">{{ invitation.course_name }}</h3>
            </hgroup>
            <div class="card-item-secondary-info">
              <p>
                <img :src="siteUrl + '/images/user-icon.svg'" />
                <span>{{ invitation.facility_name }}</span>
              </p>
              <p v-if="invitation.state_name !== null && invitation.city_name !== null">
                <img :src="siteUrl + '/images/location.svg'" />
                <span>{{invitation.state_name}} , {{invitation.city_name}}</span>
              </p>
            </div>
            <div class="card-item-date-info">
              <div class="start">
                <p class="card-item-date-label">Start</p>
                <p class="card-item-date">{{ moment(invitation.start_date).format("YYYY/MM/DD") }}</p>
                <p
                  class="card-item-time"
                >{{ moment(invitation.start_time, "h:mm a").format("HH:mm:ss") }}</p>
              </div>
              <div class="end">
                <p class="card-item-date-label">End</p>
                <p class="card-item-date">{{ moment(invitation.end_date).format("YYYY/MM/DD") }}</p>
                <p
                  class="card-item-time"
                >{{ moment(invitation.end_time, "h:mm a").format("HH:mm:ss") }}</p>
              </div>
            </div>
            <div class="actions">
              <button
                class="btn-secondary modal-trigger"
                data-modal="user-course"
                @click="preView(invitation)"
                data-action="view"
              >View</button>
              <button
                class="btn-primary modal-trigger"
                @click="enrollCourse(invitation)"
                data-modal="enroll"
              >Enroll</button>
            </div>
          </article>
        </div>
      </section>
    </main>
    <div class="modal add-edit-modal course-modal view" data-modal="user-course" data-action="view">
      <div class="modal-content">
        <header class="modal-header">
          <div class="header-info">
            <p class="current-action">View Course</p>
            <h3 class="headline">{{ course.prefix }} : {{ course.name }}</h3>
          </div>
          <div class="modal-header-ctas">
            <button
              class="btn-secondary modal-action"
              data-action="cancel"
              @click="cancel('cancel','user-course')"
              data-modal="user-course"
            >Close</button>
            <button
              class="btn-danger-secondary modal-action modal-trigger"
              v-if="hideWithDraw"
              @click="withDraw(withdrawData)"
              data-modal="withdraw"
            >Withdraw</button>
          </div>
        </header>
        <section class="modal-cards">
          <article class="modal-card" data-card="courses" data-number="0">
            <header class="modal-card-header">
              <h3 class="small-headline">Course Information</h3>
            </header>
            <section class="modal-card-data">
              <div class="row stretch-height">
                <div class="col-2">
                  <div class="row">
                    <div class="col-2">
                      <h3>Code</h3>
                      <p class="no-margin">{{ course.prefix }}</p>
                    </div>
                    <div class="col-2">
                      <h3>Name</h3>
                      <p class="no-margin">{{ course.name }}</p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">
                      <h3>Instructor</h3>
                      <p class="no-margin"></p>
                    </div>
                    <div class="col-2">
                      <h3>Seats Available</h3>
                      <p class="no-margin">{{ course.availableSeat }}/{{ course.totalSeats }}</p>
                    </div>
                  </div>
                  <section class="form-section course-schedule">
                    <div class="row">
                      <div class="col-2">
                        <div class="form-block">
                          <span>Start Date</span>
                          <p class="bold-font course-dates">
                            <span
                              class="course-start-date"
                            >{{ moment(course.start_date).format("YYYY/MM/DD") }}</span>
                          </p>
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-block">
                          <span>End Date</span>
                          <p class="bold-font course-dates">
                            <span
                              class="course-start-date"
                            >{{ moment(course.end_date).format("YYYY/MM/DD") }}</span>
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-2">
                        <div class="form-block">
                          <span>Start Time</span>
                          <p class="bold-font course-dates">
                            <span
                              class="course-start-date"
                            >{{ moment(course.startTime, "h:mm a").format("HH:mm:ss") }}</span>
                          </p>
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-block">
                          <span>End Time</span>
                          <p class="bold-font course-dates">
                            <span
                              class="course-start-date"
                            >{{ moment(course.endTime, "h:mm a").format("HH:mm:ss") }}</span>
                          </p>
                        </div>
                      </div>
                    </div>
                  </section>
                </div>
                <div class="col-2">
                  <div class="row vertical">
                    <div class>
                      <h3>Details</h3>
                      <p>{{ course.details }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </article>
        </section>
      </div>
    </div>

    <div class="modal delete-modal logout-modal" data-modal="logout">
      <div class="modal-content">
        <h3>Are you sure you want to log out?</h3>
        <button
          class="btn-secondary modal-action"
          @click="cancel('cancel','logout')"
          data-modal="logout"
          data-action="cancel"
        >Cancel</button>
        <button class="btn-primary">Log Out</button>
      </div>
    </div>

    <div class="modal delete-modal" data-modal="delete">
      <div class="modal-content">
        <h3 class="h3">Delete Course?</h3>
        <p>
          To confirm your decision to delete the course,
          <b>PSC383 (Aviation)</b>, please type
          <i>delete</i> in the field below.
        </p><input type="text" placeholder="Type "delete"" />
        <button
          class="btn-secondary modal-action"
          data-modal="delete"
          data-action="cancel"
          @click="cancel('cancel','delete')"
        >Cancel</button>
        <button class="btn-primary">Delete</button>
      </div>
    </div>

    <div class="modal delete-modal withdraw" data-modal="withdraw">
      <div class="modal-content">
        <h3 class="h3">Withdraw from {{withdraw.prefix}} : {{withdraw.name}}?</h3>
        <p>You will no longer have a reserved seat for this course.</p>
        <button
          class="btn-secondary modal-action"
          @click="cancel('cancel','withdraw')"
          data-modal="withdraw"
          data-action="cancel"
        >Cancel</button>
        <button @click="withDrawInvitation(withdraw.id)" class="btn-primary">Withdraw</button>
      </div>
    </div>

    <div class="modal register-modal enroll-course" data-modal="enroll">
      <div class="modal-content">
        <h3>Enroll in {{course_prefix}}: {{course_name}}?</h3>
        <button
          class="btn-secondary modal-action"
          @click="cancel('cancel','enroll')"
          data-modal="enroll"
          data-action="cancel"
        >Cancel</button>
        <button @click="enrollment(enrollment_id)" class="btn-primary">Enroll</button>
      </div>
    </div>

    <div class="modal add-edit-modal admin-modal" data-modal="profile" data-action="edit">
      <div class="modal-content">
        <header class="modal-header">
          <div class="header-info">
            <h3 class="headline">Edit Profile</h3>
          </div>
          <div class="modal-header-ctas">
            <button
              class="btn-secondary modal-action"
              data-action="cancel"
              data-modal="profile"
              @click="cancel('cancel','profile')"
            >Cancel</button>
            <button class="btn-primary modal-action">Update</button>
          </div>
        </header>
        <section class="modal-cards">
          <article class="modal-card" data-card="main"></article>
          <article class="modal-card" data-card="course">
            <header class="modal-card-header">
              <h3 class="small-headline">Employee Info</h3>
            </header>
            <section class="modal-card-data">
              <div class="row">
                <div class="col-2">
                  <label class="form-block">
                    <span>First Name</span>
                    <input type="text" value="John" />
                  </label>
                </div>
                <div class="col-2">
                  <label class="form-block">
                    <span>Last Name</span>
                    <input type="text" value="Smith" />
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="col-2">
                  <label class="form-block">
                    <span>Email Address</span>
                    <input type="email" value="name@email.com" />
                  </label>
                </div>
                <div class="col-2">
                  <label class="form-block">
                    <span>Phone number</span>
                    <input type="tel" value="000-000-0000" />
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="col-2">
                  <label class="form-block">
                    <span>Job Title</span>
                    <input type="text" value="Supervisor of Stuff" />
                  </label>
                </div>
                <div class="col-2">
                  <div class="row">
                    <div class="col-2">
                      <label class="form-block">
                        <span>Marketing Title</span>
                        <select>
                          <option>Example</option>
                        </select>
                      </label>
                    </div>
                    <div class="col-2">
                      <label class="form-block">
                        <span>Sector</span>
                        <select>
                          <option>Example</option>
                        </select>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-2">
                  <label class="form-block">
                    <span>New Password</span>
                    <input type="password" />
                  </label>
                </div>
                <div class="col-2">
                  <label class="form-block">
                    <span>Repeat Password</span>
                    <input type="password" />
                  </label>
                </div>
              </div>
            </section>
          </article>
          <article class="modal-card" data-card="food"></article>
          <article class="modal-card" data-card="hotel"></article>
          <article class="modal-card" data-card="giveaway"></article>
        </section>
        <section class="modal-add-actions">
          <article class="modal-action current-action" data-add="course"></article>
          <article class="modal-action" data-add="food"></article>
          <article class="modal-action" data-add="hotel"></article>
          <article class="modal-action" data-add="giveaway"></article>
        </section>
      </div>
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
      withdrawData: {
        id: null,
        course_prefix: "",
        course_name: ""
      },
      eventTitle: "",
      api: api,
      invitations: "",
      course_prefix: "",
      course_name: "",
      enrollment_id: "",
      hideWithDraw: false,
      withdraw: {
        id: null,
        prefix: "",
        name: ""
      },
      course: {
        id: null,
        prefix: "",
        name: "",
        totalSeats: "",
        availableSeat: "",
        instructor_name: "",
        startDate: "",
        endDate: "",
        startTime: "",
        endTime: "",
        details: ""
      }
    };
  },
  computed: mapGetters(["hasEventData", "userData"]),
  watch: {},
  methods: {
    changeView(dataView) {
      var elements = document.getElementsByClassName("view-selection-link");
      elements[0].classList.remove("active-link");
      $(".selection").attr("data-view", `${dataView}`);
    },
    withDraw(withDraw) {
      var elements = document.getElementsByClassName("withdraw");
      elements[0].classList.add("active-modal");
      this.withdraw.id = withDraw.id;
      this.withdraw.prefix = withDraw.course_prefix;
      this.withdraw.name = withDraw.course_name;
    },
    preView(invitationData) {
      let instructors = JSON.parse(invitationData.instructor_name);
      let instructorName = "";
      if (instructors.length > 0) {
        instructorName = instructors[0].instructorName;
      } else {
        instructorName = "";
      }
      var elements = document.getElementsByClassName("view");
      elements[0].classList.add("active-modal");
      this.course.id = invitationData.id;
      this.course.prefix = invitationData.course_prefix;
      this.course.name = invitationData.course_name;
      this.course.totalSeats = invitationData.seats;
      this.course.instructor_name = instructorName;
      this.course.startDate = invitationData.start_date;
      this.course.endDate = invitationData.end_date;
      this.course.startTime = invitationData.start_time;
      this.course.endTime = invitationData.end_time;
      this.course.details = invitationData.details;
      this.course.availableSeat = invitationData.available_seats;
      this.withdrawData.id = invitationData.id;
      this.withdrawData.course_prefix = invitationData.course_prefix;
      this.withdrawData.course_name = invitationData.course_name;
    },
    withDrawInvitation(id) {
      window.axios.post(api.withDrawCourse, { id: id }).then(response => {
        this.invitations = response.data;
        this.cancel("cancel", "withdraw");
        this.getCoursesInvitation();
        this.enrolled();
      });
    },
    cancel(dataAction, dataModal) {
      var action = dataAction,
        modal = dataModal;
      if (action == "cancel" || action == "close") {
        $('.modal[data-modal="' + modal + '"]').removeClass("active-modal");
      }
    },
    notEnrolled() {
      var elements = document.getElementsByClassName("enroll")[0];
      elements.classList.add("hide");
      elements.classList.remove("show");
      var element = document.getElementsByClassName("not-enroll");
      element[0].classList.remove("hide");
      element[0].classList.add("show");
      this.hideWithDraw = false;
    },
    enrolled() {
      var elements = document.getElementsByClassName("not-enroll");
      elements[0].classList.add("hide");
      elements[0].classList.remove("show");
      var element = document.getElementsByClassName("enroll")[0];
      element.classList.remove("hide");
      element.classList.add("show");
      this.hideWithDraw = true;
    },
    enrollCourse(invitation) {
      var elements = document.getElementsByClassName("enroll-course");
      elements[0].classList.add("active-modal");
      invitation.id;
      this.course_prefix = invitation.course_prefix;
      this.course_name = invitation.course_name;
      this.enrollment_id = invitation.id;
    },
    getCoursesInvitation() {
      let user_id = this.userData.id;
      window.axios
        .post(api.courseInviteUser, { id: user_id })
        .then(response => {
          if (response.data.length > 0) {
            this.invitations = response.data;
          }
        });
    },
    enrollment(id) {
      window.axios.post(api.enrollUser, { id: id }).then(response => {
        this.invitations = response.data;
        this.cancel("cancel", "enroll");
        this.getCoursesInvitation();
        this.enrolled();
      });
    }
  },
  mounted() {
    this.getCoursesInvitation();
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


