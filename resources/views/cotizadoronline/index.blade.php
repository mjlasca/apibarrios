@include('header.index')

@section('title')
    <title>Cotizador Online</title>
@endsection
<link href="{{asset('css/estilo.css')}}" rel="stylesheet">
@php
$fechavigenciadesde = date("Y-m-d");
@endphp

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
                                <img style="width: 100%;max-width: 200px;margin-top:50px;" src="img/imgsancor1.png"
                                    alt="">
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
        <form id="formpoliza" >
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="iidd" value="1" >
        </form>

        <section class="forms mb-5">
            <form onsubmit="return false">
                
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
                                        <img
                                            src="https://img.icons8.com/material-rounded/48/000000/chevron-right.png" />
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
                            <h4>Datos del Tomador <a onclick="contentfeedback('El tomador es la persona que contrata el seguro')"
                                    href="#" title="El tomador es la persona que contrata el seguro"> <small> <span
                                            class="badge bg-primary">?</span></small></a>
                            </h4>

                            <p>
                                Bienvenido(a) al sistema cotizador y emisor online, en 3 pasos podrá generar un
                                certificado sin salir de casa, diligencie los siguientes datos
                            </p>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-sm-4">
                                <label for="inputEmail4" class="form-label">Tipo Documento</label>
                                <select name="tipodocuemntotomador"  id="tipodocuemntotomador" onchange="objTomador.tipodocumento = this.value" class="form-control">
                                    <option value="">Seleccione el tipo</option>
                                    <option value="DNI">DNI: Documento Nacional de Identidad</option>
                                    <option value="LE">LE: Libreta de Enrolamiento</option>
                                    <option value="LC">LC: Libreta Cívica</option>
                                    <option value="CUIT">CUIT: Clave único de identificación tributaria</option>
                                    <option value="CI">CI: Cedula de Identidad</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Documento</label>
                                <input type="text" class="form-control" name="documentotomador" id="documentotomador" onchange="objTomador.documento = this.value">
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" name="nombretomador" onchange="objTomador.nombres = this.value" id="nombretomador">
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Apellido completo</label>
                                <input type="text" class="form-control" onchange="objTomador.apellidos = this.value" name="apellidotomador" id="apellidotomador">
                            </div>
                            <div class="col-sm-8">
                                <label for="inputPassword4" class="form-label">Dirección</label>
                                <input type="text" class="form-control" onchange="objTomador.direccion = this.value" name="direcciontomador" id="direcciontomador">
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" onchange="objTomador.telefono = this.value" name="telefonotomador" id="telefonotomador">
                            </div>
                            <div class="col-sm-1">
                                <label for="inputPassword4" class="form-label">Cód.Postal</label>
                                <input type="text" class="form-control" onchange="objTomador.codpostal = this.value" name="codpostal" onkeyup="postal(this.value)" id="codpostal">
                            </div>
                            <div class="col-sm-3">
                                <label for="inputPassword4" class="form-label">Localidad</label>
                                <input type="text" class="form-control" onchange="objTomador.localidad = this.value" name="localidad" id="localidad" onchange="postal(this.value,2)" require>
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Ciudad</label>
                                <input type="text" class="form-control" onchange="objTomador.ciudad = this.value" name="ciudad" id="ciudad" require>
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Fecha Nacimiento</label>
                                <input type="date" class="form-control" onchange="objTomador.fechanacimiento = this.value" name="fechanacimientotomador" id="fechanacimientotomador" require>
                            </div>
                            <div class="col-sm-4">
                                <label for="inputPassword4" class="form-label">Email</label>
                                <input type="mail" class="form-control" onchange="objTomador.email = this.value" name="emailtomador" id="emailtomador" require>
                            </div>
                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">Sexo</label  >
                                <select  name="sexotomador" id="sexotomador" class="form-control" onchange="objTomador.sexo = this.value" require>
                                    <option value="">Seleccione el tipo</option>
                                    <option value="MASCULINO">MASCULINO</option>
                                    <option value="FEMENINO">FEMENINO</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">Situación impositiva</label>
                                <select name="situaciontomador" id="situaciontomador" class="form-control"  onchange="objTomador.situacionimpositiva = this.value" require>
                                    <option value="" selected>Seleccione el tipo</option>
                                    <option value="Consumidor Final"  >Consumidor Final</option>
                                    <option value="Exento">Exento</option>
                                    <option value="Monotributista">Monotributista</option>
                                    <option value="Responsable Inscripto">Responsable Inscripto</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="col-sm-12">
                                <a href="#datospoliza" onclick="agregartomador(1)" class="btn btn-primary mt-2">Siguiente paso</a>
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
                                <input type="date" autocomplete="off" class="form-control" min="{{$fechavigenciadesde}}" onchange="objcoberturavigencia.vigenciadesde = this.value" id="vigenciadesde"  value="{{$fechavigenciadesde}}" require>
                            </div>
                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">Meses (Mínimo 1)</label>
                                <input type="number" onchange="sumarmes(this.value)"   class="form-control" id="meses" value="1"
                                    min="1" max="6" require>
                            </div>
                            <div class="col-sm-3">
                                <label for="inputPassword4" class="form-label">Vigencia hasta</label>
                                <input type="date" class="form-control" onchange="objcoberturavigencia.vigenciahasta = this.value" id="vigenciahasta" readonly require>
                            </div>
                            <div class="col-sm-4">
                                </label>
                                <label for="inputPassword4" class="form-label">Cobertura <a
                                        onclick="contentfeedback('<b>(SA)</b>Suma Asegurada por muerte accidental o invalidez <br><b>(GM)</b>Gastos Médicos  <br><b>(DED)</b>Deducible <small>Sólo se aplica sobre la cobertura de Gastos Médicos(GM)</small><br><br>Esta cobertura sólo será válida dentro del barrio privado')">
                                        <small> <span class="badge bg-primary">?</span></small></a>
                                    <select name="cobertura" id="cobertura"  class="form-control"
                                        onchange="seleccioncobertura(this.value)" require>
                                        <option value="">Seleccione la cobertura</option>
                                        @foreach ($data['coberturas'] as $value)
                                            <option value="{{ $value->nombre }}">{{ $value->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                <div id="promociones">

                                </div>
                                <small>La presente cobertura solo aplica para dentro de Barrios Privados</small>
                            </div>
                            <!--
              <div class="col-sm-3">
                <label for="inputPassword4" class="form-label">Cantidad de asegurados</label>
                <input onchange="agregarfilas()" type="number" class="form-control" id="asegurados" autocomplete="false" require>
              </div>
              -->
                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">PREMIO</label>
                                <input type="text" readonly class="form-control  bg-warning" onchange="objcoberturavigencia.premio = this.value" style="font-weight: bold;"
                                    id="premio" require>
                            </div>
                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">TOTAL A PAGAR</label>
                                <input type="text" readonly class="form-control bg-warning" onchange="objcoberturavigencia.premiototal = this.value" style="font-weight: bold;"
                                    id="premiototal" require>
                            </div>

                            <div class="col-sm-2">
                                <label for="inputPassword4" class="form-label">Barrio(sin guiones) </label>
                                <div class=" input-group">
                                    <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" onclick="revisarcobertura()" onchange="borrarguiones(this.value)" placeholder="Escribe el CUIT" class="form-control"  id="barrio" require>
                                    <div class="input-group-append">
                                        <button onclick="mostrartab(0); seleccinarbarrio()" class="btn btn-primary" type="button" title="Agregar Barrio">+</button>
                                        
                                    </div>
                                </div>
                                <small>(Cláusula de no Repetición)</small>
                                <div id="mensajebarrio" class="text-danger">

                                </div>

                            </div>
                            <div class="col-sm-2">
                                <br>
                                
                                <a href="#" class="btn btn-primary mt-2"  onclick="mostrartab(0); if( revisarcobertura() ) contentfeedback(agregargrupo(),'Grupo de barrios')">Grupo de Barrios</a>                                
                            </div>

                            <div class="col-sm-4">
                                <br>
                                <a href="#filpc1" class="btn btn-primary mt-2" onclick="agregartomador(); mostrartab(1)">¿Desea agregar el tomador como Asegurado?</a> 
                            </div>


                        </div>

                        <div id="mayores60" class="mt-3">
                            
                        </div>


                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                              <button class="nav-link active" id="personastab-tab" data-bs-toggle="tab" data-bs-target="#personastab" type="button" role="tab" aria-controls="personastab" aria-selected="true">Personas</button>
                            </li>
                            <li class="nav-item" role="presentation">
                              <button class="nav-link" id="barriostab-tab" data-bs-toggle="tab" data-bs-target="#barriostab" type="button" role="tab" aria-controls="barriostab" aria-selected="false">Barrios</button>
                            </li>
                            
                          </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="personastab" role="tabpanel" aria-labelledby="personastab-tab">
                                <div class="">
                                    <p>PERSONAS ASEGURADAS:</p>
                                    <table  id="tablepolizas">
                                        <thead></thead>
                                        <tbody id="fila">
                                        </tbody>
        
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="barriostab" role="tabpanel" aria-labelledby="barriostab-tab">
                                <div  >
                                    <table >
                                        <thead></thead>
                                        <tbody id="tablabarrios">
                                        </tbody>
                                    </table>
                                    <br>
                                    <div id="avisosa">

                                    </div>
                                </div>

                            </div>
                        </div>
                       
                        


                        <div class="row mt-3">
                            <div id="messageasegurados">

                            </div>
                            <div class="col-sm-6">
                                <a id="botagregafila" href="#filpc1" id="agregarpersona" onclick="mostrartab(1); agregarfilas()" class="btn btn-block btn-primary">Haz Clic aquí y adiciona asegurados</a>
                                <a href="#restotal" id="restotal" class="btn btn-warning">TOTAL</a>
                                <a href="#resumen" onclick="next()" class="btn btn-primary">SIGUIENTE PASO</a>
                                <div id="mayores60" class="mt-3">
                            
                                </div>
                            </div>


                        </div>

                    </div>

                    <div id="datosdepago" class="col-sm-12 formpoliza">

                        <div id="pasarela" class="alert alert-secondary">
                            RESUMEN
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
    </div>

    </section>

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
                <button id="botfeedback" onclick="cerrarmodalfeedback()" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
    </div>

    

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    
    <script src="{{ asset('js/peticiones.js') }}"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>


    
    <script>
        window.onload = function() {
            deshabilitaRetroceso();
            sumarmes(1);
            objParametros.csrf = document.getElementById("_token").value;
        }
        

        function deshabilitaRetroceso(){
            window.location.hash="no-back-button";
            window.location.hash="Again-No-back-button" //chrome    
            window.onhashchange=function(){window.location.hash="no-back-button";}
        }

        
        function mostrartab(tab){//0 : barrio 1 : personas
            var tabpersona = document.getElementById("personastab-tab");
            var tabbarrio = document.getElementById("barriostab-tab");
            var panelbarrio = document.getElementById("barriostab");
            var panelpersona = document.getElementById("personastab");

            if(tab == 0){
                if(tabbarrio.className == "nav-link"){
                    tabpersona.className = "nav-link";
                    tabbarrio.className = "nav-link active";
                    tabpersona.setAttribute("aria-selected", false);
                    tabbarrio.setAttribute("aria-selected", true);
                    panelpersona.className = "tab-pane fade";
                    panelbarrio.className = "tab-pane fade active show";
                }
                
            }else{
                if(tabpersona.className == "nav-link"){
                    tabbarrio.className = "nav-link";
                    tabpersona.className = "nav-link active";
                    tabbarrio.setAttribute("aria-selected", false);
                    tabpersona.setAttribute("aria-selected", true);
                    panelbarrio.className = "tab-pane fade";
                    panelpersona.className = "tab-pane fade active show";
                }
            }
            
            
        }
        

        document.getElementById("datospoliza").style.display = "none";
        document.getElementById("datosdepago").style.display = "none";
        document.getElementById("preview").style.display = "none";

        const actividades = @json($data['actividades']);
        const clasificaciones = @json($data['clasificaciones']);
        const coberturas = @json($data['coberturas']);
        const provincias = @json($data['provincias']);
        const barrios = @json($data['barrios']);
        const gruposbarrios = @json($data['gruposbarrios']);
        const gruposbarriosnombres = @json($data['gruposbarriosnombres']);
        
        var barriosagregados = [];
        var valcobertura;
        var mayoresde60 = 0;

        var objTomador = {"documento" : null, "tipodocumento" : null, "nombres":null,"apellidos":null,"direccion":null,"telefono":null,"codpostal":null,"localidad":null,"ciudad":null,"fechanacimiento":null,"email":null,"sexo":null,"situacionimpositiva":null};
        var objParametros = {"csrf" : null, "ultmod" : null};
        var personasaseguradas = [];
        var objcoberturavigencia = {"vigenciadesde" : null,"vigenciahasta" : null, "meses" : null, "cobertura" : null, "premio" : null, "premiototal" : null, "promociones": null};
        var filasgenerales = 0;
        var filaspc = 0;
        var vecesnext = 0;
        let opcionesactividades;
        let opcionesgruposbarrios;

        objcoberturavigencia.vigenciadesde = document.getElementById("vigenciadesde").value;


        function borrarguiones(dat){
            document.getElementById("barrio").value = dat.replace(/-/g, '');
        }

        async function savep(){
            const res = await savepropuesta('{{url('/savepropuesta')}}');
            console.log(res);
            if(res.success){
                contentfeedback(res.res);
                // Agrega credenciales de SDK
                const mp = new MercadoPago("{{ config('services.mercadopago.key') }}", {
                        locale: 'es-AR'
                });

                // Inicializa el checkout
                mp.checkout({
                    preference: {
                        id: res.preference
                    },
                    autoOpen: true,
                    render: {
                            container: '.cho-container', // Indica el nombre de la clase donde se mostrará el botón de pago
                            label: 'Pagar', // Cambia el texto del botón de pago (opcional)
                    }
                });
                //location.href = "{{url('/paypropuesta')}}?total="+objcoberturavigencia.premiototal+"&idpropuesta="+res.idpropuesta+"&prefijo="+res.prefijo+"&tomador="+objTomador.nombres+" "+objTomador.apellidos;
            }else{
                contentfeedback(res.res,'Error en el proceso', 'bg-danger text-white');   
            }
        }
    </script>
    <script src="{{ asset('js/functions.js') }}"></script>



    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

@include('footer.index')

</html>




    

   

