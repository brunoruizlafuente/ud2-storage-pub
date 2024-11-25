<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
{
    // Obtener la lista de archivos en el directorio app
    $files = Storage::disk('local')->files();

    // Retornar el JSON esperado
    return response()->json([
        'mensaje' => 'Listado de ficheros',
        'contenido' => $files,
    ], 200);
}



     /**
     * Recibe por parámetro el nombre de fichero y el contenido. Devuelve un JSON con el resultado de la operación.
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
        $filename = $request->input('filename'); // Obtener el nombre del archivo
        $content = $request->input('content');  // Obtener el contenido del archivo
    
        // Validación: Parámetros faltantes
        if (!$filename || !$content) {
            return response()->json([
                'mensaje' => 'Parámetros faltantes.',
            ], 422);
        }
    
        // Validación: Verificar si el archivo ya existe
        if (Storage::exists($filename)) {
            return response()->json([
                'mensaje' => 'El archivo ya existe',
            ], 409);
        }
    
        // Crear el archivo con el contenido proporcionado
        Storage::put($filename, $content);
    
        // Respuesta exitosa
        return response()->json([
            'mensaje' => 'Guardado con éxito',
        ], 200);
    }
    


     /**
     * Recibe por parámetro el nombre de fichero y devuelve un JSON con su contenido
     *
     * @param name Parámetro con el nombre del fichero.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $filename)
{
    // Verificar si el archivo existe
    if (!Storage::disk('local')->exists($filename)) {
        return response()->json([
            'mensaje' => 'Archivo no encontrado',
        ], 404);
    }

    // Leer el contenido del archivo
    $content = Storage::disk('local')->get($filename);

    return response()->json([
        'mensaje' => 'Archivo leído con éxito',
        'contenido' => $content,
    ], 200);
}


    /**
     * Recibe por parámetro el nombre de fichero, el contenido y actualiza el fichero.
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $filename)
{
    $content = $request->input('content');

    // Validación de parámetros
    if (!$content) {
        return response()->json([
            'mensaje' => 'Parámetro de contenido faltante.',
        ], 422);
    }

    // Verificar si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe', // Cambiado para que coincida con el test
        ], 404);
    }

    // Actualizar el contenido del archivo
    Storage::put($filename, $content);

    return response()->json([
        'mensaje' => 'Actualizado con éxito', // Cambiado para que coincida con el test
    ], 200);
}



    /**
     * Recibe por parámetro el nombre de ficher y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $filename)
{
    // Verificar si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404);        
    }

    // Eliminar el archivo
    Storage::delete($filename);

    return response()->json([
        'mensaje' => 'Eliminado con éxito',
    ], 200);
}
}
