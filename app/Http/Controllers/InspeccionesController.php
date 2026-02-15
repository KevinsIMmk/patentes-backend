<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InspeccionesController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        // URL base de la API
        $this->apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * ================================
     *  VISTA → Registrar Inspección
     * ================================
     */
    public function index()
    {
        $inspecciones = Http::get("{$this->apiUrl}/inspecciones")->json()['data'] ?? [];
        $patentes     = Http::get("{$this->apiUrl}/patentes")->json()['data'] ?? [];

        return view('inspecciones', compact(
            'inspecciones',
            'patentes'
        ));
    }

    /**
     * =========================================
     *  VISTA → Listado + edición de inspecciones
     * =========================================
     */
    public function lista()
    {
        $inspecciones = Http::get("{$this->apiUrl}/inspecciones")->json()['data'] ?? [];
        $estados      = Http::get("{$this->apiUrl}/estados_inspeccion")->json()['data'] ?? [];
        $tiposDocumento = Http::get("{$this->apiUrl}/tipos_documento")->json()['data'] ?? [];

        return view('editarInspecciones', compact(
            'inspecciones',
            'estados',
            'tiposDocumento'
        ));
    }

    /**
     * ==================================
     *  Crear nueva inspección
     * ==================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'trimestre'         => 'required|string',
            'patente_idPatente' => 'required|integer',
            'fecha_inspeccion'  => 'required|date',
            'motivo'            => 'nullable|string',
        ]);

        // 📌 Datos enviados a la API
        $data = [
            'trimestre' => $request->trimestre,
            'fecha_inspeccion' => $request->fecha_inspeccion,
            'patente_idPatente' => $request->patente_idPatente,
            'motivo' => $request->motivo,

            // 🔒 Inspector fijo
            'idInspector' => 1,
            'inspector' => 'Kevin Irigoyen',

            // Valores automáticos
            'estado_inspeccion_idestado_inspeccion' => 1, // Pendiente
            'tipo_documento_idtipo_documento' => 11,      // Acta
        ];

        $response = Http::post("{$this->apiUrl}/inspecciones", $data);

        if ($response->successful()) {
            return redirect()
                ->route('inspecciones.index')
                ->with('success', 'Inspección creada correctamente.');
        }

        return redirect()
            ->route('inspecciones.index')
            ->with('error', 'Error al crear la inspección: ' . $response->body());
    }

    /**
     * ==================================
     *  Editar inspección
     * ==================================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_inspeccion'                      => 'required|date',
            'estado_inspeccion_idestado_inspeccion' => 'required|integer',
            'tipo_documento_idtipo_documento'       => 'required|integer',
        ]);

        $response = Http::put(
            "{$this->apiUrl}/inspecciones/{$id}",
            $request->only([
                'fecha_inspeccion',
                'estado_inspeccion_idestado_inspeccion',
                'tipo_documento_idtipo_documento',
                'motivo'
            ])
        );

        if ($response->successful()) {
            return redirect()
                ->route('inspecciones.editar')
                ->with('success', 'Inspección actualizada correctamente');
        }

        return redirect()
            ->route('inspecciones.editar')
            ->with('error', 'No se pudo actualizar la inspección');
    }

    /**
     * ==================================
     *  Agregar observación
     * ==================================
     */
    public function guardarObservaciones(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255'
        ]);

        $response = Http::post(
            "{$this->apiUrl}/inspecciones/{$id}/observaciones",
            ['descripcion' => $request->descripcion]
        );

        if ($response->successful()) {
            return redirect()
                ->route('inspecciones.editar')
                ->with('success', 'Observación agregada correctamente');
        }

        return redirect()
            ->route('inspecciones.editar')
            ->with('error', 'No se pudo guardar la observación');
    }
}
