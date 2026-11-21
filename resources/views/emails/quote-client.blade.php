@extends('emails.layout')
@section('body')
<h1 style="font-size:20px;margin:0 0 16px">Merci {{ explode(' ', trim($quote->nom))[0] }}, votre demande est bien reçue.</h1>
<p>Nous avons enregistré votre demande de devis <strong>{{ $quote->reference }}</strong> ({{ $label }}, {{ $quote->localisation }}).</p>
<p>Un chargé d'affaires VOKAMIN étudie votre projet et vous recontacte rapidement. Pour toute précision, répondez simplement à cet email ou écrivez-nous sur WhatsApp au {{ setting('whatsapp') }}.</p>
<p style="margin-top:24px">L'équipe VOKAMIN<br><em>{{ setting('tagline') }}</em></p>
@endsection
