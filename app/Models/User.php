<?php

namespace App\Models;

use App\Concerns\GuardsDeletionWhenReferenced;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $photo_path
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property bool $is_active
 * @property array<string, mixed>|null $report_preferences
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'photo_path', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use GuardsDeletionWhenReferenced, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'report_preferences' => 'array',
        ];
    }

    /** @param  Builder<User>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function createdPilgrims(): HasMany
    {
        return $this->hasMany(Pilgrim::class, 'created_by');
    }

    public function updatedPilgrims(): HasMany
    {
        return $this->hasMany(Pilgrim::class, 'updated_by');
    }

    public function createdFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'created_by');
    }

    public function createdAirlines(): HasMany
    {
        return $this->hasMany(Airline::class, 'created_by');
    }

    public function createdAirports(): HasMany
    {
        return $this->hasMany(Airport::class, 'created_by');
    }

    public function activatedHajjSeasons(): HasMany
    {
        return $this->hasMany(HajjSeason::class, 'activated_by');
    }

    /** @return array<string, string> */
    public function referencedByRelations(): array
    {
        return [
            'createdPilgrims' => 'Hajj registrations entered',
            'createdFlights' => 'flights entered',
            'createdAirlines' => 'airlines entered',
            'createdAirports' => 'airports entered',
            'activatedHajjSeasons' => 'Hajj seasons activated',
        ];
    }

    /** @return array<int, callable(User): ?string> */
    public function referencedByChecks(): array
    {
        return [
            function (User $user): ?string {
                $count = Pilgrim::query()->where('updated_by', $user->id)->count();

                if ($count === 0) {
                    return null;
                }

                return sprintf(
                    'Cannot delete this user because they are linked to %d updated Hajj registration(s).',
                    $count,
                );
            },
        ];
    }

    public function deletionResourceLabel(): string
    {
        return 'user';
    }

    /** @return list<string>|null */
    public function reportColumns(string $reportKey): ?array
    {
        $columns = $this->report_preferences[$reportKey] ?? null;

        if (! is_array($columns) || $columns === []) {
            return null;
        }

        /** @var list<string> $columns */
        return array_values($columns);
    }

    /** @param  list<string>  $columns */
    public function saveReportColumns(string $reportKey, array $columns): void
    {
        $preferences = $this->report_preferences ?? [];
        $preferences[$reportKey] = array_values($columns);

        $this->forceFill(['report_preferences' => $preferences])->save();
    }

    /** @return Attribute<?string, never> */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->photo_path || ! Storage::disk('public')->exists($this->photo_path)) {
                return null;
            }

            return asset('storage/'.$this->photo_path);
        });
    }
}
