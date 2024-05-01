import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import CustomersView from '@/views/CustomersView.vue'
import Navbar from '@/components/Navbar.vue'
import CustomerModal from '@/components/customers/CustomerModal.vue'

vi.mock('@/axios', async (importOriginal) => {
  const original = await importOriginal() as { [key: string]: any };
  return {
    ...original,
    get: vi.fn(() => Promise.resolve({ data: [] })),
    post: vi.fn(() => Promise.resolve({ data: { message: "Cliente deletado com sucesso" } }))
  }
})

describe('CustomersView', () => {
  it('deve renderizar corretamente', () => {
    const wrapper = mount(CustomersView, {
      global: {
        components: {
          Navbar,
          CustomerModal
        }
      }
    })

    expect(wrapper.text()).toContain('CLIENTES')
    expect(wrapper.find('.search-input').exists()).toBe(true)
    expect(wrapper.findAll('th').length).toBe(7)
  })

  it('deve abrir o modal para criar/editar um novo cliente', async () => {
    const wrapper = mount(CustomersView, {
      global: {
        components: {
          Navbar,
          CustomerModal
        }
      }
    })

    await wrapper.find('.button-style').trigger('click')
    expect(wrapper.vm.showModal).toBe(true)
  })
})
