@php
    $month = array(
    'january' => 'Enero',
    'february' => 'Febrero',
    'march' => 'Marzo',
    'april' => 'Abril',
    'may' => 'Mayo',
    'june' => 'Junio',
    'july' => 'Julio',
    'august' => 'Agosto',
    'september' => 'Septiembre',
    'october' => 'Octubre',
    'november' => 'Noviembre',
    'december' => 'Diciembre'
  );
@endphp
<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Póliza {{ $data[0]->documento  }} - {{ $data[0]->prefijo  }}{{ $data[0]->idpropuesta  }}</title>

  <style>
    body {
      font-family: 'Nunito', sans-serif;
      height: 100%;
    }

    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    table {
      width: 100%;
      border: solid 0px;
      padding: 0px;
      margin: 0;
      border-collapse: collapse;
    }

    .table-line {
      border: solid 1px;
    }

    .table-line td {
      border: solid 1px;
      padding: 5px;
    }

    p {
      text-align: justify;
      font-size: 12px;
    }

    th,
    td {
      text-align: justify;
      font-size: 11px;
    }

    .trgris {
      background-color: #ccc;
    }

    .footer {

      position: fixed;
      /*El div será ubicado con relación a la pantalla*/
      left: 0px;
      /*A la derecha deje un espacio de 0px*/
      right: 0px;
      /*A la izquierda deje un espacio de 0px*/
      bottom: 0px;
      /*Abajo deje un espacio de 0px*/

      height: 80px;
    }

    .page-break{
      page-break-before : always;
    }

    .sello {
      text-align: center;
      position: fixed;
      align-items: center;
      left: 30%;
      bottom: 170px;
    }

    .sello img {
      width: 280px;
    }

    .p1 {
      padding: 10px;
    }

    .tr-b td{
      border: solid 1px;
      padding: 0px;
    }

    .td-b{
      border: solid 1px;
    }

    #recibo{
      /*border : solid 1px #000;*/
    }
    .footer-certificated{
      margin-top: 200px;
    }
    .footer-certificated img{
      margin-top: -200px;
      float: right;
      width: 250px;
    }
    .detail-right{
      margin-top: -70px;
    }

  </style>

</head>

