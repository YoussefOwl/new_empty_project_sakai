<?php

namespace App\Models\AppParameters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sidebar_buttons extends Model
{
    use HasFactory;
    protected $fillable = [
        "icon",
        "title",
        "routerLink"
    ];
    protected $table = 'sidebar_buttons';
    protected $primaryKey = 'id';
}
