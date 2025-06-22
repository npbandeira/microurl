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

# Produção
composer start:prod
```
## API

### Endpoints

- `POST /api/micro-url` - Criar URL encurtada
- `GET /api/micro-url/{shortCode}` - Obter informações da URL
- `GET /{shortCode}` - Redirecionamento

Veja a documentação completa em [docs/API.md](docs/API.md)

## Exemplo de Uso

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
const response = await fetch('http://localhost:8000/api/micro-url', {
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