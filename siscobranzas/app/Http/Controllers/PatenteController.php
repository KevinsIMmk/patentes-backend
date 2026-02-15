<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\PatenteImport;
use Maatwebsite\Excel\Facades\Excel;


class PatenteController extends Controller
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_BASE_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * LISTADO + BÚSQUEDA
     */
    public function index(Request $request)
    {
        $search = $request->input('buscar');


        $patentesResponse = Http::get("{$this->apiUrl}/patentes");
        $patentes = $patentesResponse->json('data') ?? $patentesResponse->json() ?? [];

        $tiposPatenteResponse = Http::get("{$this->apiUrl}/tipos_patente");
        $tiposPatente = $tiposPatenteResponse->json('data') ?? $tiposPatenteResponse->json() ?? [];

        $contribuyentesResponse = Http::get("{$this->apiUrl}/contribuyentes");
        $contribuyentes = $contribuyentesResponse->json('data') ?? $contribuyentesResponse->json() ?? [];

        $estadosResponse = Http::get("{$this->apiUrl}/estados_patente");
        $estados = $estadosResponse->json('data') ?? $estadosResponse->json() ?? [];

        if (!empty($search)) {
            $patentes = array_filter($patentes, fn ($p) =>
                isset($p['numero_patente']) &&
                str_contains((string)$p['numero_patente'], (string)$search)
            );
        }

        return view('patente', compact(
            'patentes',
            'search',
            'tiposPatente',
            'contribuyentes',
            'estados'
        ));
    }

    /**
     * ACTUALIZAR ESTADO DE PATENTE
     */
    public function updateEstado(Request $request, $id)
    {
        $response = Http::put("{$this->apiUrl}/patentes/{$id}/estado", [
            'estado_patente_idestado_patente' => $request->estado_patente_idestado_patente
        ]);

        return $response->successful()
            ? redirect()->back()->with('success', 'Estado actualizado correctamente')
            : redirect()->back()->with('error', 'No se pudo actualizar el estado');
    }

    /**
     * ACTUALIZAR DATOS ADMINISTRATIVOS DE LA PATENTE
     */
    public function updateDatos(Request $request, $id)
    {
        // ...existing code...

        $response = Http::put("{$this->apiUrl}/patentes/{$id}/editar", [
            'direccion_comercial' => $request->direccion_comercial,
            'actividad_patente' => $request->actividad_patente,
            'contribuyente_idcontribuyente' => $request->contribuyente_idcontribuyente,
            'tipo_patente_idtipo_patente' => $request->tipo_patente_idtipo_patente,
            'estado_patente_idestado_patente' => $request->estado_patente_idestado_patente,
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Patente actualizada correctamente');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar la patente');
    }


    /**
     * IMPORTAR EXCEL
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls',
            'tipo_patente_idtipo_patente' => 'required|integer'
        ]);

        try {
            Excel::import(
                new PatenteImport($request->tipo_patente_idtipo_patente),
                $request->file('archivo_excel')
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $msg = $e->validator->errors()->first('archivo_excel') ?? 'Error al importar el archivo.';
            return back()->with('error', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Patentes importadas correctamente');
    }


    public function generarPdf($id)
    {
        // Consumir API que retorna la patente completa
        $response = Http::get("{$this->apiUrl}/patentes/{$id}/full");

        if (! $response->successful()) {
            return redirect()->back()->with('error', 'No fue posible obtener los datos de la patente');
        }

        $patente = $response->json('data');

        // Renderizar vista PDF
        $pdf = Pdf::loadView('patente_pdf', compact('patente'));

        return $pdf->download('Patente_'.$patente['numero_patente'].'.pdf');
    }
}
