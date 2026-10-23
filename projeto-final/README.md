# Feedback Widget — projeto final

Aplicação Laravel consumidora do pacote interno `acme/laravel-feedback-widget`. O resultado demonstra Alpine.js no Blade, auto-discovery, configuração, views e assets publicáveis, validação no servidor e persistência de feedback.

## Requisitos

- PHP 8.3 ou superior
- Composer 2
- Node.js 22.12 ou superior, ou 20.19 ou superior
- SQLite

## Preparação

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
npm run build
```

O pacote já está configurado como repositório Composer do tipo `path`. Para refazer a instalação e a publicação:

```bash
composer require acme/laravel-feedback-widget:@dev
php artisan vendor:publish --tag=feedback-widget-config --force
php artisan vendor:publish --tag=feedback-widget-assets --force
```

## Execução

```bash
php artisan serve
```

- Demonstração: `http://127.0.0.1:8000/demo/feedback`
- Health check: `http://127.0.0.1:8000/api/health`

## Qualidade

```bash
php artisan test --compact
vendor/bin/pint --test
npm run build
```

Consulte o [TESTING.md](TESTING.md) para o roteiro de demonstração e validação.
