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
        Schema::create('subject_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('year_implemented');
            $table->timestamps();

            $table->unique(['program_id', 'title', 'year_implemented']);
            $table->index(['program_id', 'year_implemented']);
        });

        Schema::create('curriculum_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_category_id')->constrained('subject_categories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('semester', ['1ST', '2ND', 'SUMMER']);
            $table->unsignedTinyInteger('year_level');
            $table->timestamps();

            $table->unique(['curriculum_id', 'subject_id', 'semester', 'year_level'], 'curriculum_entries_schedule_unique');
            $table->index(['curriculum_id', 'year_level', 'semester'], 'curriculum_entries_curriculum_term_index');
        });

        Schema::create('prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_entry_id')->constrained('curriculum_entries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('label');
            $table->timestamps();
        });

        Schema::create('prerequisite_subjects', function (Blueprint $table) {
            $table->foreignId('prerequisite_id')->constrained('prerequisites')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('curriculum_entry_id')->constrained('curriculum_entries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['prerequisite_id', 'curriculum_entry_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prerequisite_subjects');
        Schema::dropIfExists('prerequisites');
        Schema::dropIfExists('curriculum_entries');
        Schema::dropIfExists('curricula');
        Schema::dropIfExists('subject_categories');
    }
};
