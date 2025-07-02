<?php

namespace App\Models\AppParameters;

use App\Models\users\roles;
use Illuminate\Database\Eloquent\Model;

class config_can_access_keys extends Model
{
    protected $fillable = ['key_label','description'];
    protected $table = 'config_can_access_keys';
    protected $primaryKey = 'id';

    /* ----------------------- config_can_access ---------------------- */
    public function roles()
    {
        return $this->belongsToMany(roles::class, 'config_can_access', 'id_can_access_key', 'id_role');
    }
}
