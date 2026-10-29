<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained()->restrictOnDelete();
            $table->morphs('source');
            // Unique: an entry can only be reversed once. Postgres allows many NULLs.
            $table->foreignId('reverses_entry_id')->nullable()->unique()
                ->constrained('journal_entries')->restrictOnDelete();
            $table->string('description');
            $table->timestamp('posted_at');
            $table->timestamps();

            $table->index(['household_id', 'posted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
