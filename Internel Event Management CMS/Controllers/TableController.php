<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableShowRequest;
use App\Http\Requests\TableStoreRequest;
use App\Http\Requests\TableDeleteRequest;
use App\Http\Requests\TableEditRequest;
use Illuminate\Http\Request;
use App\Table;
use App\Seat;
//helpers
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;
use App\Helpers\TableHelper;

class TableController extends Controller {

    /**
     * @SWG\Get(
     *     path="/table/show",
     *     description="view all tables",
     *     tags={"Tables"},
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
        //get all Tables
        $table = Table::all();
        return response()->json($table, 200);
    }

    /**
     * @SWG\Get(
     *     path="/table/show/{id}",
     *     tags={"Tables"},
     *     description="view All tables",
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
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
    public function show(TableShowRequest $request) {
        //validator
        $validated = $request->validated();

        //view specific table 
        $table = Table::find($request->id);
        if (!empty($table)) {
            $table->tableable;
            return response()->json($table, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/table/store",
     *      tags={"Tables"},
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
     *          name="description",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="seat_qty",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="tableable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="tableable_type",
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
    public function edit(TableEditRequest $request) {

        $validated = $request->validated();

        $table = Table::find($request->id);
        if (empty($table)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        if ($request->seat_qty != $table->seat_qty) {
            if ($request->seat_qty > $table->seat_qty) {
                $qty_seats = $request->seat_qty - $table->seat_qty;
                TableHelper::manage_seats($qty_seats, $table->id);
            } else {
                $error = "you can't decrease number of seats that are already assinged to registrants.";
                return response()->json(compact('error'), 400);
            }
        }


        $table->user_id = NULL;
        $table->name = $request->name;
        $table->description = $request->description;
        $table->status = $request->status;
        $table->seat_qty = $request->seat_qty;
        $table->tableable_id = $request->tableable_id;
        $table->tableable_type = $request->tableable_type;
        $table->save();

        $sucess = 'Table updated successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/table/edit",
     *      tags={"Tables"},
     *      operationId="Edit",
     *      summary="Edit course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="description",
     *          in="formData",
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="seat_qty",
     *          in="formData",
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="tableable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="tableable_type",
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
    public function store(TableStoreRequest $request) {
        $validated = $request->validated();
        switch ($request->input('tableable_type')) {
            case "event":
                $tableable_type = 'App\Event';
                break;
            case "course":
                $tableable_type = 'App\CourseDetail';
                break;
        }
         Table::where('tableable_id', $request->tableable_id)->where('tableable_type', $tableable_type)->delete();
        if(!empty($request->seats)){
         foreach($request->seats  as $table_seats_data){
            if(!empty($table_seats_data['totalSeats'])){
                $table = new Table;
                $table->user_id = NULL;
                $table->name = 'table-'.$table_seats_data['id'];
                $table->description = 'table-'.$table_seats_data['id'];
                $table->status = $request->status;
                $table->seat_qty = count($table_seats_data['totalSeats']);
                $table->tableable_id = $request->tableable_id;
                $table->tableable_type = $tableable_type;
                $table->save();
                TableHelper::manage_seats($table_seats_data['totalSeats'], $table->id);
               }
           }
        }
        $sucess = 'Table register successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/table/delete/{id}",
     *     description="Delete table",
     *      tags={"Tables"},
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
    public function delete(TableDeleteRequest $request) {
        $validated = $request->validated();

        $table = Table::find($request->id);
        if (!empty($table)) {
            $table->delete($table->id);
            Seat::where('table_id',$request->id)->delete();
            $sucess = 'Table Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
