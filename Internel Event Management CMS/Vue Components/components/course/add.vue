<template>
  <section class="modal-card-data">
    <div class="row">
      <div class="col-2">
        <div class="row">
          <div class="col-2">
            <label class="form-block" for="course-prefix">
              <span>Course Prefix</span>
              <input
                type="number"
                v-on:keyup="seletcedPrefix()"
                v-model="form.prefix"
                placeholder="Course Prefix"
              >
              <ul class="prefix-options" v-if="courseResults.length > 0">
                <li
                  v-for="(courseResult,key) in courseResults"
                  @click="selectCourse(courseResult.id)"
                >{{ courseResult.prefix }}</li>
              </ul>

              <ul class="prefix-options" v-if="noPrefixExist">
                <li>No Result Found</li>
              </ul>
              <div
                class="invalid-feedback form-error"
                v-show="v.courseForm.prefix.$error"
              >Course Prefix is required.</div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block" for="course-number">
              <span>Course Number</span>
              <input type="text" v-model="form.number" placeholder="PSC Course # (Next Aval.)">
              <div
                class="invalid-feedback form-error"
                v-show="v.courseForm.number.$error"
              >PSC Course # (Next Aval.) is required.</div>
            </label>
          </div>
        </div>
      </div>
      <div class="col-2">
        <label class="form-block">
          <span>Course Name</span>
          <input
            type="text"
            v-model="form.name"
            placeholder="Course Name Auto-populate from 'Prefix'"
          >
          <div
            class="invalid-feedback form-error"
            v-show="v.courseForm.name.$error"
          >Course Name is required.</div>
        </label>
      </div>
    </div>
    <div class="row stretch-height">
      <div class="col-2">
        <section class="form-section course-schedule">
          <div class="row">
            <div class="col-2">
              <div class="row">
                <label class="form-block" for="course-semester">
                  <span>Semester</span>
                  <input type="text" v-model="form.semester" placeholder="Semester">
                </label>
              </div>
            </div>
            <div class="col-2">
              <div class="row" :class="{'no-margin' : !courseDateModal}">
                <div v-if="courseDateModal" @click="courseDate" class="form-block">
                  <span>Start / End Date</span>
                  <p v-if="!editModal" class="bold-font course-dates">
                    <span class="course-start-date">{{ moment().format("YYYY/MM/DD")}}</span>
                    <span class="course-date-divider">&mdash;</span>
                    <span class="course-end-date">{{ moment().format("YYYY/MM/DD")}}</span>
                  </p>
                  <p v-else class="bold-font course-dates">
                    <span
                      class="course-start-date"
                    >{{ moment(form.start_date).format("YYYY/MM/DD")}}</span>
                    <span class="course-date-divider">&mdash;</span>
                    <span class="course-end-date">{{ moment(form.end_date).format("YYYY/MM/DD")}}</span>
                  </p>
                </div>
              </div>
              <div v-if="!courseDateModal" class="row no-margin">
                <div class="col-2 no-margin">
                  <label class="form-block" for="start-date">
                    <span>Start Date</span>
                    <date-picker
                      :class="'course-date'"
                      v-model="form.start_date"
                      placeholder="mm/dd/yyyy"
                    ></date-picker>
                    <div
                      class="invalid-feedback form-error"
                      v-show="v.courseForm.start_date.$error"
                    >Start Date is required.</div>
                  </label>
                </div>
                <div class="col-2 no-margin">
                  <label class="form-block" for="end-date">
                    <span>End Date</span>
                    <date-picker
                      :class="'course-date'"
                      v-model="form.end_date"
                      placeholder="mm/dd/yyyy"
                    ></date-picker>
                    <div
                      class="invalid-feedback form-error"
                      v-show="v.courseForm.end_date.$error"
                    >End Date is required.</div>
                  </label>
                </div>
              </div>

              <div
                v-if="courseRegistrationDateModal"
                :class="{'no-margin' : !courseRegistrationDateModal}"
                class="row"
              >
                <div @click="regRegsitrationCourseDate" class="form-block">
                  <span>Reg. Start / End Date</span>
                  <p v-if="!editModal" class="bold-font course-dates">
                    <span class="course-start-date">{{ moment().format("YYYY/MM/DD")}}</span>
                    <span class="course-date-divider">&mdash;</span>
                    <span class="course-end-date">{{ moment().format("YYYY/MM/DD")}}</span>
                  </p>
                  <p v-else class="bold-font course-dates">
                    <span
                      class="course-start-date"
                    >{{ moment(form.reg_start_date).format("YYYY/MM/DD")}}</span>
                    <span class="course-date-divider">&mdash;</span>
                    <span
                      class="course-end-date"
                    >{{ moment(form.reg_end_date).format("YYYY/MM/DD")}}</span>
                  </p>
                </div>
              </div>
              <div v-if="!courseRegistrationDateModal" class="row no-margin">
                <div class="col-2 no-margin">
                  <label class="form-block" for="registration-start-date">
                    <span>REG. START</span>
                    <date-picker
                      :class="'course-date'"
                      v-model="form.reg_start_date"
                      placeholder="mm/dd/yyyy"
                    ></date-picker>
                    <div
                      class="invalid-feedback form-error"
                      v-show="v.courseForm.reg_start_date.$error"
                    >Reg.Start Date required.</div>
                  </label>
                </div>
                <div class="col-2 no-margin">
                  <label class="form-block" for="registration-end-date">
                    <span>REG. END</span>
                    <date-picker
                      :class="'course-date'"
                      v-model="form.reg_end_date"
                      placeholder="mm/dd/yyyy"
                    ></date-picker>
                    <div
                      class="invalid-feedback form-error"
                      v-show="v.courseForm.reg_end_date.$error"
                    >Reg.End Date required.</div>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="form-block" for="course-time-of-day">
              <span>Time of Day</span>
              <div class="row">
                <div class="col-2">
                  <select name="start_time" v-model="form.start_time">
                    <option value>Select Start Time</option>
                    <option
                      v-for="(timeInterval,key) in startTimeIntervals"
                      :value="timeInterval"
                      v-bind:key="key"
                    >{{ timeInterval }}</option>
                  </select>
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.courseForm.start_time.$error"
                  >Start Time is required.</div>
                </div>
                <div class="col-2">
                  <select name="end_time" v-model="form.end_time">
                    <option value>Select End Time</option>
                    <option
                      v-for="(endtimeInterval , key) in endTimeIntervals"
                      :value="endtimeInterval"
                      v-bind:key="key"
                    >{{ endtimeInterval }}</option>
                  </select>
                  <div
                    class="invalid-feedback form-error"
                    v-show="v.courseForm.end_time.$error"
                  >End Time is required.</div>
                </div>
              </div>
            </label>
          </div>
        </section>
      </div>
      <div class="col-2">
        <label class="form-block">
          <span>Course Details</span>
          <textarea class="course-text-area" v-model="form.details" placeholder="Type details here..."></textarea>
          <span
            class="invalid-feedback "
            v-show="v.courseForm.details.$error"
          >Course Details is required.</span>
        </label>
      </div>
    </div>
    <div class="row">
      <div class="col-2">
        <div class="row">
          <div class="col-2">
            <label class="form-block" for="course-room-number">
              <span>Room Number</span>
              <input type="number" v-model="form.room_number">
              <div
                class="invalid-feedback form-error"
                v-show="v.courseForm.room_number.$error"
              >Room Number is required.</div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block" for="course-number-of-seats">
              <span>Number of Seats</span>
              <input type="number" v-model="form.seats">
              <div
                class="invalid-feedback form-error"
                v-show="v.courseForm.seats.$error"
              >Number of Seats is required.</div>
            </label>
          </div>
        </div>
      </div>
      <div class="col-2">
        <label class="form-block" for="course-prerequisites">
          <span>Prerequisites</span>
          <input type="text" v-model="form.prerequisites">
          <div
            class="invalid-feedback form-error"
            v-show="v.courseForm.prerequisites.$error"
          >prerequisites is required.</div>
        </label>
      </div>
    </div>
    <div class="row">
      <div class="col-2">
        <label class="form-block">
          <span>Instructor(s)</span>
          <div v-for="(instructor, index) in instructors" v-bind:key="index" class="course-flex">
            <div class="row v-center course">
              <input
                :class="{ 'top-15' : index > 0 }"
                v-model="instructor.instructorName"
                type="text"
              >
              <div
                class="invalid-feedback form-error"
                v-if="instructor.error"
              >Instructor is required.</div>
            </div>
            <div :class="{ 'remov-btn' : index > 0 }">
              <span
                href="#"
                class="remove-input"
                v-if="index > 0"
                @click="removeInstructor(index)"
                icon="delete"
              >remove</span>
              <button
                type="button"
                v-if="index + 1 === instructors.length"
                :class="{ 'top-15' : index > 0 }"
                @click="addInstructor"
                class="btn-add"
              >
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
                          <path d="M12,-7.02216063e-15 L12,24" id="Line"></path>
                          <path
                            d="M12,0.48 L12,23.52"
                            id="Line-Copy"
                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                          ></path>
                        </g>
                      </g>
                    </g>
                  </g>
                </svg>
              </button>
            </div>
          </div>
        </label>
      </div>
      <div class="col-2">
        <div class="row">
          <div class="col-2">
            <label class="form-block">
              <span>AV Needs</span>
              <div v-for="(avNeed, avIndex) in avNeeds" v-bind:key="avIndex" class="course-flex">
                <div class="row v-center course">
                  <input :class="{ 'top-15' : avIndex > 0 }" v-model="avNeed.Name" type="text">
                  <div class="invalid-feedback form-error" v-if="avNeed.error">AV Need is required.</div>
                </div>
                <div :class="{ 'remov-btn' : avIndex > 0 }">
                  <span
                    href="#"
                    class="remove-input"
                    v-if="avIndex > 0"
                    @click="removeAvNeeds(avIndex)"
                    icon="delete"
                  >remove</span>
                  <button
                    type="button"
                    v-if="avIndex + 1 === avNeeds.length"
                    :class="{ 'top-15' : avIndex > 0 }"
                    @click="addAvNeeds"
                    class="btn-add"
                  >
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
                              <path d="M12,-7.02216063e-15 L12,24" id="Line"></path>
                              <path
                                d="M12,0.48 L12,23.52"
                                id="Line-Copy"
                                transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                              ></path>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </button>
                </div>
              </div>
            </label>
          </div>
          <div class="col-2">
            <label class="form-block">
              <span>AV Prov</span>
              <div
                v-for="(avPro, avProsIndex) in avPros"
                v-bind:key="avProsIndex"
                class="course-flex"
              >
                <div class="row v-center course">
                  <input :class="{ 'top-15' : avProsIndex > 0 }" v-model="avPro.Name" type="text">
                  <div class="invalid-feedback form-error" v-if="avPro.error">AV Pro is required.</div>
                </div>
                <div :class="{ 'remov-btn' : avProsIndex > 0 }">
                  <span
                    href="#"
                    class="remove-input"
                    v-if="avProsIndex > 0"
                    @click="removeAddPros(avProsIndex)"
                    icon="delete"
                  >remove</span>
                  <button
                    type="button"
                    v-if="avProsIndex + 1 === avPros.length"
                    :class="{ 'top-15' : avProsIndex > 0 }"
                    @click="addPros"
                    class="btn-add"
                  >
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
                              <path d="M12,-7.02216063e-15 L12,24" id="Line"></path>
                              <path
                                d="M12,0.48 L12,23.52"
                                id="Line-Copy"
                                transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                              ></path>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </button>
                </div>
              </div>
            </label>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import { api } from "../../config";
