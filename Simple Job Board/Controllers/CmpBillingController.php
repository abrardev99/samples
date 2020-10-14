<?php

namespace App\Http\Controllers;

use App\CmpBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CmpBillingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }


    public function index($emp_post_job_id , $apptltr_id)
    {
        return view('admin.billing_cmp' , compact('emp_post_job_id' , 'apptltr_id'));
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


    public function store(Request $request)
    {
        $user = Auth::user();
        $cmpBilling = new CmpBilling();
        $cmpBilling->emp_post_job_id = $request->emp_post_job_id;
        $cmpBilling->apptltr_id = $request->apptltr_id;
        $cmpBilling->invoice_no = $request->invoice_no;
        $cmpBilling->sent_date = $request->sent_date;
        $cmpBilling->invoice_value = $request->invoice_value;
        $cmpBilling->paid_date = $request->paid_date;
        $cmpBilling->cheque_no = $request->cheque_no;
        $cmpBilling->confirmed = $request->confirmed;
        $user->cmpBilling()->save($cmpBilling);
        $request->session()->flash('status', 'Invoice Details Updated Successfully');
        return redirect('dashboard/admin/billing');
    }


    public function show($cmpBillingId)
    {
        $cmpBilling = CmpBilling::find($cmpBillingId);
        return view('admin.billing_cmp' , compact('cmpBilling'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CmpBilling  $cmpBilling
     * @return \Illuminate\Http\Response
     */
    public function edit(CmpBilling $cmpBilling)
    {
        //
    }


    public function update(Request $request, $cmpBillingId)
    {
        $cmpBilling = CmpBilling::find($cmpBillingId);
        $cmpBilling->invoice_no = $request->invoice_no;
        $cmpBilling->sent_date = $request->sent_date;
        $cmpBilling->invoice_value = $request->invoice_value;
        $cmpBilling->paid_date = $request->paid_date;
        $cmpBilling->cheque_no = $request->cheque_no;
        $cmpBilling->confirmed = $request->confirmed;
        $cmpBilling->save();
        $request->session()->flash('status', 'Invoice Details Updated Successfully');
        return redirect('dashboard/admin/billing');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CmpBilling  $cmpBilling
     * @return \Illuminate\Http\Response
     */
    public function destroy(CmpBilling $cmpBilling)
    {
        //
    }
}
