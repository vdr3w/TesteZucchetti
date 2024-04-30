<template>
  <div class="modal" v-if="show">
    <div class="modal-content">
      <span class="close" @click="close">&times;</span>
      <h2>{{ product.id ? 'Editar Produto' : 'Adicionar Novo Produto' }}</h2>
      <div class="form-container">
        <form @submit.prevent="submitForm">
          <label for="name">Nome:</label>
          <input class="input-style" id="name" v-model="product.name" required />

          <label for="price">Preço:</label>
          <input class="input-style" id="price" type="number" v-model="product.price" required />

          <label for="quantity">Quantidade:</label>
          <input
            class="input-style"
            id="quantity"
            type="number"
            v-model="product.quantity"
            required
          />

          <div class="modal-buttons">
            <button class="button-style" type="submit">
              {{ product.id ? 'ATUALIZAR' : 'SALVAR' }}
            </button>
            <button class="button-style" type="button" @click="close">FECHAR</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    show: Boolean,
    product: Object
  },
  methods: {
    close() {
      this.$emit('close')
    },
    async submitForm() {
      let url = this.product.id
        ? `http://localhost:8000/product/update`
        : `http://localhost:8000/product/create`
      try {
        const response = await axios.post(url, this.product)
        alert(response.data.message)
        this.close()
        this.$emit('refresh')
      } catch (error) {
        console.error('Erro ao salvar produto:', error)
        alert('Falha ao salvar produto.')
      }
    }
  }
}
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 5px;
  width: 300px;
}

.close {
  cursor: pointer;
  position: absolute;
  top: 10px;
  right: 14px;
  font-size: 24px;
  color: var(--preto);
}

.form-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.input-style {
  width: 90%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid var(--cinza);
  border-radius: 4px;
}

.modal-buttons {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.button-style {
  background-color: var(--preto);
  color: #ffffff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: 0.3s ease;
  width: 48%;
}

.button-style:hover {
  background-color: #1d2127;
}
</style>
