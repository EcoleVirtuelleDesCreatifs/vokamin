@extends('emails.layout')
@section('body')
<h1 style="font-size:20px;margin:0 0 16px">Nouveau message — {{ $msg->reference }}</h1>
<p><strong>{{ $msg->nom }}</strong> · {{ $msg->email }} · {{ $msg->tel ?: '—' }}<br>Sujet : {{ $msg->sujet }}</p>
<p>{!! nl2br(e($msg->message)) !!}</p>
@endsection
