<template>
  <div class="customers-container">
    <Navbar />
    <h1>CLIENTES</h1>
    <div class="card">
      <div class="search-section">
        <input type="text" placeholder="Pesquise por nome, cpf ou email" v-model="searchQuery" />
        <button @click="openNewCustomerModal">NOVO</button>
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
          <td>{{ customer.cpf }}</td>
          <td>{{ customer.address }}</td>
          <td>{{ customer.email }}</td>
          <td>{{ customer.cep }}</td>
          <td>
            <button @click="openEditCustomerModal(customer)">Editar</button>
            <button @click="deleteCustomer(customer.id)">Deletar</button>
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
import axios from 'axios'
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
    openNewCustomerModal() {
      this.currentCustomer = { name: '', cpf: '', email: '', cep: '', address: '' } // Dados vazios para novo cliente
      this.showModal = true
    },
    openEditCustomerModal(customer) {
      this.currentCustomer = { ...customer } // Preenche os dados com o cliente existente para edição
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    async fetchCustomers() {
      try {
        const response = await axios.get('http://localhost:8000/customer/list')
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
        const response = await axios.post(`http://localhost:8000/customer/delete?id=${id}`)
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
      return this.customers.filter(
        (customer) =>
          customer.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          customer.cpf.includes(this.searchQuery) ||
          customer.email.toLowerCase().includes(this.searchQuery.toLowerCase())
      )
    }
  }
}
</script>

<style scoped>
.customers-container {
  text-align: center;
  width: 100%;
}

.card {
  width: 80%;
  margin: auto;
  background-color: #f5f5f5;
  padding: 20px;
}

.search-section {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
</style>
