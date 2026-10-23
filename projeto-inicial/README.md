# Feedback Widget — projeto inicial

Ponto de partida da aula de Alpine.js e desenvolvimento de pacotes Laravel. Laravel, Alpine.js, Tailwind CSS, SQLite, o pacote Composer local e todos os artefatos-base já estão instalados.

O projeto executa antes da aula, mas o fluxo final será construído seguindo o [TESTING.md](TESTING.md).

```bash
composer install
npm install
php artisan migrate:fresh
npm run build
php artisan test --compact
php artisan serve
```

Requer PHP 8.3+, Composer 2 e Node.js 22.12+ ou 20.19+.

- Ponto de partida: `http://127.0.0.1:8000/demo/feedback`
- Health check: `http://127.0.0.1:8000/api/health`
