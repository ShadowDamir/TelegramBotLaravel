<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'messageText',
        'image',
        'sendingDate',
        'isSended',
        'creatorId',
        'created_at',
        'updated_at'
    ];
}
