@extends('legal.layout', ['heroTitle' => 'Politique de <em>confidentialité</em>', 'heroLead' => 'Comment VOKAMIN collecte, utilise et protège vos données personnelles.', 'toc' => ['responsable' => 'Responsable du traitement', 'donnees' => 'Données collectées', 'finalites' => 'Finalités', 'duree' => 'Durée de conservation', 'droits' => 'Vos droits', 'securite' => 'Sécurité']])
@section('title', 'Politique de confidentialité')
@section('legal')
<h2 id="responsable">Responsable du traitement</h2>
<p>{{ setting('company_name') }}, {{ setting('address') }}, Abidjan. Contact : {{ setting('email') }}.</p>
<h2 id="donnees">Données collectées</h2>
<p>Nous collectons uniquement les données transmises via nos formulaires :</p>
<ul><li><strong>Demande de devis</strong> : nom, entreprise, téléphone, WhatsApp, email, informations sur le projet et pièces jointes ;</li><li><strong>Contact</strong> : nom, téléphone, email, message ;</li><li><strong>Candidature</strong> : nom, coordonnées, CV ;</li><li><strong>Lettre d'information et plaquette</strong> : nom et email.</li></ul>
<p>La mesure d'audience interne enregistre les pages consultées et la provenance des visites, sans cookie ni donnée permettant de vous identifier.</p>
<h2 id="finalites">Finalités</h2>
<p>Ces données servent exclusivement à répondre à vos demandes, établir des devis, gérer les candidatures et, avec votre accord, vous envoyer nos actualités. Elles ne sont jamais vendues à des tiers.</p>
<h2 id="duree">Durée de conservation</h2>
<div class="table-wrap"><table class="table"><thead><tr><th>Données</th><th>Durée</th></tr></thead><tbody><tr><td>Demandes de devis et de contact</td><td>3 ans après le dernier échange</td></tr><tr><td>Candidatures</td><td>2 ans</td></tr><tr><td>Lettre d'information</td><td>Jusqu'à désinscription</td></tr></tbody></table></div>
<h2 id="droits">Vos droits</h2>
<p>Conformément à la loi n° 2013-450 du 19 juin 2013 relative à la protection des données à caractère personnel, vous disposez d'un droit d'accès, de rectification, d'opposition et de suppression. Écrivez à {{ setting('email') }}. Vous pouvez également saisir l'ARTCI.</p>
<h2 id="securite">Sécurité</h2>
<p>Le site est protégé par HTTPS. Les formulaires sont protégés contre le spam, les fichiers joints sont contrôlés et stockés hors de l'espace public. L'administration est protégée par mot de passe avec limitation des tentatives.</p>
@endsection
