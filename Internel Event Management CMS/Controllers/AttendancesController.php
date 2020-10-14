<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceShowRequest;
use App\Http\Requests\AttendanceStoreRequest;
use App\Http\Requests\AttendanceEditRequest;
use App\Http\Requests\AttendanceDeleteRequest;
use Illuminate\Http\Request;
use App\Attendance;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class AttendancesController extends Controller {

    /**
     * @SWG\Get(
     *     path="/attendance/show",
     *     description="view all attendance",
     *     tags={"Attendance"},
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
        //get all Attendance
        $attendance = Attendance::with('users', 'seats', 'tables','tables.tableable')->get();
        return response()->json($attendance, 200);
    }

    /**
     * @SWG\Get(
     *     path="/attendance/show/{id}",
     *     description="view attendance",
     *      tags={"Attendance"},
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
    public function show(AttendanceShowRequest $request) {

        $validated = $request->validated();

        //view specific Attendance
        $attendance = Attendance::findOrFail($request->id);
        $attendance->users;
        $attendance->seats;
        return response()->json(compact('attendance'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/attendance/store",
     *      tags={"Attendance"},
     *      operationId="Save",
     *      summary="save attendance",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="user_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="table_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="seat_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="att_date_time",
     *          in="formData",
     *          required=true, 
     *          type="string" 
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
    public function store(AttendanceStoreRequest $request) {

        //validator
        $validated = $request->validated();

        // create registrant
        $attendance = new Attendance;
        $attendance->user_id = $request->input('user_id');
        $attendance->table_id = $request->input('table_id');
        $attendance->seat_id = $request->input('seat_id');
        $attendance->att_date_time = $request->input('att_date_time');
        $attendance->joining_status = $request->input('joining_status');
        $attendance->save();

        //success message
        $sucess = 'Attendance Created Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/attendance/edit",
     *      tags={"Attendance"},
     *      operationId="edit",
     *      summary="edit attendance",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="attendance_id",
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
     *          name="table_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="seat_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="att_date_time",
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
    public function edit(AttendanceEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $attendance = Attendance::find($request->attendance_id);
        if (empty($attendance)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        $attendance->user_id = $request->input('user_id');
        $attendance->table_id = $request->input('table_id');
        $attendance->seat_id = $request->input('seat_id');
        $attendance->att_date_time = $request->input('att_date_time');
        $attendance->joining_status = $request->input('joining_status');
        $attendance->save();

        //success message
        $sucess = 'Attendance Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/attendance/delete/{id}",
     *     description="delete attendance",
     *      tags={"Attendance"},
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
    public function delete(AttendanceDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $attendance = Attendance::find($request->id);
        if (!empty($attendance)) {
            $attendance->delete();
            $sucess = 'attendance deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
