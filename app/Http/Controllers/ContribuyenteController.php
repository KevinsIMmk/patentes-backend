<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ContribuyenteController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        // URL base definida en .env
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }


    /**
     * Obtener contribuyentes desde API (con cache)
     */
    public function index()
    {
        $contribuyentes = Cache::remember('contribuyentes_list', 60, function () {
            $response = Http::get("$this->apiUrl/contribuyentes");

            if (!$response->successful()) {
                return [];
            }

            return $response->json()['data'] ?? $response->json();
        });

        return view('contribuyentes.index', compact('contribuyentes'));
    }


    /**
     * Endpoint interno para cargar combos/selects
     */
    public function getForSelect()
    {
        $response = Http::get("$this->apiUrl/contribuyentes");

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
     * Mostrar contribuyente específico
     */
    public function show($id)
    {
        $response = Http::get("$this->apiUrl/contribuyentes/$id");

        if (!$response->successful()) {
            return back()->with('error', 'Contribuyente no encontrado');
        }

        $contribuyente = $response->json()['data'];

        return view('contribuyentes.show', compact('contribuyente'));
    }
}
