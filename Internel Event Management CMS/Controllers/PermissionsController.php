<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRole;
use App\Http\Requests\AssignRole;
use Illuminate\Http\Request;
use App\Role;
use App\Permission;
use App\Helpers\RegistrationHelper;
use App\Helpers\UserHelper;

class PermissionsController extends Controller {

    /**
     * @SWG\Get(
     *     path="/permission/show",
     *     description="View All Permission",
     *     tags={"Permission"},
     *     security={{"Bearer": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Missing Data"
     *     )
     * )
     */
    public function show(Request $request) {

        $permission = Permission::all();

        return response()->json($permission, 200);
    }

}
