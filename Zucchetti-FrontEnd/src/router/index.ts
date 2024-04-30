import CustomersView from '@/views/CustomersView.vue';
import ProductView from '@/views/ProductView.vue';
import PaymentMethodView from '@/views/PaymentMethodView.vue';
import SaleView from '@/views/SaleView.vue';
import NewSaleView from '@/views/NewSaleView.vue';

import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/clientes',
      name: 'Customers',
      component: CustomersView
    },
    {
      path: '/produtos',
      name: 'Products',
      component: ProductView
    },
    {
      path: '/metodos-de-pagamento',
      name: 'PaymentMethods',
      component: PaymentMethodView
    },
    {
      path: '/vendas',
      name: 'Sales',
      component: SaleView
    },
    {
      path: '/nova-venda',
      name: 'NewSale',
      component: NewSaleView
    }
  ]
})

export default router
