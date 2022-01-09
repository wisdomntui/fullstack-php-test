@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <p>
            Hello {{$data['hmo_name']}}, your order with batch name {{$data['batch_name']}} has been submitted by {{$data['provider_name']}}.
        </p>
    </div>
</div>
@endsection
