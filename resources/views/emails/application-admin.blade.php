@extends('emails.layout')
@section('body')
<h1 style="font-size:20px;margin:0 0 16px">Nouvelle candidature — {{ $app->reference }}</h1>
<p><strong>{{ $app->nom }}</strong> · {{ $app->email }} · {{ $app->tel }}<br>Poste : {{ $app->poste }}<br>CV joint : {{ $app->cv_path ? 'oui (à télécharger dans l\'administration)' : 'non' }}</p>
@if ($app->message)<p>{!! nl2br(e($app->message)) !!}</p>@endif
@endsection
