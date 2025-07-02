<?php

namespace App\Models\Devise;

use Illuminate\Database\Eloquent\Model;

class devises extends Model
{
    protected $fillable = [
        'label',
        'abrv',
        'description',
        'updated_at'
    ];
    protected $table = 'devises';
    protected $primaryKey = 'id';

    /* ------------------------------ transactions ------------------------------ */
    public function transactions() {
        return $this->hasMany(transactions::class, 'id_devise');
    }
}