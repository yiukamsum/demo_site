<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Notify extends Model
{
    protected $table = "notification";

    public function add(User $user,array $message,string $sender_id )
    {
        $input['sender_member_id'] = $sender_id;
        $input['receiver_member_id'] = $user->member_id;
        $input['title'] = $message['title'];
        $input['content'] = $message['content'];
        $input['status'] = 'enable';
        $input['deleted'] = 0;
        $this->insert($input);

    }
}
