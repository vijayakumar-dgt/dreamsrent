@extends('generalsetting::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('generalsetting.name') !!}</p>
@endsection
