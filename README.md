# Site vitrine VOKAMIN Côte d'Ivoire

Laravel 11 (PHP 8.2+) · Vue 3 · Vite · Bootstrap 5 · MySQL

## Fonctionnalités (conformes au cahier des charges)

| Cahier des charges | Réalisation |
|---|---|
| Pages Accueil, Entreprise, Services, Réalisations, Références, Actualités, Devis, Contact | Pages Blade rendues côté serveur (référencement), composants Vue pour les parties interactives |
| Pages complémentaires | Recrutement, Mentions légales, Confidentialité, Cookies, Plan du site, 404, 500/503 |
| Fiches services et projets | 7 services et 8 projets créés au premier lancement, modifiables dans l'administration |
| Galerie photo | Visionneuse plein écran (clavier, glissé tactile), légendes, chargement progressif, comparateur avant/après, lien vidéo |
| Formulaire de devis | Assistant en 4 étapes, pièces jointes (5 fichiers, 10 Mo), validation, référence unique, email à VOKAMIN et confirmation au client, enregistrement en base |
| WhatsApp | Bouton flottant + message pré-rempli modifiable dans l'administration |
| Administration protégée | `/admin` : connexion (5 tentatives/minute), tableau de bord, demandes, réalisations (galerie, couverture, mise en avant, publication), services, actualités, références, offres d'emploi, coordonnées et textes, plaquette PDF, mot de passe |
| Statistiques | Visiteurs, pages vues, provenance, projets les plus vus, clics WhatsApp, appels, formulaires envoyés (mesure interne sans cookie) + Google Analytics optionnel après consentement |
| Sécurité | HTTPS, CSRF, limitation de fréquence des formulaires, champ anti-robot, contrôle des fichiers, pièces jointes stockées hors de l'espace public |
| Référencement | Titre et description par page, URL lisibles, balises Open Graph, données structurées, `sitemap.xml` dynamique, `robots.txt`, textes alternatifs |
| Performance | Images redimensionnées automatiquement à l'envoi, chargement différé, cache navigateur et compression (`.htaccess`), caches Laravel |

## Structure

```
app/Http/Controllers         Pages publiques, formulaires, sitemap
app/Http/Controllers/Admin   API de l'administration
app/Models                   Service, Project, ProjectImage, Post, ClientReference, JobOffer, QuoteRequest…
app/Support                  Paramètres éditables, mise en forme des articles, références
config/vokamin.php           Coordonnées par défaut et limites d'envoi de fichiers
database/seeders/data        Contenus de départ (issus de la plaquette)
resources/views              Gabarits Blade (site, emails, administration)
resources/js/components      Composants Vue du site
resources/js/admin           Application Vue de l'administration
resources/css                Styles (site.css = charte VOKAMIN)
public/images                Photos et logos livrés
```

## Installation locale

```bash
composer install
npm install
cp .env.example .env        # puis renseigner la base de données
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev                 # ou npm run build
php artisan serve
```

En local, mettez `APP_ENV=local`, `APP_DEBUG=true` et `SESSION_SECURE_COOKIE=false` dans `.env`.

Administration : `http://localhost:8000/admin` avec `ADMIN_EMAIL` / `ADMIN_PASSWORD` du fichier `.env`.

La mise en ligne est décrite dans **DEPLOIEMENT.md**.
