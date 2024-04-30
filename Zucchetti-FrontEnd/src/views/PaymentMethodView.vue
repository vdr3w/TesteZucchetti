<template>
  <div class="payment-methods-container">
    <Navbar />
    <h1>MÉTODOS DE PAGAMENTO</h1>
    <div class="card">
      <div class="search-section">
        <input
          type="text"
          placeholder="PESQUISE POR NOME OU PARCELAS"
          v-model="searchQuery"
          class="search-input"
        />
        <button class="button-style" @click="openNewPaymentMethodModal">NOVO</button>
      </div>
      <table>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Parcelas Máximas</th>
          <th>Ações</th>
        </tr>
        <tr v-for="method in filteredMethods" :key="method.id">
          <td>{{ method.id }}</td>
          <td>{{ method.name }}</td>
          <td>{{ method.installments }}</td>
          <td class="button-container">
            <button class="button-style" @click="openEditPaymentMethodModal(method)">EDITAR</button>
            <button class="button-style" @click="deletePaymentMethod(method.id)">DELETAR</button>
          </td>
        </tr>
      </table>
    </div>
    <payment-method-modal
      :show="showModal"
      @close="closeModal"
      :method="currentMethod"
      @refresh="fetchPaymentMethods"
    ></payment-method-modal>
  </div>
</template>

<script>
import api from '@/axios';
import PaymentMethodModal from '@/components/paymentmethod/PaymentMethodModal.vue'
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'PaymentMethodView',
  components: {
    PaymentMethodModal,
    Navbar
  },
  data() {
    return {
      methods: [],
      searchQuery: '',
      showModal: false,
      currentMethod: null
    }
  },
  created() {
    this.fetchPaymentMethods()
  },
  methods: {
    openNewPaymentMethodModal() {
      this.currentMethod = { name: '', installments: 0 }
      this.showModal = true
    },
    openEditPaymentMethodModal(method) {
      this.currentMethod = { ...method }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    async fetchPaymentMethods() {
      try {
        const response = await api.get('/payment-method/list')
        this.methods = response.data
      } catch (error) {
        console.error('Erro ao buscar métodos de pagamento:', error)
      }
    },
    async deletePaymentMethod(id) {
      try {
        const response = await api.post(`/payment-method/delete?id=${id}`)
        alert(response.data.message)
        this.fetchPaymentMethods()
      } catch (error) {
        console.error('Erro ao deletar método de pagamento:', error)
        alert('Falha ao deletar método de pagamento.')
      }
    }
  },
  computed: {
    filteredMethods() {
      return this.methods
        .filter(
          (method) =>
            method.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            method.installments.toString().includes(this.searchQuery)
        )
        .sort((a, b) => a.id - b.id)
    }
  }
}
</script>

<style scoped>
.payment-methods-container {
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
}

.search-section {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.search-input {
  flex-grow: 1;
  margin-right: 10px;
  padding: 10px;
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

.button-container {
  display: flex;
  justify-content: space-evenly;
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
</style>
