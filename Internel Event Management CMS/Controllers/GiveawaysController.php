<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiveAwayShowRequest;
use App\Http\Requests\GiveAwayStoreRequest;
use App\Http\Requests\GiveAwayEditRequest;
use App\Http\Requests\GiveAwayDeleteRequest;
use Illuminate\Http\Request;
use App\Giveaway;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class GiveawaysController extends Controller {

    /**
     * @SWG\Get(
     *     path="/give_away/show",
     *     description="view All Give Away",
     *     tags={"Give Away"},
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
        //get all give aways
        $give_away = Giveaway::all();
        return response()->json($give_away, 200);
    }

    /**
     * @SWG\Get(
     *     path="/give_away/show/{id}",
     *     description="view course",
     *     tags={"Give Away"},
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
    public function show(GiveAwayShowRequest $request) {

        $validated = $request->validated();

        //view specific Give away
        $give_away = Giveaway::with(['sizes', 'city', 'states'])->where('id', $request->id)->get();
        if (!empty($give_away)) {
            return response()->json($give_away, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/give_away/store",
     *     tags={"Give Away"},
     *      operationId="Save",
     *      summary="save course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *   @SWG\Parameter(
     *          name="giveawayable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="giveawayable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="item",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="quantity",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="sizes",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="vendor",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="items",
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
    public function store(GiveAwayStoreRequest $request) {

        //validator
        $validated = $request->validated();

        switch ($request->input('giveawayable_type')) {
            case "event":
                $giveawayable_type = 'App\Event';
                break;
            case "course":
                $giveawayable_type = 'App\CourseDetail';
                break;
        }
        //create event
        $give_away = new Giveaway;
        $give_away->giveawayable_id = $request->input('giveawayable_id');
        $give_away->giveawayable_type = $giveawayable_type;
        $give_away->state_id = $request->input('state_id');
        $give_away->city_id = $request->input('city_id');
        $give_away->zip = $request->input('zip');
        $give_away->phone = $request->input('phone');
        $give_away->items = $request->input('items');
        $give_away->quantity = $request->input('quantity');
        $give_away->sizes = $request->input('sizes') ? 1 : 0;
        $give_away->vendor = $request->input('vendor');
        $give_away->address = $request->input('address');
        $give_away->save();

        //success message
        $sucess = 'Give Away Added Successfully! ';
        return response()->json(compact('sucess', 'give_away'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/give_away/edit",
     *      tags={"Give Away"},
     *      operationId="edit",
     *      summary="Edit Give Away",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="give_away_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="giveawayable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="giveawayable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="address",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="item",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="quantity",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *  @SWG\Parameter(
     *          name="sizes",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="vendor",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="items",
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
    public function edit(GiveAwayEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check Give Away exist
        $give_away = Giveaway::find($request->give_away_id);
        if (empty($give_away)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        switch ($request->input('giveawayable_type')) {
            case "event":
                $giveawayable_type = 'App\Event';
                break;
            case "course":
                $giveawayable_type = 'App\CourseDetail';
                break;
        }



        $give_away->giveawayable_id = $request->input('giveawayable_id');
        $give_away->giveawayable_type = $giveawayable_type;
        $give_away->state_id = $request->input('state_id');
        $give_away->city_id = $request->input('city_id');
        $give_away->zip = $request->input('zip');
        $give_away->phone = $request->input('phone');
        $give_away->items = $request->input('items');
        $give_away->quantity = $request->input('quantity');
        $give_away->sizes = $request->input('sizes');
        $give_away->vendor = $request->input('vendor');
        $give_away->address = $request->input('address');
        $give_away->save();

        //success message
        $sucess = 'Give Away Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/give_away/delete/{id}",
     *     description="Delete Give Away",
     *     tags={"Give Away"},
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
    public function delete(GiveAwayDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $give_away = Giveaway::find($request->id);
        if (!empty($give_away)) {
            $give_away->delete();
            $sucess = 'Give Away Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
