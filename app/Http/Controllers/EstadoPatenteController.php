<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EstadoPatenteController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        // URL base de tu API
        $this->apiUrl = env('API_URL', 'http://localhost:8000/api');
    }


    /**
     * Obtener todos los estados desde la API
     */
    public function index()
    {
        $response = Http::get("$this->apiUrl/estados-patente");

        if (!$response->successful()) {
            return back()->with('error', 'No se pudieron cargar los estados de patente');
        }

        $estados = $response->json();

        return view('estados.index', compact('estados'));
    }


    /**
     * Obtener estados para combos (uso interno)
     */
    public function getEstados()
    {
        $response = Http::get("$this->apiUrl/estados-patente");

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response->json()['data'] ?? $response->json()
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => []
        ], 500);
    }


    /**
     * Actualizar estado de una patente vía API
     */
    public function updateEstado(Request $request, $idPatente)
    {
        $request->validate([
            'estado_patente_idestado_patente' => 'required|integer'
        ]);

        $response = Http::put("$this->apiUrl/patentes/$idPatente/estado", [
            'estado_patente_idestado_patente' => $request->estado_patente_idestado_patente
        ]);

        if (!$response->successful()) {
            return back()->with('error', 'No fue posible actualizar el estado');
        }

        return back()->with('success', 'Estado actualizado correctamente');
    }
}
