<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class MailController extends Controller
{
    function mailSetting(){
        $data['user'] = User::getUserCompanyDetail(Auth::user()->member_id);
        return view('page.setting.my_mail',$data);
    }
}
