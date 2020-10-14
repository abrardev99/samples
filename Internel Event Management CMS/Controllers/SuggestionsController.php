<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeatShowRequest;
use App\Http\Requests\SeatStoreRequest;
use Illuminate\Http\Request;
use App\User;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class SuggestionsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/suggestion",
     *     description="view all suggestion",
     *     tags={"Suggestion"},
     *    @SWG\Parameter(
     *          name="role_name",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *     ),
     *   @SWG\Parameter(
     *          name="value",
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
    public function show(Request $request) {

        //get all seat
//        $seat = Role::with('users', 'course_details')->get();

//        return response()->json($seat, 200);
    }

}
