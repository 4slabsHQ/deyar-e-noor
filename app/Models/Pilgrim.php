<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use Database\Factories\PilgrimFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Pilgrim extends Model
{
    /** @use HasFactory<PilgrimFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hajj_year',
        'entry_date',
        'form_owner_id',
        'company_id',
        'maktab_category_id',
        'package_id',
        'qurbani_included',
        'care_off_id',
        'pod_city_id',
        'room_type_id',
        'gender',
        'surname',
        'given_name',
        'father_husband_name',
        'full_name',
        'passport_no',
        'date_of_birth',
        'birth_place',
        'passport_expiry',
        'address',
        'mobile',
        'cnic',
        'blood_group',
        'mehram_name',
        'mehram_relation_id',
        'waris_name',
        'waris_cnic',
        'waris_relation_id',
        'waris_mobile',
        'family_code',
        'family_number',
        'family_member_suffix',
        'age',
        'photo_path',
        'passport_path',
        'visa_path',
        'ticket_path',
        'comments',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'hajj_year' => 'integer',
            'entry_date' => 'date',
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
            'gender' => Gender::class,
            'blood_group' => BloodGroup::class,
            'family_number' => 'integer',
            'age' => 'integer',
            'qurbani_included' => 'boolean',
        ];
    }

    public function formOwner(): BelongsTo
    {
        return $this->belongsTo(FormOwner::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function maktabCategory(): BelongsTo
    {
        return $this->belongsTo(MaktabCategory::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function careOff(): BelongsTo
    {
        return $this->belongsTo(CareOff::class);
    }

    public function podCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'pod_city_id');
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function mehramRelation(): BelongsTo
    {
        return $this->belongsTo(MehramRelation::class);
    }

    public function warisRelation(): BelongsTo
    {
        return $this->belongsTo(WarisRelation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function flights(): BelongsToMany
    {
        return $this->belongsToMany(Flight::class)
            ->withPivot('assigned_by')
            ->withTimestamps();
    }

    /** @return Attribute<?string, never> */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicStorageUrl($this->photo_path));
    }

    /** @return Attribute<?string, never> */
    protected function passportUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicStorageUrl($this->passport_path));
    }

    /** @return Attribute<?string, never> */
    protected function visaUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicStorageUrl($this->visa_path));
    }

    /** @return Attribute<?string, never> */
    protected function ticketUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->publicStorageUrl($this->ticket_path));
    }

    protected function publicStorageUrl(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.$path);
    }
}
