# Roteiro completo — do projeto inicial ao final

Este roteiro transforma este projeto no mesmo resultado de `../projeto-final`. A stack e os artefatos repetitivos já existem para concentrar a aula em Alpine.js, pacotes, Service Providers, auto-discovery e publicação.

## 1. Validar o ponto de partida

```bash
composer install
npm install
php artisan migrate:fresh
npm run build
php artisan test --compact
php artisan serve
```

Abra `http://127.0.0.1:8000/demo/feedback`. O botão deve comprovar que Alpine já está ativo. Verifique também o pacote local:

```bash
composer show acme/laravel-feedback-widget
php artisan package:discover
```

## 2. Configuração pública do pacote

Em `packages/acme/laravel-feedback-widget/config/feedback-widget.php`:

```php
<?php

return [
    'title' => env('FEEDBACK_WIDGET_TITLE', 'Como foi sua experiência?'),
    'button_label' => env('FEEDBACK_WIDGET_BUTTON_LABEL', 'Enviar feedback'),
    'success_message' => env('FEEDBACK_WIDGET_SUCCESS_MESSAGE', 'Obrigado pelo feedback!'),
    'accent_color' => env('FEEDBACK_WIDGET_ACCENT_COLOR', '#047857'),
];
```

Esta é a API de customização da aplicação consumidora.

## 3. Service Provider

Complete `packages/acme/laravel-feedback-widget/src/FeedbackWidgetServiceProvider.php`:

```php
public function register(): void
{
    $this->mergeConfigFrom(__DIR__.'/../config/feedback-widget.php', 'feedback-widget');
}

public function boot(): void
{
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'feedback-widget');

    $this->publishes([
        __DIR__.'/../config/feedback-widget.php' => config_path('feedback-widget.php'),
    ], 'feedback-widget-config');

    $this->publishes([
        __DIR__.'/../public' => public_path('vendor/feedback-widget'),
    ], 'feedback-widget-assets');
}
```

`register()` combina configuração; `boot()` integra recursos após o container estar pronto. O provider já está no `extra.laravel.providers` do Composer.

```bash
composer dump-autoload
php artisan package:discover
php artisan vendor:publish --tag=feedback-widget-config --force
php artisan vendor:publish --tag=feedback-widget-assets --force
```

## 4. Persistência

Dentro do callback da migration `2026_10_23_000000_create_feedback_table.php`, depois de `$table->id()`:

```php
$table->unsignedTinyInteger('rating');
$table->text('message');
$table->string('email')->nullable();
$table->string('source')->default('feedback-widget');
```

Complete `app/Models/Feedback.php`:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;

use HasFactory;

protected $table = 'feedback';

protected $fillable = ['rating', 'message', 'email', 'source'];

protected function casts(): array
{
    return ['rating' => 'integer'];
}
```

```bash
php artisan migrate:fresh
```

## 5. Validação e controller

Retorne estas regras em `StoreFeedbackRequest::rules()`:

```php
return [
    'rating' => ['required', 'integer', 'between:1,5'],
    'message' => ['required', 'string', 'min:10', 'max:1000'],
    'email' => ['nullable', 'email', 'max:255'],
];
```

Substitua o controller pela implementação:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

final class StoreFeedbackController extends Controller
{
    public function __invoke(StoreFeedbackRequest $request): JsonResponse
    {
        $feedback = Feedback::create($request->validated());

        return response()->json([
            'id' => $feedback->id,
            'message' => config('feedback-widget.success_message'),
        ], 201);
    }
}
```

Laravel mantém validação e regras de negócio; Alpine cuida apenas do estado visual.

## 6. Rotas

Substitua `routes/web.php` por:

```php
<?php

use App\Http\Controllers\StoreFeedbackController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/demo/feedback');
Route::view('/demo/feedback', 'demo')->name('feedback.demo');
Route::post('/feedback', StoreFeedbackController::class)->name('feedback.store');

Route::get('/api/health', fn () => response()->json([
    'status' => 'ok',
    'package' => 'acme/laravel-feedback-widget',
]));
```

## 7. Componente Alpine reutilizável

Substitua `packages/acme/laravel-feedback-widget/public/feedback-widget.js`:

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('feedbackWidget', ({ endpoint, csrfToken, successMessage }) => ({
        open: false,
        submitting: false,
        success: false,
        rating: null,
        message: '',
        email: '',
        errors: {},
        successMessage,

        close() {
            this.open = false;
            this.reset();
        },

        reset() {
            this.submitting = false;
            this.success = false;
            this.rating = null;
            this.message = '';
            this.email = '';
            this.errors = {};
        },

        async submit() {
            this.submitting = true;
            this.errors = {};
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    rating: this.rating,
                    message: this.message,
                    email: this.email || null,
                }),
            });
            const payload = await response.json();
            this.submitting = false;

            if (response.status === 422) {
                this.errors = payload.errors;
                return;
            }
            if (! response.ok) {
                this.errors = { form: ['Não foi possível enviar o feedback.'] };
                return;
            }

            this.successMessage = payload.message;
            this.success = true;
            this.rating = null;
            this.message = '';
            this.email = '';
        },
    }));
});
```

Publique o novo arquivo:

```bash
php artisan vendor:publish --tag=feedback-widget-assets --force
```

## 8. View do pacote

Em `resources/views/components/widget.blade.php` do pacote, prepare os dados:

```blade
@props(['endpoint'])

