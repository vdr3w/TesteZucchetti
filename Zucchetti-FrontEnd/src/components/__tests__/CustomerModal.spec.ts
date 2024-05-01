import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import CustomerModal from '@/components/customers/CustomerModal.vue';
import axios from '@/axios';

beforeEach(() => {
  window.alert = vi.fn();
  vi.spyOn(console, 'error').mockImplementation(() => { }); 
});

describe('CustomerModal', () => {
  it('deve submeter novo cliente e emitir evento de atualização', async () => {
    vi.spyOn(axios, 'post').mockResolvedValue({ data: { success: true, message: "Cliente criado com sucesso com ID 1" } });
    const wrapper = mount(CustomerModal, {
      props: {
        show: true,
        customer: { id: null, name: 'Drew Vieira', cpf: '123.456.789-10', email: 'drew.vieira@example.com', cep: '12345-678', address: 'Rua Exemplo, 100, Cidade, Estado' }
      }
    });

    await wrapper.find('form').trigger('submit.prevent');
    await wrapper.vm.$nextTick();
    await new Promise(setImmediate);

    expect(wrapper.emitted('refresh')).toBeTruthy();
    expect(window.alert).toHaveBeenCalledWith("Cliente criado com sucesso com ID 1");
    vi.restoreAllMocks();
  });

  it('deve tratar erros ao salvar o cliente', async () => {
    vi.spyOn(axios, 'post').mockRejectedValue({ response: { data: { success: false, error: "Falha ao salvar cliente." } } });
    const wrapper = mount(CustomerModal, {
      props: {
        show: true,
        customer: { id: null, name: '', cpf: '', email: '', cep: '', address: '' }
      }
    });

    await wrapper.find('form').trigger('submit.prevent');
    await wrapper.vm.$nextTick();

    expect(window.alert).toHaveBeenCalledWith("Falha ao salvar cliente.");
    vi.restoreAllMocks();
  });
});
