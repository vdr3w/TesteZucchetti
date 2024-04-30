<template>
  <Navbar />
  <div class="new-sale-container">
    <h1>Cadastrar Nova Venda</h1>
    <button @click="$router.push({ name: 'Sales' })" class="button-style">VOLTAR</button>
    <div class="cards">
      <div class="card">
        <h2>Dados da Venda</h2>
        <form @submit.prevent="submitSale">
          <div class="form-group">
            <label for="customer">Nome do Cliente:</label>
            <select id="customer" v-model="sale.customerId" class="input-style input-select">
              <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                {{ customer.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="paymentMethod">Forma de Pagamento:</label>
            <select
              id="paymentMethod"
              v-model="sale.paymentMethodId"
              class="input-style input-select"
            >
              <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                {{ method.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="installments">Parcelas:</label>
            <input
              type="number"
              id="installments"
              v-model="sale.installments"
              placeholder="Número de parcelas"
              class="input-style input-number"
            />
          </div>
          <div class="form-group">
            <label for="total">Valor Total:</label>
            <input
              type="number"
              id="total"
              :value="totalSale"
              placeholder="Valor total da venda"
              readonly
              class="input-style input-number"
            />
          </div>
          <button class="button-style" type="submit">COMPRAR</button>
        </form>
      </div>
      <div class="card">
        <h2>Adicionar Produtos</h2>
        <div class="right-form-group">
          <label for="productSelect">Escolha o Produto</label>
          <select id="productSelect" v-model="selectedProduct" class="input-style input-select">
            <option v-for="product in products" :key="product.id" :value="product.id">
              {{ product.name }}
            </option>
          </select>
        </div>
        <div class="right-form-group">
          <label for="productQuantity">Digite a Quantidade</label>
          <input
            id="productQuantity"
            type="number"
            v-model="selectedQuantity"
            min="1"
            placeholder="Qtd"
            class="input-style input-number"
          />
        </div>
        <button class="button-style" @click="addProduct">ADICIONAR PRODUTO</button>
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
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'NewSaleView',
  components: {
    Navbar
  },
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
      const payload = {
        customerId: this.sale.customerId,
        paymentMethodId: this.sale.paymentMethodId,
        items: this.addedProducts.map((p) => ({
          productId: p.id,
          quantity: p.quantity
        })),
        installments: this.sale.installments
      }
      axios
        .post('http://localhost:8000/sale/create', payload)
        .then((response) => {
          if (response.data.success) {
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
  padding: 20px;
  justify-content: center;
}

.cards {
  display: flex;
  justify-content: space-around;
  width: 100%;
  max-width: 1200px;
}

.card {
  flex: 1;
  margin: 10px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: var(--cinza);
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.form-group {
  width: 80%;
  margin-bottom: 20px;
}

.right-form-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 80%;
  margin-bottom: 20px;
}

.input-style {
  width: 100%;
  padding: 10px;
  border-radius: 4px;
  border: 1px solid var(--preto);
}

.input-select {
  max-width: 300px;
}

.input-number {
  max-width: 180px;
}

.button-style {
  padding: 10px 15px;
  background-color: var(--preto);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 10px;
}

.button-style:hover {
  background-color: #1d2127;
}

ul {
  list-style: none;
  padding: 0;
}

li {
  padding: 5px 0;
}
</style>
