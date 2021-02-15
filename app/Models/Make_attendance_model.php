<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make_attendance_model extends Model
{
    use HasFactory;
    protected $table = "make_attandance";
    protected $fillable = [
        'mobile_no', 'location_address','longitude','latitude','datetime','user_id',
    ];

}
