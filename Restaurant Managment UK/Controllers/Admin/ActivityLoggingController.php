<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class ActivityLoggingController extends Controller
{
    public function index()
    {
        if (! Gate::allows('users_manage')) {
            return abort(401);
        }
        
        $activities = Activity::orderByDesc('id')->paginate(100);
        return view('admin.logging.index', compact('activities'));
    }

    public function destroy()
    {
        if (!Gate::allows('users_manage')) {
            return abort(401);
        }

        Artisan::call('activitylog:clean');

        toast('Logs older then a year has been deleted');
        return redirect()->route('admin.logging.activities', 'success');
    }
}
