<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalIssueController extends Controller
{
    public function create(): View
    {
        return view('propuesta.emision');
    }

    public function searchClients(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = cliente::where('id', 'LIKE', "%{$query}%")
            ->where('codestado', '1')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'tipo_id', 'fecha_nacimiento', 'telefono', 'email']);

        return response()->json($clients);
    }

    public function saveClient(Request $request): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => null,
        ];

        try {
            $id = trim($request->input('id'));
            $nombres = trim($request->input('nombres'));
            $apellidos = trim($request->input('apellidos'));
            $tipoId = trim($request->input('tipo_id', 'DNI'));
            $fechaNacimiento = trim($request->input('fecha_nacimiento'));
            $telefono = trim($request->input('telefono'));
            $email = trim($request->input('email'));

            if (empty($id) || empty($nombres) || empty($apellidos) || empty($fechaNacimiento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Documento, nombres, apellidos y fecha de nacimiento son obligatorios',
                ]);
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El correo electrónico no es válido',
                ]);
            }

            $client = cliente::where('id', $id)->first();

            if ($client) {
                $client->nombres = $nombres;
                $client->apellidos = $apellidos;
                $client->tipo_id = $tipoId;
                $client->fecha_nacimiento = $fechaNacimiento;
                $client->telefono = $telefono;
                $client->email = $email;
                $client->codestado = '1';
                $client->ultmod = now();
                $client->save();
            } else {
                $client = cliente::create([
                    'id' => $id,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'tipo_id' => $tipoId,
                    'fecha_nacimiento' => $fechaNacimiento,
                    'telefono' => $telefono,
                    'email' => $email,
                    'codestado' => '1',
                    'ultmod' => now(),
                ]);
            }

            $data['success'] = true;
            $data['message'] = 'Cliente guardado correctamente';
        } catch (\Exception $e) {
            $data['message'] = 'Error al guardar el cliente: ' . $e->getMessage();
        }

        return response()->json($data);
    }
}
