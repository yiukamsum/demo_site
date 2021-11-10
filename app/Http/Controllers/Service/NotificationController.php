<?php

namespace App\Http\Controllers\Service;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use App\User;
use App\Notify;
use App\Notifications\RealTimeNotification;

class NotificationController extends Controller
{
    function getNotification(){
        $user = User::find(Auth::user()->member_id);
        $user->notify(new RealTimeNotification(Auth::user()));
        // User find should be useless in this case? 
        // Auth::user()->notify(new RealTimeNotification(Auth::user()))
    }

    function notificationSetting(){
        $data['user'] = User::getUserCompanyDetail(Auth::user()->member_id);
        return view('page.setting.notification_center',$data);
    }

    function readNotification(Request $request){
        $id = $request->id;
        if (Notify::where('id',$id)->update(["read_datetime"=>Carbon::now()])) {
            return "success";
        }
        return "Read notification failed";
    }

    function deleteNotification(Request $request){
        $id = $request->id;
        if (Notify::where('id',$id)->update(["deleted"=>1])) {
            return "success";
        }
        return "Deleted notification failed";
    }
}
