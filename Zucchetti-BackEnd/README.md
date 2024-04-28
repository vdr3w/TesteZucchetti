# Zucchetti-BackEnd
 
# Documentação da API de Gerenciamento de Produtos e Vendas 🛍️

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
</br>
![Doctrine ORM](https://img.shields.io/badge/Doctrine-ORM-blue.svg)
![Made with Love in Brazil](https://img.shields.io/badge/Made%20with-Love%20in%20Brazil-green)
</br>

Esta documentação cobre os endpoints disponíveis para gerenciar produtos, clientes, métodos de pagamento e vendas, detalhando métodos, parâmetros necessários e exemplos de respostas.
</br>
</br>
![#tamojunto](https://img.shields.io/badge/%23tamojunto-blue.svg)
***
![Diagrama de Classes](https://i.imgur.com/rai5X3W.png)
***
## 📦📦 Endpoints de Produto

### 🆕 Criar Produto

![Exemplo de Requisição no Insomnia](https://i.imgur.com/HUCmPp5.png)

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
***
### 📋 Listar Produtos

![Exemplo de Requisição no Insomnia](https://i.imgur.com/alysT5T.png)

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
***
### 🔍 Exibir Produto

![Exemplo de Requisição no Insomnia](https://i.imgur.com/duCPvjK.png)

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
***
### ✏️ Atualizar Produto

![Exemplo de Requisição no Insomnia](https://i.imgur.com/8IG7O4g.png)

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
***
### ❌ Deletar Produto

![Exemplo de Requisição no Insomnia](https://i.imgur.com/ZpnvYJN.png)

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
***
## 👥👥 Endpoints de Cliente

### 🆕 Criar Cliente

![Exemplo de Requisição no Insomnia](https://i.imgur.com/o9pHhsA.png)

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
  "name": "Drew Vieira",
  "cpf": "123.456.789-10",
  "email": "drew.vieira@example.com",
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
***
### 📋 Listar Clientes

![Exemplo de Requisição no Insomnia](https://i.imgur.com/ueKAzCS.png)

```http
GET /customer/list
```

**Exemplo de Resposta:**

```json
[
  {
    "id": 1,
    "name": "Drew Vieira",
    "cpf": "123.456.789-10",
    "email": "drew.vieira@example.com",
    "cep": "12345-678",
    "address": "Rua Exemplo, 100, Cidade, Estado"
  }
]
```
***
### 🔍 Exibir Cliente

![Exemplo de Requisição no Insomnia](https://i.imgur.com/aplBKqm.png)

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
  "name": "Drew Vieira",
  "cpf": "123.456.789-10",
  "email": "drew.vieira@example.com",
  "cep": "12345-678",
  "address": "Rua Exemplo, 100, Cidade, Estado"
}
```
***
### ✏️ Atualizar Cliente

![Exemplo de Requisição no Insomnia](https://i.imgur.com/dk8EIoY.png)

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
  "name": "Drew Vieira atualizado",
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
***
### ❌ Deletar Cliente

![Exemplo de Requisição no Insomnia](https://i.imgur.com/mcneQUt.png)

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
***
## 💳💳 Endpoints de Método de Pagamento

### 🆕 Criar Método de Pagamento

![Exemplo de Requisição no Insomnia](https://i.imgur.com/OhM70kC.png)

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
***
### 📋 Listar Métodos de Pagamento

![Exemplo de Requisição no Insomnia](https://i.imgur.com/OwwhBgB.png)

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
***
### 🔍 Exibir Método de Pagamento

![Exemplo de Requisição no Insomnia](https://i.imgur.com/WEaf5N9.png)

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
***
### ✏️ Atualizar Método de Pagamento

![Exemplo de Requisição no Insomnia](https://i.imgur.com/yIToFPp.png)

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
***
### ❌ Deletar Método de Pagamento

![Exemplo de Requisição no Insomnia](https://i.imgur.com/S9NbbSs.png)

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
***
## 💸💸 Endpoints de Vendas

### 🆕 Criar Venda

![Exemplo de Requisição no Insomnia](https://i.imgur.com/OgH0nzb.png)

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
***
### 📋 Listar Vendas

![Exemplo de Requisição no Insomnia](https://i.imgur.com/1yrwzo7.png)

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
***
### 🔍 Exibir Venda (ID)

![Exemplo de Requisição no Insomnia](https://i.imgur.com/NeUSm3v.png)

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
***
### 🔍 Listar Vendas por Cliente

![Exemplo de Requisição no Insomnia](https://i.imgur.com/COpAfF3.png)

- **Endpoint:** `/sale/listByCustomer`
- **Método HTTP:** GET
- **Parâmetros:**
  - `customerId`: integer - ID do cliente especificado como parâmetro de consulta na URL, por exemplo, `/sale/listByCustomer?customerId=1`.
  
**Exemplo de Requisição:**

```http
GET /sale/listByCustomer?customerId=1
```

**Exemplo de Resposta Sucesso:**

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

**Exemplo de Resposta Erro (cliente não encontrado):**

```json
{
  "success": false,
  "error": "Cliente não encontrado."
}
```

**Exemplo de Resposta Erro (nenhuma venda encontrada para o cliente):**

```json
{
  "success": false,
  "error": "Nenhuma venda encontrada para o cliente especificado."
}
```
***
### ✏️ Atualizar Venda

![Exemplo de Requisição no Insomnia](https://i.imgur.com/Cnf0jIs.png)

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
      "customerId": 6,
      "paymentMethodId": 1,
      "items": [
        {
          "productId": 7,
          "quantity": 10
        },
        {
          "productId": 8,
          "quantity": 20
        }
      ],
      "installments": 4
 }
```

**Exemplo de Resposta Sucesso:**

```json
 {
      "success": true,
      "message": "Venda criada com sucesso com ID 20, Total: $129.7",
      "installments": 4,
      "installmentAmount": 32.43
 }
```
***
### ❌ Deletar Venda

![Exemplo de Requisição no Insomnia](https://i.imgur.com/FAjjE2N.png)

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
  "error": "Venda em uso ou não encontrada."
}
```

