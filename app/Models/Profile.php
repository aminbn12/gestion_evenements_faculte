<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'birth_date',
        'gender',
        'nationality',
        'cin',
        'address',
        'city',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'linkedin',
        'orcid',
        'researchgate',
        'google_scholar',
        'bio',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country, $this->postal_code]);
        return implode(', ', $parts);
    }

    /**
     * Get the profile's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }
}
