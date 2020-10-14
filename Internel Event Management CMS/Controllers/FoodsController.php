<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodShowRequest;
use App\Http\Requests\FoodStoreRequest;
use App\Http\Requests\FoodEditRequest;
use App\Http\Requests\FoodDeleteRequest;
use Illuminate\Http\Request;
use App\Food;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class FoodsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/food/show",
     *     description="view All food",
     *     tags={"Food"},
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
        $food = Food::all();
        return response()->json($food, 200);
    }

    /**
     * @SWG\Get(
     *     path="/food/show/{id}",
     *     description="view food",
     *      tags={"Food"},
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
    public function show(FoodShowRequest $request) {

        //validator
        $validated = $request->validated();

        //view specific Course
        $food = Food::with('city', 'states')->where('id', $request->id)->get();
        if (!empty($food)) {
            return response()->json($food, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/food/store",
     *      tags={"Food"},
     *      operationId="Save",
     *      summary="save course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="foodable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="foodable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="caterer",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
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
     *          name="date",
     *          in="formData",
     *          required=true, 
     *          description="start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end",
     *          in="formData",
     *          required=true, 
     *          description="end date",
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          description="Phone",
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(FoodStoreRequest $request) {

        //validator
        $validated = $request->validated();

        switch ($request->input('foodable_type')) {
            case "event":
                $foodable_type = 'App\Event';
                break;
            case "course":
                $foodable_type = 'App\CourseDetail';
                break;
        }

        //create event
        $food = new Food;
        $food->foodable_id = $request->input('foodable_id');
        $food->foodable_type = $foodable_type;
        $food->caterer = $request->input('caterer');
        $food->address = $request->input('address');
        $food->state_id = $request->input('state_id');
        $food->city_id = $request->input('city_id');
        $food->zip = $request->input('zip');
        $food->date = $request->input('date');
        $food->phone = $request->input('phone');
        $food->save();

        //success message
        $sucess = 'Food Register Successfully! ';
        return response()->json(compact('sucess', 'food'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/food/edit",
     *      tags={"Food"},
     *      operationId="edit",
     *      summary="edit event",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="food_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="foodable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="foodable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="caterer",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
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
     *          name="strat",
     *          in="formData",
     *          required=true, 
     *          description="start date",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="end",
     *          in="formData",
     *          required=true, 
     *          description="end date",
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          description="Phone",
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(FoodShowRequest $request) {

        //validator
        $validated = $request->validated();


        switch ($request->input('foodable_type')) {
            case "event":
                $foodable_type = 'App\Event';
                break;
            case "course":
                $foodable_type = 'App\CourseDetail';
                break;
        }

        //check record exist
        $food = Food::find($request->food_id);
        if (empty($food)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        //create event
        $food->foodable_id = $request->input('foodable_id');
        $food->foodable_type = $foodable_type;
        $food->caterer = $request->input('caterer');
        $food->address = $request->input('address');
        $food->state_id = $request->input('state_id');
        $food->city_id = $request->input('city_id');
        $food->zip = $request->input('zip');
        $food->date = $request->input('date');
        $food->phone = $request->input('phone');
        $food->save();


        //success message
        $sucess = 'Food Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/food/delete/{id}",
     *     description="Delete Food",
     *      tags={"Food"},
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
    public function delete(FoodShowRequest $request) {

        //validator
        $validated = $request->validated();

        $food = Food::find($request->id);
        if (!empty($food)) {
            $food->delete();
            $sucess = 'Food Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
