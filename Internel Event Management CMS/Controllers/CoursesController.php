<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseShowRequest;
use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseEditRequest;
use App\Http\Requests\CourseDeleteRequest;
use App\Http\Requests\CourseSearchRequest;
use App\Http\Requests\CourseCountRequest;
use App\Http\Requests\CourseListRequest;
use Illuminate\Http\Request;
use App\Course;
use App\CourseDetail;
use App\RegisterCourse;
use App\ManageEvent;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class CoursesController extends Controller {

    /**
     * @SWG\Get(
     *     path="/course/show",
     *     description="view All course",
     *     tags={"Courses"},
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
        //get all courses
        $events = Course::all();
        return response()->json($events, 200);
    }

    /**
     * @SWG\Get(
     *     path="/course/show/{id}",
     *     description="view course",
     *      tags={"Courses"},
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
    public function show(CourseShowRequest $request) {

        $validated = $request->validated();
        //view specific Course
        $course = Course::find($request->id);
        if (!empty($course)) {
            return response()->json($course, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/course/store",
     *      tags={"Courses"},
     *      operationId="Save",
     *      summary="save course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="prefix",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="next_avl",
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
    public function store(CourseStoreRequest $request) {

        $validated = $request->validated();

        //create event
        $course = new Course;
        $course->name = $request->input('name');
        $course->prefix = $request->input('prefix');
        $course->next_avl = $request->input('next_avl');
        $course->save();

        //success message
        $sucess = 'Course Register Successfully! ';
        return response()->json(compact('sucess', 'course'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/course/edit",
     *      tags={"Courses"},
     *      operationId="edit",
     *      summary="edit Course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="course_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="prefix",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="next_avl",
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
    public function edit(CourseEditRequest $request) {

        $validated = $request->validated();

        //check course exist
        $course = Course::find($request->course_id);
        if (empty($course)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        //update event
        $course->name = $request->input('name');
        $course->prefix = $request->input('prefix');
        $course->next_avl = $request->input('next_avl');
        $course->save();

        //success message
        $sucess = 'Course Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/course/delete/{id}",
     *     description="Delete course",
     *      tags={"Courses"},
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
    public function delete(CourseDeleteRequest $request) {

        $validated = $request->validated();

        $course = Course::find($request->id);
        if (!empty($course)) {
            $course->delete();
            $sucess = 'Course Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/course/search",
     *      tags={"Courses"},
     *      summary="Search Course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="search_value",
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
    public function search(CourseSearchRequest $request) {
        $validated = $request->validated();
        return Course::where('prefix', 'like', '%' . $request->search_value . '%')->get();
    }

    /**
     * @SWG\Get(
     *     path="/event/courses/count/{id}",
     *     description="Course Count",
     *     tags={"Events"},
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          description="event id",
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
    public function get_course_count(CourseCountRequest $request) {
        $validated = $request->validated();
        //get Course Count
        $events_count = RegisterCourse::where('event_id', $request->id)->count();
        return response()->json(json_decode($events_count), 200);
    }

}
