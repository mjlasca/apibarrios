<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <title>Descargar póliza</title>
</head>

<style>
  .formpoliza {
    padding: 20px;
    background-color: #171c32;
    color: #fff;
    border: solid 1px #ccc;
    border-radius: 20px;
    box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);
  }



  .btn-broker {
    background-color: #1488ca;
    color: #fff;
    font-weight: bold;
  }

  .btn-broker:hover {
    background-color: #fff;
    color: #000;
  }
</style>

<body>

  <div class="container">

    <div class="row  justify-content-md-center">
      <div class="cuadro col-sm-6">
        <div class="text-center">
          <img width="200" src="img/brokerlogo.png" alt="">


        </div>
        <div class="formpoliza">
          <h4>Aqui podras consultar y descargar tu poliza de accidentes personales si esta vigente. Ingresa tu numero de documento: </h4>
          <form id="formpoliza" method="POST" action="{{ url('/consultapoliza') }}">
            @csrf
            <div class="mb-3">
              <div class="input-group">
                <div class="input-group-prepend">
                  <select name="tipodocumento" class="form-control" id="tipodocumento">
                    <option value="DNI">DNI</option>
                    <option value="LE">LE</option>
                    <option value="LC">LC</option>
                    <option value="CUIT">CUIT</option>
                    <option value="CI">CI</option>
                  </select>
                </div>
                <input type="text" require='require' class="form-control" placeholder="Nro. Documento" name="documento" id="documento" require>
              </div>

              <button type="submit" class="btn btn-broker mt-2" onclick="descargapdf()">Descargar</button>



          </form>
        </div>
        <div id="errores">

        </div>




      </div>
      <p class="text-center">
        <small>
          BROKER DEL PUERTO / TU TRANQUILIDAD VALE
          <br>www.brokerdelpuerto.com / barriosprivados@brokerdelpuerto.com
          <br>Tel. (03327-485189) Cel. 15-55841038 / Sarmiento 3314 (1621 - Benavidez)
        </small>
      </p>

      <div id="lista">
        @if($data)
        <div class="text-center">

          <h3>Póliza(s) vigentes a descargar</h3>
          <ol>
            @foreach($data as $value)
            <li class="bg-primary mb-1 p-1 ">
              <a href="{{ url('/descargapdfpoliza') }}?id={{$value->id_propuesta}}&prefijo={{$value->prefijo}}" target="_blank">
                <div class="text-white">
                  {{$value->prefijo}}{{$value->id_propuesta}} <b> Vigencia desde : </b> {{ $value->fechaDesde }} <b> hasta </b> {{ $value->fechaHasta }}
                </div>
              </a>
            </li>
            @endforeach
          </ol>
        </div>
        @endif
      </div>

    </div>
  </div>

  </div>

  <script>
    const status = "{{$success}}";
    if (status == null || status == "")
      document.getElementById('lista').innerHTML = `<h4 class="text-danger">
      Con ese número de Documento no encontramos ninguna Póliza vigente. </h4>
      <h5>Caso necesario sugerimos que entres en contacto con nosotros al whatsapp: <a href="https://wa.me/+5491155841038" target="_blank"> +54 9 11 5584 1038</a> y hagas tu pedido</h5>
      `;
  </script>




  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>