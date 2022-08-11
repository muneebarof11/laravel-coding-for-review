<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionRequest extends Model
{
    use HasFactory;

    protected $table = 'connection_requests';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'status'
    ];

}
