<?php

namespace App\Http\Controllers;

use App\Config_items;
use App\Mail\SendMobileAccessMail;
use App\User;

use App\Traits\FavouriteMetricsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use FavouriteMetricsTrait;
    
    public function index(){
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $data = User::all();
        return datatables()->of($data)
            ->addColumn('action','user_action_button')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request){

        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $user_id = $request->user_id;
        $isAdd = isset($user_id) ? false : true;
        $item  = Config_items::get()->first();
        
        $pass = 'required|min:6';
        if( $item != null ){
            if($item->enable === true ){
                $pass = 'required|min:'. $item->password_min_character;
                if($item->password_alphanumeric === true){
                    $pass = $pass. '|alpha_num';
                }
            }
        }
        $config = [
            'password' => $pass
        ];
        
        $rules = [];
        if( $isAdd ){
            // new user
            $rules = User::rules($config);
        } else {
            // update
            if(empty($request->password)){
                $config = [];
            }
            $rules = User::rules($config, $user_id);
        }
         
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response ()->json($validator->errors(), 422);
        } else {
            $user = null;
	        if( $isAdd ){
                $remember_token = str_random(10);
                $user =  User::updateOrCreate(
                    ['id' => $user_id],
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'remember_token' => $remember_token,
	                    'changed_password' => $request->changed_password,
                        'user_role' => $request->user_role,
                        'lock'=>$request->lock,
                        'inactive'=>$request->inactive,
                        'mobile_app_access'=>$request->mobile_app_access
                    ]);

		        if($request->mobile_app_access == 1) {
			        $this->sendMail($request);
		        }

                $this->setMetricDefault($user->id);
            } else {

		        $user = User::find($user_id);
		        $user_mobile_app_access= $user->mobile_app_access;

		        if(empty($request->password)){
                    $user =  User::updateOrCreate(
                        ['id' => $user_id],
                        [
                            'name' => $request->name,
                            'email' => $request->email,
                            'user_role' => $request->user_role,
	                        'changed_password' => $request->changed_password,
                            'lock'=>$request->lock,
                            'inactive'=>$request->inactive,
	                        'mobile_app_access'=>$request->mobile_app_access
                        ]);
                }else{
                    $user =  User::updateOrCreate(
                        ['id' => $user_id],
                        [
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => bcrypt($request->password),
	                        'changed_password' => $request->changed_password,
                            'user_role' => $request->user_role,
                            'lock'=>$request->lock,
                            'inactive'=>$request->inactive,
	                        'mobile_app_access'=>$request->mobile_app_access
                        ]);
                }

		        if($request->mobile_app_access != $user_mobile_app_access ){
			        if($request->mobile_app_access == 1) {
				        $this->sendMail($request);
			        }
		        }
            }

            return response ()->json($user);
        }
        
        //return response ()->json($user);
    }

    public function edit($id, Request $request){
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $where = array('id' => $id);
        $user  = User::where($where)->first();

        return response ()->json($user);
    }

    public function delete($id){
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        $user = User::where('id',$id)->delete();

        return response ()->json($user);
    }

   public function changePassword(Request $request){
	   $item  = Config_items::get()->first();
	   $pass = 'required|min:6|confirmed';
	   if( $item != null ){
		   if($item->enable === true ){
			   $pass = 'required|min:'. $item->password_min_character;
			   if($item->password_alphanumeric === true){
				   $pass = $pass. '|alpha_num';
			   }
			   $pass = $pass. '|confirmed';
		   }
	   }

	   $this->validate($request, [
		   'password' => $pass,
	   ]);

	   $user = Auth::user();
	   $user->password = bcrypt($request->password);
	   $user->changed_password = true;
	   $user->save();
	   return redirect('/home');

    }

    public function sendMail(Request $request){
    	$name = $request->name;
    	$email = $request->email;
	    Mail::to($email)->send(new SendMobileAccessMail($name, $email));
    }

}