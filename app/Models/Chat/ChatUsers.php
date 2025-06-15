<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatUsers extends Model {
    protected $table = 'chat_users';
    public $timestamps = false;

    public function user(): HasOne {
        return $this->hasOne(User::class);
    }
}
