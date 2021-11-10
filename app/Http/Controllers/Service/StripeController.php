<?php

namespace App\Http\Controllers\Service;
use App\Http\Controllers\Controller;

use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponHistory;
use App\Models\Payment\Payment;
use App\Models\Payment\Plan;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Event;
use DB;
use Carbon\Carbon;

class StripeController extends Controller
{
    function __construct(){
        $this->stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
    }

    function create_subscription(Request $request){
        // if(!Auth::check())return redirect()->route('login');
        if(Auth::check()){
            $input = $request->all();

            if (Auth::user()->stripe_id == null) {
                $customer = $this->stripe->customers->create([
                    'email' => Auth::user()->email
                ]);
               User::where('member_id', Auth::user()->member_id)->update(['stripe_id' => $customer['id']]);
               //Auth::user()->update(['stripe_id' => $customer['id']]);
            }

            $data = [
                'customer' => Auth::user()->stripe_id ?? $customer['id'],
                'items' => [[
                    'price' => $input['price_id'],
                ]],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                ];

            $this->getCouponCode($request, $coupon_code);

            if(isset($coupon_code)){
                $data['promotion_code'] = Coupon::where('code',$coupon_code)->first()->stripe_promo_id;
            }

            $subscription = $this->stripe->subscriptions->create($data);

            $this->stripe->paymentIntents->update(
                $subscription->latest_invoice->payment_intent->id,
                ['metadata' => ['subscription_id' => $subscription->id]]
            );

            return response()->json([
                'status' => 'success',
                'pi_id' => $subscription->latest_invoice->payment_intent->id,
                'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret
            ], 200);
        }else{
            return redirect()->route('login');
        }
    }

    function create_invoice(Request $request){
        // if(!Auth::check())return redirect()->route('login');
        if(Auth::check()){
            $input = $request->all();

            if (Auth::user()->stripe_id == null) {
                $customer = $this->stripe->customers->create([
                    'email' => Auth::user()->email
                ]);
               User::where('member_id', Auth::user()->member_id)->update(['stripe_id' => $customer['id']]);
               // Auth::user()->update(['stripe_id' => $customer['id']]);
            }

            $plan = Plan::where('stripe_price_id',$input['price_id'])->first();

            $data = [
                'customer' => Auth::user()->stripe_id ?? $customer['id'],
                'amount' => number_format(floatval($plan->price), 2, '', ''),
                'currency' => 'hkd',
                'metadata' => ['integration_check' => 'accept_a_payment'],
                'description' => $plan->plan_id,
            ];

            if(isset($input['payment_method_types'])){
                if($input['payment_method_types'] != ""){
                    $data['payment_method_types'] = [$input['payment_method_types']];
                }
            }

            if(isset($input['payment_method_types'])){
                if($input['payment_method_types'] != ""){
                    $data['payment_method_types'] = [$input['payment_method_types']];
                }
            }

            $this->getCouponCode($request, $coupon_code);

            if(isset($coupon_code)){
                $coupon_data = Coupon::where('code',$coupon_code)->first();
                $amount = floatval($plan->price)*(1-($coupon_data->percent_off/100));
                $data['amount'] = number_format($amount, 2, '', '');
            }

            $intent = $this->stripe->paymentIntents->create($data);

            if(isset($coupon_code)){
                CouponHistory::insert(['coupon_id'=>$coupon_data->coupon_id,'member_id'=>Auth::user()->member_id,'created_at'=>Carbon::now(),"deleted"=>0]);
            }

            return response()->json([
                'status' => 'success',
                'pi_id' => $intent->id,
                'clientSecret' => $intent->client_secret
            ], 200);
        }else{
            return redirect()->route('login');
        }
    }

    function getCouponCode(Request $request, &$coupon_code){
        if($request->session()->exists('transfer_register')){
            if(isset($request->session()->get('transfer_register')['coupon'])){
                $coupon_code = $request->session()->get('transfer_register')['coupon'];
            }
        }
        if($request->session()->exists('service_register')){
            if(isset($request->session()->get('service_register')['coupon'])){
                $coupon_code = $request->session()->get('service_register')['coupon'];
            }
        }
        if($request->session()->exists('investor')){
            if(isset($request->session()->get('investor')['coupon'])){
                $coupon_code = $request->session()->get('investor')['coupon'];
            }
        }
        if($request->session()->exists('startup_register')){
            if(isset($request->session()->get('startup_register')['coupon'])){
                $coupon_code = $request->session()->get('startup_register')['coupon'];
            }
        }
        if($request->session()->exists('incubator_register')){
            if(isset($request->session()->get('incubator_register')['coupon'])){
                $coupon_code = $request->session()->get('incubator_register')['coupon'];
            }
        }
    }

