<?php

namespace App\Models\users;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $fillable = [
        'id_role',
        'has_access',
        'nom',
        'prenom',
        'email',
        'password',
        'if_has_sound',
        'if_considered_in_reports',
        'is_admin',
        'image_user',
        'created_at',
        'updated_at',
    ];
    // les champs non afficher lors d'un select *
    protected $hidden = ['password'];
    protected $table = 'users';
    protected $primaryKey = 'id';

    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // base64_encode()
        // md5()
        // 1 : md5(base64_encode(1)) == "cdd96d3cc73d1dbdaffa03cc6cd7339b"
        return
        [
            'id_role_for_api_middleware' => Crypt::encryptString($this->id_role), // crypté et utilisé pour le middleware check rôles des routes sur l'api
            'api_id_user' => Crypt::encryptString($this->id), // crypté et utilisé pour le log des actions
            'unique_claim' => env('unique_claim')
        ];
    }

    public function role()
    {
        return $this->belongsTo(roles::class, 'id_role');
    }
}