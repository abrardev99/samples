<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableShowRequest;
use App\Http\Requests\TableStoreRequest;
use App\Http\Requests\TableDeleteRequest;
use App\Http\Requests\TableEditRequest;
use Illuminate\Http\Request;
use App\Table;
//helpers
use App\Helpers\EmailHelper;
use App\Helpers\UserHelper;
use App\Helpers\TableHelper;

class TimeIntervalController extends Controller {

    /**
     * @SWG\Get(
     *     path="/start_time_interval",
     *     description="Start time Interval",
     * tags={"admin"},
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
    public function time_slots() {
        $options = array();
        $min30 = array('00', '30');
        foreach (range(0, 23) as $fullhour) {
            $parthour = $fullhour > 12 ? $fullhour - 12 : $fullhour;
            foreach ($min30 as $int) {
                if ($fullhour > 11) {
                    $options[$fullhour . "." . $int] = $parthour . ":" . $int . " PM";
                } else {
                    if ($fullhour == 0) {
                        $parthour = '12';
                    }
                    $options[$fullhour . "." . $int] = $parthour . ":" . $int . " AM";
                }
            }
        }

        return response()->json($options);
    }

    /**
     * @SWG\Get(
     *     path="/end_time_interval",
     *     description="End time Interval",
     * tags={"admin"},
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
    public function end_time_slots() {
        $options = array();
        $min30 = array('00', '30');
        foreach (range(0, 23) as $fullhour) {
            $parthour = $fullhour > 12 ? $fullhour - 12 : $fullhour;
            foreach ($min30 as $int) {
                if ($fullhour > 11) {
                    $options[$fullhour . "." . $int] = $parthour . ":" . $int . " PM";
                } else {
                    if ($fullhour == 0) {
                        $parthour = '12';
                    }
                    $options[$fullhour . "." . $int] = $parthour . ":" . $int . " AM";
                }
            }
        }


        return response()->json($options);
    }

}
