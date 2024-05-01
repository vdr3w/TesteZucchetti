<template>
  <div class="customers-container">
    <Navbar />
    <h1>CLIENTES</h1>
    <div class="card">
      <div class="search-section">
        <input
          id="search"
          type="text"
          placeholder="PESQUISE POR NOME, CPF OU E-MAIL"
          v-model="searchQuery"
          class="search-input"
        />
        <button class="button-style" @click="openNewCustomerModal">NOVO</button>
      </div>
      <table>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>CPF</th>
          <th>Endereço</th>
          <th>Email</th>
          <th>CEP</th>
          <th>Ações</th>
        </tr>
        <tr v-for="customer in filteredCustomers" :key="customer.id">
          <td>{{ customer.id }}</td>
          <td>{{ customer.name }}</td>
          <td>{{ formatCpf(customer.cpf) }}</td>
          <td>{{ customer.address }}</td>
          <td>{{ customer.email }}</td>
          <td>{{ customer.cep }}</td>
          <td class="button-container">
            <button class="button-style" @click="openEditCustomerModal(customer)">EDITAR</button>
            <button class="button-style" @click="deleteCustomer(customer.id)">DELETAR</button>
          </td>
        </tr>
      </table>
    </div>
    <customer-modal
      :show="showModal"
      @close="closeModal"
      :customer="currentCustomer"
      @refresh="fetchCustomers"
    ></customer-modal>
  </div>
</template>

<script>
import api from '@/axios';
import CustomerModal from '@/components/customers/CustomerModal.vue'
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'CustomersView',
  components: {
    Navbar,
    CustomerModal
  },
  data() {
    return {
      customers: [],
      searchQuery: '',
      showModal: false,
      currentCustomer: null
    }
  },
  created() {
    this.fetchCustomers()
  },
  methods: {
    formatCpf(cpf) {
      return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
    },
    openNewCustomerModal() {
      this.currentCustomer = { name: '', cpf: '', email: '', cep: '', address: '' }
      this.showModal = true
    },
    openEditCustomerModal(customer) {
      this.currentCustomer = { ...customer }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    async fetchCustomers() {
      try {
        const response = await api.get('/customer/list')
        this.customers = response.data
      } catch (error) {
        console.error('Erro ao buscar clientes:', error)
      }
    },
    async editCustomer(customer) {
      this.openModal(customer)
    },
    async deleteCustomer(id) {
      try {
        const response = await api.post(`/customer/delete?id=${id}`)
        alert(response.data.message)
        this.fetchCustomers()
      } catch (error) {
        console.error('Erro ao deletar cliente:', error)
        alert('Falha ao deletar cliente.')
      }
    }
  },
  computed: {
    filteredCustomers() {
      return this.customers
        .filter(
          (customer) =>
            customer.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            customer.cpf.includes(this.searchQuery) ||
            customer.email.toLowerCase().includes(this.searchQuery.toLowerCase())
        )
        .sort((a, b) => a.id - b.id)
    }
  }
}
</script>

<style scoped>
.customers-container {
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
