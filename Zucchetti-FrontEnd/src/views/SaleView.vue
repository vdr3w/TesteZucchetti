<template>
  <div class="sales-container">
    <Navbar />
    <h1>VENDAS</h1>
    <div class="card">
      <div class="search-section">
        <button class="button-style clear-filter" @click="clearFilter">LIMPAR FILTRO</button>

        <select class="search-input" v-model="selectedCustomerId" @change="fetchSalesByCustomer">
          <option value="">Selecione um Cliente</option>
          <option v-for="customer in customersSelect" :key="customer.id" :value="customer.id">
            {{ customer.name }}
          </option>
        </select>

        <button class="button-style new-sale" @click="gotoNewSale">NOVA VENDA</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Método de Pagamento</th>
            <th>Produtos e Quantidades</th>
            <th>Preço Total</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sale in sales" :key="sale.id">
            <td>{{ sale.id }}</td>
            <td>{{ getCustomerName(sale.customer) }}</td>
            <td>{{ getPaymentMethodName(sale.paymentMethod) }}</td>
            <td>
              <ul>
                <li v-for="item in sale.items" :key="item.productId">
                  {{ getProductName(item.productId) }}, Quantidade: {{ item.quantity }}
                </li>
              </ul>
            </td>
            <td>{{ calculateTotalPrice(sale.items) }}</td>
            <td class="action-cell">
              <div class="button-container">
                <button class="button-style" @click="editSale(sale)">EDITAR</button>
                <button class="button-style" @click="deleteSale(sale.id)">DELETAR</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <SaleEditModal
      :visible="editModalVisible"
      :sale="currentSale"
      :paymentMethods="modalPaymentMethods"
      @close="editModalVisible = false"
      @submit="updateSale"
    ></SaleEditModal>
  </div>
</template>

