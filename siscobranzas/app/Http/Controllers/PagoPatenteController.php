<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PagoPatenteController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    public function index(Request $request)
    {
        // 1. Obtener pagos
        $response = Http::get("{$this->apiUrl}/pagos_patente");
        
        // Manejo de seguridad para el JSON: si la clave 'data' no existe, usar la respuesta completa.
        $responseData = $response->json(); 
        $pagos = $response->successful() ? ($responseData['data'] ?? $responseData ?? []) : [];

        // 2. Buscador por número de patente (filtrado local)
        $search = $request->query('search');
        if ($search) {
            $pagos = array_filter($pagos, function ($pago) use ($search) {
                // Asegurar que la relación 'patente' y el campo existen antes de usar str_contains
                return isset($pago['patente']['numero_patente']) &&
                    str_contains(strtolower($pago['patente']['numero_patente']), strtolower($search));
            });
        }

        // 3. Obtener estados de pago
        $estadosResponse = Http::get("{$this->apiUrl}/estados_pago");
        $estadosData = $estadosResponse->json();
        $estados = $estadosResponse->successful() ? ($estadosData['data'] ?? []) : [];

        return view('pagos', compact('pagos', 'estados', 'search'));
    }

    public function store(Request $request)
    {
        $response = Http::post("{$this->apiUrl}/pagos_patente", $request->all());

        if ($response->successful()) {
            return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente');
        } else {
            // Mejorar el manejo de errores, mostrando mensajes de validación si existen
            $errorMessage = 'Error al registrar el pago.';
            $responseJson = $response->json();
            if (isset($responseJson['message'])) {
                 $errorMessage = $responseJson['message'];
            }
            
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    public function buscarPatente(Request $request)
    {
        $numero = $request->query('numero');
        $rut = $request->query('rut');

        $response = Http::get("{$this->apiUrl}/buscar_patente", [
            'numero' => $numero,
            'rut' => $rut
        ]);

        $responseData = $response->json();
        $patenteEncontrada = $response->successful() ? ($responseData['data'] ?? null) : null;

        // Para evitar variables indefinidas en la vista:
        // Cargar los pagos y estados para la tabla de abajo, tal como lo hace 'index'
        $pagosResponse = Http::get("{$this->apiUrl}/pagos_patente");
        $pagosData = $pagosResponse->json();
        $pagos = $pagosResponse->successful() ? ($pagosData['data'] ?? $pagosData ?? []) : [];
        
        $estadosResponse = Http::get("{$this->apiUrl}/estados_pago");
        $estadosData = $estadosResponse->json();
        $estados = $estadosResponse->successful() ? ($estadosData['data'] ?? []) : [];

        return view('pagos', [
            'patenteEncontrada' => $patenteEncontrada,
            'pagos' => $pagos,
            'estados' => $estados,
            'search' => null
        ]);
    }
}