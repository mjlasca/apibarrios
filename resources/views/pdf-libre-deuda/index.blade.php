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

  <title>Certificado Libre deuda {{ $data[0]->documento  }} - {{ $data[0]->prefijo  }}{{ $data[0]->reg  }}</title>

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
    .footer-certificated{
      margin-top: 300px;
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

  <section id="recibo">

    <div>
    <img  src="img/certificadolibreSancor.png" alt="">
    </div>
    <br><br><br><br><br><br>
    <div>
      <h2>CERTIFICADO DE LIBRE DEUDA</h2>
    </div>
    <div class="text-right">
        Buenos Aires, {{date('d')}}  {{ strtoupper($month[strtolower(date('F'))]) }} de {{date('Y')}} 
    </div>
    <br>
    <div>
        
        Por intermedio de la presente certificamos, que la Póliza del Ramo Accidentes Personales No. {{ $data[0]->prefijo }}-{{ $data[0]->idpropuesta }} contratada por el asegurado 
        @foreach($lineasdata as $val)
          {{$val->nombres}}  {{$val->apellidos}}  {{$val->tipo_documento}}:{{$val->documento}},
        @endforeach
         vigente entre el {{\Carbon\Carbon::parse($data[0]->fechaDesde)->format('d/m/Y')}} y el {{\Carbon\Carbon::parse($data[0]->fechaHasta)->format('d/m/Y')}}, de facturación n 1 Cuota, conforme a nuestro registros se abona mediante efectivo, encontrándose abonada en su totalidad.
        Se extiende el presente certificado para ser presentado ante quien corresponda. 
        
    </div>
    
    <div class="footer-certificated">
      
        
      <div>
        <div >
          <p>
            Organizador : {{ $data[0]->organizador }}
            <br>
            Productor : {{ $data[0]->productor }}
          </p>
          <img  src="img/firmaCeLibre.png" alt="">
          <p class="text-right detail-right">
            Broker del puerto<br>
            Cobranzas<br>
            Nayibe El Mailki
          </p>
        </div>
        
          
        
        

      </div>
      
      
    </div>
    


  </section>


</body>



</html>