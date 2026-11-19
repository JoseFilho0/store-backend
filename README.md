# B7Store Backend

A B7Store é uma API REST de e-commerce feita com PHP e Laravel. Este repositório contém apenas o backend da loja. Para explorar o catálogo, montar um carrinho, fazer login e simular um pedido, envie requisições pelo Insomnia e veja as respostas JSON. A rota `/` exibe a página padrão do Laravel.

## Como rodar localmente

Você precisa de Git, PHP 8.4 ou superior com a extensão `pdo_sqlite`, Composer 2 e Insomnia. O projeto usa SQLite por padrão, em um arquivo local.

Clone o repositório, instale as dependências e prepare a configuração local.

```sh
git clone https://github.com/JoseFilho0/store-backend.git
cd store-backend
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```

Mantenha `DB_CONNECTION=sqlite` no `.env`. Os comandos abaixo criam o arquivo do banco, aplicam as migrações e carregam produtos, banners, categorias e uma conta de demonstração. Execute a carga inicial uma vez em um banco novo.

```sh
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Deixe o servidor rodando. A URL base das requisições é `http://127.0.0.1:8000/api`.

## Como testar no Insomnia

Crie uma requisição HTTP, escolha o método, cole a URL e adicione o cabeçalho `Accept: application/json` em **Headers**. Para enviar dados em uma requisição POST, selecione **Body → JSON** e cole o corpo indicado. Clique em **Send** para ver o status HTTP e o corpo da resposta.

### 1. Verifique se a API está rodando

Envie `GET http://127.0.0.1:8000/api/ping`. Você deve receber HTTP 200 com este corpo.

```json
{ "pong": true }
```

### 2. Consulte o catálogo e monte o carrinho

Envie `GET http://127.0.0.1:8000/api/products?limit=5&orderBy=price`. A resposta traz até cinco itens no campo `products`, ordenados pelo maior preço. Pegue o `id` de um produto e consulte seus detalhes em `GET /api/product/{id}`.

Para montar o carrinho, envie `POST http://127.0.0.1:8000/api/cart/mount` com o JSON abaixo. Use um ID retornado na listagem.

```json
{ "ids": [1] }
```

Consulte o frete simulado em `GET /api/cart/shipping?zipcode=12345-678`. A API retorna um valor fixo de 7 e um prazo de 3 dias.

### 3. Faça login e consulte os endereços

Envie `POST http://127.0.0.1:8000/api/user/login` com a conta de demonstração criada pelo seeder.

```json
{
  "email": "john.doe@example.com",
  "password": "12345678"
}
```

Copie o valor do campo `token` na resposta. Crie a requisição `GET http://127.0.0.1:8000/api/user/addresses` e, na aba **Auth**, selecione **Bearer Token** e cole o token. O Insomnia enviará o cabeçalho `Authorization: Bearer <token>`. A resposta traz os endereços da conta no campo `addresses`.

### 4. Simule um pedido

Envie `POST http://127.0.0.1:8000/api/cart/finish` com o mesmo Bearer Token. Use um `productId` do catálogo e um `addressId` da conta autenticada.

```json
{
  "cart": [{ "productId": 1, "quantity": 2 }],
  "addressId": 1
}
```

Em caso de sucesso, a API grava o pedido como pendente e retorna HTTP 200 com corpo vazio.

## Rotas da API

| Método | Rota | O que faz | Bearer Token |
| --- | --- | --- | --- |
| GET | `/api/ping` | Verifica se a API está respondendo | Não |
| GET | `/api/banners` | Lista banners | Não |
| GET | `/api/products` | Lista produtos. Aceita `limit`, `orderBy` (`views`, `selling` ou `price`) e `metadata` (JSON com IDs de metadados como chaves e IDs de valores como valores) | Não |
| GET | `/api/product/{id}` | Busca um produto pelo ID | Não |
| GET | `/api/product/{id}/related` | Lista produtos da mesma categoria. Aceita `limit` | Não |
| GET | `/api/categories/{slug}/metadata` | Consulta os metadados de uma categoria, como `camisetas` | Não |
| POST | `/api/cart/mount` | Busca os produtos de um carrinho pela lista `ids` | Não |
| GET | `/api/cart/shipping` | Simula o frete para o parâmetro `zipcode` | Não |
| POST | `/api/user/register` | Cria uma conta com `name`, `email` e `password` | Não |
| POST | `/api/user/login` | Retorna um token do Laravel Sanctum | Não |
| GET | `/api/user/addresses` | Lista os endereços da conta autenticada | Sim |
| POST | `/api/user/addresses` | Cadastra um endereço com `zipcode`, `street`, `number`, `city`, `state`, `country` e `complement` opcional | Sim |
| POST | `/api/cart/finish` | Cria um pedido com `cart` e `addressId` | Sim |

Para conferir as rotas pelo terminal, rode `php artisan route:list --path=api`.
