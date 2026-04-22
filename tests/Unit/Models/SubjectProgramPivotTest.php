<?php

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates the expected subject_program pivot table', function () {
    expect(Schema::hasTable('subject_program'))->toBeTrue()
        ->and(Schema::hasColumns('subject_program', ['subject_id', 'program_id', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasColumn('subject_program', 'id'))->toBeFalse();
});

it('allows subjects and programs to be attached through the subject_program pivot', function () {
    $subject = Subject::factory()->create();
    $program = Program::factory()->create();

    $subject->programs()->attach($program->id);

    expect($subject->fresh()->programs->modelKeys())->toContain($program->id)
        ->and($program->fresh()->subjects->modelKeys())->toContain($subject->id);
});

it('prevents duplicate subject-program assignments', function () {
    $subject = Subject::factory()->create();
    $program = Program::factory()->create();

    $subject->programs()->attach($program->id);

    expect(fn () => $subject->programs()->attach($program->id))
        ->toThrow(QueryException::class);
});

it('removes pivot assignments when a subject is force deleted', function () {
    $subject = Subject::factory()->create();
    $program = Program::factory()->create();

    $subject->programs()->attach($program->id);
    $subject->forceDelete();

    expect(DB::table('subject_program')->count())->toBe(0);
});
