<?php

namespace App\Http\Controllers;

//request

use App\Http\Requests\EventShowRequest;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventEditRequest;
use App\Http\Requests\EventDeleteRequest;
use App\Http\Requests\RegisterEventCourse;
//end of request
use Illuminate\Http\Request;
use App\Event;
use App\Seat;
use App\ManageEvent;
use App\RegisterCourse;
use App\EventRegistrant;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;
use App\Helpers\TableHelper;

class EventsController extends Controller {

    const type = 'event';

    /**
     * @SWG\Get(
     *     path="/event/details/{id}",
     *     description="View All events",
     *     tags={"Events"},
     *    @SWG\Parameter(
     *          name="id",
     *          description="Id is the status active or not active you can pass 1 or 0",
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
    public function getAll(Request $request) {
        //get all events
        $events = Event::with(['tables', 'courseDetails', 'courseDetails.course', 'state', 'city', 'foods'])->where('status', $request->id)->get();
        foreach ($events as $index => $event) {
            $registrants = EventRegistrant::where('event_id', $event->id)->get();
            if ($registrants->isNotEmpty()) {
                $event->registrants = count($registrants);
            } else {
                $event->registrants = 0;
            }
        }

        return response()->json($events, 200);
    }

    /**
     * @SWG\Get(
     *     path="/event/show/{id}",
     *     description="View event",
     *      tags={"Events"},
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
    public function show(EventShowRequest $request) {
        $validated = $request->validated();
        //view specific event
        $event = Event::with(['tables', 'hotels', 'foods', 'foods.city', 'foods.states', 'giveaways', 'giveaways.sizes', 'giveaways.city', 'giveaways.states', 'hotels.city', 'hotels.states', 'courseDetails', 'courseDetails.course','courseDetails.courseRegistrants','courseDetails.city', 'courseDetails.states', 'state', 'city','tables'])->find($request->id);
        if (!empty($event)) {
            if(!empty($event->tables)){
               foreach($event->tables as $table){
                   $table->seats = Seat::where('table_id', $table->id)->get();
               }     
            }
            return response()->json($event, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/event/store",
     *      tags={"Events"},
     *      operationId="Save",
     *      summary="save event",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="event_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="reg_start_date",
     *          in="formData",
     *          required=true, 
     *          description="Registration start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="reg_end_date",
     *          in="formData",
     *          required=true, 
     *          description="Registration end date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="start_date",
     *          in="formData",
     *          required=true, 
     *          description="start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end_date",
     *          in="formData",
     *          required=true, 
     *          description="end date",
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="details",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          description="user status must be (1 or 0)",
     *           required=true, 
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(EventStoreRequest $request) {

        //validator
        $validated = $request->validated();

        //create event
        $event = new Event;
        $event->name = $request->input('event_name');
        $event->state_id = $request->input('state_id');
        $event->city_id = $request->input('city_id');
        $event->reg_start_date = $request->input('reg_start_date');
        $event->reg_end_date = $request->input('reg_end_date');
        $event->start_date = $request->input('start_date');
        $event->end_date = $request->input('end_date');
        $event->details = $request->input('details');
        $event->status = $request->input('status');
        $event->seats = 5;
        $event->save();

        //success message
        $sucess = 'Event Created Successfully! ';

        $response = array(
            'sucess' => $sucess,
            'event_id' => $event->id
        );

        return response()->json(compact('response'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/event/register/course",
     *      tags={"Events"},
     *      operationId="RegisterEventCourse",
     *      summary="Register Event Course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="course_detail_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="event_id",
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
    public function register_courses(RegisterEventCourse $request) {

        //validator
        $validated = $request->validated();

        //create event
        $register_event_course = new RegisterCourse;
        $register_event_course->course_detail_id = $request->input('course_detail_id');
        $register_event_course->event_id = $request->input('event_id');
        $register_event_course->save();

        //success message
        $sucess = 'Course Register Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/event/edit",
     *      tags={"Events"},
     *      operationId="edit",
     *      summary="edit event",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="event_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="event_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="reg_start_date",
     *          in="formData",
     *          required=true, 
     *          description="Registration start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="reg_end_date",
     *          in="formData",
     *          required=true, 
     *          description="Registration end date",
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="start_date",
     *          in="formData",
     *          required=true, 
     *          description="start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end_date",
     *          in="formData",
     *          required=true, 
     *          description="end date",
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="details",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          description="user status must be (1 or 0)",
     *           required=true, 
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(EventEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $event = Event::find($request->event_id);
        if (empty($event)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        //update event
        $event->name = $request->input('event_name');
        $event->state_id = $request->input('state_id');
        $event->city_id = $request->input('city_id');
        $event->reg_start_date = $request->input('reg_start_date');
        $event->reg_end_date = $request->input('reg_end_date');
        $event->start_date = $request->input('start_date');
        $event->end_date = $request->input('end_date');
        $event->details = $request->input('details');
        $event->status = $request->input('status');
        $event->save();

        //success message
        $sucess = 'Event Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/event/delete/{id}",
     *     description="Delete User",
     *      tags={"Events"},
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
    public function delete(EventDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $event = Event::find($request->id);
        if (!empty($event)) {
            $event->delete();
            $sucess = 'Event Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
