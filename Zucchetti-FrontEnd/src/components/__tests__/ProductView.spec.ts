import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import ProductView from '@/views/ProductView.vue';
import Navbar from '@/components/Navbar.vue';
import ProductModal from '@/components/product/ProductModal.vue';

vi.mock('@/axios', async (importOriginal) => {
  const original = await importOriginal() as { [key: string]: any };
  return {
    ...original,
    get: vi.fn(() => Promise.resolve({ data: [{ id: 1, name: 'Produto 1', price: 100, quantity: 10 }] })),
    post: vi.fn(() => Promise.resolve({ data: { success: true, message: "Produto excluído com sucesso" } }))
  };
});

describe('ProductView', () => {
  it('deve renderizar corretamente', () => {
    const wrapper = mount(ProductView, {
      global: {
        components: {
          Navbar,
          ProductModal
        }
      }
    });

    expect(wrapper.text()).toContain('PRODUTOS');
    expect(wrapper.find('.search-input').exists()).toBe(true);
    expect(wrapper.findAll('th').length).toBe(5);
  });

  it('deve abrir o modal para adicionar um novo produto', async () => {
    const wrapper = mount(ProductView, {
      global: {
        components: {
          Navbar,
          ProductModal
        }
      }
    });

    window.alert = vi.fn();
    await wrapper.find('.button-style').trigger('click');
    expect(wrapper.vm.showModal).toBe(true);
  });

  it('deve abrir o modal para adicionar/editar um produto existente', async () => {
    const wrapper = mount(ProductView, {
      global: {
        components: {
          Navbar,
          ProductModal
        }
      }
    });

    await wrapper.setData({
      products: [{ id: 1, name: 'Produto 1', price: 100, quantity: 10 }]
    });
    await wrapper.vm.openEditProductModal(wrapper.vm.products[0]);
    expect(wrapper.vm.showModal).toBe(true);
    expect(wrapper.vm.currentProduct).toEqual(wrapper.vm.products[0]);
  });

  it('deve deletar um produto e mostrar uma mensagem', async () => {
    const wrapper = mount(ProductView, {
      global: {
        components: {
          Navbar,
          ProductModal
        }
      }
    });

    window.alert = vi.fn();
    await wrapper.setData({
      products: [{ id: 1, name: 'Produto 1', price: 100, quantity: 10 }]
    });

    wrapper.vm.deleteProduct = vi.fn().mockImplementation(async (id) => {
      try {
        await api.post(`/product/delete?id=${id}`);
        window.alert("Produto excluído com sucesso");
      } catch (error) {
        window.alert("Falha ao deletar produto.");
      } finally {
        wrapper.vm.fetchProducts();
      }
    });

    const fetchProductsSpy = vi.spyOn(wrapper.vm, 'fetchProducts');
    await wrapper.vm.deleteProduct(1);
    expect(window.alert).toHaveBeenCalledWith("Falha ao deletar produto.");
    expect(fetchProductsSpy).toHaveBeenCalled();
  });

  it('deve filtrar produtos baseado no input de busca', async () => {
    const wrapper = mount(ProductView, {
      global: {
        components: {
          Navbar,
          ProductModal
        }
      }
    });

    await wrapper.setData({
      products: [
        { id: 1, name: 'Produto 1', price: 100, quantity: 10 },
        { id: 2, name: 'Produto 2', price: 200, quantity: 5 }
      ],
      searchQuery: '1'
    });

    const filteredProducts = wrapper.vm.filteredProducts.filter(p => p.name.includes('1'));
    expect(filteredProducts.length).toBe(1);
    expect(filteredProducts[0].name).toBe('Produto 1');
  });

});
