<?php

namespace App\Http\Controllers;

//auth request
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller {

    /**
     * @SWG\Post(
     *      path="/authenticate",
     *      tags={"Login"},
     *      operationId="authenticate",
     *      summary="Login User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          required=true,
     *          type="string" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function authenticate(AuthRequest $request) {

        $validated = $request->validated();

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid login credential'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $user = $request->user();

        return response()->json(compact('token', 'user'));
    }

}
