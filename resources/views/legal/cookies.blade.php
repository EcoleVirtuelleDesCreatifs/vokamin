@extends('legal.layout', ['heroTitle' => 'Politique relative aux <em>cookies</em>', 'heroLead' => 'Les cookies utilisés sur le site et comment gérer vos préférences.', 'toc' => ['definition' => "Qu'est-ce qu'un cookie ?", 'utilises' => 'Cookies utilisés', 'preferences' => 'Gérer mes préférences']])
@section('title', 'Politique relative aux cookies')
@section('legal')
<h2 id="definition">Qu'est-ce qu'un cookie ?</h2>
<p>Un cookie est un petit fichier déposé sur votre appareil lors de la consultation d'un site.</p>
<h2 id="utilises">Cookies utilisés</h2>
<div class="table-wrap"><table class="table"><thead><tr><th>Cookie</th><th>Finalité</th><th>Durée</th></tr></thead><tbody>
<tr><td>Session et sécurité (XSRF-TOKEN, session)</td><td>Fonctionnement des formulaires et protection contre les envois frauduleux</td><td>Session</td></tr>
<tr><td>vk_cookies (stockage local)</td><td>Mémoriser votre choix</td><td>13 mois</td></tr>
<tr><td>Google Analytics (si activé)</td><td>Statistiques de fréquentation, uniquement après votre accord</td><td>13 mois</td></tr>
</tbody></table></div>
<h2 id="preferences">Gérer mes préférences</h2>
<div data-vue="CookiePreferences"></div>
@endsection
