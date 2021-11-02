<?php

namespace App\Broadcasting;

use App\User;
use App\Message;


class messageChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $user
     * @return array|bool
     */
    public function join(User $user, Message $message)
    {
        return $user->member_id === $message->receiver_member_id;
    }
}
