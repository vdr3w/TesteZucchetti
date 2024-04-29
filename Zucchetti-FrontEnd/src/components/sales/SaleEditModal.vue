<template>
  <div v-if="visible" class="modal">
    <div class="modal-content">
      <span @click="closeModal" class="close">&times;</span>
      <h2>Edit Sale</h2>
      <form @submit.prevent="submitForm">
        <div>
          <label>Customer ID:</label>
          <span>{{ sale.customerDetails.id }}</span>
        </div>
        <div>
          <label>Customer Name:</label>
          <span>{{ sale.customerDetails.name }}</span>
        </div>
        <div>
          <label for="paymentMethodId">Payment Method:</label>
          <select id="paymentMethodId" v-model="sale.paymentMethodId">
            <option v-for="(name, id) in paymentMethods" :key="id" :value="id">
              {{ name }}
            </option>
          </select>
        </div>
        <div v-for="(item, index) in sale.items" :key="index">
          <label>Product ID:</label>
          <input type="number" v-model="item.productId" />
          <label>Quantity:</label>
          <input type="number" v-model="item.quantity" />
        </div>
        <button type="submit">Update Sale</button>
      </form>
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
      event.preventDefault();
      event.stopPropagation();
      this.$emit('submit', this.sale);
    }
  }
}
</script>

<style scoped>
.modal {
  display: flex;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0, 0, 0);
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>
