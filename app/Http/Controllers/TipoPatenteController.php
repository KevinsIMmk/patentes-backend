<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TipoPatenteController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        // URL API definida en .env
        $this->apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Mostrar lista de tipos de patente en vista
     */
    public function index()
    {

        $tipos = Cache::remember('tipos_patente_list', 60, function () {
            $response = Http::get("$this->apiUrl/tipos_patente");
            if (!$response->successful()) {
                return [];
            }
            return $response->json()['data'] ?? $response->json();
        });

        return view('tipos_patente.index', compact('tipos'));
    }

    /**
     * Endpoint interno para cargar combos en formularios
     */
    public function getForSelect()
    {

        $response = Http::get("$this->apiUrl/tipos_patente");

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $response->json()['data'] ?? $response->json()
        ]);
    }

    /**
     * Mostrar un tipo de patente específico
     */
    public function show($id)
    {
        $response = Http::get("$this->apiUrl/tipos_patente/$id");

        if (!$response->successful()) {
            return back()->with('error', 'Tipo de patente no encontrado');
        }

        $tipo = $response->json()['data'];

        return view('tipos_patente.show', compact('tipo'));
    }
}
