@include('header.index')

@section('title')
    <title>Cotizador Online</title>
@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
@php
$fechavigenciadesde = date("Y-m-d");
@endphp

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
                    <label for="nro_comprobante">No. de comprobante:</label>
                    <input type="text" name="nro_comprobante" id="nro_comprobante" required>
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




    

   

