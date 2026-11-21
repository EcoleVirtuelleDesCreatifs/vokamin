@extends('emails.layout')
@section('body')
<h1 style="font-size:20px;margin:0 0 16px">Nouvelle demande de devis — {{ $quote->reference }}</h1>
<table cellpadding="6" style="font-size:14px;border-collapse:collapse;width:100%">
@foreach (['Prestation' => $label, 'Nature du bâtiment' => $quote->nature, 'Localisation' => $quote->localisation, 'Surface' => $quote->surface ? $quote->surface.' m²' : '—', 'Délai' => $quote->delai, 'Budget' => $quote->budget, 'Nom' => $quote->nom, 'Entreprise' => $quote->entreprise, 'Téléphone' => $quote->tel, 'WhatsApp' => $quote->whatsapp, 'Email' => $quote->email, 'Rendez-vous' => $quote->rdv, 'Pièces jointes' => $quote->files()->count()] as $k => $v)
<tr style="border-bottom:1px solid #e2e5ea"><td style="color:#5a6275;width:40%">{{ $k }}</td><td><strong>{{ $v ?: '—' }}</strong></td></tr>
@endforeach
</table>
<p style="margin-top:20px"><strong>Description :</strong><br>{!! nl2br(e($quote->description)) !!}</p>
<p><a href="{{ route('admin.dashboard') }}#devis" style="background:#0a7322;color:#fff;padding:12px 18px;text-decoration:none;display:inline-block">Ouvrir dans l'administration</a></p>
@endsection
