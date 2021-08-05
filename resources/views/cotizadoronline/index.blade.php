@include('header.index')

@section('title')
<title>Cotizador Online</title>
@endsection

<style>
  .formpoliza {
    padding: 20px;
    background-color: #171c32;
    color: #fff;
    border-radius: 20px;
  }


  #id {
    overflow: auto;
  }


  .avertical {
    display: flex;
    align-items: center;
  }

  .asegurados {
    overflow-x: hidden;
    overflow-y: auto;
    width: 100%;

  }

  .asegurado {
    margin-top: 20px;
    display: inline-block;
    padding: 20px;
    width: 100%;
    height: 355px;
    border: solid 2px #eee;
    border-radius: 20px;
  }

  .modal-broker {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1060;
    display: none;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
    background-color: #00000052;
  }
</style>

<body>

  <div class="container">

    <section class="header">
      <div class="row  justify-content-sm-center">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-6">
              <div class="text-center">
                <img style="width: 100%;max-width: 200px;" src="img/brokerlogo.png" alt="">
              </div>
            </div>
            <div class="col-6">
              <div class="text-center">
                <img style="width: 100%;max-width: 200px;margin-top:50px;" src="img/imgsancor1.png" alt="">
              </div>
            </div>
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

        </div>

      </div>

    </section>


    <section class="forms mb-5">
      <form>
        <div class="row  justify-content-sm-center">
          <div class="titulo">
            <h4 class="text-center">
              EMISIÓN DE CERTIFICADOS PARA PARA BARRIOS PRIVADOS <br>
              <div class="row justify-content-sm-center">
                <div class="col-4">
                  <a id="preview" onclick="preview()" href="#">
                    <img src="https://img.icons8.com/material-rounded/48/000000/chevron-left.png" />
                  </a>
                </div>
                <div class="col-4 mt-2">
                  <b>
                    <span class="text-primary" id="paso">PASO 1/3</span>
                  </b>
                </div>
                <div class="col-4">
                  <a id="next" onclick="next()" href="#">
                    <img src="https://img.icons8.com/material-rounded/48/000000/chevron-right.png" />
                  </a>
                </div>

              </div>
            </h4>
          </div>
          <!--
          <div class="col-sm-1 avertical d-none d-sm-none d-sm-flex">
            <a id="preview" onclick="preview()" href="#">
              <img src="https://img.icons8.com/material-rounded/48/000000/chevron-left.png" />
            </a>
          </div>
          -->
          <div id="datostomador" class="col-sm-12 formpoliza">

            <div class="row">
              <h4>Datos del Tomador <a onclick="alert('El tomador es la persona que contrata el seguro')" href="#" title="El tomador es la persona que contrata el seguro"> <small> <span class="badge bg-primary">?</span></small></a>
              </h4>

              <p>
                Bienvenido(a) al sistema cotizador y emisor online, en 3 pasos podrá generar un certificado sin salir de casa, diligencie los siguientes datos
              </p>
            </div>
            <hr>

            <div class="row">

              <div class="col-sm-4">
                <label for="inputEmail4" class="form-label">Tipo Documento</label>
                <select name="tipodocumento" id="" class="form-control">
                  <option value="">Seleccione el tipo</option>
                  <option value="DNI">DNI</option>
                  <option value="LE">LE</option>
                  <option value="LC">LC</option>
                  <option value="CUIT">CUIT</option>
                  <option value="CI">CI</option>
                </select>
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Documento</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-sm-8">
                <label for="inputPassword4" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="inputPassword4">
              </div>
              <div class="col-sm-1">
                <label for="inputPassword4" class="form-label">Cód.Postal</label>
                <input type="text" class="form-control" onkeyup="postal(this.value)" id="codpostal">
              </div>
              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">Localidad</label>
                <input type="text" class="form-control" id="localidad" require>
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Ciudad</label>
                <input type="text" class="form-control" id="ciudad" require>
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Fecha Nacimiento</label>
                <input type="date" class="form-control" id="inputPassword4" require>
              </div>
              <div class="col-sm-4">
                <label for="inputPassword4" class="form-label">Email</label>
                <input type="mail" class="form-control" id="inputPassword4" require>
              </div>
              <div class="col-sm-2">
                <label for="inputPassword4" class="form-label">Sexo</label>
                <select name="sexo" id="" class="form-control" require>
                  <option value="">Seleccione el tipo</option>
                  <option value="M">MASCULINO</option>
                  <option value="F">FEMENINO</option>
                </select>
              </div>
              <div class="col-sm-2">
                <label for="inputPassword4" class="form-label">Situación impositiva</label>
                <select name="situacionimpositiva" id="" class="form-control" require>
                  <option value="">Seleccione el tipo</option>
                  <option value="M">Consumidor Final</option>
                  <option value="F">Exento</option>
                  <option value="F">Monotributista</option>
                  <option value="F">Responsable Inscripto</option>
                  <option value="F">Otro</option>
                </select>
              </div>

              <div class="col-sm-12">
                <a onclick="next()" class="btn btn-primary mt-2">Avanzar y Agregar</a>
              </div>

            </div>
          </div>

          <!--POLIZAS-->
          <div id="datospoliza" class="col-sm-12 formpoliza">

            <div class="row">
              <h4>
                Calcule y Contrate el costo de la cobertura deseada:
              </h4>
            </div>

            <hr>
            <div class="row">

              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">Vigencia desde</label>
                <input type="date" autocomplete="off" class="form-control" id="vigenciadesde" require>
              </div>
              <div class="col-sm-2">
                <label for="inputPassword4" class="form-label">Meses</label>
                <input type="number" onchange="sumarmes(this.value)" class="form-control" id="meses" min="1" max="6" require>
              </div>
              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">Vigencia hasta</label>
                <input type="date" class="form-control" id="vigenciahasta" readonly require>
              </div>
              <div class="col-sm-4">
                </label>
                <label for="inputPassword4" class="form-label">Cobertura <a onclick="alert('(SA)Suma Asegurada por muerte o invalidez \n(GM)Gastos Médicos  \n(DED)Deducible')"> <small> <span class="badge bg-primary">?</span></small></a>
                  <select name="situacionimpositiva" id="" class="form-control" onchange="seleccioncobertura(this.value)" require>
                    <option value="">Seleccione la cobertura</option>
                    @foreach($data["coberturas"] as $value)
                    <option value="{{$value->nombre}}">{{$value->nombre}}.
                      SA: {{number_format($value->suma)}}
                      GM: {{number_format($value->gastos)}}
                      DED: {{number_format($value->deducible)}}
                    </option>
                    @endforeach
                  </select>
              </div>
              <!--
              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">Cantidad de asegurados</label>
                <input onchange="agregarfilas()" type="number" class="form-control" id="asegurados" autocomplete="false" require>
              </div>
              -->
              <div class="col-sm-2">
                <label for="inputPassword4" class="form-label">PREMIO</label>
                <input type="text" readonly class="form-control  bg-warning" style="font-weight: bold;" id="premio" require>
              </div>
              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">TOTAL A PAGAR</label>
                <input type="text" readonly class="form-control bg-warning" style="font-weight: bold;" id="premiototal" require>
              </div>

              <div class="col-sm-4">
                <br>
                <a href="#" class="btn btn-primary mt-2" onclick="next()">AGREGAR TOMADOR</a>
              </div>


            </div>

            
            <hr>

            <div id="personaspolizas" class="asegurados d-block d-sm-block d-md-none">
            </div>

            <div class="d-none d-sm-none d-md-block">
              <table id="tablepolizas">
                <thead></thead>
                <tbody id="fila">
                </tbody>

              </table>
            </div>


            <div class="row mt-3">
              <div id="messageasegurados">

              </div>
              <div class="col-sm-6">
                <a href="#" onclick="agregarfilas(1)" class="btn btn-block btn-primary">Agregar persona</a>
                <a href="#" id="restotal" class="btn btn-warning">TOTAL</a>
              </div>


            </div>

          </div>


          <div id="datosasegurados" class="formpoliza">

            

          </div>

          <div id="datosdepago" class="col-sm-12 formpoliza">

            <div class="alert alert-success">
              PASARELA DE PAGO
            </div>
            <a id="botpago" onclick="pagar()" href="#" class="btn btn-primary">REALIZAR PAGO</a>


          </div>

          <!--
          <div class="col-sm-1 avertical d-none d-sm-none d-sm-flex">
            <a id="next" onclick="next()" href="#">
              <img src="https://img.icons8.com/material-rounded/48/000000/chevron-right.png" />
            </a>
          </div>
          -->
        </div>
      </form>
    </section>




  </div>

  </div>


  <div id="exampleModal" class="modal-broker">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Nueva persona</h5>
          <a onclick="cerrarventana()" class="btn btn-danger">x</a>
        </div>
        <div class="modal-body">





        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary">Agregar</button>
        </div>
      </div>
    </div>
  </div>




  <script>
    const actividades = @json($data["actividades"]);
    const clasificaciones = @json($data["clasificaciones"]);
    const coberturas = @json($data["coberturas"]);
    const provincias = @json($data["provincias"]);

    var filasgenerales = 0;
    var filasmovil = 0;
    var filaspc = 0;

    let opcionesactividades;

    actividades.forEach(element => {
      opcionesactividades += `<option value='${element.reg}'>${element.nombre}</option> `;
    })

    var vecesnext = 0;

    document.getElementById("datospoliza").style.display = "none";
    document.getElementById("datosdepago").style.display = "none";
    document.getElementById("exampleModal").style.display = "none";
    document.getElementById("datosasegurados").style.display = "none";
    document.getElementById("preview").style.display = "none";


    function probando() {
      var childDivs = document.getElementById('personaspolizas').getElementsByTagName('div');


      for (let i = 0; i < childDivs.length; i++) {
        const divhijo = childDivs[i].getElementsByTagName("input");
        for (let j = 0; j < divhijo.length; j++) {

          if (divhijo[j].id.indexOf("documentomodal") == 0)
            console.log("--> Documento " + divhijo[j].value);
          if (divhijo[j].id.indexOf("apellidosmodal") == 0)
            console.log("--> Apellido " + divhijo[j].value);
          if (divhijo[j].id.indexOf("nombresmodal") == 0)
            console.log("--> Nombre " + divhijo[j].value);

        }

        const seldiv = childDivs[i].getElementsByTagName("select");

        console.log("TIPO " + seldiv[0].value)

      }



    }

    const filasasegurados = `
                <td>
                  <input type="text" class="form-control" id="inputPassword4" placeholder="Documento">
                </td>
                <td>
                  <select name="tipodocumento" id="" class="form-control">
                    <option value="">Tipo</option>
                    <option value="DNI">DNI</option>
                    <option value="LE">LE</option>
                    <option value="LC">LC</option>
                    <option value="CUIT">CUIT</option>
                    <option value="CI">CI</option>
                  </select>
                </td>
                <td>
                  <input type="text" class="form-control" id="inputPassword4" placeholder="Apellidos">
                </td>
                <td>
                  <input type="text" class="form-control" id="inputPassword4" placeholder="Nombres">
                </td>
                <td>
                  <input type="date" class="form-control" id="inputPassword4" placeholder="Fecha Nacimiento">
                </td>
                <td>
                  <select name="tipodocumento" id="" class="form-control" onchange="asignacionactividad(this)">
                    <option value="">Actividad</option>
                    ${opcionesactividades}
                  </select>
                </td>
                <td>
                  <select name="tipodocumento" id="" class="form-control">
                    <option value="">Clasificación</option>
                  </select>
                </td>
                <td>
                  <button onclick="deleteRow(this)" class="btn btn-danger">x</button>
                </td>
    `;


    function asignacionactividad(row) {

      document.getElementById("tablepolizas").rows[row.parentNode.parentNode.rowIndex].cells[6].innerHTML =
        `
          <select class='form-control'>
            <option value=''>Clasificación</value>
            ${opcionesclasificaciones(row.value)}
          </select>
        `;

    }

    function asignacionactividad_movil(fila, val) {

      document.getElementById("clasificacionmodal" + fila).innerHTML =
        `
          <select class='form-control' id="clasificacionmodal${fila}">
            <option value=''>Clasificación</value>
            ${opcionesclasificaciones(val.value)}
          </select>
        `;
    }

    function opcionesclasificaciones(cod) {

      let concat = "";
      clasificaciones.map((cla) => {
        if (cla.id_actividad == cod) {
          concat += `
                  <option value = "${cla.reg}">${cla.nombre}</option>
              `;
        }
      });

      return concat;
    }


    const filasaseguradosmovil = (fil) => `
              
                <button onclick="deletediv(this,${fil})" class="btn btn-danger">x</button><span style="float:right;font-size:20px;" id='numdiv${fil}'>#${fil}</span>
                <input type="text" class="form-control" id="documentomodal${fil}" placeholder="Documento">
                
                <select name="" id="tipodocumentomodal${fil}" class="form-control">
                  <option value="">Tipo</option>
                  <option value="DNI">DNI</option>
                  <option value="LE">LE</option>
                  <option value="LC">LC</option>
                  <option value="CUIT">CUIT</option>
                  <option value="CI">CI</option>
                </select>
              
                <input type="text" class="form-control" id="apellidosmodal${fil}" placeholder="Apellidos">
              
                <input type="text" class="form-control" id="nombresmodal${fil}" placeholder="Nombres">
              
                <input type="date" class="form-control" id="nacimientomodal${fil}" placeholder="Fecha Nacimiento">
              
                <select name="" id="actividadmodal${fil}" class="form-control" onchange="asignacionactividad_movil(${fil},this)">
                  <option value="">Actividad</option>
                  ${opcionesactividades}
                </select>
              
                <select name="" id="clasificacionmodal${fil}" class="form-control">
                  <option value="">Clasificación</option>
                </select>
              
    `;


    function numerarspan() {
      const divspolizas = document.getElementById('personaspolizas').getElementsByTagName('div');

      for (let i = 0; i < divspolizas.length; i++) {
        divspolizas[i].getElementsByTagName("span")[0].textContent = "#" + (i + 1);
      }
    }


    function deletediv(div, fil) {
      var row = div.parentNode;
      row.parentNode.removeChild(row);

      numerarspan();
      filasgenerales--;
      totalizar();
    }


    function deleteRow(btn) {
      var row = btn.parentNode.parentNode;
      row.parentNode.removeChild(row);

      filasgenerales--;
      totalizar();
    }

    function totalizar() {

      let total = 0;

      total = filasgenerales *  parseFloat(document.getElementById("premio").value) * parseInt(document.getElementById("meses").value);

      document.getElementById("premiototal").value = total;
      document.getElementById("restotal").innerText = "TOTAL : " + total;

    }

    function postal(val) {

      const vecpostal = provincias.filter(pro => pro.codpostal.trim() == val.trim());

      if (vecpostal.length) {
        document.getElementById("localidad").value = vecpostal[0].provincia;
        document.getElementById("ciudad").value = vecpostal[0].ciudad;
      }

    }


    function agregarfilas(cant = 0) {
      
      filasmovil++;
      filaspc++;
      filasgenerales++;

      const tr = document.createElement("tr");
      tr.innerHTML = filasasegurados
      document.getElementById("fila").appendChild(tr);

      const div = document.createElement("div");
      div.className = "asegurado";
      div.innerHTML = filasaseguradosmovil(filasmovil);
      document.getElementById("personaspolizas").appendChild(div);

      numerarspan();


      totalizar();

    }

    function mostrar(id) {
      var fil = `
                <tr>
                  <td class='text-right'>987654</td>
                  <td>DNI</td>
                  <td>ORTIZ TORO</td>
                  <td>PEDRO</td>
                  <td>01/02/1970</td>
                  <td>Construcción</td>
                  <td>Demolición y Excavación</td>
                </tr>
      `;
      if (id == 'fila1') {
        fil = `
        <tr>
                  <td class='text-right'>123456</td>
                  <td>DNI</td>
                  <td>LASLUISA CASTAÑO</td>
                  <td>MARIO</td>
                  <td>10/10/2000</td>
                  <td>Construcción</td>
                  <td>Demolición y Excavación</td>
                </tr>
                
        `;
        next();

      } else {
        document.getElementById("premio").value = 170;
        document.getElementById("premiototal").value = 680;
      }

      document.getElementById("fila").innerHTML = document.getElementById("fila").innerHTML + fil;
    }

    function next() {

      /*if (vecesnext == 2) {
        document.getElementById("datostomador").style.display = "none";
        //document.getElementById("datospoliza").style.display = "none";
        document.getElementById("datosasegurados").style.display = "none";
        document.getElementById("datosdepago").style.display = "block";
        document.getElementById("next").style.display = "none";
        vecesnext++;
      }*/

      if (vecesnext == 1) {
        document.getElementById("datostomador").style.display = "none";
        document.getElementById("datospoliza").style.display = "none";
        document.getElementById("datosasegurados").style.display = "none";
        document.getElementById("datosdepago").style.display = "block";
        document.getElementById("next").style.display = "none";
        vecesnext++;
      }

      if (vecesnext == 0) {
        document.getElementById("datostomador").style.display = "none";
        document.getElementById("datospoliza").style.display = "block";
        document.getElementById("datosdepago").style.display = "none";
        document.getElementById("datosasegurados").style.display = "none";
        document.getElementById("preview").style.display = "block";
        vecesnext++;

      }

      document.getElementById("paso").innerText = "PASO " + (vecesnext + 1) + "/3";
    }

    function preview() {

      if (vecesnext == 1) {
        document.getElementById("datostomador").style.display = "block";
        document.getElementById("datospoliza").style.display = "none";
        document.getElementById("preview").style.display = "none";
        document.getElementById("datosasegurados").style.display = "none";
        document.getElementById("datosdepago").style.display = "none";
        vecesnext--;
      }


      if (vecesnext == 2) {
        document.getElementById("datostomador").style.display = "none";
        document.getElementById("datospoliza").style.display = "block";
        document.getElementById("datosdepago").style.display = "none";
        document.getElementById("datosasegurados").style.display = "none";
        document.getElementById("next").style.display = "block";
        vecesnext--;
      }

      if (vecesnext == 3) {
        document.getElementById("datostomador").style.display = "none";
        document.getElementById("datospoliza").style.display = "none";
        document.getElementById("datosdepago").style.display = "none";
        document.getElementById("datosasegurados").style.display = "block";
        document.getElementById("next").style.display = "block";
        vecesnext--;
      }


      document.getElementById("paso").innerText = "PASO " + (vecesnext + 1) + "/3";
    }


    function filapoliza() {
      const fila = document.getElementById('filap').parentNode;

    }


    function sumarmes(value) {

      const fec = document.getElementById("vigenciadesde").value;
      const hoy = new Date(fec.substr(6, 2) + "/" + fec.substr(9, 2) + "/" + fec.substr(0, 4));
      const secfec = new Date(hoy.setDate(hoy.getDate() + (parseInt(value) * 30)));


      let mes = secfec.getMonth() + 1;
      mes = mes < 10 ? "0" + mes : mes;

      let dia = secfec.getDate();
      dia = dia < 10 ? "0" + dia : dia;

      const fecha = secfec.getFullYear() + "-" + mes + "-" + dia;

      document.getElementById('vigenciahasta').value = fecha;

      totalizar();

    }


    function pagar() {
      document.getElementById("botpago").textContent = "PROCESANDO...";
      setTimeout(() => {
        document.getElementById("botpago").textContent = "PAGO APROBADO";
        setTimeout(() => {
          window.open("http://127.0.0.1:8000/descargapdfpoliza?id=7217&prefijo=A", "_blank");
        }, 500);
      }, 3000);
    }

    function seleccioncobertura(dato) {
      const val = coberturas.filter(cob => cob.nombre == dato);
      document.getElementById("premio").value = val[0].vrMensual;
      totalizar();
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
</body>

</html>