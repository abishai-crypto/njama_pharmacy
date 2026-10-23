<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackupLog;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupLog::with('creator')->latest()->paginate(15);
        return view('backups.index', compact('backups'));
    }

    public function createBackup(Request $request)
    {
        try {
            $dbName = config('database.connections.mysql.database', 'njama_pharmacy');
            $filename = 'backup_njama_pharmacy_' . date('Y_m_d_His') . '.sql';
            $backupDir = storage_path('app/backups');

            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Generate clean SQL backup file
            $tables = DB::select('SHOW TABLES');
            $dbKey = "Tables_in_{$dbName}";

            $handle = fopen($filepath, 'w');
            fwrite($handle, "-- NJAMA PHARMACY DATABASE BACKUP\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Database: {$dbName}\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $tableObj) {
                $tableName = (array)$tableObj;
                $table = reset($tableName);

                // Table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createArr = (array)$createTable[0];
                $createSql = $createArr['Create Table'] ?? reset($createArr);

                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createSql . ";\n\n");

                // Table data
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArr = (array)$row;
                    $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArr));
                    $values = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes((string)$val) . "'";
                    }, array_values($rowArr));

                    fwrite($handle, "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n");
                }
                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);

            $sizeBytes = filesize($filepath);

            BackupLog::create([
                'filename' => $filename,
                'disk' => 'local',
                'size_bytes' => $sizeBytes,
                'status' => 'completed',
                'created_by' => auth()->id(),
                'notes' => 'Manual full database backup',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_created',
                'description' => "Created database backup {$filename} (" . round($sizeBytes / 1024, 2) . " KB)",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return back()->with('success', "Hifadhi ya nakala (Backup) '{$filename}' imetengenezwa kikamilifu.");

        } catch (\Exception $e) {
            return back()->with('error', 'Hitilafu wakati wa kutengeneza backup: ' . $e->getMessage());
        }
    }

    public function download(BackupLog $backup)
    {
        $filepath = storage_path('app/backups/' . $backup->filename);
        if (!file_exists($filepath)) {
            return back()->with('error', 'Faili ya backup haikupatikana.');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup_downloaded',
            'description' => "Downloaded database backup {$backup->filename}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return response()->download($filepath);
    }
}
