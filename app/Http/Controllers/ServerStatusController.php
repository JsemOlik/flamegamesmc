<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\AdminLog;

class ServerStatusController extends Controller
{
    public function index()
    {
        $result = Cache::remember('server_status_data', 15, function () {
            $panelUrl = rtrim(env('PTERODACTYL_PANEL_URL1'), '/');
            $apiKey = env('PTERODACTYL_API_KEY1');

            $serversToCheck = [
                'fe89fc90',
                '3db54e5e',
                '0aef5bde',
                '2027770a',
                '271c6921'
            ];

            $result = [];

            foreach ($serversToCheck as $serverId) {
                // Get server details
                $detailsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->get("$panelUrl/api/client/servers/$serverId");

                if (!$detailsResponse->ok()) {
                    continue;
                }

                $serverInfo = $detailsResponse->json()['attributes'];
                $name = $serverInfo['name'];

                // Get resource usage
                $usageResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Accept' => 'application/json',
                ])->get("$panelUrl/api/client/servers/$serverId/resources");

                if (!$usageResponse->ok()) {
                    continue;
                }

                $usage = $usageResponse->json();
                $status = $usage['attributes']['current_state'];
                $isOnline = $status === 'running';
                $memoryBytes = $usage['attributes']['resources']['memory_bytes'] ?? 0;
                $memoryMBUsed = round($memoryBytes / 1024 / 1024);
                $memoryLimitMB = $serverInfo['limits']['memory'] ?? 0;
                $memoryMB = $memoryMBUsed . ' MB / ' . $memoryLimitMB . ' MB';

                $result[] = [
                    'id' => $serverId,
                    'name' => $name,
                    'online' => $isOnline,
                    'players' => 0,
                    'tps' => 0,
                    'ram' => $memoryMB,
                    'status' => $status,
                ];
            }

            return $result;
        });

        return response()->json($result);
    }

    public function recentLogs()
    {
        $logs = AdminLog::with('admin')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $time = $log->created_at->format('H:i');
                $admin = $log->admin ? $log->admin->name : 'Admin';
                $target = $log->target_type ? (ucfirst($log->target_type) . ' #' . $log->target_id) : '';
                $details = $log->details ? json_decode($log->details, true) : [];
                $msg = match ($log->action) {
                    'open_ticket' => "$admin otevřel ticket $target",
                    'close_ticket' => "$admin uzavřel ticket $target",
                    'update_ticket_status' => "$admin změnil status ticketu $target",
                    'restart_server' => "$admin restartoval server $target",
                    'start_server' => "$admin spustil server $target",
                    'stop_server' => "$admin vypnul server $target",
                    'change_password' => "$admin změnil heslo uživatele " . (isset($details['user_email']) ? $details['user_email'] : ''),
                    'change_role' => "$admin změnil roli uživatele " . (isset($details['user_email']) ? $details['user_email'] : '') . ' na ' . (isset($details['new_role']) ? $details['new_role'] : ''),
                    default => "$admin provedl akci: {$log->action} $target",
                };
                return [
                    'time' => $time,
                    'message' => $msg,
                ];
            });
        return response()->json($logs);
    }
}
