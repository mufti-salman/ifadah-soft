<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseSystemStatusController extends Controller
{
    /**
     * Display database and system status with backup history.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Database Status
        |--------------------------------------------------------------------------
        */

        $databaseStatus = [
            'connected' => false,
            'driver' => config('database.default'),
            'database' => config(
                'database.connections.' . config('database.default') . '.database'
            ),
            'message' => 'Database connection failed.',
            'tables_count' => 0,
        ];

        $tableNames = [];

        try {
            DB::connection()->getPdo();

            $databaseStatus['connected'] = true;
            $databaseStatus['message'] = 'Database connection successful.';

            if (config('database.default') === 'mysql') {
                $tableNames = $this->getDatabaseTables();
            }

            $databaseStatus['tables_count'] = count($tableNames);

        } catch (\Throwable $e) {
            report($e);

            $databaseStatus['message'] =
                'Database connection could not be verified.';
        }

        /*
        |--------------------------------------------------------------------------
        | Application Information
        |--------------------------------------------------------------------------
        */

        $systemInfo = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_debug' => (bool) config('app.debug'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Storage Status
        |--------------------------------------------------------------------------
        */

        $storageStatus = [
            'writable' => is_writable(storage_path()),

            'framework_writable' => is_writable(
                storage_path('framework')
            ),

            'logs_writable' => is_writable(
                storage_path('logs')
            ),

            'public_disk' => config('filesystems.default'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Cache Status
        |--------------------------------------------------------------------------
        */

        $cacheStatus = [
            'driver' => config('cache.default'),
            'configured' => !empty(config('cache.default')),
        ];

        /*
        |--------------------------------------------------------------------------
        | Queue Status
        |--------------------------------------------------------------------------
        */

        $queueStatus = [
            'driver' => config('queue.default'),
            'configured' => !empty(config('queue.default')),
        ];

        /*
        |--------------------------------------------------------------------------
        | Maintenance Mode
        |--------------------------------------------------------------------------
        */

        $maintenanceStatus = app()->isDownForMaintenance();

        /*
        |--------------------------------------------------------------------------
        | Disk Space
        |--------------------------------------------------------------------------
        */

        $diskStatus = [
            'free_bytes' => @disk_free_space(base_path()),
            'total_bytes' => @disk_total_space(base_path()),
        ];

        /*
        |--------------------------------------------------------------------------
        | Backup Directory
        |--------------------------------------------------------------------------
        |
        | Backup files are stored outside the public directory.
        | If the directory does not exist, try to create it.
        |
        */

        $backupDirectory = $this->backupDirectory();

        try {
            $this->ensureBackupDirectory();
        } catch (\Throwable $e) {
            report($e);
        }

        /*
        |--------------------------------------------------------------------------
        | Backup History
        |--------------------------------------------------------------------------
        */

        $backupHistory = $this->getBackupHistory();

        /*
        |--------------------------------------------------------------------------
        | Backup Statistics
        |--------------------------------------------------------------------------
        */

        $backupStats = [
            'total' => count($backupHistory),

            'total_bytes' => array_sum(
                array_column($backupHistory, 'size_bytes')
            ),

            'latest' => $backupHistory[0] ?? null,

            'directory_writable' => (
                is_dir($backupDirectory)
                && is_writable($backupDirectory)
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'super-admin.database-system-status.index',
            compact(
                'databaseStatus',
                'systemInfo',
                'storageStatus',
                'cacheStatus',
                'queueStatus',
                'maintenanceStatus',
                'diskStatus',
                'tableNames',
                'backupHistory',
                'backupStats'
            )
        );
    }

    /**
     * Create a full database backup or selected-table backup.
     */
    public function createBackup(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Check Database Driver
        |--------------------------------------------------------------------------
        */

        if (config('database.default') !== 'mysql') {
            return back()->with(
                'error',
                'এই Backup System বর্তমানে MySQL/MariaDB-এর জন্য তৈরি।'
            );
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Verify Database Connection
            |--------------------------------------------------------------------------
            */

            DB::connection()->getPdo();

            /*
            |--------------------------------------------------------------------------
            | Get Available Tables
            |--------------------------------------------------------------------------
            */

            $tableNames = $this->getDatabaseTables();

            if (empty($tableNames)) {
                return back()->with(
                    'error',
                    'ডেটাবেজে কোনো টেবিল পাওয়া যায়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */

            $validated = $request->validate([
                'backup_type' => [
                    'required',
                    Rule::in(['full', 'selected']),
                ],

                'selected_tables' => [
                    'required_if:backup_type,selected',
                    'array',
                    'min:1',
                ],

                'selected_tables.*' => [
                    'required',
                    'string',
                    'distinct',
                    Rule::in($tableNames),
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Determine Tables
            |--------------------------------------------------------------------------
            */

            $backupType = $validated['backup_type'];

            if ($backupType === 'full') {
                $tables = $tableNames;
            } else {
                $tables = $validated['selected_tables'];
            }

            $tables = array_values(
                array_unique($tables)
            );

            if (empty($tables)) {
                return back()->with(
                    'error',
                    'Backup তৈরির জন্য কোনো টেবিল পাওয়া যায়নি।'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Prepare Backup Directory
            |--------------------------------------------------------------------------
            */

            $this->ensureBackupDirectory();

            /*
            |--------------------------------------------------------------------------
            | Generate Unique Filename
            |--------------------------------------------------------------------------
            */

            $timestamp = now()->format('Y-m-d_H-i-s');

            $filename = sprintf(
                'database_%s_%s_%s.sql.gz',
                $backupType,
                $timestamp,
                bin2hex(random_bytes(4))
            );

            $path = $this->backupDirectory()
                . DIRECTORY_SEPARATOR
                . $filename;

            /*
            |--------------------------------------------------------------------------
            | Generate Backup
            |--------------------------------------------------------------------------
            */

            $this->writeDatabaseBackup(
                $path,
                $tables
            );

            clearstatcache(true, $path);

            /*
            |--------------------------------------------------------------------------
            | Verify Backup File
            |--------------------------------------------------------------------------
            */

            if (
                !is_file($path)
                || filesize($path) === false
                || filesize($path) === 0
            ) {
                if (is_file($path)) {
                    @unlink($path);
                }

                throw new \RuntimeException(
                    'Backup file was not created correctly.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Log Successful Backup
            |--------------------------------------------------------------------------
            */

            Log::info('Database backup created successfully.', [
                'type' => $backupType,
                'tables_count' => count($tables),
                'filename' => $filename,
                'size_bytes' => filesize($path),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            $backupLabel = $backupType === 'full'
                ? 'Full Database'
                : 'Selected Tables';

            return redirect()
                ->route('dashboard.database-system-status.index')
                ->with(
                    'success',
                    $backupLabel
                    . ' Backup সফল হয়েছে। '
                    . count($tables)
                    . 'টি টেবিল সংরক্ষণ করা হয়েছে।'
                );

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            report($e);

            Log::error('Database backup failed.', [
                'message' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Database Backup তৈরি করা যায়নি। '
                . 'Database connection, backup directory permission '
                . 'এবং Laravel log পরীক্ষা করুন।'
            );
        }
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup(
        string $filename
    ): BinaryFileResponse {
        $path = $this->resolveBackupPath($filename);

        abort_unless(
            $path !== null,
            404,
            'Backup file পাওয়া যায়নি।'
        );

        return response()->download(
            $path,
            $filename,
            [
                'Content-Type' => 'application/gzip',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename)
    {
        $path = $this->resolveBackupPath($filename);

        abort_unless(
            $path !== null,
            404,
            'Backup file পাওয়া যায়নি।'
        );

        if (!@unlink($path)) {
            return back()->with(
                'error',
                'Backup ফাইল মুছে ফেলা যায়নি। '
                . 'Storage permission পরীক্ষা করুন।'
            );
        }

        Log::info('Database backup deleted.', [
            'filename' => $filename,
        ]);

        return back()->with(
            'success',
            'Backup ফাইল সফলভাবে মুছে ফেলা হয়েছে।'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Database Table Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return base table names from MySQL/MariaDB.
     */
    private function getDatabaseTables(): array
    {
        $rows = DB::select('SHOW FULL TABLES');

        $tables = [];

        foreach ($rows as $row) {
            $values = array_values((array) $row);

            $tableName = $values[0] ?? null;
            $tableType = $values[1] ?? null;

            if (!$tableName) {
                continue;
            }

            /*
             * Exclude views because this backup exporter
             * handles physical tables.
             */
            if (
                $tableType !== null
                && strtoupper((string) $tableType) !== 'BASE TABLE'
            ) {
                continue;
            }

            $tables[] = (string) $tableName;
        }

        sort($tables);

        return $tables;
    }

    /*
    |--------------------------------------------------------------------------
    | Backup Directory Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return the private backup directory.
     */
    private function backupDirectory(): string
    {
        return storage_path(
            'app/private/database-backups'
        );
    }

    /**
     * Create and verify the backup directory.
     */
    private function ensureBackupDirectory(): void
    {
        $directory = $this->backupDirectory();

        if (!is_dir($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true,
                true
            );
        }

        clearstatcache(true, $directory);

        if (!is_dir($directory)) {
            throw new \RuntimeException(
                'Backup directory could not be created: '
                . $directory
            );
        }

        if (!is_writable($directory)) {
            throw new \RuntimeException(
                'Backup directory is not writable: '
                . $directory
            );
        }
    }

    /**
     * Resolve a safe backup path.
     *
     * Only generated backup filenames are accepted.
     */
    private function resolveBackupPath(
        string $filename
    ): ?string {
        $pattern = '/\Adatabase_(?:full|selected)_'
            . '\d{4}-\d{2}-\d{2}_'
            . '\d{2}-\d{2}-\d{2}_'
            . '[a-f0-9]{8}\.sql\.gz\z/';

        if (!preg_match($pattern, $filename)) {
            return null;
        }

        $directory = $this->backupDirectory();

        $path = $directory
            . DIRECTORY_SEPARATOR
            . $filename;

        $realDirectory = realpath($directory);
        $realPath = realpath($path);

        if (
            $realDirectory === false
            || $realPath === false
            || !is_file($realPath)
        ) {
            return null;
        }

        if (dirname($realPath) !== $realDirectory) {
            return null;
        }

        return $realPath;
    }

    /*
    |--------------------------------------------------------------------------
    | SQL Backup Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Write a compressed SQL backup.
     *
     * Exports table definitions and rows in batches.
     */
    private function writeDatabaseBackup(
        string $path,
        array $tables
    ): void {
        $gzip = gzopen($path, 'wb6');

        if ($gzip === false) {
            throw new \RuntimeException(
                'Could not open backup file for writing.'
            );
        }

        $completed = false;

        try {
            $databaseName = config(
                'database.connections.mysql.database'
            );

            $header = "-- Database Backup\n"
                . '-- Database: ' . $databaseName . "\n"
                . '-- Created: ' . now()->toDateTimeString() . "\n"
                . "-- Tables and data exported by Laravel.\n\n"
                . "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $this->writeGzip($gzip, $header);

            foreach ($tables as $table) {
                $quotedTable = $this->quoteIdentifier($table);

                /*
                |--------------------------------------------------------------------------
                | Read Table Structure
                |--------------------------------------------------------------------------
                */

                $createResult = DB::select(
                    'SHOW CREATE TABLE ' . $quotedTable
                );

                if (empty($createResult)) {
                    throw new \RuntimeException(
                        'Could not read table schema: ' . $table
                    );
                }

                $createValues = array_values(
                    (array) $createResult[0]
                );

                $createSql = $createValues[1] ?? null;

                if (!$createSql) {
                    throw new \RuntimeException(
                        'Could not read CREATE TABLE statement: '
                        . $table
                    );
                }

                $tableHeader = "\n-- ----------------------------------------\n"
                    . '-- Table: ' . $table . "\n"
                    . "-- ----------------------------------------\n"
                    . 'DROP TABLE IF EXISTS ' . $quotedTable . ";\n"
                    . $createSql . ";\n\n";

                $this->writeGzip(
                    $gzip,
                    $tableHeader
                );

                /*
                |--------------------------------------------------------------------------
                | Read Column Names
                |--------------------------------------------------------------------------
                */

                $columns = DB::getSchemaBuilder()
                    ->getColumnListing($table);

                if (empty($columns)) {
                    continue;
                }

                $quotedColumns = implode(
                    ', ',
                    array_map(
                        fn ($column) => $this->quoteIdentifier($column),
                        $columns
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | Export Table Data
                |--------------------------------------------------------------------------
                */

                $pdo = DB::connection()->getPdo();

                foreach (DB::table($table)->cursor() as $row) {
                    $rowValues = (array) $row;

                    $values = [];

                    foreach ($columns as $column) {
                        $value = $rowValues[$column] ?? null;

                        if ($value === null) {
                            $values[] = 'NULL';
                            continue;
                        }

                        $quotedValue = $pdo->quote(
                            (string) $value
                        );

                        if ($quotedValue === false) {
                            throw new \RuntimeException(
                                'Could not encode a database value in table: '
                                . $table
                            );
                        }

                        $values[] = $quotedValue;
                    }

                    $insertSql = 'INSERT INTO '
                        . $quotedTable
                        . ' ('
                        . $quotedColumns
                        . ') VALUES ('
                        . implode(', ', $values)
                        . ");\n";

                    $this->writeGzip(
                        $gzip,
                        $insertSql
                    );
                }

                $this->writeGzip(
                    $gzip,
                    "\n"
                );
            }

            $this->writeGzip(
                $gzip,
                "SET FOREIGN_KEY_CHECKS=1;\n"
            );

            $completed = true;

        } finally {
            $closed = gzclose($gzip);

            if (
                (!$completed || !$closed)
                && is_file($path)
            ) {
                @unlink($path);
            }
        }
    }

    /**
     * Quote a MySQL identifier.
     */
    private function quoteIdentifier(
        string $identifier
    ): string {
        return '`'
            . str_replace('`', '``', $identifier)
            . '`';
    }

    /**
     * Write data to a gzip stream.
     */
    private function writeGzip(
        $gzip,
        string $contents
    ): void {
        $length = strlen($contents);
        $offset = 0;

        while ($offset < $length) {
            $written = gzwrite(
                $gzip,
                substr($contents, $offset)
            );

            if ($written === false || $written === 0) {
                throw new \RuntimeException(
                    'Failed while writing the backup file.'
                );
            }

            $offset += $written;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Backup History Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Return backup history, newest first.
     */
    private function getBackupHistory(): array
    {
        $directory = $this->backupDirectory();

        if (!is_dir($directory)) {
            return [];
        }

        try {
            $files = File::files($directory);
        } catch (\Throwable $e) {
            report($e);

            return [];
        }

        $history = [];

        $pattern = '/\Adatabase_(?:full|selected)_'
            . '\d{4}-\d{2}-\d{2}_'
            . '\d{2}-\d{2}-\d{2}_'
            . '[a-f0-9]{8}\.sql\.gz\z/';

        foreach ($files as $file) {
            $filename = $file->getFilename();

            if (!preg_match($pattern, $filename)) {
                continue;
            }

            $sizeBytes = $file->getSize();
            $modifiedAt = $file->getMTime();

            $history[] = [
                'filename' => $filename,

                'type' => str_starts_with(
                    $filename,
                    'database_full_'
                )
                    ? 'Full Database'
                    : 'Selected Tables',

                'size_bytes' => $sizeBytes,

                'size' => $this->formatBytes(
                    $sizeBytes
                ),

                'created_at' => date(
                    'd M Y, h:i A',
                    $modifiedAt
                ),

                'timestamp' => $modifiedAt,
            ];
        }

        usort(
            $history,
            fn ($a, $b) =>
                $b['timestamp'] <=> $a['timestamp']
        );

        return $history;
    }

    /**
     * Convert bytes into a readable size.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format(
                $bytes / 1073741824,
                2
            ) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format(
                $bytes / 1048576,
                2
            ) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format(
                $bytes / 1024,
                2
            ) . ' KB';
        }

        return $bytes . ' bytes';
    }
}