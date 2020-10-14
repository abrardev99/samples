<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRole;
use App\Http\Requests\AssignRole;
use Illuminate\Http\Request;
use App\Country;
use App\State;
use App\City;

class LocationController extends Controller {

    /**
     * @SWG\POST(
     *     path="/location/countries",
     *     description="View All Countries",
     *     tags={"Location"},
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
    public function countries(Request $request) {
        $countries = Country::limit(10)->get();
        return response()->json($countries, 200);
    }

    /**
     * @SWG\POST(
     *     path="/location/states",
     *     description="View All Countries",
     *     tags={"Location"},
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
    public function states(Request $request) {
        $states = State::limit(10)->get();
        return response()->json($states, 200);
    }

    /**
     * @SWG\POST(
     *     path="/location/cities",
     *     description="View All Countries",
     *     tags={"Location"},
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
    public function cities(Request $request) {
        $cities = City::limit(10)->get();
        return response()->json($cities, 200);
    }

    /**
     * @SWG\POST(
     *     path="/location/search/states",
     *     description="View All States",
     *     tags={"Location"},
     *    @SWG\Parameter(
     *          name="name",
     *          in="path",
     *          required=true, 
     *          type="string" 
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
    public function search_states(Request $request) {

        $states = State::where('name', $request->name)->get();
        return response()->json($states, 200);
    }

    /**
     * @SWG\POST(
     *     path="/location/search/cities",
     *     description="View All Cities",
     *     tags={"Location"},
     *     @SWG\Parameter(
     *          name="name",
     *          in="path",
     *          required=true, 
     *          type="string" 
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
    public function search_cities(Request $request) {

        $cities = City::where('name', $request->name)->get();
        return response()->json($cities, 200);
    }

}
