@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <chat-element user-id="{{ Auth::User()->id }}"></chat-element>
        </div>
    </div>
</div>
@endsection