<script>
import axios from 'axios'
import SaleEditModal from '@/components/sales/SaleEditModal.vue'
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'SaleView',
  components: {
    Navbar,
    SaleEditModal
  },
  data() {
    return {
      sales: [],
      paymentMethods: {},
      customersSelect: [],
      customers: {},
      products: {},
      editModalVisible: false,
      currentSale: null,
      modalPaymentMethods: {},
      selectedCustomerId: ''
    }
  },
  methods: {
    editSale(sale) {
      if (!sale || !sale.id || !sale.customer) {
        console.error('Dados incompletos para a venda:', sale)
        alert('Dados incompletos para edição.')
        return
      }

      axios
        .get(`http://localhost:8000/customer/show?id=${sale.customer}`)
        .then((response) => {
          this.currentSale = {
            ...sale,
            customerDetails: response.data,
            paymentMethodId: sale.paymentMethod,
            items: sale.items.map((item) => ({
              productId: item.productId,
              quantity: item.quantity
            }))
          }
          this.editModalVisible = true
        })
        .catch((error) => {
          console.error('Erro ao buscar detalhes do cliente:', error)
          this.currentSale = {
            ...sale,
            customerDetails: { id: sale.customer, name: 'Desconhecido' },
            paymentMethodId: sale.paymentMethod,
            items: sale.items
          }
          this.editModalVisible = true
        })
    },
    updateSale(updatedSale) {
      if (!updatedSale || !updatedSale.id) {
        console.error('Dados da venda incompletos ou venda não definida:', updatedSale)
        alert('Erro ao tentar atualizar: venda não definida.')
        return
      }

      axios
        .post(`http://localhost:8000/sale/update?id=${updatedSale.id}`, {
          customerId: updatedSale.customerDetails.id,
          paymentMethodId: updatedSale.paymentMethodId,
          items: updatedSale.items.map((item) => ({
            productId: item.productId,
            quantity: item.quantity
          }))
        })
        .then((response) => {
          if (response.data.success) {
            alert(`Venda atualizada com sucesso: ${response.data.message}`)
            this.fetchSales()
          } else {
            alert(response.data.message)
          }
          this.editModalVisible = false
        })
        .catch((error) => {
          console.error('Erro ao atualizar venda:', error)
          alert('Falha ao atualizar venda.')
          this.editModalVisible = false
        })
    },
    fetchSalesByCustomer() {
      if (this.selectedCustomerId) {
        axios
          .get(`http://localhost:8000/sale/listByCustomer?customerId=${this.selectedCustomerId}`)
          .then((response) => {
            if (response.data.length === 0) {
              this.sales = []
              alert('Este cliente não possui vendas realizadas.')
            } else {
              this.sales = response.data
            }
          })
          .catch((error) => {
            console.error('Error fetching sales by customer:', error)
            alert('Falha ao carregar vendas para o cliente selecionado.')
          })
      } else {
        this.fetchSales()
      }
    },
    clearFilter() {
      this.selectedCustomerId = ''
      this.fetchSales()
    },
    fetchSales() {
      axios
        .get('http://localhost:8000/sale/list')
        .then((response) => {
          this.sales = response.data.sort((a, b) => a.id - b.id)
          this.fetchPaymentMethods()
          this.fetchCustomers()
          this.fetchProducts()
        })
        .catch((error) => {
          console.error('Error fetching sales:', error)
          alert('Failed to load sales.')
        })
    },
    fetchPaymentMethods() {
      axios
        .get('http://localhost:8000/payment-method/list')
        .then((response) => {
          this.paymentMethods = response.data.reduce((acc, method) => {
            acc[method.id] = method.name
            return acc
          }, {})
        })
        .catch((error) => {
          console.error('Error fetching payment methods:', error)
        })
    },
    fetchCustomers() {
      axios
        .get('http://localhost:8000/customer/list')
        .then((response) => {
          this.customers = response.data.reduce((acc, customer) => {
            acc[customer.id] = customer.name
            return acc
          }, {})
        })
        .catch((error) => {
          console.error('Error fetching customers:', error)
        })
    },
    fetchCustomersForSelect() {
      axios
        .get('http://localhost:8000/customer/list')
        .then((response) => {
          this.customersSelect = response.data
        })
        .catch((error) => {
          console.error('Error fetching customers for select:', error)
        })
    },
    fetchProducts() {
      axios
        .get('http://localhost:8000/product/list')
        .then((response) => {
          this.products = response.data.reduce((acc, product) => {
            acc[product.id] = { name: product.name, price: product.price }
            return acc
          }, {})
        })
        .catch((error) => {
          console.error('Error fetching products:', error)
        })
    },
    getPaymentMethodName(id) {
      return this.paymentMethods[id] || 'Unknown'
    },
    getCustomerName(id) {
      return this.customers[id] || 'Unknown'
    },
    getProductName(id) {
      return this.products[id]?.name || 'Unknown'
    },
    calculateTotalPrice(items) {
      const total = items.reduce((total, item) => {
        const productPrice = this.products[item.productId]?.price || 0
        return total + productPrice * item.quantity
      }, 0)
      return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(total)
    },
    deleteSale(id) {
      axios
        .post(`http://localhost:8000/sale/delete?id=${id}`)
        .then((response) => {
          if (response.data.success) {
            alert('Venda excluída com sucesso.')
            this.fetchSales()
          } else {
            alert(response.data.error)
          }
        })
        .catch((error) => {
          console.error('Error deleting sale:', error)
          alert('Falha ao excluir venda.')
        })
    },
    gotoNewSale() {
      this.$router.push({ name: 'NewSale' })
    },
    fetchModalPaymentMethods() {
      axios
        .get('http://localhost:8000/payment-method/list')
        .then((response) => {
          this.modalPaymentMethods = response.data.reduce((acc, method) => {
            acc[method.id] = method.name
            return acc
          }, {})
        })
        .catch((error) => {
          console.error('Error fetching modal payment methods:', error)
        })
    }
  },
  created() {
    this.fetchSales()
    this.fetchModalPaymentMethods()
    this.fetchCustomers()
    this.fetchCustomersForSelect()
  }
}
</script>

<style scoped>
.sales-container {
  text-align: center;
  width: 100%;
  font-family: var(--fonte-padrao);
}

.card {
  width: 80%;
  margin: auto;
  background-color: var(--cinza);
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

.search-section {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.search-input {
  flex-grow: 1;
  margin: 0 10px;
  padding: 10px;
  border: 1px solid var(--preto);
  border-radius: 4px;
}

.clear-filter,
.new-sale {
  flex-basis: 15%;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin: auto;
  background-color: #ffffff;
}

th,
td {
  border: 1px solid var(--preto);
  padding: 12px;
  text-align: left;
  color: var(--fonte-fundo-branco);
}

th {
  background-color: var(--azul-zucchetti);
  color: var(--fonte-fundo-azul);
}

tbody tr:nth-child(odd) {
  background-color: var(--cinza);
}

td.action-cell {
  text-align: center;
  vertical-align: middle;
}

.button-style {
  background-color: var(--preto);
  color: #ffffff;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: 0.3s ease;
}

.button-style:hover {
  background-color: #1d2127;
}

.button-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
</style>
