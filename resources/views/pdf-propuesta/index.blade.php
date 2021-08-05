<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Póliza {{ $data[0]->documento  }} - {{ $data[0]->prefijo  }}{{ $data[0]->reg  }}</title>

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

    .sello {
      text-align: center;
      position: fixed;
      align-items: center;
      left: 5%;
      bottom: 170px;
    }

    .sello img {
      width: 280px;
    }

    .p1 {
      padding: 10px;
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
      Constancia de Póliza - Propuesta N°: {{$data[0]->prefijo}} - {{ $data[0]->reg }}
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
        <td colspan="3">LASLUISA CASTAÑO, MARIO</td>
      </tr>
      <tr>
        <td class="text-center">Tipo y número de documento</td>
        <td colspan="3">DNI {{ $data[0]->documento  }}</td>
      </tr>
      <tr>
        <td class="text-center">BARRIOS PRIVADOS en los que realizará la tarea declarada</td>
        <td colspan="3">
          A QUIEN CORRESPONDA<br>
          @foreach($barriospropuesta as $val)
            {{$val->id_barrio}} - {{$val->nombre}}; 
          @endforeach
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
        <td class="text-center">{{$val->tipo_documento}}|{{$val->documento}} </td>
        <td class="text-center">{{$val->fecha_nacimiento}} </td>
        <td class="text-left">{{$val->actividad}} </td>
      </tr>
      @endforeach
    </tbody>ss
  </table>

  <div>
    <p class="text-center"> <b> VIGENCIA : DEL {{ $data[0]->fechaDesde }} A {{ $data[0]->fechaHasta }}</b></p>
    <p>
      (*) Se aclara que son asegurables personas de 17 a 65 años inclusive.
      <br>(**) Se deja constancia que se dará cobertura a la actividad declarada hasta 15 metros de altura.se deberá cumplir además con el resto de condiciones de
      <br>asegurabilidad de Sancor Coop.de Seguros Ltda.
      <br>Coberturas y Capitales Asegurados
      <br>Hechos ocurridos a causa de las actividades y/o tareas declaradas en la correspondiente solicitud, exclusivamente cuando las mismas sean desempeñadas por el asegurado o los asegurados en los Barrios Privados declarados, incluido los trayectos para trasladarse de un barrio Privado a otro y/o in itinere.
      <br>- MUERTE ACCIDENTAL ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- INVALIDEZ TOTAL Y PARCIAL PERMANENTE POR ACCIDENTE ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- ASISTENCIA MEDICO FARMACÉUTICA POR REINTEGRO ${{ number_format($data[0]->cobertura_gastos,2) }}(con deducible de ${{ number_format($data[0]->cobertura_deducible,2) }})
    </p>
  </div>

  <div class="sello">
    @if($data[0]->paga == 1)
    <img width="140" src="img/imgpago.png" alt="">
    @endif
  </div>

  <div class="footer">
    <table>
      <tr>
        <td class="text-right">
          <img width="140" src="img/brokerlogo.png" alt="">
        </td>
        <td>
          <p class="text-center">
            BROKER DEL PUERTO ...
            <br>TU TRANQUILIDAD VALE
            <br>www.brokerdelpuerto.com
            <br>barriosprivados@brokerdelpuerto.com
            <br>Tel. (03327-485189) Cel. 15-55841038
            <br>Sarmiento 3314 (1621 - Benavidez)
          </p>
        </td>
      </tr>
    </table>






  </div>

</body>



</html>