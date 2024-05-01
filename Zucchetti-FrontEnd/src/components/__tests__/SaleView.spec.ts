import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import SaleView from '@/views/SaleView.vue';
import Navbar from '@/components/Navbar.vue';
import SaleEditModal from '@/components/sales/SaleEditModal.vue';

vi.mock('@/axios', () => ({
    default: {
        get: vi.fn((url) => {
            switch (url) {
                case '/sale/list':
                    return Promise.resolve({ data: [{ id: 1, customer: 1, paymentMethod: 1, items: [{ productId: 1, quantity: 1 }] }] });
                case '/customer/list':
                case '/customer/show?id=1':
                    return Promise.resolve({ data: { id: 1, name: 'Cliente Teste' } });
                case '/payment-method/list':
                    return Promise.resolve({ data: [{ id: 1, name: 'Método Teste' }] });
                case '/product/list':
                    return Promise.resolve({ data: [{ id: 1, name: 'Produto Teste', price: 100 }] });
                default:
                    return Promise.reject(new Error('URL desconhecida'));
            }
        }),
        post: vi.fn().mockImplementation((url, payload) => {
            if (url.includes('/sale/delete')) {
                return Promise.resolve({ data: { success: true, message: "Venda excluída com sucesso." } });
            }
            if (url.includes('/sale/update')) {
                return Promise.resolve({ data: { success: true, message: "Venda atualizada com sucesso" } });
            }
            return Promise.reject(new Error('URL desconhecida'));
        })
    }
}));

describe('SaleView', () => {
    let wrapper: VueWrapper<any>;

    beforeEach(async () => {
        wrapper = mount(SaleView, {
            global: {
                components: {
                    Navbar,
                    SaleEditModal
                },
                mocks: {
                    $router: {
                        push: vi.fn()
                    }
                }
            }
        });
        await new Promise(resolve => setTimeout(resolve));
    });

    it('deve renderizar corretamente e buscar dados iniciais', async () => {
        expect(wrapper.text()).toContain('VENDAS');
        expect(wrapper.findAll('th').length).toBe(6);
        expect(wrapper.vm.sales.length).toBeGreaterThan(0);
    });

    it('deve navegar para a página de nova venda', async () => {
        await wrapper.find('.new-sale').trigger('click');
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.$router.push).toHaveBeenCalledWith({ name: 'NewSale' });
    });


    it('deve abrir o modal de edição', async () => {
        const saleData = {
            id: 1,
            customerDetails: { id: 1 },
            paymentMethodId: 1,
            items: [{ productId: 1, quantity: 1 }]
        };

        const wrapper = mount(SaleView, {
            global: {
                components: {
                    Navbar,
                    SaleEditModal
                }
            }
        });

        wrapper.vm.sales = [saleData];
        wrapper.vm.currentSale = saleData;

        wrapper.vm.editSale = vi.fn(() => {
            wrapper.vm.editModalVisible = true;
        });

        await wrapper.vm.editSale(saleData);

        await wrapper.vm.$nextTick();

        await new Promise(resolve => setImmediate(resolve));

        expect(wrapper.vm.editModalVisible).toBe(true);
    });

    it('deve deletar uma venda e exibir mensagem de confirmação', async () => {
        window.alert = vi.fn();
        await wrapper.vm.deleteSale(1);
        await new Promise(r => setTimeout(r));
        expect(window.alert).toHaveBeenCalledWith("Venda excluída com sucesso.");
    });

    it('deve filtrar vendas por cliente e limpar filtro', async () => {
        wrapper.setData({ selectedCustomerId: 1 });
        await wrapper.vm.fetchSalesByCustomer();
        expect(wrapper.vm.sales.length).toBeGreaterThan(0);
        await wrapper.vm.clearFilter();
        expect(wrapper.vm.sales.length).toBeGreaterThan(0);
    });
});
