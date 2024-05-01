![Zucchetti](https://www.zucchettibrasil.com.br/templates/website/img/logo.png)

## 🧍 **CustomersView** - Visão de Clientes
![TELA CLIENTES](https://i.imgur.com/KTN8lX9.png)
![MODAL CLIENTES](https://i.imgur.com/0cz57xj.png)

### Descrição
`CustomersView` é utilizada para gerenciar informações dos clientes, permitindo visualizar, adicionar, editar e excluir clientes.

### Funcionalidades
- **Listagem de Clientes:** Exibe todos os clientes com informações como ID, nome, CPF, endereço, email e CEP.
- **Busca de Clientes:** Filtra os clientes por nome, CPF ou email.
- **Adição de Clientes:** Abre um modal para inserir os dados de um novo cliente.
- **Edição de Clientes:** Abre um modal com os dados do cliente selecionado para edição.
- **Exclusão de Clientes:** Permite excluir um cliente específico.

### Componentes
- **Navbar:** Barra de navegação comum a todas as views.
- **CustomerModal:** Modal para adicionar ou editar informações de clientes.

***

## 📦 **ProductView** - Visão de Produtos
![TELA PRODUTOS](https://i.imgur.com/bwTKZ1G.png)
![MODAL PRODUTOS](https://i.imgur.com/VgvUqPR.png)

### Descrição
`ProductView` facilita o gerenciamento dos produtos, permitindo listar, adicionar, editar e remover produtos do inventário.

### Funcionalidades
- **Listagem de Produtos:** Mostra todos os produtos com detalhes como ID, nome, preço e quantidade.
- **Busca de Produtos:** Filtra produtos por nome ou quantidade.
- **Adição de Produtos:** Abre um modal para inserção de novos produtos.
- **Edição de Produtos:** Permite editar informações de produtos existentes através de um modal.
- **Exclusão de Produtos:** Oferece a opção de remover produtos.

### Componentes
- **Navbar**
- **ProductModal:** Modal utilizado para adicionar ou editar produtos.

***

## 💳 **PaymentMethodView** - Visão de Métodos de Pagamento
![TELA METODOS DE PAGAMENTO](https://i.imgur.com/afRDMIr.png)
![MODAL METODOS DE PAGAMENTO](https://i.imgur.com/L7Ze20V.png)

### Descrição
`PaymentMethodView` gerencia os métodos de pagamento disponíveis, permitindo a adição, atualização e exclusão.

### Funcionalidades
- **Listagem de Métodos de Pagamento:** Exibe todos os métodos com ID, nome e o máximo de parcelas.
- **Busca de Métodos:** Permite filtrar métodos de pagamento por nome ou número de parcelas.
- **Adição de Métodos:** Modal para inserção de um novo método de pagamento.
- **Edição de Métodos:** Edição de um método de pagamento existente através de um modal.
- **Exclusão de Métodos:** Remove métodos de pagamento.

### Componentes
- **Navbar**
- **PaymentMethodModal:** Modal para criar ou editar métodos de pagamento.

***

## 🛒 **SaleView** - Visão de Vendas
![TELA VENDAS](https://i.imgur.com/dmockJH.png)
![MODAL VENDAS](https://i.imgur.com/DArFYBW.png)

### Descrição
`SaleView` é utilizada para visualizar e gerenciar vendas, incluindo funcionalidades para listar vendas, adicionar novas vendas e editar ou excluir vendas existentes.

### Funcionalidades
- **Listagem de Vendas:** Lista todas as vendas com detalhes relevantes como ID da venda, cliente, método de pagamento, produtos e total.
- **Busca de Vendas por Cliente:** Filtra vendas por cliente.
- **Nova Venda:** Redireciona para a view `NewSaleView` para adicionar uma nova venda.
- **Edição de Vendas:** Permite modificar detalhes de uma venda existente.
- **Exclusão de Vendas:** Habilita a remoção de vendas.

### Componentes
- **Navbar**
- **SaleEditModal:** Modal para edição de vendas.

***

## 🆕 **NewSaleView** - Adicionar Nova Venda

![TELA NOVA VENDA](https://i.imgur.com/EZUBvG9.png)

### Descrição
`NewSaleView` permite aos usuários inserir novas vendas no sistema, especificando detalhes como cliente, método de pagamento, produtos e quantidade.

### Funcionalidades
- **Seleção de Cliente e Método de Pagamento:** Usuários podem escolher um cliente e um método de pagamento a partir de seletores dropdown.
- **Adição de Produtos à Venda:** Permite adicionar produtos à venda e especificar a quantidade.
- **Cálculo Automático do Total:** O total é calculado com base nos produtos adicionados à venda.
- **Submissão da Venda:** Envia os detalhes da venda para o backend.

### Componentes
- **Navbar**


## 🚏 Rotas do Frontend

As rotas no frontend são configuradas para gerenciar a navegação entre as diversas telas da aplicação. Cada rota está associada a uma view específica e inclui metadados para controle de acesso, como a necessidade de autenticação.

### Configuração das Rotas

| Caminho                | Nome            | Componente          | Requer Autenticação | Descrição                           |
|------------------------|-----------------|---------------------|---------------------|-------------------------------------|
| `/login`               | `Login`         | `LoginView`         | Não                 | Tela de login para acesso ao sistema. |
| `/unauthorized`        | `Unauthorized`  | `UnauthorizedView`  | Não                 | Tela exibida quando um acesso não autorizado é detectado. |
| `/clientes`            | `Customers`     | `CustomersView`     | Sim                 | Tela para gestão de clientes. Permite listar, adicionar, editar e remover clientes. |
| `/produtos`            | `Products`      | `ProductView`       | Sim                 | Tela para gestão de produtos. Inclui funcionalidades para adicionar, editar e deletar produtos. |
| `/metodos-de-pagamento`| `PaymentMethods`| `PaymentMethodView` | Sim                 | Tela para gestão de métodos de pagamento. Permite criar, editar e excluir métodos de pagamento. |
| `/vendas`              | `Sales`         | `SaleView`          | Sim                 | Tela para visualização e gestão de vendas. Inclui funcionalidades para adicionar e editar vendas. |
| `/nova-venda`          | `NewSale`       | `NewSaleView`       | Sim                 | Tela para criação de novas vendas, permitindo especificar produtos, cliente e método de pagamento. |

### Middleware de Autenticação

Um middleware de autenticação verifica se o usuário está autenticado antes de permitir o acesso às rotas que requerem autenticação. Se não estiver autenticado, o usuário é redirecionado para a tela de não autorizado.

```javascript
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !localStorage.getItem('token')) {
    next('/unauthorized');
  } else {
    next();
  }
});
