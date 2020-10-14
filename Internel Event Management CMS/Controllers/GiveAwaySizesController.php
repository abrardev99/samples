<?php

namespace App\Http\Controllers;

use App\Http\Requests\GiveAwaySizeShowRequest;
use App\Http\Requests\GiveAwaySizeStoreRequest;
use App\Http\Requests\GiveAwaySizeEditRequest;
use App\Http\Requests\GiveAwaySizeDeleteRequest;
use Illuminate\Http\Request;
use App\GiveAwaySize;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;

class GiveAwaySizesController extends Controller {

    /**
     * @SWG\Get(
     *     path="/give_away_sizes/show",
     *     description="view All Give Aways",
     *     tags={"Give Away Sizes"},
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
        //get all give away sizes
        $give_away_sizes = GiveAwaySize::all();
        return response()->json($give_away_sizes, 200);
    }

    /**
     * @SWG\Get(
     *     path="/give_away_sizes/show/{id}",
     *     description="view specific give away",
     *     tags={"Give Away Sizes"},
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
    public function show(GiveAwaySizeShowRequest $request) {

        //validator
        $validated = $request->validated();


        //view specific give away
        $give_away_size = GiveAwaySize::find($request->id);
        if (!empty($give_away_size)) {
            return response()->json($give_away_size, 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/give_away_sizes/store",
     *     tags={"Give Away Sizes"},
     *      operationId="Save",
     *      summary="save course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="giveaway_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="x_small",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="small",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="medium",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="large",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="two_x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="three_x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="media",
     *          in="formData",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(GiveAwaySizeStoreRequest $request) {

        //validator
        $validated = $request->validated();

        //create give away sizes
        $give_away_sizes = new GiveAwaySize;
        $give_away_sizes->giveaway_id = $request->input('giveaway_id');
        $give_away_sizes->x_small = $request->input('x_small');
        $give_away_sizes->small = $request->input('small');
        $give_away_sizes->medium = $request->input('medium');
        $give_away_sizes->large = $request->input('large');
        $give_away_sizes->x_large = $request->input('x_large');
        $give_away_sizes->two_x_large = $request->input('two_x_large');
        $give_away_sizes->three_x_large = $request->input('three_x_large');

        if (!empty($request->file('media'))) {
            $image = $request->file('media');

            $imageName = $image->getClientOriginalName();
            $fileName = uniqid() . '-' . $imageName;
            $directory = public_path('/images/');
            $imageUrl = $directory . $fileName;
            \Image::make($image)->resize(250, 250)->save($imageUrl);
            $image->move(public_path('original'), $fileName);

            $give_away_sizes->media = $fileName;
        }

        $give_away_sizes->save();

        //success message
        $sucess = 'Give Away Created Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/give_away_sizes/edit",
     *     tags={"Give Away Sizes"},
     *      operationId="edit",
     *      summary="Edit Give Away Sizes",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="giveaway_size_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="giveaway_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="x_small",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="small",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="medium",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="large",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="two_x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="three_x_large",
     *          in="formData",
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="media",
     *          in="formData",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(GiveAwaySizeEditRequest $request) {

        //validator
        $validated = $request->validated();

        //check record exist
        $give_away_sizes = GiveAwaySize::find($request->giveaway_size_id);
        if (empty($give_away_sizes)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        //update give away sizes
        $give_away_sizes->giveaway_id = $request->input('giveaway_id');


        $give_away_sizes->x_small = $request->has('x_small') ? $request->input('x_small') : NULL;
        $give_away_sizes->small = $request->has('small') ? $request->input('small') : NULL;
        $give_away_sizes->medium = $request->has('medium') ? $request->input('medium') : NULL;
        $give_away_sizes->large = $request->has('large') ? $request->input('large') : NULL;
        $give_away_sizes->x_large = $request->has('x_large') ? $request->input('x_large') : NULL;
        $give_away_sizes->two_x_large = $request->has('two_x_large') ? $request->input('two_x_large') : NULL;
        $give_away_sizes->three_x_large = $request->has('three_x_large') ? $request->input('three_x_large') : NULL;

        if (!empty($request->file('media'))) {

            $image = $request->file('media');
            $imageName = $image->getClientOriginalName();
            $fileName = uniqid() . '-' . $imageName;
            $directory = public_path('/images/');
            $imageUrl = $directory . $fileName;
            \Image::make($image)->resize(250, 250)->save($imageUrl);
            $image->move(public_path('original'), $fileName);

            $give_away_sizes->media = $fileName;
        }

        $give_away_sizes->save();

        //success message
        $sucess = 'Give Away Updated Successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/give_away_sizes/delete/{id}",
     *     description="Delete Give Away Sizes",
     *     tags={"Give Away Sizes"},
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
    public function delete(GiveAwaySizeDeleteRequest $request) {

        //validator
        $validated = $request->validated();

        $give_away_size = GiveAwaySize::find($request->id);
        if (!empty($give_away_size)) {
            $give_away_size->delete();
            $sucess = 'Give Away Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

}
