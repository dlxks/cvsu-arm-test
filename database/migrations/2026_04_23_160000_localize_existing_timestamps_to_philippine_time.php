<?php

use App\Support\LegacyTimestampBackfill;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        LegacyTimestampBackfill::apply(DB::connection());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        LegacyTimestampBackfill::revert(DB::connection());
    }
};
