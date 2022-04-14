<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'id',
        'token',
        'username',
        'startMessage',
        'webhookURL'
    ];
}
