# MicroUrl - API de Encurtamento de URLs

API de encurtamento de URLs com alta performance usando Redis para armazenamento.

## Requisitos

- PHP 8.4 ou superior
- Redis 7.2 ou superior
- Composer

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/microurl.git
cd microurl
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o Redis:
```bash
cp .env.example .env
# Edite o arquivo .env com suas configurações
```

4. Inicie o servidor:
```bash
composer start
```

## Endpoints da API

### 1. Criar URL Curta

Cria uma nova URL curta.

```http
POST /api/micro-url
```

#### Request Body
```json
{
    "url": "https://exemplo.com",
}
```

#### Resposta de Sucesso (201 Created)
```json
{
    "success": true,
    "message": "URL encurtada com sucesso",
    "data": {
        "original_url": "https://exemplo.com",
        "short_code": "ABC123XY",
        "short_url": "http://localhost:8000/ABC123XY",
        "visits": 0,
        "created_at": "2024-03-20 10:30:00",
        "expires_at": "2024-12-31 23:59:59"
    }
}
```

#### Resposta de Erro (400 Bad Request)
```json
{
    "success": false,
    "message": "Erro de validação",
    "errors": {
        "url": "URL inválida"
    }
}
```

### 2. Obter Informações da URL

Retorna informações sobre uma URL curta.

```http
GET /api/{short_code}
```

#### Resposta de Sucesso (200 OK)
```json
{
    "success": true,
    "message": "URL encontrada",
    "data": {
        "original_url": "https://exemplo.com",
        "short_code": "ABC123XY",
        "short_url": "http://localhost:8000/ABC123XY",
        "visits": 42,
        "created_at": "2024-03-20 10:30:00",
        "expires_at": "2024-12-31 23:59:59"
    }
}
```

#### Resposta de Erro (404 Not Found)
```json
{
    "success": false,
    "message": "URL não encontrada"
}
```

### 3. Redirecionamento

Redireciona para a URL original.

```http
GET /{short_code}
```

#### Resposta
- Redirecionamento 302 para a URL original
- Se a URL expirou, retorna 410 Gone
- Se a URL não existe, retorna 404 Not Found

## Códigos de Status

- `200 OK`: Requisição bem-sucedida
- `201 Created`: Recurso criado com sucesso
- `400 Bad Request`: Erro de validação
- `404 Not Found`: Recurso não encontrado
- `410 Gone`: URL expirada
- `429 Too Many Requests`: Limite de requisições excedido
- `500 Internal Server Error`: Erro interno do servidor

## Estrutura de Dados

### URL
```json
{
    "original_url": "string",
    "short_code": "string",
    "visits": "integer",
    "created_at": "timestamp",
    "expires_at": "timestamp"
}
```

## Configuração

### Variáveis de Ambiente
```env
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_DATABASE=0
REDIS_PREFIX=url:
REDIS_DEFAULT_TTL=2592000
```

## Exemplos de Uso

### cURL

1. Criar URL curta:
```bash
curl -X POST http://localhost:8000/api/ \
  -H "Content-Type: application/json" \
  -d '{"url": "https://exemplo.com"}'
```

2. Obter informações:
```bash
curl http://localhost:8000/api/ABC123XY
```

3. Acessar URL curta:
```bash
curl -L http://localhost:8000/ABC123XY
```

### JavaScript (Fetch)

```javascript
// Criar URL curta
const response = await fetch('http://localhost:8000/api/', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        url: 'https://exemplo.com'
    })
});

const data = await response.json();
```

## Limitações

- URLs expiram após 30 dias por padrão
- Códigos curtos têm 8 caracteres
- URLs devem começar com http:// ou https://

## Segurança

- Validação de URLs
- Proteção contra XSS
- Headers de segurança configurados
- Expiração automática de URLs

## Performance

- Armazenamento em Redis
- Cache de URLs mais acessadas
- Incremento atômico de visitas
- Validação eficiente de códigos únicos 