<?php

namespace App\Http\Controllers;

//validation request
use App\Http\Requests\CourseDetailShowRequest;
use App\Http\Requests\CourseDetailStoreRequest;
use App\Http\Requests\CourseDetailEditRequest;
use App\Http\Requests\CourseDetailDeleteRequest;
//end of validation request
use Illuminate\Http\Request;
use App\Course;
use App\CourseDetail;
use App\ManageEvent;
use App\Seat;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;
use App\Helpers\TableHelper;


class CourseDetailsController extends Controller {

    const type = 'course';

    /**
     * @SWG\Get(
     *     path="/course/details/show",
     *     description="view all courses details",
     *     tags={"Course Details"},
     *     security={{"Bearer": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */
    public function getAll(Request $request) {
        //get all courses details
        $course = CourseDetail::with(['tables', 'course', 'event'])->get();
        return response()->json($course, 200);
    }

    /**
     * @SWG\Get(
     *    path="/course/details/show/{id}",
     *    description="view specific course details",
     *     tags={"Course Details"},
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
     *    security={{"Bearer": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */
    public function show(CourseDetailShowRequest $request) {
        $validated = $request->validated();
        //view specific Course details
        $course = CourseDetail::with(['tables', 'course', 'foods', 'foods.city', 'foods.states', 'giveaways', 'giveaways.city', 'giveaways.states', 'hotels', 'hotels.city', 'hotels.states','tables'])->find($request->id);
        if (!empty($course)) {
            if(!empty($course->tables)){
               foreach($course->tables as $table){
                   $table->seats = Seat::where('table_id', $table->id)->get();
               }     
            }
            return response()->json($course, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/course/details/store",
     *     tags={"Course Details"},
     *      operationId="Save",
     *      summary="save course details",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="course_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="reg_start_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="reg_end_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="start_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="end_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="start_time",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end_time",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="facility_name",
     *          in="formData",
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="room_number",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="prerequisites",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="instructor_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="av_needs",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="av_pro",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="details",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="credit_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="semester",
     *          in="formData",
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="duration_type",
     *          in="formData",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(CourseDetailStoreRequest $request) {

        //validator
        $validated = $request->validated();

        //create event
        $course = new CourseDetail;
        $course->course_id = $request->input('course_id');
        $course->reg_start_date = $request->input('reg_start_date');
        $course->reg_end_date = $request->input('reg_end_date');
        $course->start_date = $request->input('start_date');
        $course->end_date = $request->input('end_date');
        $course->start_time = $request->input('start_time');
        $course->end_time = $request->input('end_time');
        $course->facility_name = $request->input('facility_name');
        $course->city_id = $request->input('city_id');
        $course->address = $request->input('address');
        $course->state_id = $request->input('state_id');
        $course->zip = $request->input('zip');
        $course->room_number = $request->input('room_number');
        $course->prerequisites = $request->input('prerequisites');
        $course->instructor_name = json_encode($request->input('instructor_name'));
        $course->av_needs = json_encode($request->input('av_needs'));
        $course->av_pro = json_encode($request->input('av_pro'));
        $course->details = $request->input('details');
        $course->credit_type = $request->input('credit_type');
        $course->duration_type = $request->input('duration_type');
        $course->semester = $request->input('semester');
        $course->seats = $request->input('seats');
        $course->save();


        //success message
        $sucess = 'Course Register Successfully! ';
        return response()->json(compact('sucess', 'course'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/course/details/edit",
     *     tags={"Course Details"},
     *      operationId="edit",
     *      summary="edit Course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="course_detail_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="course_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="reg_start_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="reg_end_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="start_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="end_date",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="start_time",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end_time",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="facility_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="room_number",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *  @SWG\Parameter(
     *          name="prerequisites",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="instructor_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="av_needs",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="av_pro",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="details",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="credit_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="semester",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="duration_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(CourseDetailEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check course exist
        $course = CourseDetail::find($request->course_detail_id);
        if (empty($course)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }
        $course->course_id = $request->input('course_id');
        $course->reg_start_date = $request->input('reg_start_date');
        $course->reg_end_date = $request->input('reg_end_date');
        $course->start_date = $request->input('start_date');
        $course->end_date = $request->input('end_date');
        $course->start_time = $request->input('start_time');
        $course->end_time = $request->input('end_time');
        $course->facility_name = $request->input('facility_name');
        $course->city_id = $request->input('city_id');
        $course->address = $request->input('address');
        $course->state_id = $request->input('state_id');
        $course->zip = $request->input('zip');
        $course->room_number = $request->input('room_number');
        $course->prerequisites = $request->input('prerequisites');
        $course->instructor_name = json_encode($request->input('instructor_name'));
        $course->av_needs = json_encode($request->input('av_needs'));
        $course->av_pro = json_encode($request->input('av_pro'));
        $course->details = $request->input('details');
        $course->credit_type = $request->input('credit_type');
        $course->duration_type = $request->input('duration_type');
        $course->semester = $request->input('semester');
        $course->seats = $request->input('seats');
        $course->save();

        //success message
        $sucess = 'Course Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *    path="/course/details/delete/{id}",
     *    description="delete course detail" ,
     *     tags={"Course Details"},
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
     *  security={{"Bearer": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */
    public function delete(CourseDetailDeleteRequest $request) {
        $validated = $request->validated();
        $course = CourseDetail::find($request->id);
        if (!empty($course)) {
            $course->delete();
            $sucess = 'Course Detail Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
