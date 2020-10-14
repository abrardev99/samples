<?php

namespace App\Http\Controllers;

use App\PayBonus;
use App\RefPayment;
use App\User;
use Illuminate\Http\Request;

class RefPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
    public function store($jAptLtr_id , $jAptLtr_exact_salary , $user_id ,$emp_post_job_id, $starterBonus , $refByUserLevel1Id,
                 $level1Bonus , $refByUserLevel2Id , $level2Bonus)
    {
        $refPay = new RefPayment();
        $refPay->jappt_ltr_id = $jAptLtr_id;
        $refPay->exact_salary = $jAptLtr_exact_salary;
        $refPay->starter_user_id = $user_id;
        $refPay->emp_post_job_id = $emp_post_job_id;
        $refPay->starter_bonus = $starterBonus;
        $refPay->level1_user_id = $refByUserLevel1Id;
        $refPay->level1_bonus = $level1Bonus;
        $refPay->level2_user_id = $refByUserLevel2Id;
        $refPay->level2_bonus = $level2Bonus;
        $refPay->isPaid = false;
        $refPay->save();

    }


    public function show($refPaymentId)
    {
        $refPay = RefPayment::find($refPaymentId);

        $starter_user_id = $refPay->starter_user_id;
        $level1_user_id =$refPay->level1_user_id;
        $level2_user_id =$refPay->level2_user_id;

        $level1_user_bank = null;
        $level2_user_bank = null;

        $starter_user = User::find($starter_user_id)->bankDetail;
        if($level1_user_id != null){
            $level1_user_bank = User::find($level1_user_id)->bankDetail;
        }

        if($level2_user_id != null){
            $level2_user_bank = User::find($level2_user_id)->bankDetail;
        }


        $payBonusStarter = null;
        $payBonusLevel1 = null;
        $payBonusLevel2 = null;
        if(PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $starter_user_id)->exists()){
            $payBonusStarter = PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $starter_user_id)->get()->first();

        }
        if(PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $level1_user_id)->exists()){
            $payBonusLevel1 = PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $level1_user_id)->get()->first();
        }
        if(PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $level2_user_id)->exists()){
            $payBonusLevel2 = PayBonus::where('ref_pay_id', $refPay->id)->where('topay_user_id' , $level2_user_id)->get()->first();
        }



        return view('admin.billing_refpayments' , compact('refPay' , 'starter_user' , 'level1_user_bank'
        , 'level2_user_bank' , 'payBonusStarter' , 'payBonusLevel1' , 'payBonusLevel2'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RefPayment  $refPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(RefPayment $refPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RefPayment  $refPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RefPayment $refPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RefPayment  $refPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(RefPayment $refPayment)
    {
        //
    }
}
