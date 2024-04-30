<template>
  <div class="modal" v-if="show">
    <div class="modal-content">
      <span class="close" @click="close">&times;</span>
      <h2>{{ method.id ? 'Editar Método' : 'Adicionar Novo Método' }}</h2>
      <div class="form-container">
          <form @submit.prevent="submitForm">
            <label for="name">Nome:</label>
            <input class="input-style" id="name" v-model="method.name" required />
            
            <label for="installments">Parcelas Máximas:</label>
            <input class="input-style"  id="installments" type="number" v-model="method.installments" required />
            
            <div class="modal-buttons">
                <button class="button-style" type="submit">{{ method.id ? 'ATUALIZAR' : 'SALVAR' }}</button>
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
    method: Object
  },
  methods: {
    close() {
      this.$emit('close')
    },
    async submitForm() {
      let url = this.method.id
        ? `http://localhost:8000/payment-method/update?id=${this.method.id}`
        : `http://localhost:8000/payment-method/create`

      try {
        const response = await axios.post(url, {
          name: this.method.name,
          installments: this.method.installments
        })
        if (response.data.success) {
          alert(response.data.message)
          this.close()
          this.$emit('refresh')
        } else {
          alert('Erro: ' + response.data.error)
        }
      } catch (error) {
        console.error('Erro ao salvar método de pagamento:', error)
        alert('Falha ao salvar método de pagamento.')
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

.form-container { /* Centralização do formulário */
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.input-style { /* Estilo aplicado nos inputs */
  width: 90%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid var(--cinza);
  border-radius: 4px;
}

.modal-buttons { /* Estilo aplicado nos botões */
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.button-style { /* Estilo uniforme para os botões */
  background-color: var(--preto);
  color: #ffffff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: 0.3s ease;
  width: 48%; /* Ajuste para manter os botões lado a lado com espaço */
}

.button-style:hover {
  background-color: #1d2127;
}
</style>