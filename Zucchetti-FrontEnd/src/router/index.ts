import { createRouter, createWebHistory } from 'vue-router';
import CustomersView from '@/views/CustomersView.vue';
import ProductView from '@/views/ProductView.vue';
import PaymentMethodView from '@/views/PaymentMethodView.vue';
import SaleView from '@/views/SaleView.vue';
import NewSaleView from '@/views/NewSaleView.vue';
import LoginView from '@/views/LoginView.vue';
import UnauthorizedView from '@/views/UnauthorizedView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: LoginView
    },
    {
      path: '/unauthorized',
      name: 'Unauthorized',
      component: UnauthorizedView
    },
    {
      path: '/clientes',
      name: 'Customers',
      component: CustomersView,
      meta: { requiresAuth: true }
    },
    {
      path: '/produtos',
      name: 'Products',
      component: ProductView,
      meta: { requiresAuth: true }
    },
    {
      path: '/metodos-de-pagamento',
      name: 'PaymentMethods',
      component: PaymentMethodView,
      meta: { requiresAuth: true }
    },
    {
      path: '/vendas',
      name: 'Sales',
      component: SaleView,
      meta: { requiresAuth: true }
    },
    {
      path: '/nova-venda',
      name: 'NewSale',
      component: NewSaleView,
      meta: { requiresAuth: true }
    }
  ]
});

router.beforeEach((to, from, next) => {
  // Verifica se a rota requer autenticação e se o token está presente
  if (to.meta.requiresAuth && !localStorage.getItem('token')) {
    // Redireciona para a página de não autorizado se não estiver autenticado
    next('/unauthorized');
  } else {
    // Continua para a rota pretendida se tudo estiver ok
    next();
  }
});

export default router;
