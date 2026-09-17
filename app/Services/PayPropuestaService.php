<?php

namespace App\Services;

use App\Models\Propuesta;
use App\Models\LineasPropuesta;
use App\Models\payregistry;
use App\Models\Cola;
use App\Models\logs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayPropuestaService
{
    private Propuesta $propuestaModel;
    private LineasPropuesta $lineaModel;
    private logs $logsModel;

    public function __construct(
        Propuesta $propuestaModel,
        LineasPropuesta $lineaModel,
        logs $logsModel
    ) {
        $this->propuestaModel = $propuestaModel;
        $this->lineaModel = $lineaModel;
        $this->logsModel = $logsModel;
    }

    public function pay(array $data): array
    {
        $this->validate($data);

        $propuesta = $this->findProposal(
            $data['idpropuesta'],
            $data['prefijopropuesta'],
            $data['codempresa']
        );

        $this->ensureCanBePaid($propuesta);

        return $this->processPayment($data, $propuesta);
    }

    private function validate(array $data): void
    {
        $rules = [
            'idpropuesta' => 'required|integer|min:1',
            'prefijopropuesta' => 'required|string|max:5',
            'tipopago' => 'required|string|max:50',
            'compformapago' => 'required|string|max:100',
            'usuariopaga' => 'required|string|max:100',
            'fecha_paga' => 'required|date_format:Y-m-d H:i:s',
            'codempresa' => 'required|string|max:50',
            'fecha_comprobante' => 'nullable|date_format:Y-m-d',
            'valor_pagado' => 'required|numeric|min:0',
            'cuit_pagador' => 'required|string',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    private function findProposal(int $idpropuesta, string $prefijo, string $codempresa): Propuesta
    {
        $propuesta = $this->propuestaModel
            ->where('idpropuesta', $idpropuesta)
            ->where('prefijo', $prefijo)
            ->where('codempresa', $codempresa)
            ->first();

        if (!$propuesta) {
            throw new \Exception('La propuesta no existe');
        }

        return $propuesta;
    }

    private function ensureCanBePaid(Propuesta $propuesta): void
    {
        if ($propuesta->codestado != 1) {
            throw new \Exception('La propuesta se encuentra anulada');
        }

        if ($propuesta->paga != 0) {
            throw new \Exception('La propuesta ya se encuentra pagada');
        }

        $alreadyRegistered = payregistry::where('idpropuesta', $propuesta->idpropuesta)
            ->where('prefijo', $propuesta->prefijo)
            ->exists();

        if ($alreadyRegistered) {
            throw new \Exception('El pago de esta propuesta ya fue registrado');
        }
    }

    private function processPayment(array $data, Propuesta $propuesta): array
    {
        try {
            return DB::transaction(function () use ($data, $propuesta) {
                $fechaPaga = $data['fecha_paga'] ?: now('America/Argentina/Buenos_Aires')->format('Y-m-d H:i:s');

                $this->propuestaModel
                    ->where('idpropuesta', $data['idpropuesta'])
                    ->where('prefijo', $data['prefijopropuesta'])
                    ->where('codempresa', $data['codempresa'])
                    ->update([
                        'codestado' => 1,
                        'paga' => 1,
                        'csrf' => NULL,
                        'usuariopaga' => $data['usuariopaga'],
                        'fecha_paga' => $fechaPaga,
                        'tipopago' => $data['tipopago'],
                        'compformadepago' => $data['compformapago'],
                        'version' =>  DB::raw('version + 1'),
                        'fecha_comprobante' => $data['fecha_comprobante'],
                        'valor_pagado' => $data['valor_pagado'],
                        'cuit_pagador' => $data['cuit_pagador'],
                        'comprobante_bitrix' => $data['comprobante_bitrix'] ?? NULL,
                    ]);

                $this->lineaModel
                    ->where('id_propuesta', $data['idpropuesta'])
                    ->where('prefijo', $data['prefijopropuesta'])
                    ->where('codempresa', $data['codempresa'])
                    ->update(['codestado' => 1]);

                payregistry::create([
                    'idpropuesta' => $data['idpropuesta'],
                    'prefijo' => $data['prefijopropuesta'],
                    'usuariopaga' => $data['usuariopaga'],
                    'fecha_paga' => $fechaPaga,
                    'tipopago' => $data['tipopago'],
                    'compformadepago' => $data['compformapago'],
                    'fecha_comprobante' => $data['fecha_comprobante'],
                    'valor_pagado' => $data['valor_pagado'],
                    'cuit_pagador' => $data['cuit_pagador'],
                ]);

                Cola::create([
                    'entity' => 'propuestas',
                    'entity_id' => $propuesta->id,
                    'codempresa' => $propuesta->codempresa,
                ]);

                return ['success' => true, 'message' => 'Se ha hecho el pago de la propuesta con éxito'];
            });
        } catch (\Exception $ex) {
            $this->logsModel->saveerror($ex->getMessage(), '', '', '150');
            throw new \Exception('No se pudo procesar el pago de la propuesta');
        }
    }
}
