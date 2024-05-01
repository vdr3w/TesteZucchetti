<template>
  <div class="modal" v-if="show">
    <div class="modal-content">
      <span class="close" @click="close">&times;</span>
      <h2>{{ customer.id ? 'Editar Cliente' : 'Adicionar Novo Cliente' }}</h2>
      <div class="form-container">
        <form @submit.prevent="submitForm">
          <label for="name">Nome:</label>
          <input class="input-style" id="name" v-model="customer.name" required />

          <label for="cpf">CPF:</label>
          <input class="input-style" id="cpf" v-model="customer.cpf" required />

          <label for="email">Email:</label>
          <input class="input-style" id="email" v-model="customer.email" required />

          <label for="cep">CEP:</label>
          <input class="input-style" id="cep" v-model="customer.cep" required />

          <label for="address">Endereço:</label>
          <input class="input-style" id="address" v-model="customer.address" required />

          <div class="modal-buttons">
            <button class="button-style" type="submit">
              {{ customer.id ? 'ATUALIZAR' : 'SALVAR' }}
            </button>
            <button class="button-style" type="button" @click="close">FECHAR</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios';

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
      const onlyNumbers = cep.replace(/\D/g, '')
      return onlyNumbers.replace(/(\d{5})(\d{3})/, '$1-$2')
    },
    async submitForm() {
      this.customer.cep = this.formatCep(this.customer.cep)

      let url, method
      if (this.customer.id) {
        url = `/customer/update?id=${this.customer.id}`
        method = 'post'
      } else {
        url = '/customer/create'
        method = 'post'
      }

      try {
        const response = await api[method](url, this.customer)
        alert(response.data.message)
        this.close()
        this.$emit('refresh')
      } catch (error) {
        console.error('Erro ao salvar cliente:', error.response ? error.response.data : 'Server error')
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
