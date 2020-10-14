<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use App\Traits\FavouriteMetricsTrait;
use App\Config_items;
use Yajra\DataTables\Facades\DataTables;

class ConfigItemsController extends Controller
{
    use FavouriteMetricsTrait;
 
    public function index()
    {
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $data = Config_items::all();
        
        return datatables()->of($data)
        ->addColumn('action', 'password_action_button')
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        /*
        if( !isset($request->password_id) ){
            $item  = Config_items::get()->first();
            
            if( $item != null ){
                return response ('Error', 499);
            }
        }
        */
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $item =  Config_items::updateOrCreate(
            ['id' => $request->password_id],
            [
                'password_min_character' => $request->pass_minimum, 
                'password_alphanumeric' => $request->pass_alphanumeric,
                'enable' => $request->enable
            ]);
        
        return response ()->json($item);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $where = array('id' => $id);
        $item  = Config_items::where($where)->first();
        
        return response ()->json($item);
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    /*
    public function destroy($id)
    {
        $item = Config_items::where('id',$id)->delete();
     
        return response ()->json($item);
    }
    */
}
