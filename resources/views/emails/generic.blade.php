@extends('emails.layout')
@section('body')
@foreach ($lines as $line)<p style="margin:0 0 6px">{{ $line }}</p>@endforeach
@endsection
