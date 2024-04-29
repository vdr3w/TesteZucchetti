<template>
    <div class="modal" v-if="show">
      <div class="modal-content">
        <span class="close" @click="close">&times;</span>
        <h2>{{ product.id ? 'Editar Produto' : 'Adicionar Novo Produto' }}</h2>
        <form @submit.prevent="submitForm">
          <label for="name">Nome:</label>
          <input id="name" v-model="product.name" required />
  
          <label for="price">Preço:</label>
          <input id="price" type="number" v-model="product.price" required />
  
          <label for="quantity">Quantidade:</label>
          <input id="quantity" type="number" v-model="product.quantity" required />
  
          <div class="modal-buttons">
            <button type="submit">{{ product.id ? 'Atualizar' : 'Salvar' }}</button>
            <button type="button" @click="close">Fechar</button>
          </div>
        </form>
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
          : `http://localhost:8000/product/create`;
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
  }
  
  .modal-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
  }
  
  button {
    padding: 10px 20px;
  }
  </style>
  