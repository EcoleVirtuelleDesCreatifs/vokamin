# Mise en ligne du site VOKAMIN

## 1. Prérequis chez l'hébergeur

- PHP **8.2 ou plus** avec les extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `gd`, `zip`
- MySQL 5.7+ / MariaDB 10.3+
- Certificat SSL (HTTPS)
- Une adresse email professionnelle (SMTP) pour l'envoi des notifications
- Sur votre poste : PHP 8.2+, Composer 2 et Node.js 18+ (pour préparer les fichiers)

## 2. Préparer le paquet (sur votre poste)

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build          # crée public/build (CSS et JS compilés)
```

Le dossier contient alors `vendor/` et `public/build/` : il est prêt à être envoyé.

## 3a. Hébergement avec accès SSH (VPS ou mutualisé avec SSH) — recommandé

1. Envoyez le dossier (Git, SFTP ou archive) dans `/var/www/vokamin` par exemple.
2. Faites pointer la **racine du domaine** sur le dossier `public/`.
3. Lancez :

```bash
cp .env.example .env
nano .env                         # base de données, SMTP, APP_URL, compte admin
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
chmod -R 775 storage bootstrap/cache
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Le script `deploy.sh` rejoue ces étapes lors des mises à jour.

## 3b. Hébergement mutualisé sans SSH (cPanel, Plesk…)

1. Créez la base MySQL et son utilisateur dans le panneau.
2. Envoyez **tout le contenu du dossier** (avec `vendor/` et `public/build/`) par FTP, par exemple dans `~/vokamin/`.
3. Dans le panneau, définissez la racine du domaine sur `~/vokamin/public`.
   *Si ce n'est pas possible* : placez le projet à la racine du site ; le fichier `.htaccess` fourni à la racine redirige automatiquement vers `public/`.
4. Copiez `.env.example` en `.env` et complétez-le (gestionnaire de fichiers du panneau). Pour `APP_KEY`, générez une clé sur votre poste avec `php artisan key:generate --show`.
5. Importez la base : sur votre poste, lancez `php artisan migrate --seed` sur une base locale puis exportez-la (phpMyAdmin) et importez-la chez l'hébergeur. Ou, si le panneau propose un « terminal » ou des « tâches cron », exécutez `php artisan migrate --seed --force` une fois.
6. Images envoyées depuis l'admin : elles sont livrées par l'adresse `/media/...` directement depuis `storage/app/public`. Le lien `public/storage` n'est plus nécessaire.

## 4. Configuration du fichier `.env`

| Clé | Valeur |
|---|---|
| `APP_URL` | `https://www.vokamin.ci` |
| `APP_ENV` / `APP_DEBUG` | `production` / `false` |
| `DB_*` | identifiants MySQL de l'hébergeur |
| `MAIL_*` | SMTP de la messagerie professionnelle (ex. `contact@vokamin.ci`) |
| `VOKAMIN_NOTIFY_EMAIL` | adresse qui reçoit devis, messages et candidatures |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | compte administrateur créé au premier lancement |
| `VOKAMIN_GA_ID` | identifiant Google Analytics (facultatif, chargé après consentement) |

Après toute modification du `.env` : `php artisan config:cache`.

## 5. Après la mise en ligne

1. Connectez-vous sur `https://www.vokamin.ci/admin` puis **changez le mot de passe** (Coordonnées & textes).
2. Vérifiez les coordonnées, horaires, réseaux sociaux et l'hébergeur (mentions légales).
3. Envoyez la plaquette PDF.
4. Testez une demande de devis : vous devez recevoir l'email de notification et le client l'email de confirmation.
5. Activez la redirection HTTPS : décommentez les 2 lignes prévues dans `public/.htaccess`.
6. Déclarez `https://www.vokamin.ci/sitemap.xml` dans Google Search Console.

## 6. Sauvegardes

Sauvegardez régulièrement la base MySQL et le dossier `storage/app` (photos envoyées, pièces jointes des devis, CV). La plupart des hébergeurs proposent une sauvegarde automatique quotidienne à activer dans le panneau.

## 7. Mises à jour du code

```bash
git pull                     # ou envoi des fichiers modifiés
./deploy.sh
```

Les fichiers CSS/JS compilés (`public/build`) sont produits automatiquement par GitHub
(workflow « Compilation des fichiers du site ») et enregistrés dans le dépôt : pas besoin
de Node.js sur le serveur.

Sans Composer ni Node sur le serveur (mutualisé), après un `git pull` :

```bash
php artisan migrate --force   # nouvelles tables éventuelles
php artisan optimize:clear   # vide les caches de routes, config et vues
```
