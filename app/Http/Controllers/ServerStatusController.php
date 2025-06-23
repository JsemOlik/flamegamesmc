<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServerStatusController extends Controller
{
    public function index()
    {
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
                'name' => $name,
                'online' => $isOnline,
                'players' => 0,
                'tps' => 0,
                'ram' => $memoryMB,
                'status' => $status,
            ];
        }

        return response()->json($result);
    }
}
