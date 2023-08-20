@include('header.index')

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

  #modalfeedback {
    z-index: 999;
    display: none;
    background: rgba(0, 0, 0, 0.4);
    color: #171c32;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 100vw;
    transition: all .5s;
}

.bc-white{
  border: 1px solid white;
}
</style>



  <div class="container">

    <div class="row  justify-content-md-center">
      <div class="cuadro col-sm-6">
        
        <div class="formpoliza">
          <h4>Hola! Querido asegurad@! <br>Hemos creado esta página para que tu puedas consultar tus pólizas</h4>
          <p><b>¡IMPORTANTE! AQUÍ SOLO PODRAS VISUALIZAR TUS PÓLIZAS PARA BARRIOS PRIVADOS QUE ESTÉN PAGAS.</b></p>
          <p>
           1. Tener siempre a mano uma copia de Tú poliza y recibo de pago.</li>
           <br>2. Bajar um certificado de Libre de Deuda.</li>
           <br>3. Agregar clausulas de no repetición. (Solo hasta la suma asegurada contratada).</li>
          </p>
        
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
                <input type="text" class="form-control" placeholder="Nro. Documento" name="documento" id="documento" require>
                <input onfocus="(this.type='date')" type="text" class="form-control" placeholder="Fecha Nacimiento(dd/mm/aaaa)"  name="fechanacimiento" id="fechanacimiento"  required>
              </div>

              <button type="submit" class="btn btn-broker mt-2" onclick="descargapdf()">Consultar mis pólizas</button>



          </form>
        </div>
        <div id="errores">

        </div>




      </div>
      <p class="text-center">
        <small>
          BROKER DEL PUERTO / TU TRANQUILIDAD VALE
          <br>www.brokerdelpuerto.com / comercial@brokerdelpuerto.com
          <br>Tel. (03327-485189) Cel. 15-55841038 / Sarmiento 3314 (1621 - Benavidez)
        </small>
      </p>

      <div id="lista">
        @if($data)
        <div class="text-center">

          <h3>Póliza(s) vigentes a descargar</h3>
          <div>
            @foreach($data as $value)
              <div class="row p-3">
                <div class="col-12">
                  {{$value->prefijo}}{{$value->id_propuesta}} <b> Vigencia desde : </b> {{ $value->fechaDesde }} <b> hasta </b> {{ $value->fechaHasta }}
                </div>
                <div class="d-flex justify-content-center">
                  <a class="btn btn-success" href="{{ url('/descargapdfpoliza') }}?id={{$value->id_propuesta}}&prefijo={{$value->prefijo}}" target="_blank">
                    Haz click aquí y descarga póliza y recibo pagó
                  </a>
                  <a class="btn btn-primary" href="{{ url('/libre-deuda') }}/{{$value->id_propuesta}}/{{$value->prefijo}}" target="_blank">
                    Haz click aquí y descarga certificado de libre de deuda
                  </a>
                  <a class="btn btn-warning" href="{{ route('agregar_barrios', ['prefijo' => $value->prefijo, 'idpropuesta' => $value->id_propuesta]) }}" target="_blank">
                    Haz click aquí y adiciona claúsulas de no repeticion que te eixjan en el barrio
                  </a>
                </div>     
              </div>   
            @endforeach
          </div>
        </div>
        @endif
      </div>

    </div>
  </div>

  </div>

  <div id="modalfeedback">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
          <div id="barratitle" class="">
              <h5 class="modal-title" id="titlecontent"></h5>
              <button onclick="cerrarmodalfeedback()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div id="contentfeedback">
              </div>
          </div>
          <div class="modal-footer">
              <button onclick="cerrarmodalfeedback()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
          </div>
      </div>
  </div>

  <script>
    cerrarmodalfeedback();
    const status = "{{$success}}";
    const estado = "{{$estado}}";

    if(estado != ""){
      if(estado == "success"){
        contentfeedback("Se ha procesado el pago con éxito, descargue la póliza ingresando su documento.","Pago Exitoso");
      }
      if(estado == "pending"){
        contentfeedback("Cuando se haga el pago visite esta página y consulte su póliza para descargar","Pago Pendiente");
      }
    }
    
    if (status == null || status == "")
      document.getElementById('lista').innerHTML = `<h4 class="text-danger text-center">
      Con ese número de Documento no encontramos ninguna Póliza vigente o puede estar en proceso el pago.</h4>
      

      <h5 class='text-center'> Pero no te preocupes, si tu poliza esta vigente o deseas emitir una nueva, te sugerimos que entres en contacto con nosotros al Whatsapp : <a href="https://wa.me/+5491155841038" target="_blank"> +54 9 11 5584 1038</a>  (haz click y escribenos) y verificaremos a la mayor brevedad posible. Tambien puedes contactarnos al e-mail comercial@brokerdelpuerto.com y escribenos que deseas hacer y te respondemos a la mayor brevedad. <br> ¡Tu tranquilidad Vale!</h5>
      `;
      
    function contentfeedback(content,title = "Mensaje", barra = "bg-primary text-white"){

      document.getElementById("titlecontent").innerHTML = title;
      document.getElementById("contentfeedback").innerHTML = content;

      const classbarra = "modal-header "+barra;
      document.getElementById("barratitle").className = classbarra;
      document.getElementById("modalfeedback").style.display = "block";
    }

    function cerrarmodalfeedback(){
            document.getElementById("modalfeedback").style.display = "none";
    }
  </script>




  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
    @include('footer.index')
</body>


</html>