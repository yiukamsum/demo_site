<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;
use App\Notify;
use Illuminate\Support\Facades\Auth;
use app\Notifications\UserChangePW;
use Stripe\StripeClient;

use App\User;
use App\Models\Payment\Payment;
use App\Models\User\NameHistory;

class MemberController extends Controller
{
    function show(){
        $data['path'] = Storage::url(Auth::user()->icon_path);
        return view("page.member.member_list",$data);
    }

    //setting page
    function showSetting(){
        $data['path'] = Storage::url(Auth::user()->icon_path);
        return view("page.account.setting",$data);
    }

    function updateIcon(Request $request){
        $path = $request->file('image')->storeAs('public/'.Auth::user()->member_id.'/icon',Auth::user()->member_id.'.png');
        if(User::where("member_id",Auth::user()->member_id)->first()->icon_path == $path ){
            return 'success';
        }
        if(User::where("member_id",Auth::user()->member_id)->update(["icon_path"=>$path]) ) {
            return 'success';
        }
        return 'Change Icon Failed';
    }

    function getIcon(Request $request){
        $path = User::where("member_id",Auth::user()->member_id)->first()->icon_path;
        $path = Storage::url($path);
        return $path;
    }

    function updatePassword(Request $request){
        $this->validate($request, [
            'c_password'   => 'required',
            'new_password' => 'required|min:8|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
            'check_password' => 'required|same:new_password'
        ]);

        $data = $request->all();
        if(Hash::check($data['c_password'],Auth::user()->password)){
            User::where("member_id",Auth::user()->member_id)->update(['password'=>Hash::make($data['new_password'])]);
            $message['title'] = "Password Set";
            $message['content'] = "Your Password was set at ".Carbon::now()->toDateTimeString();
            $notify = new Notify;
            $notify->add(Auth::user(),$message,0);
            return "success";
        }
        return 'Current Password is not correct!';
    }

    function basicSetting(){
        $data['user'] = User::leftJoin('company_detail','company_detail.member_id','=','member.member_id')
                            ->where('member.member_id',Auth::user()->member_id)
                            ->first();
        $data['plan'] = User::getUserPlanDetail(Auth::user()->member_id);

        $data['payment'] = Payment::getUserPaymentHistory(Auth::user()->member_id);

        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));

        $data['card'] = [];
        if(Auth::user()->email != null){
            if (Auth::user()->stripe_id == null) {
                $customer = $stripe->customers->create([
                    'email' => Auth::user()->email
                ]);
                User::where('member_id', Auth::user()->member_id)->update(['stripe_id' => $customer['id']]);
                $data['card'] = $stripe->paymentMethods->all([
                    'customer' => $customer['id'],
                    'type' => 'card',
                ])->data;
            }else{
                $data['card'] = $stripe->paymentMethods->all([
                    'customer' => Auth::user()->stripe_id,
                    'type' => 'card',
                ])->data;
            }
        }
        // if(Auth::user()->email != null){
        //     if (Auth::user()->stripe_id == null) {
        //         $customer = $stripe->customers->create([
        //             'email' => Auth::user()->email
        //         ]);
        //         User::where('member_id', Auth::user()->member_id)->update(['stripe_id' => $customer['id']]);
        //         $data['card'] = $stripe->paymentMethods->all([
        //             'customer' => $customer['id'],
        //             'type' => 'card',
        //         ])->data;
        //     }else{
        //         $data['card'] = $stripe->paymentMethods->all([
        //             'customer' => Auth::user()->stripe_id,
        //             'type' => 'card',
        //         ])->data;
        //     }
        // }else{
        //     $data['card'] = [];
        // }
        return view('page.setting.basic_info_billing_info',$data);
    }



    function noRegisterSetting(){
        $data['user'] = User::getUserCompanyDetail(Auth::user()->member_id);
        return view('page.setting.basic_info_nonreg',$data);
    }

    function editData(Request $request){
        $input = $request->all();

        switch ($input['action']) {
            case 'name':
                if(NameHistory::where('member_id',Auth::user()->member_id)->where('unlock_date','>',Carbon::now())->count() !=0 ){
                    return "The name has been changed within 24 hours";
                }
                $data['first_name'] = $input['first_name'];
                $data['last_name'] = $input['last_name'];
                break;
            case 'email':
                if(User::where('email',$input['data'])->count() > 0){
                    return"Email has been registered";
                }
                $data[$input['action']] = $input['data'];
                break;
            default:
                $data[$input['action']] = $input['data'];
                break;
        }

        if(User::where('member_id',Auth::user()->member_id)->update($data)){
            if($input['action']=="name"){
                Auth::user()->name_histories()->create(['unlock_date'=>Carbon::now()->addDay()]);
                // NameHistory::insert(['member_id'=>Auth::user()->member_id]);
            }
            if($input['action']=="email"){
                $message['title'] = "Email account Update";
                $message['content'] = "Your Email account was updated at ".Carbon::now()->toDateTimeString();
                $notify = new Notify;
                $notify->add(Auth::user(),$message,0);
            }
            return "success";
        }
        return "Data Edit failed";
    }
}
