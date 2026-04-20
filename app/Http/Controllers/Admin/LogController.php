<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Display logs page.
     */
    public function index(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logs = $this->getLogs($logFile);
        $logFiles = $this->getAvailableLogFiles();
        
        // Statistiques
        $stats = $this->getLogStats($logs);
        
        // Filtres
        $filteredLogs = $this->filterLogs($logs, $request);
        
        return view('admin.logs.index', compact('filteredLogs', 'logFiles', 'logFile', 'stats'));
    }
    
    /**
     * Display activity logs.
     */
    public function activity(Request $request)
    {
        $query = ActivityLog::with('user')->latest();
        
        // Filtres
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%' . $request->model . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $activities = $query->paginate(20);
        
        // Statistiques
        $stats = [
            'total_activities' => ActivityLog::count(),
            'today_activities' => ActivityLog::whereDate('created_at', today())->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
            'actions' => ActivityLog::select('action')->distinct()->pluck('action'),
        ];
        
        return view('admin.logs.activity', compact('activities', 'stats'));
    }
    
    /**
     * Show log details.
     */
    public function show($date, $filename)
    {
        $logPath = storage_path('logs/' . $date . '/' . $filename);
        
        if (!File::exists($logPath)) {
            abort(404, 'Fichier de log non trouvé.');
        }
        
        $content = File::get($logPath);
        $logs = $this->parseLogContent($content);
        
        return view('admin.logs.show', compact('logs', 'date', 'filename'));
    }
    
    /**
     * Download log file.
     */
    public function download(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logPath = storage_path('logs/' . $logFile);
        
        if (!File::exists($logPath)) {
            abort(404, 'Fichier de log non trouvé.');
        }
        
        return response()->download($logPath);
    }
    
    /**
     * Clear logs.
     */
    public function clear(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logPath = storage_path('logs/' . $logFile);
        
        if (File::exists($logPath)) {
            File::put($logPath, '');
            $this->logActivity('clear_logs', 'System', "Logs cleared: {$logFile}");
        }
        
        return back()->with('success', 'Logs effacés avec succès.');
    }
    
    /**
     * Delete log file.
     */
    public function destroy($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (File::exists($logPath)) {
            File::delete($logPath);
            $this->logActivity('delete_log', 'System', "Log file deleted: {$filename}");
        }
        
        return back()->with('success', 'Fichier de log supprimé.');
    }
    
    /**
     * Search logs.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $logFile = $request->get('file', 'laravel.log');
        $logs = $this->getLogs($logFile);
        
        $filteredLogs = array_filter($logs, function($log) use ($query) {
            return stripos($log['message'], $query) !== false ||
                   stripos($log['level'], $query) !== false ||
                   stripos($log['context'], $query) !== false;
        });
        
        $logFiles = $this->getAvailableLogFiles();
        $stats = $this->getLogStats($logs);
        
        return view('admin.logs.index', [
            'filteredLogs' => array_values($filteredLogs),
            'logFiles' => $logFiles,
            'logFile' => $logFile,
            'stats' => $stats,
            'searchQuery' => $query
        ]);
    }
    
    /**
     * Export logs.
     */
    public function export(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logs = $this->getLogs($logFile);
        $format = $request->get('format', 'json');
        
        $filteredLogs = $this->filterLogs($logs, $request);
        
        if ($format === 'json') {
            return response()->json($filteredLogs, 200, [
                'Content-Disposition' => 'attachment; filename="logs-export.json"'
            ]);
        }
        
        if ($format === 'csv') {
            return $this->exportAsCsv($filteredLogs);
        }
        
        return back()->with('error', 'Format d\'export non supporté.');
    }
    
    /**
     * Get system information.
     */
    public function system()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'database' => config('database.default'),
            'database_version' => $this->getDatabaseVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'disk_free_space' => $this->formatBytes(disk_free_space(storage_path())),
            'disk_total_space' => $this->formatBytes(disk_total_space(storage_path())),
        ];
        
        return view('admin.logs.system', compact('systemInfo'));
    }
    
    /**
     * Get available log files.
     */
    private function getAvailableLogFiles(): array
    {
        $files = [];
        $logPath = storage_path('logs');
        
        if (File::exists($logPath)) {
            $allFiles = File::files($logPath);
            foreach ($allFiles as $file) {
                if ($file->getExtension() === 'log') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'size_raw' => $file->getSize(),
                    ];
                }
            }
        }
        
        // Trier par date de modification (plus récent en premier)
        usort($files, fn($a, $b) => $b['size_raw'] <=> $a['size_raw']);
        
        return $files;
    }
    
    /**
     * Get logs from file.
     */
    private function getLogs(string $filename): array
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return [];
        }
        
        $content = File::get($logPath);
        return $this->parseLogContent($content);
    }
    
    /**
     * Parse log content.
     */
    private function parseLogContent(string $content): array
    {
        $logs = [];
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?:\n|$)/s';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $index => $match) {
            $logs[] = [
                'id' => $index,
                'timestamp' => $match[1],
                'level' => $match[3],
                'environment' => $match[2],
                'message' => trim($match[4]),
                'context' => $this->extractContext($match[4]),
                'raw' => $match[0],
            ];
        }
        
        return array_reverse($logs);
    }
    
    /**
     * Extract context from log message.
     */
    private function extractContext(string $message): string
    {
        if (preg_match('/\{.*\}/', $message, $matches)) {
            return $matches[0];
        }
        
        return '';
    }
    
    /**
     * Filter logs based on request.
     */
    private function filterLogs(array $logs, Request $request): array
    {
        return array_filter($logs, function($log) use ($request) {
            if ($request->filled('level') && strtolower($log['level']) !== strtolower($request->level)) {
                return false;
            }
            
            if ($request->filled('date')) {
                $logDate = substr($log['timestamp'], 0, 10);
                if ($logDate !== $request->date) {
                    return false;
                }
            }
            
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                return stripos($log['message'], $search) !== false ||
                       stripos($log['level'], $search) !== false;
            }
            
            return true;
        });
    }
    
    /**
     * Get log statistics.
     */
    private function getLogStats(array $logs): array
    {
        $stats = [
            'total' => count($logs),
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
            'critical' => 0,
        ];
        
        foreach ($logs as $log) {
            $level = strtolower($log['level']);
            if (isset($stats[$level])) {
                $stats[$level]++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Export logs as CSV.
     */
    private function exportAsCsv(array $logs)
    {
        $filename = 'logs-export-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'Level', 'Environment', 'Message']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log['timestamp'],
                    $log['level'],
                    $log['environment'],
                    $log['message']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get database version.
     */
    private function getDatabaseVersion(): string
    {
        try {
            $pdo = \DB::connection()->getPdo();
            return $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    /**
     * Format bytes to human readable.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Log an activity.
     */
    private function logActivity(string $action, string $model, string $details): void
    {
        if (class_exists('App\Models\ActivityLog')) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model,
                'model_id' => null,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}