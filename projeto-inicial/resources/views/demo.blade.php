<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponto de partida — Feedback Widget</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="mx-auto flex min-h-screen max-w-5xl items-center px-6 py-16">
        <section x-data="{ open: false }" class="w-full rounded-3xl bg-white/10 p-10 shadow-2xl ring-1 ring-white/15">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-300">Ponto de partida</p>
            <h1 class="mt-4 text-4xl font-bold">Alpine.js e pacotes Laravel</h1>
            <p class="mt-5 max-w-2xl text-lg text-slate-300">
                A stack está instalada, o pacote local está conectado e os artefatos Laravel já existem. O widget completo será construído durante a aula.
            </p>
            <button type="button" @click="open = ! open" class="mt-8 rounded-xl bg-emerald-500 px-5 py-3 font-semibold text-slate-950">
                Validar Alpine.js
            </button>
            <p x-cloak x-show="open" x-transition class="mt-4 rounded-xl bg-emerald-400/15 p-4 text-emerald-200">
                Alpine.js está ativo e pronto para o exercício.
            </p>
        </section>
    </main>
</body>
</html>
