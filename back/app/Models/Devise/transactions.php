<?php

namespace App\Models\Devise;

use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{

    protected $fillable = [
        'id_user_createur',
        'id_devise',
        'prix',
        'taux',
        'name_client',
        'description',
        'is_entree', // true | false
        'updated_at'
    ];
    protected $table = 'transactions';
    protected $primaryKey = 'id';
}
