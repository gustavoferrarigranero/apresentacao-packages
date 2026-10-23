@props(['endpoint'])

@php
    $widgetOptions = [
        'endpoint' => $endpoint,
        'csrfToken' => csrf_token(),
        'successMessage' => config('feedback-widget.success_message'),
    ];
@endphp

<div
    x-data="feedbackWidget({{ Illuminate\Support\Js::from($widgetOptions) }})"
    @keydown.escape.window="close()"
    class="fixed bottom-6 right-6 z-50"
>
    <button
        type="button"
        @click="open = true"
        class="rounded-full px-6 py-3 font-semibold text-white shadow-lg transition hover:-translate-y-0.5"
        style="background-color: {{ config('feedback-widget.accent_color') }}"
    >
        {{ config('feedback-widget.button_label') }}
    </button>

    <div x-cloak x-show="open" class="fixed inset-0 grid place-items-center bg-slate-950/50 p-4">
        <section
            x-show="open"
            x-transition
            @click.outside="close()"
            class="w-full max-w-lg rounded-3xl bg-white p-8 shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="feedback-widget-title"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-emerald-700">Feedback</p>
                    <h2 id="feedback-widget-title" class="mt-2 text-2xl font-bold text-slate-900">
                        {{ config('feedback-widget.title') }}
                    </h2>
                </div>
                <button type="button" @click="close()" class="text-2xl text-slate-500" aria-label="Fechar">×</button>
            </div>

            <form x-show="! success" @submit.prevent="submit" class="mt-6 space-y-5">
                <fieldset>
                    <legend class="text-sm font-semibold text-slate-700">Sua avaliação</legend>
                    <div class="mt-2 flex gap-2">
                        <template x-for="value in [1, 2, 3, 4, 5]" :key="value">
                            <button
                                type="button"
                                @click="rating = value"
                                :class="rating === value ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700'"
                                class="h-11 w-11 rounded-full font-semibold transition"
                                x-text="value"
                            ></button>
                        </template>
                    </div>
                    <p x-show="errors.rating" x-text="errors.rating?.[0]" class="mt-2 text-sm text-red-600"></p>
                </fieldset>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Mensagem</span>
                    <textarea x-model="message" rows="4" class="mt-2 w-full rounded-xl border border-slate-300 p-3" placeholder="Conte o que funcionou e o que podemos melhorar"></textarea>
                    <span x-show="errors.message" x-text="errors.message?.[0]" class="mt-2 block text-sm text-red-600"></span>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">E-mail opcional</span>
                    <input x-model="email" type="email" class="mt-2 w-full rounded-xl border border-slate-300 p-3" placeholder="voce@exemplo.com">
                    <span x-show="errors.email" x-text="errors.email?.[0]" class="mt-2 block text-sm text-red-600"></span>
                </label>

                <p x-show="errors.form" x-text="errors.form?.[0]" class="text-sm text-red-600"></p>

                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full rounded-xl px-5 py-3 font-semibold text-white disabled:opacity-60"
                    style="background-color: {{ config('feedback-widget.accent_color') }}"
                >
                    <span x-show="! submitting">Enviar</span>
                    <span x-show="submitting">Enviando...</span>
                </button>
            </form>

            <div x-show="success" class="mt-8 rounded-2xl bg-emerald-50 p-6 text-center">
                <p class="text-lg font-semibold text-emerald-900" x-text="successMessage"></p>
                <button type="button" @click="close()" class="mt-4 text-sm font-semibold text-emerald-700">Fechar</button>
            </div>
        </section>
    </div>
</div>
