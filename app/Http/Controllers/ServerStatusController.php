<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
                ])->withOptions([
                    'verify' => false,
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
                ])->withOptions([
                    'verify' => false,
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
        $logs = DB::table('admin_logs')
            ->join('users', 'admin_logs.user_id', '=', 'users.id')
            ->select('admin_logs.*', 'users.name as user_name', 'users.role as user_role')
            ->orderByDesc('admin_logs.created_at')
            ->limit(15)
            ->get()
            ->map(function ($log) {
                $time = \Carbon\Carbon::parse($log->created_at)->format('H:i');
                
                // Use the description directly since we're now storing formatted messages
                $message = $log->description;
                
                return [
                    'time' => $time,
                    'message' => $message,
                    'user' => $log->user_name,
                    'role' => $log->user_role,
                    'action' => $log->action,
                ];
            });
        return response()->json($logs);
    }
}