    function callback(){
        $endpoint_secret = env('STRIPE_WEBHOOK_KEY');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $stripe = $this->stripe;

                $result = $event->data->object;
                $member =User::where('stripe_id',$result['customer'])->where('status','enable')->first();
                $data['member_id'] = $member->member_id;
                $data['stripe_pi_id'] = $result['id'];
                $pi = $stripe->paymentIntents->retrieve(
                    $result['id'],
                    []
                );
                $data['amount'] = floatval($pi['amount'])/100;

                if($result['invoice'] != null){
                    $data['stripe_invoice_id'] = $result['invoice'];
                    $tmp = $stripe->invoices->retrieve(
                            $result['invoice'],
                            []
                        );

                    $subscription = $stripe->subscriptions->retrieve(
                        $result['metadata']['subscription_id'],
                        []
                    );

                    $data['plan_id'] = Plan::where('stripe_price_id',$subscription['items']['data'][0]['price']['id'])->first()->plan_id;

                    if($result['payment_method_types'][0]=="card"){
                        $data['payment_method'] = 'credit_card';
                    }
                    if($result['payment_method_types'][0]=="alipay"){
                        $data['payment_method'] = 'alipay';
                    }
                    if($result['payment_method_types'][0]=="wechat_pay"){
                        $data['payment_method'] = 'wechat_pay';
                    }

                    $data['payment_token'] = '';
                    $data['payment_date'] = Carbon::createFromTimestamp($tmp['lines']['data'][0]['period']['start'])->format('Y-m-d H:i:s');
                    $data['expired_date'] = Carbon::createFromTimestamp($tmp['lines']['data'][0]['period']['end'])->format('Y-m-d H:i:s');
                    $data['status'] = 'enable';
                    $data['deleted'] = 0;
                }else{

                    $plan = Plan::where('plan_id',intval($pi['description']))->first();

                    $data['plan_id'] = $plan->plan_id;

                    if($plan->type == "quarterly"){
                        $data['payment_date'] = Carbon::createFromTimestamp($pi['created'])->format('Y-m-d H:i:s');
                        $data['expired_date'] = Carbon::createFromTimestamp($pi['created'])->addMonths(4)->format('Y-m-d H:i:s');
                    }else{
                        $data['payment_date'] = Carbon::createFromTimestamp($pi['created'])->format('Y-m-d H:i:s');
                        $data['expired_date'] = Carbon::createFromTimestamp($pi['created'])->addYear()->format('Y-m-d H:i:s');
                    }

                    if($result['payment_method_types'][0]=="card"){
                        $data['payment_method'] = 'credit_card';
                    }

                    if($result['payment_method_types'][0]=="alipay"){
                        $data['payment_method'] = 'alipay';
                    }
                    if($result['payment_method_types'][0]=="wechat_pay"){
                        $data['payment_method'] = 'wechat_pay';
                    }

                    $data['payment_token'] = '';
                    $data['status'] = 'enable';
                    $data['deleted'] = 0;
                }


                if (Payment::insert($data)) {
                    return response()->json([
                        'status' => 'success',
                        'msg' => 'Payment record add Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'msg' => 'Payment record add failed'
                    ], 200);
                }

                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
                // Then define and call a method to handle the successful attachment of a PaymentMethod.
                // handlePaymentMethodAttached($paymentMethod);
                break;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }
    }

    function delete_card(Request $request){
        // if(!Auth::check())return redirect()->route('login');
        if(Auth::check()){
            $input = $request->all();
            $this->stripe->paymentMethods->detach(
                $input['card_id'],
                []
            );
            return "success";
        }
        else{
            return redirect()->route('login');
        }
    }

    function check_coupon(Request $request){
        // if(!Auth::check())return redirect()->route('login');
        if(Auth::check()){
            $input = $request->all();
            if(Coupon::where('code', $input['coupon'])->count()!=0){
                return "success";
            }else{
                return "failed";
            }
        }
        else{
            return redirect()->route('login');
        }
    }
}
