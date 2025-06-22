# API MicroUrl

## Endpoints

### 1. Criar URL Encurtada
**POST** `/api/micro-url`

**Body:**
```json
{
    "url": "https://exemplo.com/url-muito-longa",
    "expires_at": "2024-12-31 23:59:59" // opcional
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "URL encurtada com sucesso",
    "data": {
        "original_url": "https://exemplo.com/url-muito-longa",
        "short_code": "aB3cD4eF",
        "short_url": "http://localhost:8002/aB3cD4eF",
        "visits": 0,
        "created_at": "2024-01-15 10:30:00",
        "expires_at": "2024-12-31 23:59:59"
    }
}
```

### 2. Obter Informações da URL
**GET** `/api/micro-url/{shortCode}`

**Response (200):**
```json
{
    "success": true,
    "message": "URL encontrada",
    "data": {
        "original_url": "https://exemplo.com/url-muito-longa",
        "short_code": "aB3cD4eF",
        "short_url": "http://localhost:8002/aB3cD4eF",
        "visits": 5,
        "created_at": "2024-01-15 10:30:00",
        "expires_at": "2024-12-31 23:59:59"
    }
}
```

### 3. Redirecionamento
**GET** `/{shortCode}`

Redireciona automaticamente para a URL original.

## Códigos de Erro

- **400** - Requisição inválida
- **404** - URL não encontrada
- **410** - URL expirada
- **422** - Erro de validação
- **500** - Erro interno do servidor

## Estrutura do Projeto

```
microurl/
├── config/           # Configurações
│   ├── app.php      # Configuração do Slim
│   ├── container.php # Container de dependências
│   └── env.php      # Variáveis de ambiente
├── src/
│   ├── Controller/  # Controladores
│   ├── Model/       # Modelos
│   ├── View/        # Views/Respostas
│   └── Middleware/  # Middlewares
├── route/           # Definição de rotas
├── public/          # Ponto de entrada
└── docs/            # Documentação
``` 