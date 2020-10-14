<?php

namespace App\Http\Controllers;

//request
use App\Http\Requests\SeatShowRequest;
use App\Http\Requests\SeatStoreRequest;
use App\Http\Requests\SeatDeleteRequest;
use App\Http\Requests\SeatEditRequest;
//end of request
use Illuminate\Http\Request;
use App\Seat;
use App\Table;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class SeatsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/seat/show",
     *     description="view all Seats",
     *     tags={"Seats"},
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
        //get all seat
        $seat = Seat::with('users', 'table')->get();
        return response()->json($seat, 200);
    }

    /**
     * @SWG\Get(
     *     path="/seat/show/{id}",
     *     description="view All Seats",
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
     *     tags={"Seats"},
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
    public function show(SeatShowRequest $request) {
        //validator
        $validated = $request->validated();

        //view specific Seat
        $seat = Seat::find($request->id);
        if (!empty($seat)) {
            $seat->users;
            $seat->table;
            return response()->json($seat, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/seat/store",
     *      tags={"Seats"},
     *      operationId="Save",
     *      summary="save course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="table_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="user_id",
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
     *      @SWG\Parameter(
     *          name="description",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     * 
     *       @SWG\Parameter(
     *          name="status",
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
    public function store(SeatStoreRequest $request) {
        $validated = $request->validated();

        $seat_data = array(
            'user_id' => $request->input('user_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'table_id' => $request->input('table_id'),
            'user_id' => $request->input('user_id'),
        );


        $table_exist = Table::find($request->input('table_id'));
        if (empty($table_exist)) {
            $error = 'Table Not Exist ! ';
            return response()->json(compact('error'), 400);
        }

        $seat = Seat::where('table_id', $request->input('table_id'))->get();
        if (!$seat->isNotEmpty()) {
            if ($table_exist->seat_qty > 0) {
                $this->store_seat($seat_data);
                $sucess = 'seat register successfully! ';
                return response()->json(compact('sucess'), 200);
            }
        }

        //check seats availabilty
        if ($this->check_seat_availability($request->input('table_id'))) {
            $this->store_seat($seat_data);
            $sucess = 'seat register successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'seats limit exceed ! ';
            return response()->json(compact('error'), 400);
        }
    }

    //edit

    /**
     * @SWG\Post(
     *      path="/seat/edit",
     *      tags={"Seats"},
     *      operationId="Edit",
     *      summary="Edit course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="table_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="user_id",
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
     *      @SWG\Parameter(
     *          name="description",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     * 
     *       @SWG\Parameter(
     *          name="status",
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
    public function edit(SeatEditRequest $request) {

        $validated = $request->validated();

        $seat = Seat::find($request->id);
        if (empty($seat)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        $seat->table_id = $request->input('table_id');
        $seat->user_id = $request->input('user_id');
        $seat->name = $request->input('name');
        $seat->description = $request->input('description');
        $seat->status = $request->input('status');
        $seat->save();

        $sucess = 'Seat updated successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/seat/delete/{id}",
     *     description="Delete seat",
     *      tags={"Seats"},
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
    public function delete(SeatDeleteRequest $request) {
        $validated = $request->validated();
        $seat = Seat::find($request->id);
        if (!empty($seat)) {
            $seat->delete($seat->id);
            $sucess = 'Seat Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    //seats availability
    function check_seat_availability($table_id) {
        $seat = Seat::where('table_id', $table_id)->get();
        $table_seat_qty = Table::find($table_id);
        if ($seat->isNotEmpty()) {
            $seat_count = count($seat);

            //check if both equal
            if ($seat_count == $table_seat_qty->seat_qty) {
                return false;
            }
            //check if one exceeded
            if ($table_seat_qty->seat_qty > $seat_count) {
                return true;
            } else {
                return false;
            }
        }
    }

    //store seats
    function store_seat($seat_data) {
        Seat::create($seat_data);
    }

}
