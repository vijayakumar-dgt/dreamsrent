@extends('installer::app')
@section('content')
    <div class="text-center card">
        <div class="card-header d-flex justify-content-between">
            <p>Setup Complete</p>
            <a class="btn btn-outline-primary" href="{{ route('setup.smtp') }}">&laquo; Back</a>
        </div>
        <div class="card-body">
            <h3 class="py-5 text-success">Congratulations! Installation is complete.</h3>
            <div class="d-flex justify-content-center gap-2">
                <form action="{{ route('website.completed', ['type' => 'admin']) }}" method="GET" class="p-4">
                    @csrf
                    <button type="submit" class="btn btn-primary ">Visit Admin</button>
                </form>
                <form action="{{ route('website.completed', ['type' => 'home']) }}" method="GET" class="p-1">
                    @csrf
                    <button type="submit" class="btn btn-success">Visit Website</button>
                </form>
            </div>
        </div>
       
    </div>
@endsection
