<?php

namespace App\Models\users;
use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    protected $fillable = [
        'libelle_role',
        'acronym_role',
        'sidebar',
        'description',
        'created_at',
        'updated_at'
    ];
    protected $table = 'roles';
    protected $primaryKey = 'id';

    public function users() {
        return $this->hasMany(User::class, 'id_role');
    }
}
