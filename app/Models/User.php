<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Households the user currently belongs to.
     *
     * @return BelongsToMany<Household, $this, Membership, 'membership'>
     */
    public function households(): BelongsToMany
    {
        return $this->allHouseholds()->wherePivotNull('left_at');
    }

    /**
     * Current and former households.
     *
     * @return BelongsToMany<Household, $this, Membership, 'membership'>
     */
    public function allHouseholds(): BelongsToMany
    {
        return $this->belongsToMany(Household::class)
            ->using(Membership::class)
            ->as('membership')
            ->withPivot(['id', 'role', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Expense, $this>
     */
    public function expensesPaid(): HasMany
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    /**
     * @return HasMany<ExpenseSplit, $this>
     */
    public function expenseSplits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    /**
     * @return HasMany<Settlement, $this>
     */
    public function settlementsSent(): HasMany
    {
        return $this->hasMany(Settlement::class, 'from_user_id');
    }

    /**
     * @return HasMany<Settlement, $this>
     */
    public function settlementsReceived(): HasMany
    {
        return $this->hasMany(Settlement::class, 'to_user_id');
    }

    /**
     * @return HasMany<JournalLine, $this>
     */
    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
