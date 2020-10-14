<template>
  <div>
    <nav-breadcrumb v-show="eventCourse" :editEvent="true" :eventTitle="eventTitle"></nav-breadcrumb>
    <nav-breadcrumb v-show="!eventCourse" :course="true">
      <btn
        @click="addCourses($event)"
        :loading="loading"
        :labelBefore="navlabel.btnTitleBefore"
        :labelAfter="navlabel.btnTitleAfter"
        :type="'btn-primary'"
      ></btn>
    </nav-breadcrumb>
    <event-sub-nav @cards="cards" @list="list"></event-sub-nav>
    <section class="selection" :data-view="view">
      <div class="container">
        <article class="card-item events-card-item" v-for="course in courses">
          <hgroup class="card-item-headlines">
            <h2 class="card-item-headline">{{ course.course.prefix }}</h2>
            <h3 class="card-item-subheadline">{{ course.course.name }}</h3>
          </hgroup>
          <div class="card-item-secondary-info">
            <p>
              <img :src="siteUrl + '/images/user-icon.svg'" />
              <span>{{course.instructor_name[0].instructorName}}</span>
            </p>
            <p></p>
          </div>
          <div class="card-item-date-info">
            <div class="start">
              <p class="card-item-date-label">Start</p>
              <p class="card-item-date">{{ moment(course.start_date).format("YYYY-MM-DD")}}</p>
              <p class="card-item-time">{{course.start_time}}</p>
            </div>
            <div class="end">
              <p class="card-item-date-label">End</p>
              <p class="card-item-date">{{ moment(course.end_date).format("YYYY-MM-DD") }}</p>
              <p class="card-item-time">{{course.end_time}}</p>
            </div>
          </div>
          <div class="card-item-options-menu">
            <div @click="dropDown($event)" class="card-item-menu-trigger events-card-item">
              <img :src="siteUrl + '/images/card-item.svg'" />
            </div>
            <ul class="card-item-menu events-card-menu">
              <li @click="edit(course.id)" class="card-item-menu-item">
                <a class="card-item-menu-link">Edit</a>
              </li>
              <router-link
                v-if="routeName != 'eventcourse'"
                :to="{name: 'courseFood', params: { id: course.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Foods</span>
                </a>
              </router-link>

              <router-link
                v-if="routeName != 'eventcourse'"
                :to="{name: 'coursehotel', params: { id: course.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Hotels</span>
                </a>
              </router-link>

              <router-link
                v-if="routeName != 'eventcourse'"
                :to="{name: 'courseGiveaways', params: { id: course.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Give Aways</span>
                </a>
              </router-link>

              <router-link
                v-if="routeName != 'eventcourse'"
                :to="{name: 'courseSeating', params: { id: course.id }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Seating</span>
                </a>
              </router-link>

              <router-link
                v-if="routeName != 'eventcourse'"
                :to="{name: 'inviteCourse', params: { id: course.id  }}"
                tag="li"
                class="card-item-menu-item"
              >
                <a href="#" :class="'card-item-menu-link'">
                  <span>View Invitations</span>
                </a>
              </router-link>
              <li
                v-if="routeName != 'eventcourse'"
                @click="registrants(course.id)"
                class="card-item-menu-item"
              >
                <a class="card-item-menu-link">View Registrants</a>
              </li>
              <li class="card-item-menu-item">
                <a @click="deleteCourse(course.id)" class="card-item-menu-link">Delete</a>
              </li>
            </ul>
          </div>
        </article>
        <article
          @click="addCourses($event)"
          class="card-item add-item modal-trigger"
          data-modal="courses"
        >
          <img :src="siteUrl + '/images/plus.svg'" />
          <p>Add Course</p>
        </article>
      </div>
    </section>
    <course-modal
      :headerTitle="modalHeaderTitle"
      :headerDescription="modalHeaderDescription"
      :contentTitle="modalHeadercontent"
      :course="true"
      :label="label"
      :deleteModal="true"
      @cancel="cancelModal"
      @update="addModal"
      :showModal="showModal"
    >
      <add-form :form="courseForm" :v="$v" :editModal="editModal"></add-form>
    </course-modal>

    <course-modal
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
          <btn
            :loading="loading"
            @click="cancelModal()"
            :labelBefore="'Cancel'"
            :labelAfter="'Cancel'"
            :type="'btn-secondary'"
          ></btn>
          <btn
            :loading="loading"
            :labelBefore="'Delete'"
            :labelAfter="'Delete'"
            :type="'btn-primary'"
            @click="confirmDelete()"
          ></btn>
        </div>
      </section>
    </course-modal>
  </div>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import Modal from "./../shared/modal/modal.vue";
