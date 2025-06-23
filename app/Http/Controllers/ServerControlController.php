<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServerControlController extends Controller
{
    protected $panelUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->panelUrl = rtrim(env('PTERODACTYL_PANEL_URL1'), '/');
        $this->apiKey = env('PTERODACTYL_API_KEY1');
    }

    protected function sendPowerAction(string $serverId, string $action)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post("{$this->panelUrl}/api/client/servers/{$serverId}/power", [
            'signal' => $action, // start, stop, restart
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Failed to send power action'], 500);
        }

        return response()->json(['message' => ucfirst($action) . ' signal sent']);
    }

    public function start($id)
    {
        return $this->sendPowerAction($id, 'start');
    }

    public function stop($id)
    {
        return $this->sendPowerAction($id, 'stop');
    }

    public function restart($id)
    {
        return $this->sendPowerAction($id, 'restart');
    }
}