import { siteUrl } from "./../../config";
import moment from "moment";
import { mapGetters } from "vuex";
export default {
  props: ["view", "form", "v", "editModal"],
  data() {
    return {
      siteUrl: siteUrl,
      instructors: [],
      avNeeds: [],
      avPros: [],
      moment: moment,
      noPrefixExist: false,
      courseResults: "",
      blockRemoval: true,
      courseDateModal: true,
      courseRegistrationDateModal: true,
      startTimeIntervals: [],
      endTimeIntervals: []
    };
  },
  computed: mapGetters(["hasEventData"]),
  watch: {
    $props: {
      handler() {
        if (this.editModal) {
          if (this.form.instructor_name) {
            this.instructors = this.form.instructor_name;
          }

          if (this.form.av_needs) {
            this.avNeeds = this.form.av_needs;
          }

          if (this.form.av_pro) {
            this.avPros = this.form.avPros;
          }

          this.form.courses.instructor_name = this.instructors;

          this.form.courses.av_needs = this.avNeeds;

          this.form.courses.av_pro = this.avPros;
        } else {
          this.form.instructors = this.instructors;
          this.form.avPros = this.avPros;
          this.form.avNeeds = this.avNeeds;
        }
      },
      deep: true,
      immediate: true
    },
    instructors() {
      this.blockRemoval = this.instructors.length <= 1;
    },
    avNeeds() {
      this.blockAvNeedsRemoval = this.avNeeds.length <= 1;
    },
    addPros() {
      this.blockAvProRemoval = this.avPros.length <= 1;
    }
  },
  methods: {
    seletcedPrefix(id) {
      if (this.form.prefix) {
        window.axios
          .post(api.coursesSearch, { search_value: this.form.prefix })
          .then(response => {
            if (response.data.length > 0) {
              this.noPrefixExist = false;
              this.courseResults = response.data;
            } else {
              this.noPrefixExist = true;
              this.courseResults = [];
            }
          });
      }
    },
    selectCourse(id) {
      window.axios.get(api.editCourse + id).then(response => {
        this.form.course_id = response.data.id;
        this.form.prefix = response.data.prefix;
        this.form.number = response.data.next_avl;
        this.form.name = response.data.name;
        this.courseResults = [];
        this.noPrefixExist = false;
      });
    },
    addInstructor() {
      var i;
      for (i = 0; i < this.instructors.length; i++) {
        if (
          this.instructors[i].instructorName === null ||
          this.instructors[i].instructorName === ""
        ) {
          this.instructors[i].error = true;
          this.form.avNeeds = this.avNeeds;
          return;
        } else {
          this.instructors[i].error = false;
        }
      }
      let checkEmptyInstructors = this.instructors.filter(
        instructor => instructor.instructorName === ""
      );
      if (checkEmptyInstructors.length >= 1 && this.instructors.length > 0)
        return;
      this.instructors.push({
        instructorName: null,
        error: false
      });
      this.form.instructors = this.instructors;
    },
    removeInstructor(instructorId) {
      if (!this.blockRemoval) this.instructors.splice(instructorId, 1);
      this.form.instructors = this.instructors;
    },
    addAvNeeds() {
      var i;
      for (i = 0; i < this.avNeeds.length; i++) {
        if (this.avNeeds[i].Name === null || this.avNeeds[i].Name === "") {
          this.avNeeds[i].error = true;
          this.form.avNeeds = this.avNeeds;
          return;
        } else {
          this.avNeeds[i].error = false;
        }
      }

      let checkEmptyAvNeeds = this.avNeeds.filter(avNeed => avNeed.Name === "");
      if (checkEmptyAvNeeds.length >= 1 && this.avNeeds.length > 0) return;
      this.avNeeds.push({
        Name: null,
        error: false
      });

      this.form.avNeeds = this.avNeeds;
    },
    removeAvNeeds(avNeedId) {
      if (!this.blockAvNeedsRemoval) this.avNeeds.splice(avNeedId, 1);
      this.form.avNeeds = this.avNeeds;
    },
    addPros() {
      var i;
      for (i = 0; i < this.avPros.length; i++) {
        if (this.avPros[i].Name === null || this.avPros[i].Name === "") {
          this.avPros[i].error = true;
          this.form.avPros = this.avPros;
          return;
        } else {
          this.avPros[i].error = false;
        }
      }

      let checkEmptyavPros = this.avPros.filter(avPro => avPro.Name === "");
      if (checkEmptyavPros.length >= 1 && this.avPros.length > 0) return;
      this.avPros.push({
        Name: null,
        error: false
      });

      this.form.avPros = this.avPros;
    },
    removeAddPros(avProId) {
      if (!this.blockAvProRemoval) this.avPros.splice(avProId, 1);
      this.form.avPros = this.avPros;
    },
    courseDate() {
      this.courseDateModal = false;
    },
    regRegsitrationCourseDate() {
      this.courseRegistrationDateModal = false;
    },
    regCourseDate() {}
  },
  mounted() {
    this.addInstructor();
    this.addAvNeeds();
    this.addPros();
    if (this.hasEventData.start_time !== null) {
      this.startTimeIntervals = this.hasEventData.start_time;
    }
    if (this.hasEventData.end_time !== null) {
      this.endTimeIntervals = this.hasEventData.end_time;
    }
  }
};
</script>
<style>
.remove-input {
  font-size: 10px;
  margin-left: 4px;
  margin-top: 12px;
}
.top-15 {
  margin-top: 15px;
}
.no-margin {
  margin: unset;
}
.course-date input {
  width: 95%;
  border-right: 0.5px solid #cddbe3 !important;
  font-size: 11px;
}
.row.v-center.course {
  display: block;
}
.btn-add {
  height: 35px;
  margin-top: 12px;
}
.course-flex {
  display: flex;
}
.remov-btn {
  display: flex;
  padding: 17px;
}
.prefix-options {
  list-style-type: none;
  padding: 0px;
  margin-top: 0px;
  display: block;
  border: 1px solid;
  border: 0.5px solid #cddbe3;
  background: #e6eef1;
  padding: 0p;
}
.prefix-options li {
  padding: 10px;
  color: #636981;
  font-size: 0.75rem;
  font-weight: 700;
  font-size: 15px;
  border-bottom: 0.5px solid #cddbe3;
}
.course-dates {
  font-size: 13px;
}
</style>

