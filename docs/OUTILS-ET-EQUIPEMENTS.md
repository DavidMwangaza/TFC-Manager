# OUTILS LOGICIELS ET ÉQUIPEMENTS — UDBL TFC Manager

Ce document liste les principaux outils logiciels et l'infrastructure utilisée par l'application.

## Outils logiciels

- **Langage & framework**: PHP 8.2+ — **Laravel 12**
- **Gestionnaires de paquets**: Composer (PHP) et npm (Node)
- **Tooling frontend**: Node.js 18+, **Vite**, `laravel-vite-plugin`, **Tailwind CSS**, **PostCSS**, **Autoprefixer**, **Alpine.js**, **Axios**, `@tailwindcss/forms`
- **Libs backend clés**: `spatie/laravel-permission`, `smalot/pdfparser`, `laravel/tinker`
- **Tests & qualité**: PHPUnit, `fakerphp/faker`, `mockery`, `laravel/pint`
- **Développement local / process**: scripts `composer dev`, `npm run dev`, `npm run build`, `php artisan serve`, option Docker via `laravel/sail`
- **Client HTTP**: façade `Http` de Laravel (Guzzle sous-jacent)
- **Services externes**: GPTZero (détection IA) — configuration via `AI_DETECTION_API_KEY` / `AI_DETECTION_API_URL`

## Équipements et infrastructure requis

- **Base de données**: PostgreSQL 14+ recommandé (configuration par défaut dans `.env.example`). Les tests utilisent SQLite en mémoire.
- **Serveur d'application**: machine/VM/conteneur capable d'exécuter PHP 8.2+, Node.js 18+ et PostgreSQL (ex. PHP-FPM + Nginx, ou conteneur Docker)
- **Cache / queues**: Redis supporté; queue par défaut `database` (workers: `php artisan queue:listen`)
- **Stockage fichiers**: stockage local (`public/storage`) ou Amazon S3 (variables AWS_* si configurées)
- **Accès réseau**: accès sortant vers l'API GPTZero et vers le service mail choisi (SMTP/Postmark/SES/Resend)

## Preuves (fichiers du dépôt)

- Dépendances PHP: [composer.json](../composer.json)
- Dépendances JS / tooling: [package.json](../package.json)
- Configuration Vite: [vite.config.js](../vite.config.js)
- Configuration Tailwind/PostCSS: [tailwind.config.js](../tailwind.config.js), [postcss.config.js](../postcss.config.js)
- Service IA: [app/Services/AiDetectionService.php](../app/Services/AiDetectionService.php)
- Variables d'environnement exemple: [.env.example](../.env.example)
- Configuration DB: [config/database.php](../config/database.php)

---

Si vous le souhaitez, je peux :

1. intégrer ce fichier dans le sommaire du rapport,
2. générer une version PDF, ou
3. committer les modifications.
