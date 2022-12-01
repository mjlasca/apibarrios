@include('header.index')

@section('title')
    <title>Pago de pólizas</title>
@endsection

@php

// SDK de Mercado Pago
require base_path('vendor/autoload.php');
// Agrega credenciales
MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));
// Crea un objeto de preferencia
$preference = new MercadoPago\Preference();

// Crea un ítem en la preferencia
$item = new MercadoPago\Item();
$item->title = "Póliza No. ".$data["prefijo"]."-".$data["idpropuesta"]." | "."Tomador : ".$data["tomador"];
$item->quantity = 1;
$item->unit_price = $data["total"];

$preference->back_urls = array(
    "success" => url('/polizas')."?estado=success&idpropuesta=".$data["idpropuesta"]."&prefijo=".$data["prefijo"],
    "failure" => url('/cotizadoronline')."?estado=failure",
    "pending" => url('/polizas')."?estado=pending&idpropuesta=".$data["idpropuesta"]."&prefijo=".$data["prefijo"]
);

$preference->items = array($item);
$preference->save();

@endphp

<body>
  <div class="cho-container">
  </div>
  

    
    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <script>
  
        // Agrega credenciales de SDK
        const mp = new MercadoPago("{{ config('services.mercadopago.key') }}", {
                locale: 'es-AR'
        });

        // Inicializa el checkout
        mp.checkout({
            preference: {
                id: "{{ $preference->id }}"
            },
            autoOpen: true,
            render: {
                    container: '.cho-container', // Indica el nombre de la clase donde se mostrará el botón de pago
                    label: 'Pagar', // Cambia el texto del botón de pago (opcional)
            }
        });

    </script>
</body>

</html>