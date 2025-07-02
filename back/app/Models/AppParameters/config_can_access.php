<?php

namespace App\Models\AppParameters;

use Illuminate\Database\Eloquent\Model;

class config_can_access extends Model
{
    protected $fillable = [
        'id_can_access_key',
        'id_role'
    ];
    protected $table = 'config_can_access';
    protected $primaryKey = 'id';
}
