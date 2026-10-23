# Roteiro de teste — projeto final

Este roteiro confirma que o pacote interno e a aplicação consumidora cobrem o fluxo apresentado em aula.

## 1. Preparar o ambiente

```bash
composer install
npm install
php artisan migrate:fresh
npm run build
php artisan serve
```

Use Node.js 22.12 ou superior, ou 20.19 ou superior.

## 2. Confirmar o health check

```bash
curl -i http://127.0.0.1:8000/api/health
```

Resultado esperado: status `200` e JSON com `status: ok`.

## 3. Demonstrar Alpine.js no navegador

Acesse `http://127.0.0.1:8000/demo/feedback` e confirme:

1. o botão abre o modal sem recarregar a página;
2. Escape, clique fora e o botão de fechar encerram o modal;
3. a escolha da avaliação altera o estado visual;
4. mensagem e e-mail usam `x-model`;
5. o envio exibe estado de carregamento;
6. uma resposta válida mostra a confirmação e limpa o formulário;
7. uma resposta inválida mostra os erros devolvidos pelo Laravel.

## 4. Confirmar persistência

Depois de enviar o formulário, execute:

```bash
php artisan tinker --execute="dump(App\\Models\\Feedback::latest()->first()?->toArray());"
```

O registro deve conter `rating`, `message`, `email` e timestamps.

## 5. Confirmar auto-discovery

```bash
php artisan package:discover
php artisan about
```

O provider `Acme\FeedbackWidget\FeedbackWidgetServiceProvider` é declarado em `packages/acme/laravel-feedback-widget/composer.json`; a aplicação não o registra manualmente.

## 6. Confirmar recursos publicáveis

```bash
php artisan vendor:publish --tag=feedback-widget-config --force
php artisan vendor:publish --tag=feedback-widget-assets --force
test -f config/feedback-widget.php
test -f public/vendor/feedback-widget/feedback-widget.js
```

As duas tags mantêm configuração e JavaScript independentes.

## 7. Confirmar customização da aplicação

Altere `config/feedback-widget.php`, limpe o cache e recarregue a página:

```bash
php artisan config:clear
```

O título, rótulo, mensagem de sucesso e cor devem mudar sem editar os arquivos do pacote.

## 8. Executar a suíte automatizada

```bash
php artisan migrate:fresh
php artisan test --compact
vendor/bin/pint --test
npm run build
```

Critérios de conclusão:

- health check responde;
- componente do pacote é renderizado pela namespace;
- feedback válido é persistido;
- validação rejeita dados inválidos;
- configuração publicada sobrescreve o padrão do pacote;
- asset publicado registra o componente Alpine;
- testes, estilo PHP e build terminam sem erro.
