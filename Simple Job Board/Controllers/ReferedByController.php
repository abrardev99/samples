<?php

namespace App\Http\Controllers;

use App\ReferedBy;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferedByController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user)
    {
        $refBy = new ReferedBy();
        $refBy->referred_by_code = $request->referred_by_code;
        $refBy->referred_by_user_id = DB::table('users')->where('ref_code', $request->referred_by_code)->value('id');

        $user->referrers()->save($refBy);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReferedBy  $referedBy
     * @return \Illuminate\Http\Response
     */
    public function show(ReferedBy $referedBy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReferedBy  $referedBy
     * @return \Illuminate\Http\Response
     */
    public function edit(ReferedBy $referedBy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReferedBy  $referedBy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReferedBy $referedBy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReferedBy  $referedBy
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReferedBy $referedBy)
    {
        //
    }
}
