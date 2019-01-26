@extends('spark::layouts.app')

@section('content')
{{-- <home :user="user"  inline-template> --}}
    <div class="container">
        <!-- Application Dashboard -->
        <router-view></router-view>
    </div>
{{-- </home> --}}
@endsection
