@include('header.index')

@section('title')
    <title>Agregar barrios</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection

    <div class="container">
        
        <div>
            
            <h3 class="mt-5">
            Hola {{ $propuesta->nombre }}, aqui pódras incluir inmediatamente  una o varias claúsulas  a tu póliza <b>{{$propuesta->prefijo}}-{{$propuesta->idpropuesta}}</b> que te hayan pedido en el barrio(s) a ingresar y deberas considerar:
            </h3>
            <hr class="mb-5">
            <div class="alert alert-primary" role="alert">
                No pódras elegir clausulas de barrios que exijan mas suma asegurada a la que tienes en tu actual póliza <b>(${{ number_format($propuesta->cobertura_suma,2) }})</b>. 
            </div>
            <h4>Selecciona el grupo que deseas agregar</h4>
            
            <form action="{{ route('agregar_barrios_barrio') }}" method="post">
                @csrf
                @method('put')
                <input type="hidden" name="prefijo" value="{{ request('prefijo') }}">
                <input type="hidden" name="id" value="{{ request('idpropuesta') }}">
                <div class="form-group">
                    <select class="form-control" name="grupo" id="" value="{{ request('success_grupo') }}" required>
                            <option value="" checked>-- Selecciona un grupo --</option>
                        @forelse($gruposbarrios as $grupo)
                            
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                        @empty
                            <p>No hay grupos</p>
                        @endforelse
                    </select>
                </div>
                <input type="submit" value="Agregar Grupo" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
                @if(request('error_grupo'))
                    <div class="alert alert-danger" role="alert">
                        {{request('error_grupo')}}
                    </div>
                @endif
                @if(request('success_grupo'))
                    <div class="alert alert-success" role="alert">
                        Se ha agregado el grupo con éxito, puedes revisar tu póliza en el siguiente enlace
                        <a href="{{ route('descargapdfpoliza', ['id' => request('idpropuesta'), 'prefijo' => request('prefijo')]) }}" target="_blank"><b>Descargar Póliza</b></a>
                    </div>
                @endif
                
            </form>
        </div>

        <div class="mt-5">
            <h4>Agregar por barrio</h4>
            <label for="">
                <small>
                Si conoces el cuit del barrio a ingresar, asegurate que sea por la suma asegurada que el barrio te pide para que puedas incluirla. Caso la suma asegurada sea mayor, por favor entra en contacto con nosotros al Whatsapp : <a href="https://wa.me/+5491155841038" target="_blank"> +54 9 11 5584 1038</a>  (haz click y escribenos) y te aumentamos la suma asegurada y/o caso no aparezca la clausula que necesitas para poder incluirtela.
                </small>
            </label>
            <form action="{{ route('agregar_barrios_barrio') }}" method="post">
                @csrf
                @method('put')
                <input type="hidden" name="prefijo" value="{{ request('prefijo') }}">
                <input type="hidden" name="id" value="{{ request('idpropuesta') }}">
                <div class="form-group">
                    <input type="number" name="cuit" class="form-control" placeholder="Escribe el CUIT"  value="{{ request('cuit') }}" required>
                </div>
                <input type="submit" value="Agregar Barrio" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">

                @if(request('error_cuit'))
                    <div class="alert alert-danger" role="alert">
                        {{request('error_cuit')}}
                    </div>
                @endif
                @if(request('success_barrio'))
                    <div class="alert alert-success" role="alert">
                        Se ha agregado el barrio (CUIT - {{ request('success_barrio') }}) con éxito, puedes revisar tu póliza en el siguiente enlace
                        <a href="{{ route('descargapdfpoliza', ['id' => request('idpropuesta'), 'prefijo' => request('prefijo')]) }}" target="_blank"><b>Descargar Póliza</b></a>
                    </div>
                @endif
            </form>
        </div>

        <div class="mt-5 p-2">

            <!--<table class="table">
                <thead>
                    <th>CUIT</th>
                    <th>Nombre barrio</th>
                    <th>Agregar</th>
                </thead>
                <tbody>
                    @foreach($barrios as $barrio)
                        <tr>
                            <td>{{ $barrio->id }}</td>
                            <td>{{ $barrio->nombre }}</td>
                            <td>
                                <form action="{{ route('agregar_barrios_barrio') }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="prefijo" value="{{ request('prefijo') }}">
                                    <input type="hidden" name="id" value="{{ request('idpropuesta') }}">
                                    <input type="hidden" name="idbarrio" value="{{ $barrio->id }}">
                                    <input type="submit" value="Agregar" class="bg-blue-500 text-white px-2 py-1">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div >
                @if($barrios)
                    {{ $barrios->appends(['search' => request('search')])->links() }}
                @endif
            </div>
            -->

        </div>
        
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</body>

@include('footer.index')

</html>




    

   

