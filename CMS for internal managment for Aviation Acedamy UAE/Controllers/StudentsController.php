<?php

namespace App\Http\Controllers;

use App\Students;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $students = Students::all();
        return view('students', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('create-student');
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required'],
        ]);

        if($request->filled('email')){
            $request->validate([
                'email' => ['unique:students'],
            ]);
        }

        //        upload photo

        $imageName = null;
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('user/photos'), $imageName);
        }

        $user = Auth::user();

        $student = new Students();
        $student->name = $request->name;
        $student->gender = $request->gender;
        $student->dob = Carbon::parse($request->dob)->toDateString();
        $student->email = $request->email;
        $student->mobile = $request->mobile;
        $student->photo = $imageName;
        $user->students()->save($student);

        if($request->from == 'assignment'){
            toast('Student Added Successfully', 'success');
            return redirect()->back();
        }
        toast('Student Added Successfully', 'success');
        return redirect('user/students');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Students  $students
     * @return \Illuminate\Http\Response
     */
    public function show(Students $students)
    {
        //
    }


    public function edit(Students $student)
    {
        return view('update-student', compact('student'));
    }

    public function update(Request $request, Students $student)
    {
        $request->validate([
            'name' => ['required'],
        ]);

        if ($request->filled('email')){
            $request->validate([
                'email' => [Rule::unique('students')->ignore($student)]

            ]);
        }

        $student->name = $request->name;
        $student->gender = $request->gender;
        $student->dob = Carbon::parse($request->dob)->toDateString();
        $student->email = $request->email;
        $student->mobile = $request->mobile;


        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('user/photos'), $imageName);

            $student->photo = $imageName;
        }

        $student->save();
        toast('Student Updated Successfully', 'success');
        return redirect('user/students');
    }

    public function destroy(Students $student)
    {
        if ($student->delete()) {
            toast('Student and related Assessments Deleted Successfully', 'success');
            return redirect()->back();
        } else {
            toast('Unable to delete', 'error');
            return redirect()->back();
        }
    }
}
