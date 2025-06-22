# MicroUrl - Encurtador de URLs

Um encurtador de URLs simples e eficiente construído com PHP Slim Framework seguindo padrões MVC.

## Características

- ✅ Framework Slim 4 com PSR-7
- ✅ Arquitetura MVC
- ✅ Container de dependências (PHP-DI)
- ✅ Redis como banco de dados
- ✅ Validação de dados
- ✅ Respostas JSON padronizadas
- ✅ Middleware de CORS
- ✅ Tratamento de erros
- ✅ Documentação da API

## Requisitos

- PHP 8.4+
- Redis
- Composer

## Instalação

1. Clone o repositório:
```bash
git clone <repository-url>
cd microurl
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o ambiente:
```bash
cp .env.example .env
# Edite o arquivo .env com suas configurações
```

4. Inicie o Redis:
```bash
redis-server
```

5. Execute a aplicação:
```bash
# Desenvolvimento
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

### Criar URL Encurtada
```bash
curl -X POST http://localhost:8002/api/micro-url \
  -H "Content-Type: application/json" \
  -d '{"url": "https://exemplo.com/url-muito-longa"}'
```

### Acessar URL Encurtada
```bash
curl http://localhost:8002/aB3cD4eF
```

## Desenvolvimento

### Executar testes
```bash
composer test
```

### Estrutura MVC

O projeto segue o padrão MVC (Model-View-Controller):

- **Models**: Lógica de negócio e acesso a dados
- **Views**: Respostas JSON padronizadas
- **Controllers**: Manipulação de requisições e respostas
- **Middleware**: Validação e processamento de requisições

### Adicionando Novas Rotas

1. Adicione a rota em `route/routes.php`
2. Crie o controller correspondente em `src/Controller/`
3. Se necessário, crie o model em `src/Model/`

## Docker

Para executar com Docker:

```bash
docker-compose up -d
```

## Licença

MIT 