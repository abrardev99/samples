<?php

namespace App\Http\Controllers;

use App\BankDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'rolePrivilege']);
    }

    public function index()
    {
        $bank = Auth::user()->bankDetail;

        return view('jobseeker.bank_details' , compact('bank'));

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
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_no' => ['required', 'numeric'],
        ]);

        $user = Auth::user();
        $bank = new BankDetails();
        $bank->name = $request->name;
        $bank->bank_name = $request->bank_name;
        $bank->account_no = $request->account_no;
        $user->bankDetail()->save($bank);

        return redirect('bank')->with('saved' , 'Bank Details Saved Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function show(BankDetails $bankDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(BankDetails $bankDetails)
    {
        //
    }

    public function update(Request $request, $bankDetailsId)
    {
        $bankDetails = BankDetails::find($bankDetailsId);
        $bankDetails->name = $request->name;
        $bankDetails->bank_name = $request->bank_name;
        $bankDetails->account_no = $request->account_no;
        $bankDetails->save();
        return redirect('bank')->with('saved' , 'Bank Details updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankDetails  $bankDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankDetails $bankDetails)
    {
        //
    }
}
