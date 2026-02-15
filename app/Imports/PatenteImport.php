<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');


use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class PatenteImport implements ToCollection, WithHeadingRow
{
    protected $tipoPatenteId;
    protected $apiUrl;
    protected $expectedHeaders = [
        'R.U.T.',
        'NOMBRE O RAZON SOCIAL',
        'REPRESENTANTE LEGAL',
        'DIRECCIÓN PARTICULAR',
        'POBLACIÓN',
        'CONTACTO',
        'N° PATENTE',
        'DIRECCION COMERCIAL',
        // Al menos uno de estos para GIRO
        // 'ACTIVIDAD ECONOMICA ( G I R O )',
        // 'ACTIVIDAD ECONOMICA  ( G I R O )',
        // 'IDAD ECONOMICA  ( G I R O )',
        // 'GIRO',
    ];
    protected $patentesEnArchivo = [];

    public function __construct($tipoPatenteId)
    {
        $this->tipoPatenteId = $tipoPatenteId;
        $this->apiUrl = rtrim(env('VITE_API_URL'), '/') . '/api';
    }

    /**
     * Normaliza texto y elimina espacios invisibles
     */
    private function normalize($value)
    {
        $value = $value ?? '';

        // Reemplazar espacios Unicode invisibles
        $value = preg_replace('/\x{00A0}+/u', ' ', $value);

        // Colapsar espacios múltiples
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    /**
     * Normaliza encabezados del Excel
     */
    private function normalizeRowKeys($row)
    {
        $clean = [];

        foreach ($row as $key => $value) {

            // Normalizar clave
            $k = strtoupper($this->normalize($key));

            // Normalizar valor también
            $clean[$k] = $this->normalize($value);
        }

        return $clean;
    }


    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw LaravelValidationException::withMessages([
                'archivo_excel' => 'El archivo está vacío.'
            ]);
        }

        // Validar encabezados
        $firstRow = $this->normalizeRowKeys($rows->first());
        foreach ($this->expectedHeaders as $header) {
            if (!array_key_exists($header, $firstRow)) {
                throw LaravelValidationException::withMessages([
                    'archivo_excel' => "Falta el encabezado obligatorio: $header"
                ]);
            }
        }

        foreach ($rows as $row) {
            $row = $this->normalizeRowKeys($row);
            $rut = $row['R.U.T.'] ?? '';
            $numeroPatente = $row['N° PATENTE'] ?? '';

            if ($rut === '' || $numeroPatente === '') {
                throw LaravelValidationException::withMessages([
                    'archivo_excel' => 'Faltan datos obligatorios: R.U.T. o N° PATENTE.'
                ]);
            }

            // Validar duplicados en el archivo
            if (in_array($numeroPatente, $this->patentesEnArchivo)) {
                throw LaravelValidationException::withMessages([
                    'archivo_excel' => "Patente duplicada en el archivo: $numeroPatente"
                ]);
            }
            $this->patentesEnArchivo[] = $numeroPatente;

            // Validar duplicados en la base de datos/API
            $apiCheck = Http::get("{$this->apiUrl}/patentes", ['numero_patente' => $numeroPatente]);
            $apiPatentes = $apiCheck->json('data') ?? $apiCheck->json() ?? [];
            $yaExiste = false;
            foreach ($apiPatentes as $p) {
                if (isset($p['numero_patente']) && $p['numero_patente'] == $numeroPatente) {
                    $yaExiste = true;
                    break;
                }
            }
            if ($yaExiste) {
                throw LaravelValidationException::withMessages([
                    'archivo_excel' => "La patente $numeroPatente ya existe en el sistema."
                ]);
            }

            /**
             * Extraer GIRO con tolerancia a variaciones
             */
            $actividad = $row['ACTIVIDAD ECONOMICA ( G I R O )']
                ?? $row['ACTIVIDAD ECONOMICA  ( G I R O )']
                ?? $row['IDAD ECONOMICA  ( G I R O )']
                ?? $row['GIRO']
                ?? '';

            /**
             * 1️⃣ Crear / actualizar contribuyente
             */
            $response = Http::post("{$this->apiUrl}/contribuyentes", [
                'rut'                => $rut,
                'razon_social'       => $row['NOMBRE O RAZON SOCIAL'] ?? '',
                'representante_legal'=> $row['REPRESENTANTE LEGAL'] ?? '',
                'direccion'          => $row['DIRECCIÓN PARTICULAR'] ?? '',
                'poblacion'          => $row['POBLACIÓN'] ?? '',
                'contacto'           => $row['CONTACTO'] ?? '',
            ]);

            if (!$response->successful()) {

                Log::debug("❌ ERROR CONTRIBUYENTE", [
                    'rut' => $rut,
                    'response' => $response->body()
                ]);

                continue;
            }

            // Compatible con API que devuelve data|objeto directo
            $body = $response->json();

            $contribuyenteId =
                $body['data']['idcontribuyente']
                ?? $body['idcontribuyente']
                ?? null;

            if (!$contribuyenteId) {

                Log::debug("❌ SIN ID CONTRIBUYENTE", [
                    'rut' => $rut,
                    'res' => $body
                ]);

                continue;
            }

            /**
             * 2️⃣ Crear patente asociada
             */
            Http::post("{$this->apiUrl}/patentes", [
                'numero_patente'                   => $numeroPatente,
                'contribuyente_idcontribuyente'    => $contribuyenteId,

                'direccion_comercial'              => $row['DIRECCION COMERCIAL'] ?? '',
                'actividad_patente'                => $actividad,

                'tipo_patente_idtipo_patente'      => $this->tipoPatenteId,
                'estado_patente_idestado_patente'  => 1
            ]);

            Log::debug("✅ Patente importada", [
                'patente' => $numeroPatente,
                'rut'     => $rut
            ]);
        }
    }
}
