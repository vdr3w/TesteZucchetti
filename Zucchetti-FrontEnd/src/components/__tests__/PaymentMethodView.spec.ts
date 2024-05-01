import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import PaymentMethodView from '@/views/PaymentMethodView.vue'
import Navbar from '@/components/Navbar.vue'
import PaymentMethodModal from '@/components/paymentmethod/PaymentMethodModal.vue'

vi.mock('@/axios', async (importOriginal) => {
  const original = await importOriginal() as { [key: string]: any };
  return {
    ...original,
    get: vi.fn(() => Promise.resolve({ data: [{ id: 1, name: 'Crédito', installments: 12 }] })),
    post: vi.fn((url) => {
      if (url.includes('delete')) {
        return Promise.reject({ data: { message: "Falha ao deletar método de pagamento." } });
      }
      return Promise.resolve({ data: { message: "Método de pagamento deletado com sucesso" } });
    })
  }
});

describe('PaymentMethodView', () => {
  it('deve renderizar corretamente', () => {
    const wrapper = mount(PaymentMethodView, {
      global: {
        components: {
          Navbar,
          PaymentMethodModal
        }
      }
    })

    expect(wrapper.text()).toContain('MÉTODOS DE PAGAMENTO')
    expect(wrapper.find('.search-input').exists()).toBe(true)
    expect(wrapper.findAll('th').length).toBe(4)
  })

  it('deve abrir o modal para criar/editar um novo método de pagamento', async () => {
    const wrapper = mount(PaymentMethodView, {
      global: {
        components: {
          Navbar,
          PaymentMethodModal
        }
      }
    })

    await wrapper.find('.button-style').trigger('click')
    expect(wrapper.vm.showModal).toBe(true)
  })

  it('deve deletar um método de pagamento e mostrar uma mensagem', async () => {
    const wrapper = mount(PaymentMethodView, {
      global: {
        components: {
          Navbar,
          PaymentMethodModal
        }
      }
    })

    window.alert = vi.fn()
    await wrapper.setData({
      methods: [{ id: 1, name: 'Crédito', installments: 12 }]
    })
    await wrapper.vm.deletePaymentMethod(1)
    expect(window.alert).toHaveBeenCalledWith("Falha ao deletar método de pagamento.");
  })

  it('deve filtrar métodos de pagamento baseado no input de busca', async () => {
    const wrapper = mount(PaymentMethodView, {
      global: {
        components: {
          Navbar,
          PaymentMethodModal
        }
      }
    })

    await wrapper.setData({
      methods: [
        { id: 1, name: 'Crédito', installments: 12 },
        { id: 2, name: 'Débito', installments: 1 }
      ],
      searchQuery: 'créd'
    })

    const filteredMethods = wrapper.vm.filteredMethods;
    expect(filteredMethods.length).toBe(1);
    expect(filteredMethods[0].name).toBe('Crédito');
  })
})
