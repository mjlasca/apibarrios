@include('header.index')

@section('title')
    <title>Cotizador Online</title>
@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
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
        <section>
            <form action="{{  url('api/propuestas/duplicate') }}" method="post">
                @csrf

                <div>
                    <label for="forma_pago">Forma de Pago:</label>
                    <select name="forma_pago" id="forma_pago" required>
                        <option value="CBU">CBU</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="MERCADO_PAGO">Mercado Pago</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
                <input type="hidden" name="pref" value="{{$pref}}">
                <input type="hidden" name="id" value="{{$id}}">
                <div>
                    <label for="nro_comprobante">Nó de comprobante:</label>
                    <input type="text" name="nro_comprobante" id="nro_comprobante" required>
                </div>
                <div>
                    <label for="pay_date">Fecha de pago:</label>
                    <input type="date" name="pay_date" id="pay_date">
                </div>
                
                <p></p>
                <button type="submit">Enviar</button>
            </form>
        </section>

    </div>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</body>

@include('footer.index')

</html>




    

   

