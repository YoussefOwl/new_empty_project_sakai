<?php
namespace App\Models\parametrages;

use Illuminate\Database\Eloquent\Model;

class actions_logs extends Model
{
    protected $fillable = [
        'id_user',
        'libelle_log',
        'table_name',
        'description',
        'json_log_data',
        'created_at',
        'updated_at',
    ];
    protected $table = 'actions_logs';
    protected $primaryKey = 'id';
}
