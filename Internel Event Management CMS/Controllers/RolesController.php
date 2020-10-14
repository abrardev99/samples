<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRole;
use App\Http\Requests\AssignRole;
use Illuminate\Http\Request;
use App\Role;
use App\Permission;
use App\Helpers\RegistrationHelper;
use App\Helpers\UserHelper;

class RolesController extends Controller {

    /**
     * @SWG\Get(
     *     path="/role/show",
     *     description="View All role",
     *     tags={"Roles"},
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

        $role = Role::all();

        return response()->json($role, 200);
    }

    /**
     * @SWG\Post(
     *      path="/role/store",
     *      tags={"Roles"},
     *      operationId="Save",
     *      summary="Save Role",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="display_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="description",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(StoreRole $request) {
   
        $validated = $request->validated();

        $role = new Role;
        $role->name = $request->input('name');
        $role->display_name = $request->input('display_name');
        $role->description = $request->input('description');
        $role->save();

        $sucess = 'Role Added successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/role/assign",
     *      tags={"Roles"},
     *      operationId="Assign",
     *      summary="Assign Role",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="role_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="permission_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function assign(AssignRole $request) {

        $validated = $request->validated();

        // find admin role.
        $admin_role = Role::where('name', $request->input('role_name'))->first();

        // atach all permissions to admin role
        $admin_role->permissions()->attach($request->input('permission_id'));

        $sucess = 'Permission Added successfully! ';
        return response()->json(compact('sucess'), 200);
    }

}
