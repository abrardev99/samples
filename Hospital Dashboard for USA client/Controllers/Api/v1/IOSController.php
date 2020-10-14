<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Traits\ApiMetricsTrait;
use Carbon\Carbon;

class IOSController extends AuthController
{
    use ApiMetricsTrait;
    
    const clientId = "iOS";
    const PRE_PATH_METRIC_SERVICE = "/api/v1/ios/metricservices";
    
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $user = $request->user();
        if( !$user->mobile_app_access ){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse( $tokenResult->token->expires_at)->toDateTimeString()
        ]);
        
    }
    
    public function getAllMetrics(Request $request)
    {
        $metrics = $this->getAllMetricsInfo($request->user());
        
        return response()->json( $metrics );
    }
    
    public function getAllMetricServices(Request $request)
    {
        $metricService = $this->getAllMetricServicesInfo($request->user());
        
        return response()->json( $metricService );
    }
    
    public function getMetricFavorites(Request $request)
    {
        $favorites = $this->getMetricFavoritesInfo($request->user());
        
        return response()->json( $favorites );
    }
    
    public function updateMetricFavorites(Request $request)
    {
        $metric_id = $request->metric_id;
        $user_id = $request->user_id;
        
        $this->registerFavoriteMetric($metric_id, $user_id);
        
        return response()->json([
            'message' => 'Successfully updated favorites'
        ]);
    }
    
    public function getAllHospitals(Request $request)
    {
        $hospitals = $this->getAllHospitalsInfo($request->user());
        
        return response()->json( $hospitals );
    }
}