@php
    $widgetOptions = [
        'endpoint' => $endpoint,
        'csrfToken' => csrf_token(),
        'successMessage' => config('feedback-widget.success_message'),
    ];
@endphp
```

Crie a raiz, modal e formulário:

```blade
<div x-data="feedbackWidget({{ Illuminate\Support\Js::from($widgetOptions) }})" @keydown.escape.window="close()" class="fixed bottom-6 right-6 z-50">
    <button type="button" @click="open = true" class="rounded-full bg-emerald-700 px-6 py-3 font-semibold text-white">
        {{ config('feedback-widget.button_label') }}
    </button>

    <div x-cloak x-show="open" class="fixed inset-0 grid place-items-center bg-slate-950/50 p-4">
        <section x-show="open" x-transition @click.outside="close()" class="w-full max-w-lg rounded-3xl bg-white p-8">
            <h2 class="text-2xl font-bold">{{ config('feedback-widget.title') }}</h2>
            <form x-show="! success" @submit.prevent="submit" class="mt-6 space-y-5">
                <template x-for="value in [1, 2, 3, 4, 5]" :key="value">
                    <button type="button" @click="rating = value" :class="rating === value ? 'bg-emerald-700 text-white' : 'bg-slate-100'" x-text="value"></button>
                </template>
                <p x-show="errors.rating" x-text="errors.rating?.[0]" class="text-red-600"></p>
                <textarea x-model="message" rows="4" class="w-full rounded-xl border p-3"></textarea>
                <p x-show="errors.message" x-text="errors.message?.[0]" class="text-red-600"></p>
                <input x-model="email" type="email" class="w-full rounded-xl border p-3">
                <p x-show="errors.email" x-text="errors.email?.[0]" class="text-red-600"></p>
                <p x-show="errors.form" x-text="errors.form?.[0]" class="text-red-600"></p>
                <button type="submit" :disabled="submitting" class="w-full rounded-xl bg-emerald-700 p-3 text-white">
                    <span x-show="! submitting">Enviar</span><span x-show="submitting">Enviando...</span>
                </button>
            </form>
            <div x-show="success" class="mt-8 rounded-2xl bg-emerald-50 p-6">
                <p x-text="successMessage"></p>
                <button type="button" @click="close()">Fechar</button>
            </div>
        </section>
    </div>
</div>
```

O arquivo completo, com acabamento visual e acessibilidade, está em `../projeto-final/packages/acme/laravel-feedback-widget/resources/views/components/widget.blade.php`.

## 9. Consumir o pacote

Na view `resources/views/demo.blade.php`, carregue o asset antes do Vite e substitua o exemplo inicial pelo componente:

```blade
<script src="{{ asset('vendor/feedback-widget/feedback-widget.js') }}"></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

```blade
<x-feedback-widget::widget :endpoint="route('feedback.store')" />
```

`resources/js/app.js` já importa Alpine, atribui `window.Alpine` e chama `Alpine.start()`. Em `resources/css/app.css`, confirme:

```css
@source '../../packages/acme/laravel-feedback-widget/resources/views/**/*.blade.php';

[x-cloak] {
    display: none !important;
}
```

## 10. Gerar e testar

```bash
php artisan vendor:publish --tag=feedback-widget-config --force
php artisan vendor:publish --tag=feedback-widget-assets --force
php artisan optimize:clear
php artisan migrate:fresh
npm run build
php artisan serve
```

No navegador, abra, feche por Escape e clique externo, provoque erros de validação, envie dados válidos e confirme o registro:

```bash
php artisan tinker --execute="dump(App\\Models\\Feedback::latest()->first()?->toArray());"
```

Altere `config/feedback-widget.php`, execute `php artisan config:clear` e confirme que a aplicação customiza o pacote sem modificá-lo.

## 11. Testes de aceitação

Use `../projeto-final/tests/Feature/FeedbackWidgetTest.php` como resultado esperado. A suíte cobre health check, namespace da view, persistência, erros `422`, override de configuração e asset publicado.

```bash
cp ../projeto-final/tests/Feature/FeedbackWidgetTest.php tests/Feature/FeedbackWidgetTest.php
rm tests/Feature/StarterProjectTest.php
php artisan test --compact
vendor/bin/pint --test
npm run build
```

## Critérios de conclusão

- provider descoberto automaticamente;
- view renderizada por `<x-feedback-widget::widget>`;
- configuração e asset publicados por tags independentes;
- estado local funcionando com Alpine;
- regras e persistência mantidas no Laravel;
- aplicação sobrescrevendo configuração sem editar o pacote;
- migration, testes, Pint e build concluídos sem erro.
