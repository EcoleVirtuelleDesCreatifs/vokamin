@extends('legal.layout', ['heroTitle' => 'Mentions <em>légales</em>', 'heroLead' => "Informations relatives à l'éditeur et à l'hébergement du site.", 'toc' => ['editeur' => 'Éditeur du site', 'directeur' => 'Directeur de la publication', 'hebergeur' => 'Hébergement', 'propriete' => 'Propriété intellectuelle', 'responsabilite' => 'Responsabilité']])
@section('title', 'Mentions légales')
@section('legal')
<h2 id="editeur">Éditeur du site</h2>
<p>Le site est édité par <strong>{{ setting('company_name') }}</strong> ({{ setting('forme') }}), au capital de {{ setting('capital') }}.</p>
<ul><li>Siège social : {{ setting('address') }}, {{ setting('bp') }}, Côte d'Ivoire</li><li>RCCM : {{ setting('rccm') }}</li><li>Compte contribuable : {{ setting('cc') }}</li><li>Téléphone : {{ setting('tel1') }}@if (setting('tel2')) / {{ setting('tel2') }}@endif</li><li>Email : {{ setting('email') }}</li></ul>
<h2 id="directeur">Directeur de la publication</h2>
<p>{{ setting('gerant') }}, Gérant de {{ setting('company_name') }}.</p>
<h2 id="hebergeur">Hébergement</h2>
<p>{{ setting('host_name') }}</p>
<h2 id="propriete">Propriété intellectuelle</h2>
<p>L'ensemble des contenus du site (textes, photographies, rendus 3D, logos, éléments graphiques) est la propriété de {{ setting('company_name') }} ou de ses partenaires. Toute reproduction sans autorisation écrite préalable est interdite.</p>
<p>Les noms et logos des clients cités dans la rubrique Références sont la propriété de leurs titulaires respectifs.</p>
<h2 id="responsabilite">Responsabilité</h2>
<p>VOKAMIN s'efforce de fournir des informations exactes et à jour. Les rendus 3D présentés ont une valeur illustrative.</p>
@endsection
