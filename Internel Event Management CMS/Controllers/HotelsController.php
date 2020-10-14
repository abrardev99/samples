<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelShowRequest;
use App\Http\Requests\HotelStoreRequest;
use App\Http\Requests\HotelEditRequest;
use App\Http\Requests\HotelDeleteRequest;
use Illuminate\Http\Request;
use App\Hotel;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class HotelsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/hotel/show",
     *     description="View All hotels",
     *     tags={"Hotels"},
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
        //get all Hotels
        $hotels = Hotel::with(['city', 'states'])->get();
        return response()->json($hotels, 200);
    }

    /**
     * @SWG\Get(
     *     path="/hotel/show/{id}",
     *     description="View hotel",
     *      tags={"Hotels"},
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
    public function show(HotelShowRequest $request) {

        //validator
        $validated = $request->validated();

        //view specific hotel
        $hotel = Hotel::with('city', 'states')->where('id', $request->id)->get();
        if (!empty($hotel)) {
            return response()->json($hotel, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/hotel/store",
     *      tags={"Hotels"},
     *      operationId="Save",
     *      summary="save Hotel",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="hotelable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="hotelable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="name",
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
     *          name="address",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          description="user phone",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="start",
     *          in="formData",
     *          description="start date",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="end",
     *          in="formData",
     *          description="end date",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="lat",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="long",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="remarks",
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
    public function store(HotelStoreRequest $request) {

        //validator
        $validated = $request->validated();

        switch ($request->input('hotelable_type')) {
            case "event":
                $hotelable_type = 'App\Event';
                break;
            case "course":
                $hotelable_type = 'App\CourseDetail';
                break;
        }

        //create Hotel
        $hotel = new \App\Hotel();
        $hotel->hotelable_id = $request->input('hotelable_id');
        $hotel->hotelable_type = $hotelable_type;
        $hotel->name = $request->input('name');
        $hotel->state_id = $request->input('state_id');
        $hotel->city_id = $request->input('city_id');
        $hotel->start = $request->input('start');
        $hotel->end = $request->input('end');
        $hotel->address = $request->input('address');
        $hotel->zip = $request->input('zip');
        $hotel->phone = $request->input('phone');
        $hotel->lat = $request->input('lat');
        $hotel->long = $request->input('long');
        $hotel->remarks = $request->input('remarks');
        $hotel->save();

        //success message
        $sucess = 'Hotel Created Successfully! ';
        return response()->json(compact('sucess', 'hotel'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/hotel/edit",
     *      tags={"Hotels"},
     *      operationId="edit hotel",
     *      summary="edit hotel",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="hotel_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="hotelable_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="hotelable_type",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="name",
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
     *          name="address",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="zip",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          description="user phone",
     *           required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="start",
     *          in="formData",
     *          description="start date",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="end",
     *          in="formData",
     *          description="end date",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="lat",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="long",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="remarks",
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
    public function edit(HotelEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $hotel = Hotel::find($request->hotel_id);
        if (empty($hotel)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }


        switch ($request->input('hotelable_type')) {
            case "event":
                $hotelable_type = 'App\Event';
                break;
            case "course":
                $hotelable_type = 'App\CourseDetail';
                break;
        }



        //update Hotel
        // $hotel = new Hotel;
        $hotel->hotelable_id = $request->input('hotelable_id');
        $hotel->hotelable_type = $hotelable_type;
        $hotel->name = $request->input('name');
        $hotel->state_id = $request->input('state_id');
        $hotel->city_id = $request->input('city_id');
        $hotel->start = $request->input('start');
        $hotel->end = $request->input('end');
        $hotel->address = $request->input('address');
        $hotel->zip = $request->input('zip');
        $hotel->phone = $request->input('phone');
        $hotel->lat = $request->input('lat');
        $hotel->long = $request->input('long');
        $hotel->remarks = $request->input('remarks');
        $hotel->save();

        //success message
        $sucess = 'Hotel Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/hotel/delete/{id}",
     *     description="Delete User",
     *      tags={"Hotels"},
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
    public function delete(HotelDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $hotel = Hotel::find($request->id);
        if (!empty($hotel)) {
            $hotel->delete();
            $sucess = 'Hotel Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
