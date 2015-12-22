@extends('app')
@section('content')
    
    <script>
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>
    {!! dd( $invoice ) !!}

@endsection