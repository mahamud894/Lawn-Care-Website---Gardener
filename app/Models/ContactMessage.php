<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'isRead'];

    protected function casts(): array
    {
        return ['isRead' => 'boolean'];
    }
}
