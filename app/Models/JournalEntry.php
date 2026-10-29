<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * @property int $id
 * @property int $household_id
 * @property string $source_type
 * @property int $source_id
 * @property int|null $reverses_entry_id
 * @property string $description
 * @property Carbon $posted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['household_id', 'source_type', 'source_id', 'reverses_entry_id', 'description', 'posted_at'])]
class JournalEntry extends Model
{
    /**
     * Posted entries are immutable: corrections are made with a reversing entry.
     */
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Journal entries are immutable.'));
        static::deleting(fn () => throw new LogicException('Journal entries are immutable.'));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * The expense or settlement this entry was posted for.
     *
     * @return MorphTo<Model, $this>
     */
    public function source(): MorphTo
    {
        // Sources are soft-deleted, so reversals of deleted expenses still resolve.
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return HasMany<JournalLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    /**
     * The entry this one reverses, if it is a reversal.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function reverses(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reverses_entry_id');
    }

    /**
     * The entry that reversed this one, if any.
     *
     * @return HasOne<JournalEntry, $this>
     */
    public function reversal(): HasOne
    {
        return $this->hasOne(JournalEntry::class, 'reverses_entry_id');
    }
}
