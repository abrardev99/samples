<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserSearchRequest;
use Illuminate\Http\Request;
use App\User;
use App\ExternalUser;
use App\EventRegistrant;
use App\CourseRegistrants;
use App\Helpers\RegistrationHelper;
use App\Helpers\UserHelper;

class UsersController extends Controller {

    /**
     * @SWG\Post(
     *     path="/users/show/internal",
     *     description="View All Internal User",
     *     tags={"users"},
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
    public function getAllInternalUsers(Request $request) {

        $final = array();
        $start = ($request->input('page') - 1) * 10;
        $limit = $request->input('per_page');

        if (!empty($request->input('sort'))) {
            $sortColName = $request->input('sort')[0]['name'];
        } else {
            $sortColName = 'id';
        }

        if (!empty($request->input('sort'))) {
            $sortColAscDesc = $request->input('sort')[0]['order'];
        } else {
            $sortColAscDesc = 'ASC';
        }

        $users = User::with(['internal_participants.states', 'internal_participants.cities'])
                ->skip($start)
                ->take($limit)
                ->where('role_id', '=', 2)
                ->where('status', '!=', 'deleted')
                ->orderBy($sortColName, $sortColAscDesc)
                ->get();
        $final['data'] = $users;

        $final['total_records'] = User::where('role_id', '=', 2)->where('status', '!=', 'deleted')->count();

        $final['current_page'] = $request->input('page');

        return response()->json($final, 200);
    }

    /**
     * @SWG\Post(
     *     path="/users/show/event/external",
     *     description="View All External User",
     *     tags={"users"},
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
    public function getAllEventExternalUsers(Request $request) {

        $final = array();
        $start = ($request->input('page') - 1) * 10;
        $limit = $request->input('per_page');

        if (!empty($request->input('sort'))) {
            $sortColName = $request->input('sort')[0]['name'];
        } else {
            $sortColName = 'id';
        }

        if (!empty($request->input('sort'))) {
            $sortColAscDesc = $request->input('sort')[0]['order'];
        } else {
            $sortColAscDesc = 'ASC';
        }

        $users = EventRegistrant::with(['users', 'users.external_participants', 'events.attendance'])
                ->where('event_id', $request->event_id)
                ->skip($start)
                ->take($limit)
                ->orderBy($sortColName, $sortColAscDesc)
                ->get();

        $final['data'] = $users;

        $final['total_records'] = EventRegistrant::with(['users', 'users.external_participants'])->where('event_id', $request->event_id)
                ->count();

        $final['current_page'] = $request->input('page');

        return response()->json($final, 200);
    }

    /**
     * @SWG\Post(
     *     path="/users/show/course/external",
     *     description="View All External User",
     *     tags={"users"},
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
    public function getAllCourseExternalUsers(Request $request) {

        $final = array();
        $start = ($request->input('page') - 1) * 10;
        $limit = $request->input('per_page');

        if (!empty($request->input('sort'))) {
            $sortColName = $request->input('sort')[0]['name'];
        } else {
            $sortColName = 'id';
        }

        if (!empty($request->input('sort'))) {
            $sortColAscDesc = $request->input('sort')[0]['order'];
        } else {
            $sortColAscDesc = 'ASC';
        }

        $users = CourseRegistrants::with(['users', 'users.external_participants', 'courseDetails', 'courseDetails.course', 'courseDetails.attendance'])
                ->where('course_detail_id', $request->course_detail_id)
                ->skip($start)
                ->take($limit)
                ->orderBy($sortColName, $sortColAscDesc)
                ->get();

        $final['data'] = $users;

        $final['total_records'] = CourseRegistrants::with(['users', 'users.external_participants', 'courseDetails', 'courseDetails.course'])
                ->where('course_detail_id', $request->course_detail_id)
                ->skip($start)
                ->take($limit)
                ->orderBy($sortColName, $sortColAscDesc)
                ->count();

        $final['current_page'] = $request->input('page');

        return response()->json($final, 200);
    }

    /**
     * @SWG\Get(
     *     path="/users/show/internal/{id}",
     *     description="View User",
     *      tags={"users"},
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
     *    security={{"Bearer": {}}},
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
    public function showInternalUser(UserShowRequest $request, $id) {

        $validated = $request->validated();

        $user = User::find($request->id);
        if (!empty($user)) {
            if ($user->status != 'deleted') {
                return UserHelper::get_user($user);
            } else {
                $error = 'No Record Found !';
                return response()->json(compact('error'), 200);
            }
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/store",
     *      tags={"Registration"},
     *      operationId="Save",
     *      summary="Save User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="first_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="last_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          required=true,
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="password_confirmation",
     *          in="formData",
     *          required=true,
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="role_id",
     *          in="formData",
     *          required=true, 
     *          description="User role must be Id assosiated with Roles ", 
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="company",
     *          in="formData",
     *          description="external user", 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="title",
     *          in="formData",
     *          description="external user", 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          description="internal user", 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *           description="internal user", 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="job_title",
     *          in="formData",
     *          description="internal user", 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="marketing_title",
     *          in="formData",
     *          description="internal user", 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="sector",
     *          in="formData",
     *           description="internal user", 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="zip",
     *          description="admin", 
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          required=true, 
     *          description="user status must be (activated, deleted, inactive)",
     *          type="string" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function store(UserStoreRequest $request) {

        $validated = $request->validated();

        $result = RegistrationHelper::registration_validator($request->input('role_id'), $request->all());
        if (!$result) {
            $error = 'Fields are required!';
            return response()->json(compact('error'), 400);
        }

        $inserted = 1;
        $user = new User;
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->phone = $request->input('phone');
        $user->role_id = $request->input('role_id');
        $user->status = $request->input('status');
        $user->save();

        RegistrationHelper::insert_or_update_user_base_on_type($request->input('role_id'), $user->id, $request->all(), $inserted);

        $sucess = 'Registration Completed successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Put(
     *      path="/users/edit",
     *      tags={"users"},
     *      operationId="Edit",
     *      summary="Edit User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="user_id",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *      @SWG\Parameter(
     *          name="first_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="last_name",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true, 
     *          type="string" 
     *      ),
     *      @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          type="string" 
     *      ),
     *        @SWG\Parameter(
     *          name="password_confirmation",
     *          in="formData",
     *          type="string" 
     *      ),
     *       @SWG\Parameter(
     *          name="role_id",
     *          in="formData",
     *          required=true, 
     *          description="User type must be Id assosiated with Roles table", 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="phone",
     *          in="formData",
     *          required=true, 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="company",
     *          in="formData",
     *          description="external user", 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="title",
     *          in="formData",
     *          description="external user", 
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="state_id",
     *          in="formData",
     *          description="internal user", 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="city_id",
     *          in="formData",
     *           description="internal user", 
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="job_title",
     *          in="formData",
     *          description="internal user", 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="marketing_title",
     *          in="formData",
     *          description="internal user", 
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="sector",
     *          in="formData",
     *           description="internal user", 
     *          type="string" 
     *      ),
     *    @SWG\Parameter(
     *          name="zip",
     *          description="admin", 
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          required=true, 
     *          description="user status must be (activated, deleted, inactive)",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function edit(UserEditRequest $request) {

        $validated = $request->validated();

        $result = RegistrationHelper::registration_validator($request->input('role_id'), $request->all());
        if (!$result) {
            $error = 'Fields are required!';
            return response()->json(compact('error'), 400);
        }


        $update = 0;
        $user = User::find($request->user_id);
        if (empty($user)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        if (!empty($request->input('password'))) {

            $this->validate($request, [
                'password' => 'required|min:3|confirmed',
                'password_confirmation' => 'required',
            ]);

            $user->password = bcrypt($request->input('password'));
        }

        $user->phone = $request->input('phone');
        $user->role_id = $request->input('role_id');
        $user->status = $request->input('status');
        $user->save();

        RegistrationHelper::insert_or_update_user_base_on_type($request->input('role_id'), $user->id, $request->all(), $update);

        $sucess = 'User Updated successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Delete(
     *     path="/users/delete/{id}",
     *     description="Delete User",
     *      tags={"users"},
     *    @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true, 
     *          type="number" 
     *     ),
     *  security={{"Bearer": {}}},
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
    public function delete(UserDeleteRequest $request) {

        $validated = $request->validated();

        $user = User::find($request->id);
        if (!empty($user)) {
            $user->status = 'deleted';
            $user->save();
            $sucess = 'User Deleted successfully! ';
            return response()->json(compact('sucess'), 200);
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/search/internal",
     *      tags={"users"},
     *      summary="Search User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="search_value",
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
    public function internal_search(UserSearchRequest $request) {

        $validated = $request->validated();

        $final = array();
        $start = ($request->input('page') - 1) * 10;
        $limit = $request->input('per_page');

        if (!empty($request->input('sort'))) {
            $sortColName = $request->input('sort')[0]['name'];
        } else {
            $sortColName = 'id';
        }

        if (!empty($request->input('sort'))) {
            $sortColAscDesc = $request->input('sort')[0]['order'];
        } else {
            $sortColAscDesc = 'ASC';
        }

        $search = $request->input('search_value');

        $users = User::with(['internal_participants.states', 'internal_participants.cities'])->skip($start)
                ->take($limit)->where('role_id', '=', 2)->where('status', '!=', 'deleted')->where('first_name', 'like', '%' . $search . '%')
                ->orwhere('last_name', 'like', '%' . $search . '%')
                ->get();

        $final['data'] = $users;

        if ($search) {
            $final['total_records'] = count($users);
        } else {
            $final['total_records'] = $this::getCount();
        }

        $final['current_page'] = $request->input('page');

        return response()->json($final);
    }

    /**
     * @SWG\Post(
     *      path="/users/search/external_search",
     *      tags={"users"},
     *      summary="Search User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="search_value",
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
    public function external_search(UserSearchRequest $request) {

        $validated = $request->validated();

        $search = $request->input('search_value');

        if (!empty($search)) {
            $users = User::with(['external_participants.states', 'external_participants.cities'])
                    ->whereRaw("role_id='3'")
                    ->whereRaw("(first_name  like  '%$search%' or last_name like  '%$search%')")
                    ->get();
            return response()->json($users);
        } else {
            $alert = 'No Record Found';
            return response()->json(compact('alert'), 200);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/search/external_searchs",
     *      tags={"users"},
     *      summary="Search User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="search_value",
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
    public function external_searches(UserSearchRequest $request) {

        $validated = $request->validated();

        $final = array();
        $start = ($request->input('page') - 1) * 10;
        $limit = $request->input('per_page');

        if (!empty($request->input('sort'))) {
            $sortColName = $request->input('sort')[0]['name'];
        } else {
            $sortColName = 'id';
        }

        if (!empty($request->input('sort'))) {
            $sortColAscDesc = $request->input('sort')[0]['order'];
        } else {
            $sortColAscDesc = 'ASC';
        }

        $search = $request->input('search_value');

        if ($request->input('course_detail_id')) {
            $users = CourseRegistrants::with(['users', 'users.external_participants'])->whereHas('users.external_participants', function($query) use($search) {
                        $query->where('first_name', 'like', '%' . $search . '%');
                        $query->orwhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->where('course_detail_id', '=', $request->input('course_detail_id'))
                    ->skip($start)
                    ->take($limit)
                    ->get();
        } else {
            $users = EventRegistrant::with(['users', 'users.external_participants'])->whereHas('users.external_participants', function($query) use($search) {
                        $query->where('first_name', 'like', '%' . $search . '%');
                        $query->orwhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->where('event_id', '=', $request->input('event_id'))
                    ->skip($start)
                    ->take($limit)
                    ->get();
        }

        $final['data'] = $users;

        if ($search) {
            $final['total_records'] = count($users);
        } else {

            if ($request->input('course_detail_id')) {
                $final['total_records'] = CourseRegistrants::with(['users', 'users.external_participants'])
                        ->where('course_detail_id', '=', $request->input('course_detail_id'))
                        ->skip($start)
                        ->take($limit)
                        ->count();
            } else {
                $final['total_records'] = EventRegistrant::with(['users', 'users.external_participants'])
                        ->where('event_id', '=', $request->input('event_id'))
                        ->skip($start)
                        ->take($limit)
                        ->count();
            }
        }

        $final['current_page'] = $request->input('page');

        return response()->json($final);
    }

    public function getCount() {
        return User::where('role_id', '=', 2)->where('status', '!=', 'deleted')->count();
    }

    public function getTotalEventRegistCount() {
        return User::where('role_id', '=', 2)->where('status', '!=', 'deleted')->count();
    }

}
