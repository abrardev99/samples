<?php

namespace App\Http\Controllers;

use App\PayBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayBonusController extends Controller
{
    public function payBonusStarter(Request $request){
        $this->validate($request, [
            'payment_statement' => ['required'],
            ]);

        $user = Auth::user();
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);


        $payBonus = new PayBonus();
        $payBonus->topay_user_id = $request->user_id;
        $payBonus->ref_pay_id = $request->ref_pay_id;
        $payBonus->amount = $request->amount;
        $payBonus->payment_statement = $payment_statement;
        $user->payBonus()->save($payBonus);

        $request->session()->flash('saved', 'Payment Statement uploaded Successfully');
        return redirect()->back();
    }


    public function payBonuslevel1(Request $request){
        $this->validate($request, [
            'payment_statement' => ['required'],
            ]);

        $user = Auth::user();
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);


        $payBonus = new PayBonus();
        $payBonus->topay_user_id = $request->user_id;
        $payBonus->ref_pay_id = $request->ref_pay_id;
        $payBonus->amount = $request->amount;
        $payBonus->payment_statement = $payment_statement;
        $user->payBonus()->save($payBonus);

        $request->session()->flash('saved', 'Payment Statement uploaded Successfully');
        return redirect()->back();
    }

    public function payBonusLevel2(Request $request){
        $this->validate($request, [
            'payment_statement' => ['required'],
            ]);

        $user = Auth::user();
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);


        $payBonus = new PayBonus();
        $payBonus->topay_user_id = $request->user_id;
        $payBonus->ref_pay_id = $request->ref_pay_id;
        $payBonus->amount = $request->amount;
        $payBonus->payment_statement = $payment_statement;
        $user->payBonus()->save($payBonus);

        $request->session()->flash('saved', 'Payment Statement Uploaded Successfully');
        return redirect()->back();
    }

//    update

    public function payBonusStarterUpdate(Request $request, $id){
        $payBonus = PayBonus::find($id);
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);

        $payBonus->payment_statement = $payment_statement;
        $payBonus->save();
        $request->session()->flash('saved', 'Payment Statement Updated Successfully');
        return redirect()->back();

    }

    public function payBonusLevel1Update(Request $request, $id){
        $payBonus = PayBonus::find($id);
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);

        $payBonus->payment_statement = $payment_statement;
        $payBonus->save();
        $request->session()->flash('saved', 'Payment Statement Updated Successfully');
        return redirect()->back();
    }

    public function payBonusLevel2Update(Request $request, $id){
        $payBonus = PayBonus::find($id);
        $payment_statement = null;

        $fileName = request()->payment_statement->getClientOriginalName();
        $payment_statement = uniqid() ." " .  $fileName;
        request()->payment_statement->move(public_path('admin_data'), $payment_statement);

        $payBonus->payment_statement = $payment_statement;
        $payBonus->save();
        $request->session()->flash('saved', 'Payment Statement Updated Successfully');
        return redirect()->back();
    }


}
