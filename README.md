## Tecnologias

- Laravel 10
- Docker
- MySQL
- Composer

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- Docker
- Docker Compose

## Como rodar o projeto

### Build dos containers:

```
docker compose up -d --build
```
Acesse o container do Laravel:

```
docker exec -it laravel-app bash
```
Instale as dependências:
```
composer install
```
Configure o arquivo .env:

```
cp .env.example .env
```
```
php artisan key:generate
```
Ajuste as variáveis de conexão com MySQL:

A .env.example já está pronta para ser copiada


```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306 -> no docker-compose.yml está configurado com a 3307
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```
Altere também:
```
SESSION_DRIVER=file
```
Rode as migrations:
```
php artisan migrate

```

Isso criará as tabelas e para logar basta criar um usuario.

Acesse o projeto em: http://localhost:8000
