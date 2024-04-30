<template>
    <div class="payment-methods-container">
        <Navbar />
      <h1>MÉTODOS DE PAGAMENTO</h1>
      <div class="card">
        <div class="search-section">
          <input type="text" placeholder="Pesquise por nome ou parcelas" v-model="searchQuery" />
          <button @click="openNewPaymentMethodModal">NOVO</button>
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
            <td>
              <button @click="openEditPaymentMethodModal(method)">Editar</button>
              <button @click="deletePaymentMethod(method.id)">Deletar</button>
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
  import axios from 'axios'
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
      };
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
          const response = await axios.get('http://localhost:8000/payment-method/list')
          this.methods = response.data
        } catch (error) {
          console.error('Erro ao buscar métodos de pagamento:', error)
        }
      },
      async deletePaymentMethod(id) {
        try {
          const response = await axios.post(`http://localhost:8000/payment-method/delete?id=${id}`)
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
        return this.methods.filter(
          (method) =>
            method.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            method.installments.toString().includes(this.searchQuery)
        )
      }
    }
  }
  </script>
  
  <style scoped>
  .payment-methods-container {
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
  