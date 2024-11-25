<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CsvController extends Controller
{
    /**
     * Lista todos los ficheros CSV de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
        $files = collect(Storage::files('app'))->filter(function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'csv';
        })->map(function ($file) {
            return basename($file);
        });

        return response()->json([
            'mensaje' => 'Listado de ficheros CSV',
            'contenido' => $files->values()->toArray(),
        ]);
    }

    /**
     * Recibe por parámetro el nombre de fichero y el contenido CSV y crea un nuevo fichero con ese nombre y contenido en storage/app. 
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required|string',
        ]);

        $filePath = 'app/' . $request->filename;

        if (Storage::exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        Storage::put($filePath, $request->content);

        return response()->json(['mensaje' => 'Fichero guardado exitosamente']);
    }

    /**
     * Recibe por parámetro el nombre de un fichero CSV el nombre de fichero y devuelve un JSON con su contenido.
     * Si el fichero no existe devuelve un 404.
     * Hay que hacer uso lo visto en la presentación CSV to JSON.
     *
     * @param name Parámetro con el nombre del fichero CSV.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $id)
    {
        $filePath = 'app/' . $id;

        if (!Storage::exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        $content = Storage::get($filePath);

        $lines = explode("\n", trim($content));
        $headers = str_getcsv(array_shift($lines));
        $data = array_map(function ($line) use ($headers) {
            return array_combine($headers, str_getcsv($line));
        }, $lines);

        return response()->json([
            'mensaje' => 'Fichero leído con éxito',
            'contenido' => $data,
        ]);
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido CSV y actualiza el fichero CSV. 
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     * Si el contenido no es un JSON válido, devuelve un 415.
     * 
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        $filePath = 'app/' . $id;

        $request->validate([
            'content' => 'required|string',
        ]);

        if (!Storage::exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        Storage::put($filePath, $request->content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente']);
    }

    /**
     * Recibe por parámetro el nombre de fichero y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $id)
    {
        $filePath = 'app/' . $id;

        if (!Storage::exists($filePath)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        Storage::delete($filePath);

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente']);
    }
}