# Zucchetti-BackEnd
 
# Documentação da API de Gerenciamento de Produtos e Vendas 🛍️

Esta documentação cobre os endpoints disponíveis para gerenciar produtos, clientes, métodos de pagamento e vendas, detalhando métodos, parâmetros necessários e exemplos de respostas.

## 📦📦 Endpoints de Produto

### 🆕 Criar Produto

```http
POST /product/create
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `name`     | `string` | **Obrigatório**. Nome do produto.   |
| `price`    | `decimal`| **Obrigatório**. Preço do produto.  |
| `quantity` | `integer`| **Obrigatório**. Quantidade em estoque. |

**Exemplo de corpo da requisição:**

```json
{
  "name": "Funko Pop Luffytaro",
  "price": 150.00,
  "quantity": 150
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Produto criado com sucesso com ID 1"
}
```

**Exemplo de Resposta Erro (dados faltando):**

```json
{
  "success": false,
  "error": "Dados faltando para nome, preço ou quantidade."
}
```

### 📋 Listar Produtos

```http
GET /product/list
```

**Exemplo de Resposta:**

```json
[
  {
    "id": 1,
    "name": "Funko Pop Luffytaro",
    "price": 150.00,
    "quantity": 150
  },
  {
    "id": 2,
    "name": "Funko Pop Goku",
    "price": 80.00,
    "quantity": 100
  }
]
```

### 🔍 Exibir Produto

```http
GET /product/show?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do produto especificado na URL. |

**Exemplo de Resposta:**

```json
{
  "id": 1,
  "name": "Funko Pop Luffytaro",
  "price": 150.00,
  "quantity": 150
}
```

### ✏️ Atualizar Produto

```http
POST /product/update
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do produto.     |
| `name`     | `string` | Nome do produto.                    |
| `price`    | `decimal`| Preço do produto.                   |
| `quantity` | `integer`| Quantidade em estoque.              |

**Exemplo de corpo da requisição:**

```json
{
  "id": 1,
  "name": "Funko Pop Luffytaro Premium",
  "price": 1.29,
  "quantity": 150
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Produto atualizado com sucesso."
}
```

### ❌ Deletar Produto

```http
POST /product/delete?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do produto especificado na URL. |

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Produto excluído com sucesso."
}
```

**Exemplo de Resposta Erro (produto em uso):**

```json
{
  "success": false,
  "error": "Item usado em alguma venda e não pode ser deletado."
}
```

## 👥👥 Endpoints de Cliente

### 🆕 Criar Cliente

```http
POST /customer/create
```

| Parâmetro  | Tipo     | Descrição                               |
|------------|----------|-----------------------------------------|
| `name`     | `string` | **Obrigatório**. Nome do cliente.       |
| `cpf`      | `string` | **Obrigatório**. CPF do cliente.        |
| `email`    | `string` | **Obrigatório**. Email do cliente.      |
| `cep`      | `string` | **Obrigatório**. CEP do cliente.        |
| `address`  | `string` | **Obrigatório**. Endereço do cliente.   |

**Exemplo de corpo da requisição:**

```json
{
  "name": "João Silva",
  "cpf": "123.456.789-10",
  "email": "joao.silva@example.com",
  "cep": "12345-678",
  "address": "Rua Exemplo, 100, Cidade, Estado"
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Cliente criado com sucesso com ID 1"
}
```

**Exemplo de Resposta Erro (dados faltando):**

```json
{
  "success": false,
  "error": "Dados faltando para nome, cpf, email, cep ou endereço."
}
```

### 📋 Listar Clientes

```http
GET /customer/list
```

**Exemplo de Resposta:**

```json
[
  {
    "id": 1,
    "name": "João Silva",
    "cpf": "123.456.789-10",
    "email": "joao.silva@example.com",
    "cep": "12345-678",
    "address": "Rua Exemplo, 100, Cidade, Estado"
  }
]
```

### 🔍 Exibir Cliente

```http
GET /customer/show?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do cliente especificado na URL. |

**Exemplo de Resposta:**

```json
{
  "id": 1,
  "name": "João Silva",
  "cpf": "123.456.789-10",
  "email": "joao.silva@example.com",
  "cep": "12345-678",
  "address": "Rua Exemplo, 100, Cidade, Estado"
}
```

### ✏️ Atualizar Cliente

```http
POST /customer/update
```

| Parâmetro  | Tipo     | Descrição                                   |
|------------|----------|---------------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do cliente.             |
| `name`     | `string` | Nome do cliente.                            |
| `cpf`      | `string` | CPF do cliente.                             |
| `email`    | `string` | Email do cliente.                           |
| `cep`      | `string` | CEP do cliente.                             |
| `address`  | `string` | Endereço do cliente.                        |

**Exemplo de corpo da requisição:**

```json
{
  "id": 1,
  "name": "João Silva atualizado",
  "email": "novo.email@exemplo.com"
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Cliente atualizado com sucesso."
}
```

### ❌ Deletar Cliente

```http
POST /customer/delete?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do cliente especificado na URL. |

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Cliente excluído com sucesso."
}
```

**Exemplo de Resposta Erro (cliente em uso):**

```json
{
  "success": false,
  "error": "Cliente não pode ser deletado pois está em uso."
}
```

## 💳💳 Endpoints de Método de Pagamento

### 🆕 Criar Método de Pagamento

```http
POST /payment-method/create
```

