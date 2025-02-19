<?php

namespace App\Models;

use Database\Factories\ProfilFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $picture
 * @property string|null $status
 * @property int|null $user_id
 * @property Administrator|null $administrator
 */
class Profil extends Model
{
    /** @use HasFactory<ProfilFactory> */
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'picture',
        'status',
        'user_id',
    ];

    public function getPictureAttribute($value): ?string
    {
        if (str_contains($value, "http://") || str_contains($value, "https://")) {
            return $value;
        }

        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * @return BelongsTo
     */
    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }
}
