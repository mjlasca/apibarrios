@include('header.index')

@section('title')
    <title>Agregar barrios</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection

    <div class="container">
        
        <div>
            
            <h3 class="mt-5">Grupos de barrios para agregar a tu póliza {{$propuesta->prefijo}}-{{$propuesta->idpropuesta}}</h3>
            <p>Selecciona el grupo que deseas agregar</p>
            <hr class="mb-5">
            
            <form action="" method="post">
                <div class="form-group">
                    <select class="form-control" name="grupo" id="" required>
                            <option value="" checked>-- Selecciona un grupo --</option>
                        @forelse($gruposbarrios as $grupo)
                            
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                        @empty
                            <p>No hay grupos</p>
                        @endforelse
                    </select>
                </div>
                <input type="submit" value="Agregar Grupo" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
            </form>
        </div>

        <div class="mt-5">
            <h4>Buscar barrio específico</h4>
            <form action="" method="get">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Escribe el CUIT o el nombre" value="{{ request('search') }}">
                </div>
                <input type="submit" value="Agregar Barrio" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
            </form>
        </div>

        <div class="mt-5 p-2">

            <table class="table">
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
            

        </div>
        
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</body>

@include('footer.index')

</html>




    

   

