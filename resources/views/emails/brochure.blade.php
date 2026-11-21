@extends('emails.layout')
@section('body')
<h1 style="font-size:20px;margin:0 0 16px">Bonjour {{ $nom }},</h1>
@if ($url)
<p>Merci pour votre intérêt. Voici la plaquette institutionnelle de VOKAMIN Côte d'Ivoire :</p>
<p><a href="{{ $url }}" style="background:#0a7322;color:#fff;padding:12px 18px;text-decoration:none;display:inline-block">Télécharger la plaquette (PDF)</a></p>
@else
<p>Merci pour votre intérêt. Notre équipe vous adresse la plaquette institutionnelle de VOKAMIN dans les meilleurs délais.</p>
@endif
<p>L'équipe VOKAMIN</p>
@endsection
