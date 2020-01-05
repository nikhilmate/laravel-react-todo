<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;

class Todo extends Model
{
    protected $table = 'todo';
    protected $fillable = ['title', 'description', 'due', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