<body>

  <table>
    <thead>
      <tr>
        <th></th>
        <th></th>
        <th>
          <p class="text-center"><b>SEGURO DE ACCIDENTES PERSONALES EN OCASIÓN DEL <br>TRABAJO - BARRIOS PRIVADOS</b></p>
        </th>
        <th class="text-right">
          <img width="150" src="img/imgsancor2.jpg" alt="">
        </th>
      </tr>
    </thead>
  </table>



  <div>
    <p class="text-center">
      Constancia de Póliza - P N°: {{$data[0]->prefijo}} - {{ $data[0]->idpropuesta }}
    </p>
    <p>Por medio del presente, damos constancia que se otorga cobertura en el seguro de Accidentes Personales (con motivo y ocasión del trabajo) de Sancor
      Cooperativa de Seguros Ltda. las personas que se detallan a continuación y en las condiciones descriptas seguidamente, encontrándose la correspondiente
      póliza en trámite de emisión.
    </p>
  </div>


  <table class="table-line">
    <thead>
      <tr class="trgris">
        <th colspan="4" class="text-center">
          DATOS DEL TOMADOR
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width: 32%;" class="text-center">Nombres y Apellidos/Razón Social</td>
        <td colspan="3">{{ $cliente[0]->apellidos." ".$cliente[0]->nombres   }}</td>
      </tr>
      <tr>
        <td class="text-center">Tipo y número de documento</td>
        <td colspan="3">DNI {{ $data[0]->documento  }}</td>
      </tr>
      <tr>
        <td class="text-center">BARRIOS PRIVADOS en los que realizará la tarea declarada</td>
        <td colspan="3">
          A QUIEN CORRESPONDA<br>

          @php
            $concatbarrios = "";
          @endphp

          @foreach($barriospropuesta as $val)
            @php
              $concatbarrios .=$val->nombre." - ". $val->id_barrio  .", ";
            @endphp

          @endforeach

          @if(strlen($concatbarrios) > 236)

            {{substr($concatbarrios,0,236)}}
            ...<br>
            <b>Ver listado completo de barrios en la parte de abajo</b>
          @else
            {{substr($concatbarrios,0,236)}}
          @endif
        </td>
      </tr>
      <tr class="trgris">
        <td colspan="4" class="text-center">DATOS DEL BENEFICIARIO</td>
      </tr>
      <tr>
        <td colspan="4" class="text-left">Herederos legales</td>
      </tr>
      <tr class="trgris">
        <td colspan="4" class="text-center">Detalle de Personas a Asegurar</td>
      </tr>
      <tr>
        <td class="text-center">Apellido y Nombre </td>
        <td class="text-center">Tipo y No. Documento </td>
        <td class="text-center">Fecha Nacimiento (*) </td>
        <td class="text-center">Actividad<br>Tarea que realiza (**) </td>
      </tr>
      @foreach($lineasdata as $val)
      <tr>
        <td class="text-center">{{$val->apellidos}} {{$val->nombres}} </td>
        <td class="text-center">{{$val->tipo_documento}} : {{$val->documento}} </td>
        <td class="text-center">{{ \Carbon\Carbon::parse($val->fecha_nacimiento)->format('d/m/Y')}} </td>
        <td class="text-left">{{$val->actividad}} </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div>
    <p class="text-center"> <b> VIGENCIA : DEL
      @if($data[0]->codempresa)
        {{ \Carbon\Carbon::parse($data[0]->fechaDesde)->format('d/m/Y h:i A') }} A {{ \Carbon\Carbon::parse($data[0]->fechaHasta)->format('d/m/Y h:i A') }}
      @else
        {{ \Carbon\Carbon::parse($data[0]->fechaDesde)->format('d/m/Y') }} A {{ substr( \Carbon\Carbon::parse($data[0]->fechaHasta)->format('d/m/Y'), 0,10) . " 00:00:00" }}
      @endif
      <br>

    </b></p>
    <p>
      <b>Nota: Verificar la exigencia del barrio y la cobertura ya que se dará cobertura a los barrios conforme Suma asegurada mencionada en el presente certificado. Si no adquieres la suma asegurada correcta el barrio puede no dejarte ingresar y tendrás que volver a aumentar la suma asegurada</b>
    </p>
    <p>
      (*) Se aclara que son asegurables personas de 14 a 70 años inclusive.
      <br>(**) Se deja constancia que se dará cobertura a la actividad declarada hasta 15 metros de altura.se deberá cumplir además con el resto de condiciones de asegurabilidad de Sancor Coop.de Seguros Ltda.
      <br>Coberturas y Capitales Asegurados
      <br>Hechos ocurridos a causa de las actividades y/o tareas declaradas en la correspondiente solicitud, exclusivamente cuando las mismas sean desempeñadas por el asegurado o los asegurados en los Barrios Privados declarados, incluido los trayectos para trasladarse de un barrio Privado a otro y/o in itinere.
      <br>- MUERTE ACCIDENTAL ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- INVALIDEZ TOTAL Y PARCIAL PERMANENTE POR ACCIDENTE ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- ASISTENCIA MEDICO FARMACÉUTICA POR REINTEGRO ${{ number_format($data[0]->cobertura_gastos,2) }}(con deducible de ${{ number_format($data[0]->cobertura_deducible,2) }})
      <br>Cobertura in itinere incluyendo casos en que el vehículo de traslado sea motocicletas y/o bicicletas y/o vehículos similares
      <br>
      @if (strlen($concatbarrios) < 236)
      <p><b>NO REPETICIÓN</b></p>
      <p>La compañía aseguradora renuncia expresamente y de manera irrevocable al derecho de repetición contra cualquier tercero, ya sea persona física o jurídica, que pudiera ser considerado responsable, directa o indirectamente, del siniestro cubierto por la presente póliza. En virtud de esta renuncia, la aseguradora no podrá ejercer acciones de recuperación o subrogación contra ningún individuo, empresa, entidad pública o privada, eximiéndolos de cualquier obligación de reembolso derivada del pago de indemnizaciones efectuadas en cumplimiento de la cobertura contratada. {{substr($concatbarrios,0,236)}}. Se extiende el presente en Benavidez, {{date('d/m/Y')}}. Esta constancia tendrá validez si se presenta con el correspondiente recibo de pago.    </p>
      @endif


    </p>
  </div>




  <div class="sello text-center">
    @if($data[0]->paga == 1)
      @if ($data[0]->codempresa == "SEGUROSDELPILAR")
        <img  width="120" src="https://barriosprivadosstage.niveldigitalcol.com/img/pilarpagado.png" alt="">
        <br>
      @else
        <img width="140" src="img/imgpago.png" alt=""><br>
      @endif

    <small style="font-size: 9px">Documento Generado en {{$data[0]->ultmod}}</small>

    @endif
  </div>

  <div class="footer">
    <table>
      <tr>
        <td class="text-right">
          @if ($data[0]->codempresa == "SEGUROSDELPILAR")
            <img width="120" src="https://barriosprivadosstage.niveldigitalcol.com/img/pilarlogo.png" alt="">
          @else
            <img width="140" src="img/brokerlogo.png" alt="">
          @endif
        </td>
        <td>
          @if ($data[0]->codempresa == "SEGUROSDELPILAR")
            <p class="text-center">
            El mejor Seguro, estés donde estés.
            <br>segurosdelpilar.com.ar
            <br>Tel. (113291-6722)
            <br>Av. Sgto. Cayetano Beliera 2650, B 1629 Pilar,<br>provincia de Buenos Aires
          </p>
          @else
            <p class="text-center">
            BROKER DEL PUERTO ...
            <br>TU TRANQUILIDAD VALE
            <br>www.brokerdelpuerto.com
            <br>barriosprivados@brokerdelpuerto.com
            <br>Tel. (03327-485189) Cel. 15-55841038
            <br>Sarmiento 3314 (1621 - Benavidez)
          </p>
          @endif

        </td>
      </tr>
    </table>




  </div>

  @if(strlen($concatbarrios) > 236)
  <div class="page-break"></div>
      <div class="anexo">
        <img src="img/cabeceraanexo.png" width="100%" alt="">
      </div>
      <h4  class="text-center">
        SEGURO DE ACCIDENTES PERSONALES<br>
        EN OCASIÓN DEL TRABAJO - BARRIOS PRIVADOS<br>
        VIGENCIA : DEL
        @if($data[0]->codempresa)
        {{ $data[0]->fechaDesde }} A {{ $data[0]->fechaHasta }}
        @else
          {{ $data[0]->fechaDesde }} A {{  substr($data[0]->fechaHasta, 0,10) . " 00:00:00" }}
        @endif
      </h4>



      <p><b>PROPUESTA EN EMISIÓN : {{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</b></p>
      <p class="text-justify">Se deja expresa constancia por el presente que las personas que se detallan en la Propuesta No. {{$data[0]->prefijo}}-{{$data[0]->idpropuesta}} se encuentran
        cubiertas en esta aseguradora, amparadas por los riesgos de MUERTE e INVALIDEZ (total o parcial permanente) por
        ACCIDENTE y Asistencia Médica Farmacéutica según las condiciones contratadas
      </p>
      <p><b>Destino: Barrios Privados</b></p>
      <p><b>ANEXO DE NO REPETICIÓN:</b></p>
      <p class="text-justify">
        {{ $concatbarrios }}<br>
        Ya sea con fundamentos en la Ley 24.557 o en cualquier otra norma jurídica, con motivo de las prestaciones en especie o dinerarias que se vea obligada a
        otorgar o abonar al Asegurado declarado en la presente Póliza/Certificado, comprendido en la cobertura de la presente Póliza/Certificado de Accidentes
        Personales con motivo de la profesión o actividad declarada e In Itinere.
      </p>
  @endif



</body>



</html>