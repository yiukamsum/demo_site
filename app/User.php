<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Payment\Payment;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = "member";
    protected $primaryKey = 'member_id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company_detail()
    {
        // Depends on how many company details does a user have
        // return $this->hasMany(CompanyDetail::class);
        return $this->hasOne(CompanyDetail::class);
    }

    public function name_histories()
    {
        // Depends on how many company details does a user have
        // return $this->hasMany(CompanyDetail::class);
        return $this->hasMany(CompanyDetail::class);
    }

    static function getUserCompanyDetail($member_id)
    {
        return User::leftJoin('company_detail','company_detail.member_id','=','member.member_id')
                            ->where('member.member_id',$member_id)
                            ->first();
    }

    static function getUserPlanDetail($member_id)
    {
        $plan = Payment::leftJoin('plans','plans.plan_id','=','payment.plan_id')
                ->where('member_id',$member_id)
                ->whereNotNull('payment.plan_id')
                ->where('payment.status','enable')
                ->where('payment.deleted',0)
                ->select([
                    'payment.*',
                    'plans.price as price',
                    'plans.title as plan_title',
                    'plans.company_type as company_type',
                    'plans.type as plan_type',
                    ])
                ->orderby('payment.created_at','desc')
                ->first();

        return $plan;
    }
}
