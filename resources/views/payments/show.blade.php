@extends('app')
@section('content')
<script>

</script>

<div id="pi_top_space"></div>

	{{ $invoice->item_desc }} ( {{ $invoice->token }} )
	{{ $invoice->amount }}
	{{ $invoice->pi_amount }}
	{{ $invoice->pi_amount_received }}

	{{ $invoice->customer_email }}
	{{ $invoice->customer_name }}
	{{ $invoice->customer_custom }}

	{{ $invoice->status }}
	{{ $invoice->currency }}
	{{ $invoice->created_at }}
	{{ $invoice->completed_at }}

@endsection