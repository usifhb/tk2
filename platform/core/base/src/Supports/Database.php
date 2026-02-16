<?php

namespace Botble\Base\Supports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Throwable;

class Database
{
    /**
     * Restore database from SQL file using PHP/PDO only (no mysql CLI).
     * Reads file in chunks to avoid memory issues.
     */
    public static function restoreFromPath(string $pathToSqlFile, string $connection = null): void
    {
        if (! File::exists($pathToSqlFile) || File::size($pathToSqlFile) < 1024) {
            return;
        }

        DB::purge($connection);
        DB::connection()->setDatabaseName(DB::getDatabaseName());
        DB::getSchemaBuilder()->dropAllTables();

        $handle = fopen($pathToSqlFile, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Cannot open SQL file: ' . $pathToSqlFile);
        }

        $buffer = '';
        $chunkSize = 1024 * 1024; // 1MB per read

        try {
            while (! feof($handle)) {
                $buffer .= fread($handle, $chunkSize);
                // Execute complete statements (split by ; at line end to avoid splitting inside strings)
                $parts = preg_split('/;\s*[\r\n]+/', $buffer, -1, PREG_SPLIT_NO_EMPTY);
                $buffer = array_pop($parts) ?: '';
                foreach ($parts as $statement) {
                    $statement = trim($statement);
                    if ($statement !== '' && ! self::isSqlComment($statement)) {
                        DB::connection($connection)->unprepared($statement . ';');
                    }
                }
            }
            if (trim($buffer) !== '' && ! self::isSqlComment(trim($buffer))) {
                DB::connection($connection)->unprepared($buffer . (str_ends_with(trim($buffer), ';') ? '' : ';'));
            }
        } finally {
            fclose($handle);
        }
    }

    private static function isSqlComment(string $s): bool
    {
        $s = trim($s);
        return str_starts_with($s, '--') || str_starts_with($s, '/*') || str_starts_with($s, '#');
    }
}
