<?php

namespace App\Http\Controllers;

use App\Services\ExcelGeneratorService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const FIN_DEL_DIA_KEYS = [
        'nro_propuesta', 'cert_propuesta', 'tipodoc', 'documento', 'cuir',
        'apellido', 'iniciovigencia', 'finvigencia', 'meses',
        'costocobertura', 'costo_total', 'apellidotomador', 'tipodoctomador',
        'documentotomador', 'direcciontomador', 'cptomador', 'localidadtomador',
        'master', 'organizador', 'productor',
    ];

    private const FIN_DEL_DIA_HEADERS = [
        'nro_propuesta', 'cert_propuesta', 'tipodoc', 'documento', 'cuir',
        'apellido', 'iniciovigencia', 'finvigencia', 'meses',
        'costocobertura', 'costo_total', 'apellidotomador', 'tipodoctomador',
        'documentotomador', 'direcciontomador', 'cptomador', 'localidadtomador',
        'master', 'organizador', 'productor',
    ];

    private const ENVIO_KEYS = [
        'certificado', 'tipodocumento', 'documento', 'cuir', 'apellido',
        'sexo', 'fechanacimiento', 'capital', 'amf', 'subsidio', 'renta',
        'fechavigencia', 'codactividad', 'codclasifactividad', 'codtarea',
        'apellidobenef', 'tipodocumentobenef', 'documentobenef',
        'fechanacimientobenef', 'direccionbenef', 'cpbenef', 'localidadbenef',
        'direccion', 'localidad', 'cp', 'matricula', 'nrocolegiado', 'codgrupo',
        'fechainiciovigencia', 'fechafinvigencia', 'antiguedad', 'edad',
        'clausula_norepeticion', 'barrio', 'cuit_barrio', 'barrio_beneficiario',
        'costo', 'cobertura', 'grupoestadistico',
        'apellidotomador', 'tipodocumentotomador', 'documentotomador',
        'fechanacimientotomador', 'direcciontomador', 'cptomador', 'localidadtomador',
        'master', 'organizador', 'productor',
    ];

    private const ENVIO_HEADERS = [
        'certificado', 'tipodocumento', 'documento', 'cuir', 'apellido',
        'sexo', 'fechanacimiento', 'capital', 'amf', 'subsidio', 'renta',
        'fechavigencia', 'codactividad', 'codclasifactividad', 'codtarea',
        'apellidobenef', 'tipodocumentobenef', 'documentobenef',
        'fechanacimientobenef', 'direccionbenef', 'cpbenef', 'localidadbenef',
        'direccion', 'localidad', 'cp', 'matricula', 'nrocolegiado', 'codgrupo',
        'fechainiciovigencia', 'fechafinvigencia', 'antiguedad', 'edad',
        'clausula_norepeticion', 'barrio', 'cuit_barrio', 'barrio_beneficiario',
        'costo', 'cobertura', 'grupoestadistico',
        'apellidotomador', 'tipodocumentotomador', 'documentotomador',
        'fechanacimientotomador', 'direcciontomador', 'cptomador', 'localidadtomador',
        'master', 'organizador', 'productor',
    ];

    public function __construct(
        private readonly ReportService $reportService,
        private readonly ExcelGeneratorService $excelGenerator,
    ) {
    }

    public function index()
    {
        $date = now()->format('Y-m-d');
        $reports = $this->reportService->getReportsByDate($date);

        return view('reports.index', [
            'date' => $date,
            'reports' => $reports,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('date');
        $reports = $this->reportService->getReportsByDate($date);

        return view('reports.index', [
            'date' => $date,
            'reports' => $reports,
        ]);
    }

    public function downloadFinDelDia(Request $request): StreamedResponse
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        $date = $request->input('date');
        $reports = $this->reportService->getReportsByDate($date);
        $formattedDate = \Carbon\Carbon::parse($date)->format('d-m-Y');

        return $this->excelGenerator->generateDownload(
            $reports['fin_del_dia'],
            self::FIN_DEL_DIA_HEADERS,
            "FinDelDia-TODOS{$formattedDate}.xlsx",
            self::FIN_DEL_DIA_KEYS,
        );
    }

    public function downloadEnvioColectivo(Request $request): StreamedResponse
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        $date = $request->input('date');
        $reports = $this->reportService->getReportsByDate($date);
        $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');

        return $this->excelGenerator->generateDownload(
            $reports['envio_colectivo'],
            self::ENVIO_HEADERS,
            "EnvioColectivo-TODOS{$formattedDate}.xlsx",
            self::ENVIO_KEYS,
        );
    }

    public function downloadEnvioIndividual(Request $request): StreamedResponse
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);

        $date = $request->input('date');
        $reports = $this->reportService->getReportsByDate($date);
        $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');

        return $this->excelGenerator->generateDownload(
            $reports['envio_individual'],
            self::ENVIO_HEADERS,
            "EnvioInidividual-TODOS{$formattedDate}.xlsx",
            self::ENVIO_KEYS,
        );
    }
}
