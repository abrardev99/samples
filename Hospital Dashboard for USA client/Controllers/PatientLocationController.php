<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use App\Traits\FavouriteMetricsTrait;
use App\Patient_location;
use App\Patient_type;

class PatientLocationController extends Controller
{
    use FavouriteMetricsTrait;
 
    public function index()
    {
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $data = DB::table(Patient_location::getTableName().' as pl')
                ->join(Patient_type::getTableName().' as pt','pl.patienttypeid','=','pt.id')
                ->select('pl.*', 'pt.description')
                ->get();
        
        return datatables()->of($data)
        ->addColumn('action', 'action_button')
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
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $locationId = $request->location_id;
        $location =  Patient_location::updateOrCreate(
            ['id' => $locationId],
            [
                'code' => $request->location_code, 
                'location' => $request->location_name,
                'patienttypeid' => $request->patienttypeid,
                'numofbeds' => $request->numofbeds
            ]);
        
        $location->save();
        
        return response ()->json($location);
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
        $location  = Patient_location::where($where)->first();
        
        return response ()->json($location);
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $location = Patient_location::where('id',$id)->delete();
     
        return response ()->json($location);
    }
}
