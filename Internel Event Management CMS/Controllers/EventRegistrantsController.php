<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EventRegistrantShowRequest;
use App\Http\Requests\EventRegistrantStoreRequest;
use App\Http\Requests\EventRegistrantEditRequest;
use App\Http\Requests\EventRegistrantDeleteRequest;
use App\EventRegistrant;
use App\Event;
use App\User;
use App\Attendance;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class EventRegistrantsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/event_registrants/show",
     *     description="view all registrants",
     *      tags={"Event Registrants"},
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
        $registrants = EventRegistrant::with('users', 'events')->get();
        foreach ($registrants as $arr_index => $registrantslist) {
            $user = User::find($registrantslist->user_id);
            if (!empty($user)) {
                if ($user->status != 'deleted') {
                    $registrants[$arr_index]->users[$arr_index] = UserHelper::get_user($user);
                }
            }
        }
        return response()->json($registrants, 200);
    }

    /**
     * @SWG\Get(
     *     path="/event_registrants/show/{id}",
     *     description="view registrants",
     *      tags={"Event Registrants"},
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
    public function show(EventRegistrantShowRequest $request) {

        $validated = $request->validated();

        $event = EventRegistrant::where('event_id', $request->id)->get();
        if ($event->isNotEmpty()) {
            //view specific Registrants
            $registrants = EventRegistrant::find($event[0]->id);
            if (!empty($registrants)) {
                $registrants->users;
                $registrants->events;
            }
            return response()->json(compact('registrants'), 200);
        }
    }

    /**
     * @SWG\Post(
     *      path="/event_registrants/store",
     *      tags={"Event Registrants"},
     *      operationId="Save",
     *      summary="save registrants",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="event_id",
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
    public function store(EventRegistrantStoreRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $registrants = EventRegistrant::where('user_id', $request->input('user_id'))->where('event_id', $request->input('event_id'))->get();
        if ($registrants->isNotEmpty()) {
            $error = 'User Already Exist.';
            return response()->json(compact('error'));
        }

        //create registrant
        $registrants = new EventRegistrant;
        $registrants->event_id = $request->input('event_id');
        $registrants->user_id = $request->input('user_id');
        $registrants->joining_status = $request->input('joining_status');
        $registrants->save();

        //register attendace for user
        $attendance = new Attendance;
        $attendance->user_id = $request->input('user_id');
        $attendance->joining_status = 0;
        $attendance->att_date_time = date('Y-m-d H:i:s');
        $attendance->attendanceable_id = $request->input('event_id');
        $attendance->attendanceable_type = 'App\Event';
        $attendance->save();

        //success message
        $sucess = 'Registrant Created Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/event_registrants/edit",
     *      tags={"Event Registrants"},
     *      operationId="Edit Event Course",
     *      summary="Edit Event Course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="event_id",
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
    public function edit(EventRegistrantEditRequest $request) {


        //validator
        $validated = $request->validated();


        //check record exist
        $registrants = EventRegistrant::find($request->id);
        if (empty($registrants)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        $registrants->event_id = $request->input('event_id');
        $registrants->user_id = $request->input('user_id');
        $registrants->joining_status = $request->input('joining_status');
        $registrants->save();

        //success message
        $sucess = 'Registrant Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/event_registrants/delete/{id}",
     *     description="delete registrants",
     *      tags={"Event Registrants"},
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
    public function delete(EventRegistrantDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $registrants = EventRegistrant::find($request->id);
        if (!empty($registrants)) {
            $event_id = $registrants->event_id;
            $user_id = $registrants->user_id;
            Attendance::where('attendanceable_id', $event_id)->where('user_id', $user_id)->delete();
            $registrants->delete();
            $sucess = 'Registrant Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
