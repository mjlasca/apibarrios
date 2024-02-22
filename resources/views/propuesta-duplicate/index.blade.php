@include('header.index')

@section('title')
    <title>Duplicación propuesta paga</title>
@endsection

        <div class="container col-6">
            <form action="{{  url('api/propuestas/duplicate') }}" method="post">
                @csrf

                <div class="">
                    <label for="forma_pago">Forma de Pago:</label>
                    <select class="form-control" name="forma_pago" id="forma_pago" required>
                        <option value="CBU">CBU</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                        <option value="MERCADO_PAGO">Mercado Pago</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="OTRO">Otro</option>
                    </select>
                </div>
                <input class="form-control" type="hidden" name="pref" value="{{$pref}}">
                <input class="form-control" type="hidden" name="id" value="{{$id}}">
                <div>
                    <label for="nro_comprobante">No. de comprobante:</label>
                    <input class="form-control" type="text" name="nro_comprobante" id="nro_comprobante" required>
                </div>
                
                <p></p>
                <button class="btn btn-primary" type="submit">Enviar</button>
            </form>
        </div>

</body>

@include('footer.index')

</html>




    

   

