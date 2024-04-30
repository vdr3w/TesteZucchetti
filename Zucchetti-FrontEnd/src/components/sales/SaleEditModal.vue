<template>
  <div v-if="visible" class="modal">
    <div class="modal-content">
      <span @click="closeModal" class="close">&times;</span>
      <h2>EDITAR VENDA</h2>
      <div class="form-container">
        <form @submit.prevent="submitForm">
          <div class="text-edit">
            <label>ID do Cliente: </label>
            <span>{{ sale.customerDetails.id }}</span>
          </div>
          <div class="text-edit">
            <label>Nome: </label>
            <span>{{ sale.customerDetails.name }}</span>
          </div>
          <div>
            <label for="paymentMethodId">Método de Pagamento</label>
            <select class="input-style" id="paymentMethodId" v-model="sale.paymentMethodId">
              <option v-for="(name, id) in paymentMethods" :key="id" :value="id">
                {{ name }}
              </option>
            </select>
          </div>
          <div v-for="(item, index) in sale.items" :key="index">
            <label>Produto [ID]:</label>
            <input class="input-style" type="number" v-model="item.productId" />
            <label>Quantidade:</label>
            <input class="input-style" type="number" v-model="item.quantity" />
          </div>
          <div class="modal-buttons">
            <button class="button-style" type="submit">ATUALIZAR VENDA</button>
            <button class="button-style" type="button" @click="closeModal">FECHAR</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visible: Boolean,
    sale: Object,
    paymentMethods: Object
  },
  methods: {
    closeModal() {
      this.$emit('close')
    },
    submitForm(event) {
      event.preventDefault()
      event.stopPropagation()
      this.$emit('submit', this.sale)
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

.text-edit {
  margin-bottom: 10px;
}
</style>
