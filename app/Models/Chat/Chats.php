<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chats extends Model {
    protected $table = 'chats';

    protected $fillable = [
        'title'
    ];

    public function chatUsers(): HasMany {
        return $this->hasMany(ChatUsers::class);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, "chat_users", "chat_id", "user_id");
    }

    public function messages(): HasMany {
        return $this->hasMany(ChatMessages::class, "chat_id");
    }
}
