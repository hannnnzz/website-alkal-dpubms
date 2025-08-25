@extends('layouts.app')

@section('content')
    <h1>Daftar Pesanan</h1>
    <ul>
        @foreach($orders as $order)
        <li>
            {{ $order->order_id ?? $order->provider_name }} –
            Status: {{ $order->status }}
        </li>
        @endforeach
    </ul>
@endsection
