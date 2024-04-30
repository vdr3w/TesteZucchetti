<template>
  <div class="new-sale-container">
    <h1>Cadastrar Nova Venda</h1>
    <div class="cards">
      <div class="card">
        <h2>Dados da Venda</h2>
        <form @submit.prevent="submitSale">
          <div>
            <label for="customer">Nome do Cliente:</label>
            <select id="customer" v-model="sale.customerId">
              <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
              </option>
            </select>
          </div>
          <div>
            <label for="paymentMethod">Forma de Pagamento:</label>
            <select id="paymentMethod" v-model="sale.paymentMethodId">
              <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                {{ method.name }}
              </option>
            </select>
          </div>
          <div>
            <label for="installments">Parcelas:</label>
            <input
              type="number"
              id="installments"
              v-model="sale.installments"
              placeholder="Número de parcelas"
            />
          </div>
          <div>
            <label for="total">Valor Total:</label>
            <input
              type="number"
              id="total"
              :value="totalSale"
              placeholder="Valor total da venda"
              readonly
            />
          </div>
          <button type="submit">Cadastrar</button>
        </form>
      </div>
      <div class="card">
        <h2>Adicionar Produtos</h2>
        <select v-model="selectedProduct">
          <option v-for="product in products" :key="product.id" :value="product.id">
            {{ product.name }}
          </option>
        </select>
        <input type="number" v-model="selectedQuantity" min="1" placeholder="Qtd" />
        <button @click="addProduct">ADD</button>
        <ul>
          <li v-for="(product, index) in addedProducts" :key="index">
            {{ product.name }} - Quantidade: {{ product.quantity }} - Total: R$
            {{ (product.price * product.quantity).toFixed(2) }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'NewSaleView',
  data() {
    return {
      sale: {
        customerId: '',
        paymentMethod: '',
        installments: null,
        total: null
      },
      products: [],
      paymentMethods: [],
      customers: [],
      selectedProduct: null,
      selectedQuantity: 1,
      addedProducts: []
    }
  },
  computed: {
    totalSale() {
      return this.addedProducts
        .reduce((acc, product) => acc + product.price * product.quantity, 0)
        .toFixed(2)
    }
  },
  methods: {
    fetchProducts() {
      axios
        .get('http://localhost:8000/product/list')
        .then((response) => {
          this.products = response.data
        })
        .catch((error) => {
          console.error('Error fetching products:', error)
        })
    },
    fetchPaymentMethods() {
      axios
        .get('http://localhost:8000/payment-method/list')
        .then((response) => {
          this.paymentMethods = response.data
        })
        .catch((error) => {
          console.error('Error fetching payment methods:', error)
        })
    },
    fetchCustomers() {
      axios
        .get('http://localhost:8000/customer/list')
        .then((response) => {
          this.customers = response.data
        })
        .catch((error) => {
          console.error('Error fetching customers:', error)
        })
    },
    submitSale() {
      // Cria o payload com os dados corretos para a requisição
      const payload = {
        customerId: this.sale.customerId, // Assegura que estamos passando o ID do cliente
        paymentMethodId: this.sale.paymentMethodId, // Assegura que estamos passando o ID do método de pagamento
        items: this.addedProducts.map((p) => ({
          productId: p.id, // Assegura que estamos passando o ID do produto
          quantity: p.quantity
        })),
        installments: this.sale.installments // Assegura que o número de parcelas seja passado corretamente
      }

      // Faz a requisição de criação da venda
      axios
        .post('http://localhost:8000/sale/create', payload)
        .then((response) => {
          if (response.data.success) {
            // Formata a mensagem para incluir detalhes sobre parcelas e o valor de cada parcela
            const message = `Venda criada com sucesso com ID ${response.data.message.match(/ID (\d+)/)[1]}, Total: R$${response.data.message.match(/Total: \$(\d+\.\d+)/)[1]}
Parcelas: ${response.data.installments}, Valor por parcela: R$${response.data.installmentAmount}`
            alert(message)
          } else {
            alert(`Erro: ${response.data.error}`)
          }
        })
        .catch((error) => {
          console.error('Erro ao criar venda:', error)
          alert('Falha ao criar venda.')
        })
    },
    addProduct() {
      const product = this.products.find((p) => p.id === this.selectedProduct)
      if (!product) return

      const existingProduct = this.addedProducts.find((p) => p.id === this.selectedProduct)
      if (existingProduct) {
        existingProduct.quantity += parseInt(this.selectedQuantity)
      } else {
        this.addedProducts.push({ ...product, quantity: parseInt(this.selectedQuantity) })
      }
      this.selectedQuantity = 1
    }
  },
  created() {
    this.fetchProducts()
    this.fetchPaymentMethods()
    this.fetchCustomers()
  }
}
</script>

<style scoped>
.new-sale-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.cards {
  display: flex;
  justify-content: space-around;
  width: 100%;
}

.card {
  flex: 1;
  margin: 10px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: white;
}

button {
  margin-top: 10px;
}
</style>
