@extends('app')
@section('content')
	<script>

	</script>

	<div id="pi_top_space"></div>

	{{ $invoice->item_desc }}
	{{ $invoice->pi_amount }}
	{{ $invoice->status }}
	{{ $invoice->completed_at }}

@endsection