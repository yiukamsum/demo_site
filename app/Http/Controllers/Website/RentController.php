<?php

namespace App\Http\Controllers\Website;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use Dotenv\Result\Success;
use App\Models\Advertisement\Advertisement;
use App\Models\Company\Company;
use App\Models\Payment\PriceUnit;
use App\Models\Payment\RentSpace;

use App\Http\Requests\RentSpace as RentSpaceRequest;

class RentController extends Controller
{
    function search_page(Request $request){
        $data['district'] = RentSpace::select(['address'])->distinct()->get();
        $data['result'] = $request->all();

        $ad = new Advertisement;
        $ad_title = "Rent Space - 搜寻列表";
        if($ad->getAdImage($ad_title) != null){
                $data['ad_img'] = Storage::url($ad->getAdImage($ad_title)['ad_img']);
                $data['ad_url'] = $ad->getAdImage($ad_title)['ad_url'];
        }
        $map_ad_title = "Rent Space - 地图";
        if($ad->getAdImage($map_ad_title) != null){
                $data['map_ad_img'] = Storage::url($ad->getAdImage($map_ad_title)['ad_img']);
                $data['map_ad_url'] = Storage::url($ad->getAdImage($map_ad_title)['ad_url']);
        }
        return view('page.rent.list',$data);
    }

    function getlist(Request $request){
        $filter = $request->all();
        $data = RentSpace::list($filter);

        $data = $data->paginate($filter['page'], ['*'], null,1);

        $end = $filter['page'] >= $data->total();

        $data = $data->map(function($v, $k)use($filter){
            $v = collect($v);

            $v['company_logo'] = Storage::url(Company::where('member_id',$v['member_id'])->first()->logo_path);

            $v['photo1'] = Storage::url($v['photo1']);
            $v['photo2'] = Storage::url($v['photo2']);
            $v['photo3'] = Storage::url($v['photo3']);
            $v['photo4'] = Storage::url($v['photo4']);

            $v['price_unit'] = PriceUnit::where('id',$v['price_unit'])->first()->name;
            $v['management_price_unit'] = PriceUnit::where('id',$v['management_price_unit'])->first()->name;
            return $v;
        });
        return ["data"=>$data,"end"=>$end];
    }

    function info_page($id="null"){
        if($id=="null"){
            return redirect()->route('search_rent')->withErrors(['link_id'=>'This Company do not complete information.']);
        }

        $selectCol = [
                        "rent_spaces.*",
                    ];
        $data = RentSpace::where('rent_spaces.rent_space_id',$id)
                    ->where('rent_spaces.status','approved')
                    ->where('rent_spaces.deleted',0);

        $data = $data->select($selectCol)->first();

        if($data != null){
            $data = collect($data);
    
            $data['company_logo'] = Storage::url(Company::where('member_id',$data['member_id'])->first()->logo_path);
    
            $data['company_profile'] = Company::where('member_id',$data['member_id'])->first()->profile;
    
            $data['photo1'] = Storage::url($data['photo1']);
            $data['photo2'] = Storage::url($data['photo2']);
            $data['photo3'] = Storage::url($data['photo3']);
            $data['photo4'] = Storage::url($data['photo4']);
    
            $data['price_unit'] = PriceUnit::where('id',$data['price_unit'])->first()->name;
            $data['management_price_unit'] = PriceUnit::where('id',$data['management_price_unit'])->first()->name;
    
            $data['price_interval'] = $data['price_interval'] == 'yearly' ? 'Year' : ($data['price_interval'] == 'quarterly' ? 'Quarter' : 'Month');
            $data['management_price_interval'] = $data['management_price_interval'] == 'yearly' ? 'Year' : ($data['management_price_interval'] == 'quarterly' ? 'Quarter' : 'Month');
            $data['rent_range'] = $data['rent_range'] == 'yearly' ? 'One Year' : ($data['rent_range'] == 'quarterly' ? 'One Quarter' : 'One Month');
    
    
            $data['district'] = RentSpace::select(['address'])->distinct()->get();
            return view('page.rent.info',$data);
        } else {
            abort(404);
        }
    }

    function register_form(){
        $data['select_unit'] = PriceUnit::where('deleted',0)->get();
        return view('page.rent.register',$data);
    }

    function addData(RentSpaceRequest\CreateRequest $request){
        $input = $request->validated();
        $input['member_id'] = Auth::user()->member_id;
        $input['deleted'] = 0;
        $input['status'] = 'pending';
        $input['created_at'] = Carbon::now();
        unset($input['_token']);
        $input['location'] = str_replace('height=', '' ,$input['location'] );
        $input['location']  = str_replace('width=', '' ,$input['location'] );

        foreach ([1,2,3,4] as $k) {
            // why use $$ ?? its not necessary.
            $tmp_name = "photo".$k;
            $$tmp_name = $request->file($tmp_name.'_file');

            $path = $$tmp_name->storeAs('public/rent/'.Auth::user()->member_id.'/photo'.$k, (Auth::user()->member_id.'_'.Carbon::now()->format('YmdHis').'_'.$$tmp_name->getClientOriginalName()));
            $input[$tmp_name] = $path;
            unset($input[$tmp_name.'_file']);
        }
        if(RentSpace::insert($input)){
            return "success";
        }else{
            return "Rent added failed";
        }
    }
}
