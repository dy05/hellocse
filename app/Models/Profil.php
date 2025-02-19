<?php

namespace App\Models;

use Database\Factories\ProfilFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    /** @use HasFactory<ProfilFactory> */
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'picture',
        'status',
    ];
}