import Add from "./../course/add.vue";
import breadCrumbNavigation from "./../shared/breadcrumbnavigation/breadcrumbnavigation.vue";
import EditButton from "./../shared/button/button.vue";
import EventSubNav from "./../event/navbar.vue";
import moment from "moment";
import { required, email, minLength } from "vuelidate/lib/validators";
export default {
  components: {
    "course-modal": Modal,
    "add-form": Add,
    "event-sub-nav": EventSubNav,
    "nav-breadcrumb": breadCrumbNavigation,
    btn: EditButton
  },
  data() {
    return {
      courses: [],
      routeName: "",
      eventCourse: true,
      showDeleteModal: false,
      editModal: false,
      courseDetail: [],
      moment: moment,
      siteUrl: siteUrl,
      showModal: false,
      eventTitle: "",
      modalHeaderTitle: "Edit Event",
      modalHeaderDescription: "Spring 2020",
      modalHeadercontent: "Add Course",
      loading: false,
      siteUrl: siteUrl,
      navlabel: {
        btnTitleBefore: "Edit Event",
        btnTitleAfter: "Edit Event"
      },
      labelModal: {
        btnTitleBefore: "Delete Course",
        btnTitleAftere: "Delete Course"
      },
      label: {
        btnTitleBefore: "Add Course",
        btnTitleAfter: "Add Course"
      },
      view: "cards",
      courseForm: {
        course_detail_id: "",
        courses: [],
        id: "",
        prefix: "",

        number: "",
        name: "",
        semester: "",
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
        reg_start_date: moment().format("YYYY-MM-DD"),
        reg_end_date: moment().format("YYYY-MM-DD"),
        start_time: "",
        end_time: "",
        details: "",
        room_number: "",
        seats: "",
        prerequisites: "",
        instructor: "",
        av_need: "",
        av_pro: "",
        status: 1
      }
    };
  },
  validations: {
    courseForm: {
      prefix: { required },
      number: { required },
      name: { required },
      start_date: { required },
      end_date: { required },
      reg_start_date: { required },
      reg_end_date: { required },
      details: { required },
      start_time: { required },
      end_time: { required },
      room_number: { required },
      seats: { required },
      prerequisites: { required }
    }
  },
  methods: {
    deleteCourse(id) {
      this.modalHeaderTitle = "";
      this.modalHeaderDescription = "Delete Course";
      this.labelModal.btnTitleBefor = "Delete Course";
      this.labelModal.btnTitleAfter = "Delete Course";
      this.showDeleteModal = true;
      this.deleteId = id;
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
          this.eventTitle = res.data.name;
          this.courses = courseArr;
        })
        .catch(err => {
          err.response.data.error && this.$noty.error(err.response.data.error);
          err.response.data.errors
            ? this.setErrors(err.response.data.errors)
            : this.clearErrors();
        });
    },
    getCourses() {
      this.eventCourse = false;
      this.modalHeaderDescription = "Add Course";
      this.navlabel.btnTitleBefore = "Add Course";
      this.navlabel.btnTitleAfter = "Add Course";
      axios
        .get(api.viewCourse)
        .then(res => {
          const courseArr = [];
          if (res.data.length > 0) {
            for (let key in res.data) {
              res.data[key].av_needs = JSON.parse(res.data[key].av_needs);
              res.data[key].av_pro = JSON.parse(res.data[key].av_pro);
              res.data[key].instructor_name = JSON.parse(
                res.data[key].instructor_name
              );
              courseArr.push(res.data[key]);
            }
          }
          this.courses = courseArr;
        })
        .catch(err => {
          err.response.data.error && this.$noty.error(err.response.data.error);
          err.response.data.errors
            ? this.setErrors(err.response.data.errors)
            : this.clearErrors();
        });
    },
    addCourses: function(e) {
      if (!this.eventCourse) {
        this.modalHeaderTitle = "";
        this.modalHeaderDescription = "Add Course";
      } else {
        this.modalHeaderTitle = "Add Course";
        this.modalHeaderDescription = this.eventTitle;
      }
      this.modalHeadercontent = "Add Course";
      this.label.btnTitleBefore = "Add Course";
      this.label.btnTitleAftere = "Add Course";
      this.showModal = true;
      this.clearForm();
      this.editModal = false;
    },
    cancelModal() {
      this.showModal = false;
      this.showDeleteModal = false;
      this.clearForm();
    },
    updateModal() {
      console.log("update");
    },
    dropDown() {
      event.currentTarget.nextSibling.nextSibling.classList.toggle("open");
    },
    addModal() {
      let avProsError = false;
      let avNeedsError = false;
      let instructorsError = false;

      let avProsErrorCount = 0;
      let avNeedsErrorCount = 0;
      let instructorsErrorCount = 0;

      if (this.editModal) {
        this.courseForm.avPros = this.courseForm.courses.av_pro;
        this.courseForm.avNeeds = this.courseForm.courses.av_needs;
        this.courseForm.instructors = this.courseForm.courses.instructor_name;
      }

      var i;
      for (i = 0; i < this.courseForm.avPros.length; i++) {
        if (
          this.courseForm.avPros[i].Name === null ||
          this.courseForm.avPros[i].Name === ""
        ) {
          this.courseForm.avPros[i].error = true;
          avProsErrorCount++;
        } else {
          this.courseForm.avPros[i].error = false;
        }
      }

      var i;
      for (i = 0; i < this.courseForm.avNeeds.length; i++) {
        if (
          this.courseForm.avNeeds[i].Name === null ||
          this.courseForm.avNeeds[i].Name === ""
        ) {
          this.courseForm.avNeeds[i].error = true;
          avNeedsErrorCount++;
        } else {
          this.courseForm.avNeeds[i].error = false;
        }
      }

      var i;
      for (i = 0; i < this.courseForm.instructors.length; i++) {
        if (
          this.courseForm.instructors[i].instructorName === null ||
          this.courseForm.instructors[i].instructorName === ""
        ) {
          this.courseForm.instructors[i].error = true;
          instructorsErrorCount++;
        } else {
          this.courseForm.instructors[i].error = false;
        }
      }

      if (avProsErrorCount > 0) {
        avProsError = true;
      } else {
        avProsError = false;
      }

      if (avNeedsErrorCount > 0) {
        avNeedsError = true;
      } else {
        avNeedsError = false;
      }

      if (instructorsErrorCount > 0) {
        instructorsError = true;
      } else {
        instructorsError = false;
      }

      this.$v.courseForm.$touch();
      if (this.$v.courseForm.$error) return;
      if (!avProsError && !instructorsError && !avNeedsError) {
        this.courseForm.facility_name = "  ";
        this.courseForm.address = "";
        this.courseForm.city_id = "";
        this.courseForm.state_id = "";
        this.courseForm.zip = "";
        this.courseForm.instructor_name = this.courseForm.instructors;
        this.courseForm.av_needs = this.courseForm.avNeeds;
        this.courseForm.av_pro = this.courseForm.avPros;
        this.courseForm.credit_type = "";
        this.courseForm.duration_type = "";

        this.courseForm.start_date = moment(this.courseForm.start_date).format(
          "YYYY-MM-DD"
        );
        this.courseForm.end_date = moment(this.courseForm.end_date).format(
          "YYYY-MM-DD"
        );

        this.courseForm.reg_start_date = moment(
          this.courseForm.reg_start_date
        ).format("YYYY-MM-DD");
        this.courseForm.reg_end_date = moment(
          this.courseForm.reg_end_date
        ).format("YYYY-MM-DD");

        this.courseForm.start_time = moment(
          this.courseForm.start_time,
          "h:mm a"
        ).format("HH:mm:ss");
        this.courseForm.end_time = moment(
          this.courseForm.end_time,
          "h:mm a"
        ).format("HH:mm:ss");
        if (!this.courseForm.course_detail_id) {
          axios.post(api.addCoursesDetails, this.courseForm).then(res => {
            this.loading = false;
            this.showModal = false;
            if (this.eventCourse) {
              let data = {
                course_detail_id: res.data.course.id,
                event_id: this.$route.params.id
              };
              axios
                .post(api.registerEventCourse, data)
                .then(res => {
                  this.loading = false;
                  this.showModal = false;
                  this.clearForm();
                })
                .catch(err => {});
              this.getEventCourses(this.$route.params.id);
            } else {
              this.getCourses();
            }
            this.$noty.success("Course Added Successfully!");
          });
        } else {
          axios.post(api.updateCoursesDetails, this.courseForm).then(res => {
            this.loading = false;
            this.showModal = false;
            if (this.eventCourse) {
              this.getEventCourses(this.$route.params.id);
            } else {
              this.getCourses();
            }
            this.$noty.success("Course Updated Successfully!");
          });
        }
      }
    },
    cards() {
      this.view = "cards";
    },
    list() {
      this.view = "list";
    },
    registrants(id) {
      this.$router.push({ name: "courseRegistrants", params: { id: id } });
    },
    edit(id) {
      this.clearForm();
      this.editModal = true;
      this.showModal = true;
      this.modalHeaderTitle = "Edit Course";
      this.modalHeaderDescription = this.eventTitle;
      this.modalHeadercontent = "Edit Course";
      this.label.btnTitleBefore = "Update Course";
      this.label.btnTitleAftere = "Update Course";
      axios.get(api.viewCourse + id).then(response => {
        this.courseForm.course_detail_id = response.data.id;
        this.courseForm.course_id = response.data.course_id;
        this.courseForm.prefix = response.data.course.prefix;
        this.courseForm.number = response.data.course.next_avl;
        this.courseForm.name = response.data.course.name;
        this.courseForm.semester = response.data.semester;
        this.courseForm.details = response.data.details;
        this.courseForm.start_date = moment(response.data.start_date).format(
          "YYYY-MM-DD"
        );
        this.courseForm.end_date = moment(response.data.end_date).format(
          "YYYY-MM-DD"
        );
        this.courseForm.reg_start_date = moment(
          response.data.reg_start_date
        ).format("YYYY-MM-DD");
        this.courseForm.reg_end_date = moment(
          response.data.reg_end_date
        ).format("YYYY-MM-DD");

        this.courseForm.room_number = response.data.room_number;
        this.courseForm.seats = response.data.seats;
        this.courseForm.prerequisites = response.data.prerequisites;
        this.courseForm.instructor_name = JSON.parse(
          response.data.instructor_name
        );
        this.courseForm.av_needs = JSON.parse(response.data.av_needs);
        this.courseForm.av_pro = JSON.parse(response.data.av_pro);
        this.courseForm.start_time = moment(
          response.data.start_time,
          "HH:mm:ss"
        ).format("h:mm A");
        this.courseForm.end_time = moment(
          response.data.end_time,
          "HH:mm:ss"
        ).format("h:mm A");
        this.courseForm.avPros = JSON.parse(response.data.av_pro);
        this.courseForm.avNeeds = JSON.parse(response.data.av_needs);
        this.courseForm.instructors = JSON.parse(response.data.instructor_name);
        this.courseForm.courses.instructor_name = "";
        this.courseForm.courses.av_needs = "";
        this.courseForm.courses.av_pro = "";
      });
    },
    confirmDelete() {
      axios.delete(api.deletCourse + this.deleteId).then(res => {
        this.$noty.success("Course Delete Successfully!");
      });
      this.cancelModal();
      if (this.$route.params.id) {
        this.getEventCourses(this.$route.params.id);
      } else {
        this.getCourses();
      }
    },
    clearForm() {
      this.$v.$reset();
      this.courseForm.id = "";
      this.courseForm.prefix = "";
      this.courseForm.number = "";
      this.courseForm.name = "";
      this.courseForm.semester = "";
      this.courseForm.start_date = moment().format("YYYY-MM-DD");
      this.courseForm.end_date = moment().format("YYYY-MM-DD");
      this.courseForm.reg_start_date = moment().format("YYYY-MM-DD");
      this.courseForm.reg_end_date = moment().format("YYYY-MM-DD");
      this.courseForm.start_time = "";
      this.courseForm.end_time = "";
      this.courseForm.details = "";
      this.courseForm.room_number = "";
      this.courseForm.seats = "";
      this.courseForm.prerequisites = "";
      this.courseForm.instructor = "";
      this.courseForm.av_need = "";
      this.courseForm.av_pro = "";
      this.courseForm.status = "";
      this.courseForm.instructor_name = "";
      this.courseForm.av_needs = "";
      this.courseForm.av_pro = "";
      this.courseForm.course_detail_id = "";
      this.courseForm.course_id = "";
      this.courseForm.avPros = "";
      this.courseForm.avNeeds = "";
      this.courseForm.instructors = "";
    }
  },
  created() {
    this.routeName = this.$route.name;
    if (this.$route.params.id) {
      this.getEventCourses(this.$route.params.id);
    } else {
      this.getCourses();
    }
  }
};
</script>
<style>
.card-item-menu-link {
  font-size: 12px;
  padding: 0.5rem 1rem;
  color: #02082f;
}
</style>

