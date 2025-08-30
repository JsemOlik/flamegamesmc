<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withOptions([
            'verify' => false,
        ])->post("{$this->panelUrl}/api/client/servers/{$serverId}/power", [
            'signal' => $action, // start, stop, restart
        ]);

        if (!in_array($response->status(), [200, 204])) {
            // Log the response for debugging
            \Log::error('Pterodactyl power action failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'Failed to send power action', 'details' => $response->body()], 500);
        }

        // Log admin action
        if ($user && $user->role === 'admin') {
            DB::table('admin_logs')->insert([
                'admin_id' => $user->id,
                'action' => $action . '_server',
                'target_type' => 'server',
                'target_id' => $serverId,
                'details' => json_encode(['response' => $response->json()]),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => ucfirst($action) . ' signal sent']);
    }

    public function power($id, Request $request)
    {
        $signal = $request->input('signal');
        if (!in_array($signal, ['start', 'stop', 'restart', 'kill'])) {
            return response()->json(['error' => 'Invalid signal'], 400);
        }
        return $this->sendPowerAction($id, $signal);
    }
}
