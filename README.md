<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Deploy E Permissoes Em Producao

Projeto em producao:

- Caminho: `/www/wwwroot/meteorikahimob.com.br`
- Dono do projeto: `lucas`
- Usuario/grupo do webserver: `www`
- Ownership padrao recomendado: `lucas:www`

### Objetivo

Manter o Laravel capaz de escrever com seguranca em:

- `storage/`
- `bootstrap/cache/`
- `storage/framework/views`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/logs`
- uploads temporarios
- filas e jobs de processamento de imagem

### Script Automatizado

O repositório inclui o script `deploy-permissions.sh`, pensado para Ubuntu + aaPanel + NGINX + PHP-FPM.

Ele faz:

- corrige ownership do projeto para `lucas:www`
- aplica permissoes seguras por padrao
- aplica escrita com `setgid` em `storage/` e `bootstrap/cache/`
- recria diretorios criticos do Laravel
- limpa caches sem remover `.gitignore`
- executa `optimize:clear`, `config:cache`, `route:cache` e `view:cache`
- tenta reiniciar `nginx`, `php-fpm` e workers do Supervisor

Uso recomendado no servidor:

```bash
cd /www/wwwroot/meteorikahimob.com.br
chmod +x deploy-permissions.sh
./deploy-permissions.sh
```

Se precisar rodar sem reiniciar servicos automaticamente:

```bash
SKIP_SERVICE_RESTART=1 ./deploy-permissions.sh
```

Se o grupo ja estiver sincronizado e voce quiser pular a etapa de `usermod`:

```bash
SKIP_GROUP_SYNC=1 ./deploy-permissions.sh
```

### Estrategia De Ownership E Permissoes

Padrao recomendado:

- projeto inteiro: `lucas:www`
- diretorios padrao: `755`
- arquivos padrao: `644`
- diretorios gravaveis do Laravel: `2775`
- arquivos em `storage/` e `bootstrap/cache/`: `664`

O bit `setgid` nos diretorios gravaveis garante que novos arquivos herdem automaticamente o grupo `www`, evitando conflito entre:

- `git pull`
- `php artisan`
- `queue:work`
- upload/processamento de imagens
- logs
- caches compilados

### Checklist Pos-Deploy

Sequencia recomendada no servidor:

```bash
cd /www/wwwroot/meteorikahimob.com.br
git pull origin sistema-imob
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
./deploy-permissions.sh
```

Se estiver usando worker manual da fila:

```bash
php artisan queue:work --queue=default --tries=5 --timeout=300
```

Se estiver usando Supervisor, garanta que o programa do worker rode como usuario `www` ou outro usuario compativel com o grupo `www`.

### NGINX E PHP Para Uploads Grandes

No NGINX do dominio:

```nginx
client_max_body_size 256M;
client_body_timeout 300;
proxy_connect_timeout 300;
proxy_send_timeout 300;
proxy_read_timeout 300;
fastcgi_read_timeout 300;
```

No PHP:

```ini
upload_max_filesize = 256M
post_max_size = 256M
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
```

### Observacoes Operacionais

- Nunca use `chmod 777`
- Evite executar `composer`, `npm` ou `artisan` como `root`
- Prefira sempre rodar deploy como `lucas`
- Se o script adicionar `lucas` ao grupo `www`, faca logout/login antes do proximo deploy interativo
- Se o PHP-FPM do aaPanel nao usar um dos nomes padrao detectados pelo script, reinicie manualmente o pool configurado no painel
