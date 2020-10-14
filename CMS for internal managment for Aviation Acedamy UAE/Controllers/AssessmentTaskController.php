<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\AssessmentLevel;
use App\AssessmentMedia;
use App\AssessmentReport;
use App\AssessmentTask;
use App\ReportAttachment;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AssessmentTaskController extends Controller
{

    public function create($assessmentId)
    {
        return view('create-assessment-task' , compact('assessmentId'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'assessment_id' => ['required'],
            'final_level' => ['required'],
            'pro_task_1' => ['required'],
            'pro_task_2' => ['required'],
            'pro_task_2a' => ['required'],
            'pro_task_2b' => ['required'],
            'pro_task_3' => ['required'],
            'pro_task_4' => ['required'],
            'str_task_1' => ['required'],
            'str_task_2' => ['required'],
            'str_task_2a' => ['required'],
            'str_task_2b' => ['required'],
            'str_task_3' => ['required'],
            'str_task_4' => ['required'],
            'voc_task_1' => ['required'],
            'voc_task_2' => ['required'],
            'voc_task_2a' => ['required'],
            'voc_task_2b' => ['required'],
            'voc_task_3' => ['required'],
            'voc_task_4' => ['required'],
            'flu_task_1' => ['required'],
            'flu_task_2' => ['required'],
            'flu_task_2a' => ['required'],
            'flu_task_2b' => ['required'],
            'flu_task_3' => ['required'],
            'flu_task_4' => ['required'],
            'com_task_1' => ['required'],
            'com_task_2' => ['required'],
            'com_task_2a' => ['required'],
            'com_task_2b' => ['required'],
            'com_task_3' => ['required'],
            'com_task_4' => ['required'],
            'inter_task_1' => ['required'],
            'inter_task_2' => ['required'],
            'inter_task_2a' => ['required'],
            'inter_task_2b' => ['required'],
            'inter_task_3' => ['required'],
            'inter_task_4' => ['required'],
            'grade_task_1' => ['required'],
            'grade_task_2' => ['required'],
            'grade_task_2a' => ['required'],
            'grade_task_2b' => ['required'],
            'grade_task_3' => ['required'],
            'grade_task_4' => ['required'],

        ]);




        DB::transaction(function () use ($request) {

            $title = ' ';
            $user = Auth::user();
            if ($user->role == 'teacherope')
                $title = 'OPE';
            if ($user->role == 'teacherele')
                $title = 'ELE';

//        create report
            $assessmentReport = new AssessmentReport();
            $assessmentReport->assessment_id = $request->assessment_id;
            $assessmentReport->assessor_id = $user->id;
            $assessmentReport->title = $title;
            $assessmentReport->comment = $request->comment;
            $assessmentReport->report_level = $request->final_level;
            $assessmentReport->save();
            $assessmentId = $assessmentReport->id;

//        pro
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Pronunciation';
            $task->task_1 = $request->pro_task_1;
            $task->task_2 = $request->pro_task_2;
            $task->task_2a = $request->pro_task_2a;
            $task->task_2b = $request->pro_task_2b;
            $task->task_3 = $request->pro_task_3;
            $task->task_4 = $request->pro_task_4;
            $task->save();

//        str
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Structure';
            $task->task_1 = $request->str_task_1;
            $task->task_2 = $request->str_task_2;
            $task->task_2a = $request->str_task_2a;
            $task->task_2b = $request->str_task_2b;
            $task->task_3 = $request->str_task_3;
            $task->task_4 = $request->str_task_4;
            $task->save();

//        voc
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Vocabulary';
            $task->task_1 = $request->voc_task_1;
            $task->task_2 = $request->voc_task_2;
            $task->task_2a = $request->voc_task_2a;
            $task->task_2b = $request->voc_task_2b;
            $task->task_3 = $request->voc_task_3;
            $task->task_4 = $request->voc_task_4;
            $task->save();


//        flu
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Fluency';
            $task->task_1 = $request->flu_task_1;
            $task->task_2 = $request->flu_task_2;
            $task->task_2a = $request->flu_task_2a;
            $task->task_2b = $request->flu_task_2b;
            $task->task_3 = $request->flu_task_3;
            $task->task_4 = $request->flu_task_4;
            $task->save();


//        com
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Comprehension';
            $task->task_1 = $request->com_task_1;
            $task->task_2 = $request->com_task_2;
            $task->task_2a = $request->com_task_2a;
            $task->task_2b = $request->com_task_2b;
            $task->task_3 = $request->com_task_3;
            $task->task_4 = $request->com_task_4;
            $task->save();


//        inter
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Interaction';
            $task->task_1 = $request->inter_task_1;
            $task->task_2 = $request->inter_task_2;
            $task->task_2a = $request->inter_task_2a;
            $task->task_2b = $request->inter_task_2b;
            $task->task_3 = $request->inter_task_3;
            $task->task_4 = $request->inter_task_4;
            $task->save();


//        Final Grade
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Final Grade';
            $task->task_1 = $request->grade_task_1;
            $task->task_2 = $request->grade_task_2;
            $task->task_2a = $request->grade_task_2a;
            $task->task_2b = $request->grade_task_2b;
            $task->task_3 = $request->grade_task_3;
            $task->task_4 = $request->grade_task_4;


            $task->save();

            //        Attachments
            $task = new AssessmentTask();
            $task->assessment_report_id = $assessmentId;
            $task->title = 'Attachments';
            $task->save();
            $attachmentTaskId = $task->id;

//            save task 1 multiple attachments
            if ($request->hasFile('task_1_attachment')) {
                foreach ($request->task_1_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_1_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2_attachment')) {
                foreach ($request->task_2_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2a_attachment')) {
                foreach ($request->task_2a_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2a_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2b_attachment')) {
                foreach ($request->task_2b_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2b_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_3_attachment')) {
                foreach ($request->task_3_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_3_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_4_attachment')) {
                foreach ($request->task_4_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_4_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }


            //        media attachments
            if (Gate::allows('isTeacherOpe')) {
                $media = new AssessmentMedia();
                $media->assessment_id = $request->assessment_id;
                $media->title = $assessmentId . '-OPE';
                if ($request->hasFile('media')) {
                    $this->validate($request, [
                        'media' => 'mimes:audio/mpeg,mpga,mp3|max:15360',
                    ]);

                    $mediaName = time() . '.' . $request->media->extension();
                    $request->media->move(public_path('user/task'), $mediaName);
                    $media->media = $mediaName;
                }
                $media->save();
            }
        });

        toast('Assessment Report Saved Successfully', 'success');
        return redirect('user/student/assessment/details/' . $request->assessment_id);



    }


    public function edit(AssessmentReport $assessment)
    {
        return view('update-assessment-details' , compact('assessment'));
    }


    public function update(Request $request , AssessmentReport $assessment)
    {
        $request->validate([
            'final_level' => ['required'],
            'pro_task_1' => ['required' , 'numeric' , 'min:1' , 'max:9'],
            'pro_task_2' => ['required'],
            'pro_task_2a' => ['required'],
            'pro_task_2b' => ['required'],
            'pro_task_3' => ['required'],
            'pro_task_4' => ['required'],
            'str_task_1' => ['required'],
            'str_task_2' => ['required'],
            'str_task_2a' => ['required'],
            'str_task_2b' => ['required'],
            'str_task_3' => ['required'],
            'str_task_4' => ['required'],
            'voc_task_1' => ['required'],
            'voc_task_2' => ['required'],
            'voc_task_2a' => ['required'],
            'voc_task_2b' => ['required'],
            'voc_task_3' => ['required'],
            'voc_task_4' => ['required'],
            'flu_task_1' => ['required'],
            'flu_task_2' => ['required'],
            'flu_task_2a' => ['required'],
            'flu_task_2b' => ['required'],
            'flu_task_3' => ['required'],
            'flu_task_4' => ['required'],
            'com_task_1' => ['required'],
            'com_task_2' => ['required'],
            'com_task_2a' => ['required'],
            'com_task_2b' => ['required'],
            'com_task_3' => ['required'],
            'com_task_4' => ['required'],
            'inter_task_1' => ['required'],
            'inter_task_2' => ['required'],
            'inter_task_2a' => ['required'],
            'inter_task_2b' => ['required'],
            'inter_task_3' => ['required'],
            'inter_task_4' => ['required'],
            'grade_task_1' => ['required'],
            'grade_task_2' => ['required'],
            'grade_task_2a' => ['required'],
            'grade_task_2b' => ['required'],
            'grade_task_3' => ['required'],
            'grade_task_4' => ['required'],

        ]);



        DB::transaction(function () use ($request , $assessment) {

            $assessment->comment = $request->comment;
            $assessment->report_level = $request->final_level;
            $assessment->save();


            //        pro
            $task = AssessmentTask::findOrFail($request->pro_task_id);
            $task->task_1 = $request->pro_task_1;
            $task->task_2 = $request->pro_task_2;
            $task->task_2a = $request->pro_task_2a;
            $task->task_2b = $request->pro_task_2b;
            $task->task_3 = $request->pro_task_3;
            $task->task_4 = $request->pro_task_4;
            $task->save();

            //        str
            $task = AssessmentTask::findOrFail($request->str_task_id);
            $task->task_1 = $request->str_task_1;
            $task->task_2 = $request->str_task_2;
            $task->task_2a = $request->str_task_2a;
            $task->task_2b = $request->str_task_2b;
            $task->task_3 = $request->str_task_3;
            $task->task_4 = $request->str_task_4;
            $task->save();

//        voc
            $task = AssessmentTask::findOrFail($request->voc_task_id);
            $task->task_1 = $request->voc_task_1;
            $task->task_2 = $request->voc_task_2;
            $task->task_2a = $request->voc_task_2a;
            $task->task_2b = $request->voc_task_2b;
            $task->task_3 = $request->voc_task_3;
            $task->task_4 = $request->voc_task_4;
            $task->save();


//        flu
            $task = AssessmentTask::findOrFail($request->flu_task_id);
            $task->task_1 = $request->flu_task_1;
            $task->task_2 = $request->flu_task_2;
            $task->task_2a = $request->flu_task_2a;
            $task->task_2b = $request->flu_task_2b;
            $task->task_3 = $request->flu_task_3;
            $task->task_4 = $request->flu_task_4;
            $task->save();


//        com
            $task = AssessmentTask::findOrFail($request->com_task_id);
            $task->task_1 = $request->com_task_1;
            $task->task_2 = $request->com_task_2;
            $task->task_2a = $request->com_task_2a;
            $task->task_2b = $request->com_task_2b;
            $task->task_3 = $request->com_task_3;
            $task->task_4 = $request->com_task_4;
            $task->save();


//        inter
            $task = AssessmentTask::findOrFail($request->inter_task_id);
            $task->task_1 = $request->inter_task_1;
            $task->task_2 = $request->inter_task_2;
            $task->task_2a = $request->inter_task_2a;
            $task->task_2b = $request->inter_task_2b;
            $task->task_3 = $request->inter_task_3;
            $task->task_4 = $request->inter_task_4;
            $task->save();


//        Final Grade
            $task = AssessmentTask::findOrFail($request->grade_task_id);
            $task->task_1 = $request->grade_task_1;
            $task->task_2 = $request->grade_task_2;
            $task->task_2a = $request->grade_task_2a;
            $task->task_2b = $request->grade_task_2b;
            $task->task_3 = $request->grade_task_3;
            $task->task_4 = $request->grade_task_4;
            $task->save();

            //        Attachments
            $task = AssessmentTask::findOrFail($request->attachment_task_id);
            $attachmentTaskId = $task->id;


//            save task 1 multiple attachments
            if ($request->hasFile('task_1_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_1_attachment')->delete();
                foreach ($request->task_1_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_1_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_2_attachment')->delete();
                foreach ($request->task_2_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2a_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_2a_attachment')->delete();
                foreach ($request->task_2a_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2a_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_2b_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_2b_attachment')->delete();
                foreach ($request->task_2b_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_2b_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_3_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_3_attachment')->delete();
                foreach ($request->task_3_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_3_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            if ($request->hasFile('task_4_attachment')) {
                ReportAttachment::where('assessment_task_id' , $attachmentTaskId)->where('task_name' , 'task_4_attachment')->delete();
                foreach ($request->task_4_attachment as $item) {
                    $attachment = new ReportAttachment();
                    $attachment->assessment_task_id = $attachmentTaskId;
                    $attachment->task_name = 'task_4_attachment';
                    $task1Attachment = time() . '.' . $item->extension();
                    $item->move(public_path('user/task/attachments'), $task1Attachment);
                    $attachment->attachment = $task1Attachment;
                    $attachment->save();
                }
            }

            //        media attachments
            if (Gate::allows('isTeacherOpe')) {
                $media = AssessmentMedia::findOrFail($request->media_id);
                if ($request->hasFile('media')) {
                    $this->validate($request, [
                        'media' => 'mimes:audio/mpeg,mpga,mp3|max:15360',
                    ]);

                    $mediaName = time() . '.' . $request->media->extension();
                    $request->media->move(public_path('user/task'), $mediaName);
                    $media->media = $mediaName;
                }
                $media->save();
            }

        });


        toast('Assessment Updated Successfully', 'success');
        return redirect('user/student/assessment/details/' . $assessment->assessment_id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AssessmentTask  $assignmentTask
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssessmentTask $assignmentTask)
    {
        //
    }

}
