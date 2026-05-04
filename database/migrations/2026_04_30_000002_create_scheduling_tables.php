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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('sched_code', 15)->unique();
            $table->foreignId('subject_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('campus_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('college_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('semester');
            $table->string('school_year');
            $table->unsignedInteger('slots')->default(40);
            $table->enum('status', ['draft', 'pending_service_acceptance', 'pending_plotting', 'plotted', 'published'])->default('draft');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['campus_id', 'college_id', 'semester', 'school_year'], 'schedules_context_index');
            $table->index(['status', 'department_id'], 'schedules_status_department_index');
            $table->index(['college_id', 'status', 'id'], 'schedules_college_status_id_index');
            $table->index(['campus_id', 'college_id', 'department_id', 'status'], 'schedules_scope_status_index');
            $table->index(['college_id', 'status', 'sched_code'], 'schedules_college_status_code_index');
        });

        Schema::create('schedule_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('program_code');
            $table->unsignedTinyInteger('year_level')->nullable();
            $table->string('section_identifier');
            $table->enum('section_type', ['REGULAR', 'IRREGULAR', 'PETITION', 'NSTP', 'OTHERS']);
            $table->string('computed_section_name');
            $table->timestamps();

            $table->index(['program_code', 'year_level'], 'schedule_section_program_year_index');
            $table->index('computed_section_name', 'schedule_section_name_index');
            $table->index(['schedule_id', 'computed_section_name'], 'schedule_section_sched_name_index');
        });

        Schema::create('schedule_room_time', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('schedule_category_id')->constrained('schedule_categories')->cascadeOnUpdate();
            $table->enum('day', ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'])->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->timestamps();

            $table->index(['day', 'time_in', 'time_out'], 'schedule_room_time_day_time_index');
            $table->index(['room_id', 'day', 'time_in', 'time_out'], 'schedule_room_time_room_day_time_index');
            $table->index(['schedule_id', 'schedule_category_id', 'day'], 'srt_schedule_category_day_idx');
            $table->index(['day', 'schedule_category_id', 'time_in', 'time_out'], 'srt_day_category_time_idx');
        });

        Schema::create('schedule_faculty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('schedule_category_id')->constrained('schedule_categories')->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['schedule_id', 'schedule_category_id'], 'schedule_faculty_sched_category_uq');
            $table->index(['user_id', 'schedule_category_id'], 'schedule_faculty_user_category_idx');
        });

        Schema::create('schedule_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requesting_college_id')->constrained('colleges')->cascadeOnUpdate();
            $table->foreignId('servicing_college_id')->constrained('colleges')->cascadeOnUpdate();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'assigned_to_dept', 'dept_submitted', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_department_id')->nullable()->constrained('departments')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index(['servicing_college_id', 'status', 'updated_at'], 'ssr_servicing_status_updated_idx');
            $table->index(['requesting_college_id', 'updated_at'], 'ssr_requesting_updated_idx');
            $table->index(['assigned_department_id', 'status'], 'ssr_assigned_status_idx');
        });

        Schema::create('schedule_service_request_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')
                ->constrained('schedule_service_requests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['service_request_id', 'schedule_id'], 'srrs_service_schedule_unique');
            $table->index(['schedule_id', 'service_request_id'], 'srrs_schedule_service_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_service_request_schedules');
        Schema::dropIfExists('schedule_service_requests');
        Schema::dropIfExists('schedule_faculty');
        Schema::dropIfExists('schedule_room_time');
        Schema::dropIfExists('schedule_section');
        Schema::dropIfExists('schedules');
    }
};
