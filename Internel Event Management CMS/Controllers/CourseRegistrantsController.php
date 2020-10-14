<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRegistrantShowRequest;
use App\Http\Requests\CourseRegistrantStoreRequest;
use App\Http\Requests\CourseRegistrantEditRequest;
use App\Http\Requests\CourseRegistrantDeleteRequest;
// request
use Illuminate\Http\Request;
use App\CourseRegistrants;
use App\User;
use App\Attendance;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class CourseRegistrantsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/course_registrants/show",
     *     description="view all registrants",
     *      tags={"Course Registrants"},
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
        //get all registrant
        $registrants = CourseRegistrants::with('users', 'courseDetails', 'courseDetails.course')->get();
        return response()->json($registrants, 200);
    }

    /**
     * @SWG\Get(
     *     path="/course_registrants/show/{id}",
     *     description="view registrants",
     *      tags={"Course Registrants"},
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
    public function show(CourseRegistrantShowRequest $request) {

        $validated = $request->validated();

        //view specific Registrants
        $registrants = CourseRegistrants::with('users', 'courseDetails', 'courseDetails.attendance')->get();
        return response()->json(compact('registrants'), 400);
    }

    /**
     * @SWG\Post(
     *      path="/course_registrants/store",
     *      tags={"Course Registrants"},
     *      operationId="Save",
     *      summary="save registrants",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="course_detail_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="user_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="joining_status",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(CourseRegistrantStoreRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $registrants = CourseRegistrants::where('user_id', $request->input('user_id'))
                ->where('course_detail_id', $request->input('course_detail_id'))
                ->get();
        if ($registrants->isNotEmpty()) {
            $error = 'User Already Exist.';
            return response()->json(compact('error'));
        }

        //create registrant
        $registrants = new CourseRegistrants;
        $registrants->course_detail_id = $request->input('course_detail_id');
        $registrants->user_id = $request->input('user_id');
        $registrants->joining_status = $request->input('joining_status');
        $registrants->save();

        //register attendace for user
        $attendance = new Attendance;
        $attendance->user_id = $request->input('user_id');
        $attendance->joining_status = 0;
        $attendance->att_date_time = date('Y-m-d H:i:s');
        $attendance->attendanceable_id = $request->input('course_detail_id');
        $attendance->attendanceable_type = 'App\CourseDetail';
        $attendance->save();

        //success message
        $sucess = 'Registrant Created Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/course_registrants/edit",
     *      tags={"Course Registrants"},
     *      operationId="edit",
     *      summary="edit registrants",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="course_detail_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="user_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="joining_status",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(CourseRegistrantEditRequest $request) {


        //validator
        $validated = $request->validated();


        //check record exist
        $registrants = CourseRegistrants::find($request->id);
        if (empty($registrants)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        $registrants->course_detail_id = $request->input('course_detail_id');
        $registrants->user_id = $request->input('user_id');
        $registrants->joining_status = $request->input('joining_status');
        $registrants->save();

        //success message
        $sucess = 'Registrant Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/course_registrants/delete/{id}",
     *     description="delete registrants",
     *      tags={"Course Registrants"},
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
    public function delete(CourseRegistrantDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $registrants = CourseRegistrants::find($request->id);
        if (!empty($registrants)) {
            $course_detail_id = $registrants->course_detail_id;
            $user_id = $registrants->user_id;
            Attendance::where('attendanceable_id', $course_detail_id)->where('user_id', $user_id)->delete();
            $registrants->delete();
            $sucess = 'Registrant Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
