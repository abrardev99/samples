<?php

namespace App\Http\Controllers;

use App\ToolTip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolTipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tooltip = ToolTip::where('user_id', Auth::id())->get()->first();
        return view('admin.tooltips.index', compact('tooltip'));
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
    public function store(Request $request)
    {
        ToolTip::updateOrCreate([
            'user_id' => Auth::id(),
        ],[
           'user_id' => Auth::id(),
            'tooltip-trixFields' => request('tooltip-trixFields'),
            'attachment-tooltip-trixFields' => request('attachment-tooltip-trixFields'),
        ]);

        toast('Tooltip updates successfully', 'success');
        return redirect()->route('admin.tooltip.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ToolTip  $toolTip
     * @return \Illuminate\Http\Response
     */
    public function show(ToolTip $toolTip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ToolTip  $toolTip
     * @return \Illuminate\Http\Response
     */
    public function edit(ToolTip $toolTip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ToolTip  $toolTip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ToolTip $toolTip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ToolTip  $toolTip
     * @return \Illuminate\Http\Response
     */
    public function destroy(ToolTip $toolTip)
    {
        //
    }
}
