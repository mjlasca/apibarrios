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

    .page-break{
      page-break-before : always;
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

    #recibo{
      /*border : solid 1px #000;*/
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
      Constancia de Póliza - P N°: {{$data[0]->prefijo}} - {{ $data[0]->reg }}
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
        <td colspan="3">{{ $cliente[0]->nombres ." ".$cliente[0]->apellidos  }}</td>
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
              $concatbarrios .= $val->id_barrio ." - ".$val->nombre .", ";
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
        {{ \Carbon\Carbon::parse($data[0]->fechaDesde)->format('d/m/Y h:i:s') }} A {{ \Carbon\Carbon::parse($data[0]->fechaHasta)->format('d/m/Y h:i:s') }}
      @else
        {{ \Carbon\Carbon::parse($data[0]->fechaDesde)->format('d/m/Y') }} A {{ substr( \Carbon\Carbon::parse($data[0]->fechaHasta)->format('d/m/Y'), 0,10) . " 00:00:00" }}
      @endif
    </b></p>
    <p>
      (*) Se aclara que son asegurables personas de 17 a 65 años inclusive.
      <br>(**) Se deja constancia que se dará cobertura a la actividad declarada hasta 15 metros de altura.se deberá cumplir además con el resto de condiciones de asegurabilidad de Sancor Coop.de Seguros Ltda.
      <br>Coberturas y Capitales Asegurados
      <br>Hechos ocurridos a causa de las actividades y/o tareas declaradas en la correspondiente solicitud, exclusivamente cuando las mismas sean desempeñadas por el asegurado o los asegurados en los Barrios Privados declarados, incluido los trayectos para trasladarse de un barrio Privado a otro y/o in itinere.
      <br>- MUERTE ACCIDENTAL ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- INVALIDEZ TOTAL Y PARCIAL PERMANENTE POR ACCIDENTE ${{ number_format($data[0]->cobertura_suma,2) }}
      <br>- ASISTENCIA MEDICO FARMACÉUTICA POR REINTEGRO ${{ number_format($data[0]->cobertura_gastos,2) }}(con deducible de ${{ number_format($data[0]->cobertura_deducible,2) }})
      <br>Cobertura in itinere incluyendo casos en que el vehículo de traslado sea motocicletas y/o bicicletas y/o vehículos similares
    </p>
  </div>


  

  <div class="sello text-center">
    @if($data[0]->paga == 1)
    
    <img width="140" src="img/imgpago.png" alt=""><br>
    <small style="font-size: 9px">Documento Generado en {{$data[0]->ultmod}}</small>
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

  <section id="recibo">
    <div class="page-break"></div>

    <div style="z-index:9999;">
      <div style="padding : 10px;width: 47%;float: left;border:1px solid;">
        <table>
          <tr>
            <td colspan="3">
              <img src="img/imgsancor1.png" alt="">
            </td>
      
            <td class="text-right" colspan="3">
              No. {{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}<br>
              Accidentes Personales
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" >Ramo</td>
            <td class="text-center" >Prod</td>
            <td class="text-center" >Referencia</td>
            <td class="text-center" >No. Póliza</td>
            <td class="text-center" >Certif.</td>
            <td class="text-center" >Propuesta</td>
          </tr>
          <tr>
            <td class="text-center" >600</td>
            <td class="text-center" >13</td>
            <td class="text-center" >en trámite</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</td>
            <td class="text-center" >0</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->reg}}</td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" colspan="2" >Organización</td>
            <td class="text-center" >Productor</td>
            <td class="text-center" colspan="2" >Cliente</td>
            <td class="text-center" >Asociado</td>
          </tr>
          <tr>
            <td class="text-center" colspan="2" >150430</td>
            <td class="text-center" >{{ $data[0]->productor  }}</td>
            <td class="text-center" colspan="2" >DNI {{ $data[0]->documento  }}</td>
            <td class="text-center" >0</td>
          </tr>
          <tr>
            <td colspan="6">
              <br>
              Sr/es : {{ $cliente[0]->nombres. " ".$cliente[0]->apellidos }}<br>
              Domicilio : {{ $cliente[0]->direccion }}<br>
              Localidad : {{ $cliente[0]->codpostal ." - ".$cliente[0]->localidad }} <br>
              <br>
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" colspan="2" >Vencimiento</td>
            <td class="text-center" >Cuota</td>
            <td class="text-center" colspan="2" style="background-color: #fff" ></td>
            <td class="text-center" >Importe</td>
          </tr>
          <tr>
            <td class="text-center" colspan="2">{{ substr( $data[0]->fechaHasta,0,10) }}</td>
            <td class="text-center" >1/1</td>
            <td class="text-center" colspan="2"></td>
            <td class="text-center" >{{$data[0]->premio_total}}</td>
          </tr>
          <tr>
            
              @if($data[0]->paga == 1)
              <td style="text-align: center" colspan="6">
              <img  width="70%" src="img/imgpago.png" alt="">
              
              @else
              <td>
              <br>
              <br>
              <br>
              <br>
              <br>
              <br>
              @endif
            </td>
          </tr>
    
        </table>
      </div>
      <div style="padding : 10px;width: 47%;float: left;border:1px solid;">
        <table>
          <tr>
            <td colspan="3">
              <img src="img/imgsancor1.png" alt="">
            </td>
      
            <td class="text-right" colspan="3">
              No. {{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}<br>
              Accidentes Personales
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" >Ramo</td>
            <td class="text-center" >Prod</td>
            <td class="text-center" >Referencia</td>
            <td class="text-center" >No. Póliza</td>
            <td class="text-center" >Certif.</td>
            <td class="text-center" >Propuesta</td>
          </tr>
          <tr>
            <td class="text-center" >600</td>
            <td class="text-center" >13</td>
            <td class="text-center" >en trámite</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</td>
            <td class="text-center" >0</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" colspan="2" >Organización</td>
            <td class="text-center" >Productor</td>
            <td class="text-center" colspan="2" >Cliente</td>
            <td class="text-center" >Asociado</td>
          </tr>
          <tr>
            <td class="text-center" colspan="2" >150430</td>
            <td class="text-center" >{{ $data[0]->productor  }}</td>
            <td class="text-center" colspan="2" >DNI {{ $data[0]->documento  }}</td>
            <td class="text-center" >0</td>
          </tr>
          <tr>
            <td colspan="6">
              <br>
              Sr/es : {{ $cliente[0]->nombres. " ".$cliente[0]->apellidos }}<br>
              Domicilio : {{ $cliente[0]->direccion }}<br>
              Localidad : {{ $cliente[0]->codpostal ." - ".$cliente[0]->localidad }} <br>
              <br>
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" colspan="2" >Vencimiento</td>
            <td class="text-center" >Cuota</td>
            <td class="text-center" colspan="2" style="background-color: #fff" ></td>
            <td class="text-center" >Importe</td>
          </tr>
          <tr>
            <td class="text-center" colspan="2">{{ substr( $data[0]->fechaHasta,0,10) }}</td>
            <td class="text-center" >1/1</td>
            <td class="text-center" colspan="2"></td>
            <td class="text-center" >{{$data[0]->premio_total}}</td>
          </tr>
          <tr>
            @if($data[0]->paga == 1)
              <td style="text-align: center" colspan="6">
              <img  width="70%" src="img/imgpago.png" alt="">
              
              @else
              <td>
              <br>
              <br>
              <br>
              <br>
              <br>
              <br>
              @endif
            </td>
          </tr>
    
        </table>
      </div>
    </div>
    
    <div style="display: block;">
      <div style="padding : 10px;margin-top: 320px;border:1px solid;">
        <table>
          <tr>
            <td colspan="2">
              <img src="img/imgsancor1.png" alt="">
            </td>
            <td colspan="4" class="text-center">
              <small>
              CASA CENTRAL Ruta 34 km. 257<br>
              Tel. (03493) 428500 (Alternativo 420151)<br>
              FAX(03492) 490979<br>
              2322 - SUNCHALES(Sta.Fe)<br>
              </small>
            </td>
            <td colspan="4" class="text-center">
              <small>
              C.U.I.T N° 30-50004946-0<br>
              Ingresos Brutos: C.M. 921-740719-3<br>
              Caja Previsión N°: 50004946<br>
              </small>
            </td>
            <td class="text-right" colspan="2">
              No. {{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}<br>
              Accidentes Personales
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" >Ramo</td>
            <td class="text-center" >Prod</td>
            <td class="text-center" >Referencia</td>
            <td class="text-center" >No. Póliza</td>
            <td class="text-center" >Certif.</td>
            <td class="text-center" >Propuesta</td>
            <td class="text-center" colspan="2" >Organi-zación</td>
            <td class="text-center" >Productor</td>
            <td class="text-center" colspan="2" >Cliente</td>
            <td class="text-center" >Asociado</td>
          </tr>
          <tr>
            <td class="text-center" >600</td>
            <td class="text-center" >13</td>
            <td class="text-center" >en trámite</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</td>
            <td class="text-center" >0</td>
            <td class="text-center" >{{$data[0]->prefijo}}-{{$data[0]->idpropuesta}}</td>
            <td class="text-center" colspan="2" >150430</td>
            <td class="text-center" >{{ $data[0]->productor  }}</td>
            <td class="text-center" colspan="2" >DNI {{ $data[0]->documento  }}</td>
            <td class="text-center" >0</td>
          </tr>
          <tr>
            <td colspan="6">
              <br>
              Sr/es : {{ $cliente[0]->nombres. " ".$cliente[0]->apellidos }}<br>
              Domicilio : {{ $cliente[0]->direccion }}<br>
              Localidad : {{ $cliente[0]->codpostal ." - ".$cliente[0]->localidad }} <br>
              Provincia : {{ $cliente[0]->ciudad }} <br>
              <br>
            </td>
          </tr>
          <tr style="background-color: rgb(173, 173, 173)">
            <td class="text-center" >No. Cuota</td>
            <td class="text-center" >Cant. Cuota</td>
            <td class="text-center" colspan="2" >Vencimiento</td>
            <td class="text-center" colspan="7" style="background-color: #fff" ></td>
            <td class="text-center" >Importe</td>
          </tr>
          <tr>
            <td class="text-center" >1</td>
            <td class="text-center" >1</td>
            <td class="text-center" colspan="2">{{ substr( $data[0]->fechaHasta,0,10) }}</td>
            <td class="text-center" colspan="7"></td>
            <td class="text-center" >{{$data[0]->premio_total}}</td>
          </tr>
          <tr>
            @if($data[0]->paga == 1)
              <td style="text-align: center" colspan="12">
              <img  width="35%" src="img/imgpago.png" alt="">
              
              @else
              <td>
              <br>
              <br>
              <br>
              <br>
              <br>
              <br>
              @endif
            </td>
          </tr>
    
        </table>
      </div>
    </div>
    
  </section>


</body>



</html>