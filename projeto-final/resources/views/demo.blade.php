<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Widget</title>
    <script src="{{ asset('vendor/feedback-widget/feedback-widget.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-slate-900">
    <main class="mx-auto max-w-5xl px-6 py-20">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-700">Aplicação consumidora</p>
        <h1 class="mt-4 max-w-3xl text-5xl font-bold leading-tight">Um widget Alpine.js entregue por um pacote Laravel interno.</h1>
        <p class="mt-6 max-w-2xl text-xl leading-8 text-slate-600">
            Abra o widget, avalie a experiência e envie o formulário. A aplicação valida e persiste o feedback sem transferir regras de negócio para o navegador.
        </p>

        <section class="mt-12 grid gap-6 md:grid-cols-3">
            <article class="rounded-3xl bg-white p-6 shadow-sm"><strong>Blade</strong><p class="mt-2 text-slate-600">Renderiza a interface inicial.</p></article>
            <article class="rounded-3xl bg-white p-6 shadow-sm"><strong>Alpine.js</strong><p class="mt-2 text-slate-600">Controla o estado local.</p></article>
            <article class="rounded-3xl bg-white p-6 shadow-sm"><strong>Laravel</strong><p class="mt-2 text-slate-600">Valida e persiste os dados.</p></article>
        </section>
    </main>

    <x-feedback-widget::widget :endpoint="route('feedback.store')" />
</body>
</html>
