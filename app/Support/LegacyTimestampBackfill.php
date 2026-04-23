<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

final class LegacyTimestampBackfill
{
    /**
     * @var array<string, list<string>>
     */
    private const TABLE_COLUMNS = [
        'users' => ['created_at', 'updated_at', 'deleted_at'],
        'roles' => ['created_at', 'updated_at', 'deleted_at'],
        'permissions' => ['created_at', 'updated_at', 'deleted_at'],
        'campuses' => ['created_at', 'updated_at', 'deleted_at'],
        'colleges' => ['created_at', 'updated_at', 'deleted_at'],
        'departments' => ['created_at', 'updated_at', 'deleted_at'],
        'faculty_profiles' => ['created_at', 'updated_at', 'deleted_at'],
        'employee_profiles' => ['created_at', 'updated_at', 'deleted_at'],
        'programs' => ['created_at', 'updated_at', 'deleted_at'],
        'college_programs' => ['created_at', 'updated_at'],
        'subjects' => ['created_at', 'updated_at', 'deleted_at'],
        'rooms' => ['created_at', 'updated_at', 'deleted_at'],
        'subject_program' => ['created_at', 'updated_at'],
        'subject_categories' => ['created_at', 'updated_at'],
        'curricula' => ['created_at', 'updated_at'],
        'curriculum_entries' => ['created_at', 'updated_at'],
        'prerequisites' => ['created_at', 'updated_at'],
        'prerequisite_subjects' => ['created_at', 'updated_at'],
    ];

    public static function apply(ConnectionInterface $connection, int $hours = 8): void
    {
        self::shift($connection, $hours);
    }

    public static function revert(ConnectionInterface $connection, int $hours = 8): void
    {
        self::shift($connection, -$hours);
    }

    /**
     * @return array<string, list<string>>
     */
    public static function trackedTables(): array
    {
        return self::TABLE_COLUMNS;
    }

    private static function shift(ConnectionInterface $connection, int $hours): void
    {
        $schema = Schema::connection($connection->getName());

        foreach (self::TABLE_COLUMNS as $table => $columns) {
            if (! $schema->hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! $schema->hasColumn($table, $column)) {
                    continue;
                }

                $connection->table($table)
                    ->whereNotNull($column)
                    ->update([
                        $column => DB::raw(self::buildExpression($connection, $column, $hours)),
                    ]);
            }
        }
    }

    private static function buildExpression(ConnectionInterface $connection, string $column, int $hours): string
    {
        $wrappedColumn = $connection->getQueryGrammar()->wrap($column);
        $driver = $connection->getDriverName();

        return match ($driver) {
            'sqlite' => sprintf(
                "datetime(%s, '%s%d hours')",
                $wrappedColumn,
                $hours >= 0 ? '+' : '',
                $hours
            ),
            'mysql', 'mariadb' => sprintf(
                '%s(%s, INTERVAL %d HOUR)',
                $hours >= 0 ? 'DATE_ADD' : 'DATE_SUB',
                $wrappedColumn,
                abs($hours)
            ),
            default => throw new RuntimeException("Unsupported database driver [{$driver}] for legacy timestamp backfill."),
        };
    }
}
