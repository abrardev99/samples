<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationSendRequest;
use App\Http\Requests\InvitationShowRequest;
use App\Http\Requests\InvitationSearchRequest;
use App\Http\Requests\InvitationImportRequest;
use Illuminate\Http\Request;
use App\Invitation;
use App\User;
use App\CourseRegistrants;
use App\CourseDetail;
use App\Hotel;
use App\EventRegistrant;
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;
use DB;

class InvitationsController extends Controller {

    /**
     * @SWG\Post(
     *      path="/users/invite/send",
     *      tags={"Invitation"},
     *      operationId="Send New User Invite",
     *      summary="Send Invite",
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
     *          name="event_id",
     *          in="formData",
     *          type="number" 
     *      ),
     *     @SWG\Parameter(
     *          name="course_id",
     *          in="formData",
     *          type="number" 
     *      ),
     *   @SWG\Parameter(
     *          name="user_id",
     *          in="formData",
     *          required=true, 
     *          description="user id", 
     *          type="number" 
     *      ),
     *    @SWG\Parameter(
     *          name="user_type",
     *          in="formData",
     *          required=true, 
     *          description="this must be 'internal_user' or 'external_user'",
     *          type="string" 
     *      ),
     *     @SWG\Parameter(
     *          name="inviation_type",
     *          in="formData",
     *          description="this must be 'courses' or 'events'",
     *          type="string" 
     *      ),
     *   @SWG\Parameter(
     *          name="status",
     *          in="formData",
     *          required=true, 
     *          description="this must be 'approved' or 'disapproved' or 'pending'",
     *          type="string" 
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function send(InvitationSendRequest $request) {

        //validator
        $validated = $request->validated();

        if (!empty($request->input('course_detail_id'))) {
            $courses = Invitation::where('email', '=', $request->input('email'))->where('course_detail_id', '=', $request->input('course_detail_id'))->first();
            if (!empty($courses)) {
                $error = 'Invitations already send';
                return response()->json(compact('error'), 400);
            }
        }

        if (!empty($request->input('event_id'))) {
            $events = Invitation::where('email', '=', $request->input('email'))->where('event_id', '=', $request->input('event_id'))->first();
            if (!empty($events)) {
                $error = 'Invitations already send';
                return response()->json(compact('error'), 200);
            }
        }

        $user_exist = User::where('email', '=', $request->input('email'))->first();

        //store inviatation
        $invitation = new Invitation;
        $invitation->first_name = $request->input('first_name');
        $invitation->last_name = $request->input('last_name');
        $invitation->email = $request->input('email');

        if (!empty($request->input('course_detail_id'))) {
            $invitation->course_detail_id = $request->input('course_detail_id');
        } else {
            $invitation->course_detail_id = NULL;
        }

        if (!empty($request->input('event_id'))) {
            $invitation->event_id = $request->input('event_id');
        } else {
            $invitation->event_id = NULL;
        }

        if (!empty($request->input('user_id'))) {
            $invitation->user_id = $request->input('user_id');
        } else {
            $invitation->user_id = NULL;
        }

        $invitation->user_type = $request->input('user_type');
        if (empty($user_exist)) {
            $invitation->inviation_type = 'registration';
        } else {
            $invitation->inviation_type = $request->input('inviation_type');
        }

        $invitation->status = $request->input('status');
        $invitation->save();


        if (!empty($user_exist)) {
            //invitation link
            $invitation_url = url('login');

            //email data array
            $email_data = array(
                'to' => $request->input('email'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'status' => $request->input('status'),
                'template' => 'invite_email',
                'title' => 'PSCU Email',
                'body_title' => 'Join Us Here at PSC.',
            );

            $email_data['sender_name'] = $this->get_user_name();

            if ($request->input('inviation_type') == 'events') {
                $email_data['subject'] = 'Invite to Register for Event';
                $email_data['content'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. <a href='$invitation_url' class='link' style='text-decoration: none;color:gray;'>Login</a>";
            } else {
                $email_data['subject'] = 'Invite to Register for course';
                $email_data['content'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. <a href='$invitation_url' class='link' style='text-decoration: none;color:gray;'>Login</a>";
            }

            //email helper function to send email
            EmailHelper::send_email($email_data);
        } else {
            //invitation link
            $invitation_url = $this->get_invitation_registration_path($request->input('user_type'));

            //email data array
            $email_data = array(
                'to' => $request->input('email'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'status' => $request->input('status'),
                'template' => 'invite_email',
                'title' => 'PSCU Email',
                'body_title' => 'Join Us Here at PSC.',
            );

            $email_data['sender_name'] = $this->get_user_name();

            $email_data['subject'] = 'Invite to Registration';
            $email_data['content'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. <a href='$invitation_url' class='link' style='text-decoration: none;color:gray;'>Sign up now</a>";

            //email helper function to send email
            EmailHelper::send_email($email_data);
        }

        //success mesage.
        $sucess = 'Invitation send successfully! ';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/show",
     *      tags={"Invitation"},
     *      operationId="View Invite details",
     *      summary="Invite details",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="Invite Id",
     *          type="number" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function show(InvitationShowRequest $request) {

        //validator
        $validated = $request->validated();

        $inviteDetails = Invitation::find($request->id);
        if (!empty($inviteDetails)) {
            return $inviteDetails;
        } else {
            $error = 'No Record Found !';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/search",
     *      tags={"Invitation"},
     *      operationId="View Invite details",
     *      summary="Invite details by email",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          required=true, 
     *          description="Invite Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function search(InvitationSearchRequest $request) {

        //validator
        $validated = $request->validated();

        $inviteDetails = User::where('first_name', 'like', '%' . $request->search . '%')
                ->orwhere('last_name', 'like', '%' . $request->search . '%')
                ->where('role_id', '!=', 1)
                ->get();

        return response()->json($inviteDetails, 200);
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/course/user",
     *      tags={"Invitation"},
     *      operationId="View Invite details",
     *      summary="Invite details of user course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="user Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function course_user(Request $request) {
        $invitations = Invitation::select('invitation.*', 'users.first_name as fname', 'users.last_name as lname', 'users.email as emails', 'users.role_id', 'courses.name as course_name', 'courses.prefix as course_prefix', 'courses.next_avl as course_next_avl', 'courses_detail.reg_start_date as reg_start_date', 'courses_detail.reg_start_date as reg_end_date', 'courses_detail.start_date as start_date', 'courses_detail.end_date as end_date', 'courses_detail.start_time as start_time', 'courses_detail.end_time as end_time', 'courses_detail.facility_name as facility_name', 'courses_detail.instructor_name', 'courses_detail.details', 'courses_detail.seats', 'courses_detail.address as course_address', 'states.name as state_name', 'cities.name as city_name')
                        ->join('users', 'invitation.user_id', '=', 'users.id')
                        ->join('courses_detail', 'invitation.course_detail_id', '=', 'courses_detail.id')
                        ->join('courses', 'courses_detail.course_id', '=', 'courses.id')
                        ->leftjoin('states', 'courses_detail.state_id', '=', 'states.id')
                        ->leftjoin('cities', 'courses_detail.city_id', '=', 'cities.id')
                        ->where('event_id', NULL)
                        ->where('user_id', $request->id)->get();
        if ($invitations->isNotEmpty()) {
            $invitation_list = array();
            foreach ($invitations as $index => $invitation) {
                $invitation_list[$index] = $invitation;

                $invitation_list[$index]->available_seats = CourseRegistrants::where('course_detail_id', $invitation->course_detail_id)->count();
            }
            return response()->json($invitation_list, 200);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/event/user",
     *      tags={"Invitation"},
     *      operationId="View Events Invite details",
     *      summary="Invite Events Details of user",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="event id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function event_user(Request $request) {
        $invitations = Invitation::select('invitation.*', 'users.first_name as fname', 'users.last_name as lname', 'users.email as emails', 'users.role_id', 'events.name as event_name', 'events.details as event_details', 'events.status as event_status', 'events.reg_start_date as reg_start_date', 'events.reg_end_date as reg_end_date', 'events.start_date as start_date', 'events.end_date as end_date', 'events.seats as event_seats', 'states.name as state_name', 'cities.name as city_name')
                        ->join('users', 'invitation.user_id', '=', 'users.id')
                        ->leftJoin('events', 'invitation.event_id', '=', 'events.id')
                        ->leftjoin('states', 'events.state_id', '=', 'states.id')
                        ->leftjoin('cities', 'events.city_id', '=', 'cities.id')
                        ->where('course_detail_id', NULL)
                        ->where('user_id', $request->id)->get();
        if ($invitations->isNotEmpty()) {
            $invitation_list = array();
            foreach ($invitations as $index => $invitation) {
                $invitation_list[$index] = $invitation;
                $invitation_list[$index]->total_courses = DB::table('event_courses')->where('event_id', $invitation->event_id)->count();
                $invitation_list[$index]->available_seats = EventRegistrant::where('event_id', $invitation->event_id)->count();
                $hotel_name = Hotel::select('name')->where('hotelable_id', $invitation->event_id)->where('hotelable_type', 'App\Event')->first();
                $invitation_list[$index]->hotel_name = NULL;
                if (!empty($hotel_name)) {
                    $invitation_list[$index]->hotel_name = $hotel_name->name;
                }
            }
            return response()->json($invitation_list, 200);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/enroll",
     *      tags={"Invitation"},
     *      operationId="Enroll User",
     *      summary="Enroll User",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="user Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function enroll(Request $request) {

        $rules = [
            'id' => 'required',
        ];

        $this->validate($request, $rules);

        $invitation = Invitation::find($request->id);
        if (empty($invitation)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }

        switch ($invitation->inviation_type) {
            case 'courses':
                $courseRegistrants = new CourseRegistrants;
                $courseRegistrants->course_detail_id = $invitation->course_detail_id;
                $courseRegistrants->user_id = $invitation->user_id;
                $courseRegistrants->joining_status = 1;
                $courseRegistrants->save();
                break;
            case 'events':
                $eventRegistrant = new EventRegistrant;
                $eventRegistrant->event_id = $invitation->event_id;
                $eventRegistrant->user_id = $invitation->user_id;
                $eventRegistrant->joining_status = 1;
                $eventRegistrant->save();
                break;
        }

        $created_date = date('Y-m-d H:i:s', strtotime($invitation->created_at));
        $current_date = date('Y-m-d H:i:s');
        $diff = abs(strtotime($current_date) - strtotime($created_date));
        $diff_days = round($diff / 60 / 60 / 24);
        $diff_minutes = round($diff / 60, 2);

        //check if difference is less then 10 days.
        if ($diff_days > 10) {
            //success message
            $error = 'Invitation link expired';
            return response()->json(compact('error'), 400);
        } else {
            $invitation->status = 'approved';
            $invitation->save();

            //success message
            $sucess = 'Invitation Enrolled Successfully! ';
            return response()->json(compact('sucess'), 200);
        }
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/withdraw",
     *      tags={"Invitation"},
     *      operationId="Withdraw Invitation",
     *      summary="Withdraw Invitation",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="Invitation Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function withdraw(Request $request) {

        $rules = [
            'id' => 'required',
        ];

        $this->validate($request, $rules);

        $invitation = Invitation::find($request->id);
        if (empty($invitation)) {
            $error = 'No Record Found!';
            return response()->json(compact('error'));
        }
        $invitation->status = 'pending';
        $invitation->save();

        //success message
        $sucess = 'Invitation Withdraw Successfully!';
        return response()->json(compact('sucess'), 200);
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/importcsv",
     *      tags={"Invitation"},
     *      operationId="import csv for Invite",
     *     consumes={"multipart/form-data"},
     *     produces={"text/plain, application/json"},
     *      @SWG\Parameter(
     *          name="csv",
     *          in="formData",
     *          required=true, 
     *          type="file" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function importcsv(Request $request) {

        //validator
//        $validated = $request->validated();

        $file = $request->file('csv');

        // File Details 
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {

                // File upload location
                $location = 'uploads';

                // Upload file
                $file->move($location, $filename);

                // Import CSV to Database
                $filepath = public_path($location . "/" . $filename);

                // Reading file
                $file = fopen($filepath, "r");

                $importData_arr = array();
                $i = 0;

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata);

                    // Skip first row (Remove below comment if you want to skip the first row)
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
                    for ($c = 0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                }
                fclose($file);

                // Insert to MySQL database
                foreach ($importData_arr as $importData) {
                    $insertData = array(
                        "first_name" => $importData[0],
                        "last_name" => $importData[1],
                        "email" => $importData[2],
                        "user_type" => $importData[3],
                        "status" => 'pending'
                    );


                    $invitation = new Invitation;
                    // Another Way to insert records

                    $invitation->create($insertData);

                    //invitation link
                    $invitation_url = $this->get_invitation_registration_path($importData[3]);

                    $email_data = array(
                        'to' => $importData[2],
                        'first_name' => $importData[0],
                        'last_name' => $importData[1],
                        'status' => 'pending',
                        'template' => 'invite_email',
                        'title' => 'PSCU Email',
                        'body_title' => 'Join Us Here at PSC.',
                    );

                    $email_data['sender_name'] = $this->get_user_name();
                    $email_data['subject'] = 'Invite to Registration';
                    $email_data['content'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. <a href='$invitation_url' class='link' style='text-decoration: none;color:gray;'>Sign up now</a>";

                    //email helper function to send email
                    EmailHelper::send_email($email_data);
                }

                $success = 'Import Successful !';
                return response()->json(compact('success'), 200);
            } else {

                $error = 'File too large. File must be less than 2MB.';
                return response()->json(compact('error'), 400);
            }
        } else {
            $error = 'Invalid File Extension.';
            return response()->json(compact('error'), 400);
        }
    }

    /**
     * @SWG\Get(
     *     path="/users/invite/list",
     *     description="View All Invites",
     *     tags={"Invitation"},
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
    public function all(Request $request) {

        $invitation_list = Invitation::all();
        return response()->json($invitation_list, 200);
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/event",
     *      tags={"Invitation"},
     *      operationId="View Invite details",
     *      summary="Invite details of events",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="Invite Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function event_list(Request $request) {
        $invitation_list = Invitation::select('invitation.*', 'users.first_name as fname', 'users.last_name as lname', 'users.email as emails', 'users.role_id', 'events.name as event_name', 'events.name as event_name')->leftJoin('users', 'invitation.user_id', '=', 'users.id')
                        ->leftJoin('events', 'invitation.event_id', '=', 'events.id')
                        ->where('event_id', $request->id)->get();
        return response()->json($invitation_list, 200);
    }

    /**
     * @SWG\Post(
     *      path="/users/invite/course",
     *      tags={"Invitation"},
     *      operationId="View Invite details",
     *      summary="Invite details of course",
     *      consumes={"application/x-www-form-urlencoded"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          in="formData",
     *          required=true, 
     *          description="Invite Id",
     *          type="string" 
     *      ),
     *      security={{"Bearer": {}}},
     *      @SWG\Response(
     *          response=200,
     *          description="Success"
     *      )
     * )
     */
    public function course_list(Request $request) {
        $invitation_list = Invitation::select('invitation.*', 'users.first_name as fname', 'users.last_name as lname', 'users.email as emails', 'users.role_id', 'courses.name as course_name')
                        ->leftJoin('users', 'invitation.user_id', '=', 'users.id')
                        ->leftJoin('courses_detail', 'invitation.course_detail_id', '=', 'courses_detail.id')
                        ->leftJoin('courses', 'courses_detail.course_id', '=', 'courses.id')
                        ->where('course_detail_id', $request->id)->get();
        return response()->json($invitation_list, 200);
    }

    function get_user_name() {
        if (!empty(UserHelper::current_user_id())) {
            return UserHelper::get_user_name(UserHelper::current_user_id());
        } else {
            return '';
        }
    }

    function get_invitation_registration_path($type) {
        switch ($type) {
            case 'internal_user':
                return url('registration') . '/' . $type;
                break;
            case 'external_user':
                return url('registration') . '/' . $type;
                break;
        }
    }

}
