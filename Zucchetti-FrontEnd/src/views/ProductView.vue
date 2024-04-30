<template>
  <div class="products-container">
    <Navbar />
    <h1>PRODUTOS</h1>
    <div class="card">
      <div class="search-section">
        <input
          type="text"
          placeholder="PESQUISE POR NOME, ID OU QUANTIDADE"
          v-model="searchQuery"
          class="search-input"
        />
        <button class="button-style" @click="openNewProductModal">NOVO</button>
      </div>
      <table>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Preço</th>
          <th>Quantidade</th>
          <th>Ações</th>
        </tr>
        <tr v-for="product in filteredProducts" :key="product.id">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>R$ {{ product.price.toFixed(2) }}</td>
          <td>{{ product.quantity }}</td>
          <td class="button-container">
            <button class="button-style" @click="openEditProductModal(product)">EDITAR</button>
            <button class="button-style" @click="deleteProduct(product.id)">DELETAR</button>
          </td>
        </tr>
      </table>
    </div>
    <product-modal
      :show="showModal"
      @close="closeModal"
      :product="currentProduct"
      @refresh="fetchProducts"
    ></product-modal>
  </div>
</template>

<script>
import axios from 'axios'
import ProductModal from '@/components/product/ProductModal.vue'
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'ProductView',
  components: {
    Navbar,
    ProductModal
  },
  data() {
    return {
      products: [],
      searchQuery: '',
      showModal: false,
      currentProduct: null
    }
  },
  created() {
    this.fetchProducts()
  },
  methods: {
    openNewProductModal() {
      this.currentProduct = { name: '', price: 0, quantity: 0 }
      this.showModal = true
    },
    openEditProductModal(product) {
      this.currentProduct = { ...product }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    async fetchProducts() {
      try {
        const response = await axios.get('http://localhost:8000/product/list')
        this.products = response.data
      } catch (error) {
        console.error('Erro ao buscar produtos:', error)
      }
    },
    async deleteProduct(id) {
      try {
        const response = await axios.post(`http://localhost:8000/product/delete?id=${id}`)
        alert(response.data.message)
        this.fetchProducts()
      } catch (error) {
        console.error('Erro ao deletar produto:', error)
        alert('Falha ao deletar produto.')
      }
    }
  },
  computed: {
    filteredProducts() {
      return this.products
        .filter(
          (product) =>
            product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            product.quantity >= parseInt(this.searchQuery) ||
            isNaN(parseInt(this.searchQuery))
        )
        .sort((a, b) => a.id - b.id)
    }
  }
}
</script>

<style scoped>
.products-container {
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