| Parâmetro      | Tipo     | Descrição                                   |
|----------------|----------|---------------------------------------------|
| `name`         | `string` | **Obrigatório**. Nome do método de pagamento. |
| `installments` | `integer`| **Obrigatório**. Número máximo de parcelas permitidas. |

**Exemplo de corpo da requisição:**

```json
{
  "name": "Cartão de Crédito",
  "installments": 12
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Método de pagamento criado com sucesso com ID 1"
}
```

**Exemplo de Resposta Erro (dados faltando):**

```json
{
  "success": false,
  "error": "Dados faltando para nome ou parcelas."
}
```

### 📋 Listar Métodos de Pagamento

```http
GET /payment-method/list
```

**Exemplo de Resposta:**

```json
[
  {
    "id": 1,
    "name": "Cartão de Crédito",
    "installments": 12
  },
  {
    "id": 2,
    "name": "Boleto Bancário",
    "installments": 1
  }
]
```

### 🔍 Exibir Método de Pagamento

```http
GET /payment-method/show?id=1
```

| Parâmetro  | Tipo     | Descrição                               |
|------------|----------|-----------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do método de pagamento especificado na URL. |

**Exemplo de Resposta:**

```json
{
  "id": 1,
  "name": "Cartão de Crédito",
  "installments": 12
}
```

### ✏️ Atualizar Método de Pagamento

```http
POST /payment-method/update
```

| Parâmetro      | Tipo     | Descrição                                   |
|----------------|----------|---------------------------------------------|
| `id`           | `integer`| **Obrigatório**. ID do método de pagamento. |
| `name`         | `string` | Novo nome do método de pagamento.           |
| `installments` | `integer`| Novo número de parcelas permitidas.         |

**Exemplo de corpo da requisição:**

```json
{
  "id": 1,
  "name": "Cartão de Crédito Platinum",
  "installments": 24
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Método de pagamento atualizado com sucesso."
}
```

### ❌ Deletar Método de Pagamento

```http
POST /payment-method/delete?id=1
```

| Parâmetro  | Tipo     | Descrição                               |
|------------|----------|-----------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID do método de pagamento especificado na URL. |

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Método de pagamento excluído com sucesso."
}
```

**Exemplo de Resposta Erro (método em uso):**

```json
{
  "success": false,
  "error": "Método de pagamento não pode ser deletado pois está em uso."
}
```
## 💸💸 Endpoints de Vendas

### 🆕 Criar Venda

```http
POST /sale/create
```

| Parâmetro          | Tipo     | Descrição                                           |
|--------------------|----------|-----------------------------------------------------|
| `customerId`       | `integer`| **Obrigatório**. ID do cliente.                     |
| `paymentMethodId`  | `integer`| **Obrigatório**. ID do método de pagamento.         |
| `items`            | `array`  | **Obrigatório**. Array de objetos, cada contendo `productId` e `quantity`. |

**Exemplo de corpo da requisição:**

```json
{
  "customerId": 1,
  "paymentMethodId": 2,
  "items": [
    {"productId": 1, "quantity": 3},
    {"productId": 2, "quantity": 1}
  ]
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Venda criada com sucesso com ID 1 Total: $250.00"
}
```

**Exemplo de Resposta Erro (dados faltando ou erro de validação):**

```json
{
  "success": false,
  "error": "Dados faltando para customerId, paymentMethodId ou items."
}
```

### 📋 Listar Vendas

```http
GET /sale/list
```

**Exemplo de Resposta:**

```json
[
  {
    "id": 1,
    "customer": 1,
    "paymentMethod": 2,
    "items": [
      {"productId": 1, "quantity": 3},
      {"productId": 2, "quantity": 1}
    ],
    "totalPrice": 250.00
  }
]
```

### 🔍 Exibir Venda

```http
GET /sale/show?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID da venda especificado na URL. |

**Exemplo de Resposta:**

```json
{
  "id": 1,
  "customer": 1,
  "paymentMethod": 2,
  "items": [
    {"productId": 1, "quantity": 3},
    {"productId": 2, "quantity": 1}
  ],
  "totalPrice": 250.00
}
```

### ✏️ Atualizar Venda

```http
POST /sale/update
```

| Parâmetro          | Tipo     | Descrição                                           |
|--------------------|----------|-----------------------------------------------------|
| `id`               | `integer`| **Obrigatório**. ID da venda.                       |
| `customerId`       | `integer`| Novo ID do cliente (opcional).                      |
| `paymentMethodId`  | `integer`| Novo ID do método de pagamento (opcional).          |
| `items`            | `array`  | Array de objetos (opcional), cada contendo `productId` e `quantity`. |

**Exemplo de corpo da requisição:**

```json
{
  "id": 1,
  "customerId": 2,
  "items": [
    {"productId": 3, "quantity": 2}
  ]
}
```

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Venda atualizada com sucesso. Novo Total: $180.00"
}
```

### ❌ Deletar Venda

```http
POST /sale/delete?id=1
```

| Parâmetro  | Tipo     | Descrição                           |
|------------|----------|-------------------------------------|
| `id`       | `integer`| **Obrigatório**. ID da venda especificado na URL. |

**Exemplo de Resposta Sucesso:**

```json
{
  "success": true,
  "message": "Venda excluída com sucesso."
}
```

**Exemplo de Resposta Erro (venda em uso ou outro erro):**

```json
{
  "success": false,
  "error": "Erro ao excluir venda: [Descrição do erro]"
}
```

