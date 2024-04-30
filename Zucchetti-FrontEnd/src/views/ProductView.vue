<template>
  <div class="products-container">
    <Navbar />
    <h1>PRODUTOS</h1>
    <div class="card">
      <div class="search-section">
        <input
          type="text"
          placeholder="Pesquise por nome, código ou quantidade"
          v-model="searchQuery"
        />
        <button @click="openNewProductModal">NOVO</button>
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
          <td>{{ product.price }}</td>
          <td>{{ product.quantity }}</td>
          <td>
            <button @click="openEditProductModal(product)">Editar</button>
            <button @click="deleteProduct(product.id)">Deletar</button>
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
      return this.products.filter(
        (product) =>
          product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          product.quantity.toString().includes(this.searchQuery)
      )
    }
  }
}
</script>

<style scoped>
.products-container {
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
