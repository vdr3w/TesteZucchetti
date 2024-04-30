<template>
  <div class="modal" v-if="show">
    <div class="modal-content">
      <span class="close" @click="close">&times;</span>
      <h2>{{ customer.id ? 'Editar Cliente' : 'Adicionar Novo Cliente' }}</h2>
      <form @submit.prevent="submitForm">
        <label for="name">Nome:</label>
        <input id="name" v-model="customer.name" required />

        <label for="cpf">CPF:</label>
        <input id="cpf" v-model="customer.cpf" required />

        <label for="email">Email:</label>
        <input id="email" v-model="customer.email" required />

        <label for="cep">CEP:</label>
        <input id="cep" v-model="customer.cep" required />

        <label for="address">Endereço:</label>
        <input id="address" v-model="customer.address" required />

        <div class="modal-buttons">
          <button type="submit">{{ customer.id ? 'Atualizar' : 'Salvar' }}</button>
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
    customer: Object
  },
  methods: {
    close() {
      this.$emit('close')
    },
    formatCep(cep) {
      const onlyNumbers = cep.replace(/\D/g, '');
      return onlyNumbers.replace(/(\d{5})(\d{3})/, '$1-$2');
    },
    async submitForm() {
      this.customer.cep = this.formatCep(this.customer.cep);

      let url, method
      if (this.customer.id) {
        // Se 'id' existe, atualize o cliente
        url = `http://localhost:8000/customer/update?id=${this.customer.id}`
        method = 'post'
      } else {
        // Se 'id' não existe, crie um novo cliente
        url = 'http://localhost:8000/customer/create'
        method = 'post'
      }

      try {
        const response = await axios[method](url, this.customer)
        alert(response.data.message)
        this.close()
        this.$emit('refresh')
      } catch (error) {
        console.error('Erro ao salvar cliente:', error)
        alert('Falha ao salvar cliente.')
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
