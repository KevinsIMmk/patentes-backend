<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ObservacionesController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Mostrar listado de observaciones.
     * Si se pasa ?inspeccion_id=123 obtiene también los datos de la inspección
     */
    public function index(Request $request)
    {
        $inspeccion = null;
        $inspeccionId = $request->query('inspeccion_id');

        // Traer observaciones (intenta filtrar por inspección si viene el id)
        if ($inspeccionId) {
            // Intentamos obtener observaciones relacionadas con la inspección (endpoint API común)
            $obsResponse = Http::get("{$this->apiUrl}/inspecciones/{$inspeccionId}/observaciones");
            // Obtener detalle de la inspección para mostrar en la vista
            $insResponse = Http::get("{$this->apiUrl}/inspecciones/{$inspeccionId}");
            $inspeccion = $insResponse->successful() ? ($insResponse->json()['data'] ?? null) : null;
            $observaciones = $obsResponse->successful() ? collect($obsResponse->json()['data'] ?? []) : collect([]);
        } else {
            // Obtener todas las observaciones
            $response = Http::get("{$this->apiUrl}/observaciones");
            $observaciones = $response->successful() ? collect($response->json()['data'] ?? []) : collect([]);
        }

        return view('observaciones', compact('observaciones', 'inspeccion'));
    }

    /**
     * Guardar una nueva observación (texto). Si llega inspeccion_id, la asocia.
     */
    public function store(Request $request)
    {
        // Validación mínima
        $rules = [
            'observaciones' => 'required|array|min:1',
            'observaciones.*' => 'required|string',
        ];

        // si no viene inspeccion, permitir tipo_registro y motivo
        if (!$request->filled('inspeccion_id')) {
            $rules['tipo_registro'] = 'required|string';
            $rules['motivo'] = 'required|string';
        } else {
            $rules['inspeccion_id'] = 'required';
        }

        $request->validate($rules);

        // Preparar payload para la API
        $payload = [
            'observaciones' => $request->input('observaciones'),
        ];

        if ($request->filled('inspeccion_id')) {
            $payload['inspeccion_id'] = $request->input('inspeccion_id');
        } else {
            $payload['tipo_registro'] = $request->input('tipo_registro');
            $payload['motivo'] = $request->input('motivo');
        }

        // Enviar a la API
        $response = Http::post("{$this->apiUrl}/observaciones", $payload);

        if ($response->successful()) {
            // Si venimos desde una inspección, redirigir de vuelta a la vista de observaciones de esa inspección
            if ($request->filled('inspeccion_id')) {
                return redirect()->route('observaciones.index', ['inspeccion_id' => $request->inspeccion_id])
                                 ->with('success', 'Observaciones agregadas correctamente');
            }
            return redirect()->route('observaciones.index')->with('success', 'Observaciones agregadas correctamente');
        }

        return redirect()->back()->with('error', 'Error al agregar las observaciones');
    }
}
