<?php

use App\Models\CurriculumEntry;
use App\Models\Prerequisite;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates the expected prerequisite_subjects pivot table', function () {
    expect(Schema::hasTable('prerequisite_subjects'))->toBeTrue()
        ->and(Schema::hasColumns('prerequisite_subjects', ['prerequisite_id', 'curriculum_entry_id', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasColumn('prerequisite_subjects', 'id'))->toBeFalse();
});

it('allows prerequisites and curriculum entries to be attached through the prerequisite_subjects pivot', function () {
    $prerequisite = Prerequisite::factory()->create();
    $requiredEntry = CurriculumEntry::factory()->create([
        'subject_id' => Subject::factory()->create()->id,
    ]);

    $prerequisite->requiredCurriculumEntries()->attach($requiredEntry->id);

    expect($prerequisite->fresh()->requiredCurriculumEntries->modelKeys())->toContain($requiredEntry->id)
        ->and($requiredEntry->fresh()->dependentPrerequisites->modelKeys())->toContain($prerequisite->id);
});

it('prevents duplicate prerequisite-subject assignments', function () {
    $prerequisite = Prerequisite::factory()->create();
    $requiredEntry = CurriculumEntry::factory()->create([
        'subject_id' => Subject::factory()->create()->id,
    ]);

    $prerequisite->requiredCurriculumEntries()->attach($requiredEntry->id);

    expect(fn () => $prerequisite->requiredCurriculumEntries()->attach($requiredEntry->id))
        ->toThrow(QueryException::class);
});

it('removes pivot assignments when a prerequisite is deleted', function () {
    $prerequisite = Prerequisite::factory()->create();
    $requiredEntry = CurriculumEntry::factory()->create([
        'subject_id' => Subject::factory()->create()->id,
    ]);

    $prerequisite->requiredCurriculumEntries()->attach($requiredEntry->id);
    $prerequisite->delete();

    expect(DB::table('prerequisite_subjects')->count())->toBe(0);
});
