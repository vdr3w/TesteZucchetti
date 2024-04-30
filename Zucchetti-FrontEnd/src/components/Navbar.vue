<template>
  <nav class="navbar">
    <ul>
      <li><router-link to="/clientes" class="nav-link">CLIENTES</router-link></li>
      <li><router-link to="/produtos" class="nav-link">PRODUTOS</router-link></li>
      <li>
        <router-link to="/metodos-de-pagamento" class="nav-link">MÉTODOS DE PAGAMENTO</router-link>
      </li>
      <li><router-link to="/vendas" class="nav-link">VENDAS</router-link></li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'Navbar'
}
</script>

<style scoped>
.navbar {
  font-size: larger;
  background-color: var(--azul-zucchetti);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 0.5rem 0;
}

.navbar ul {
  list-style: none;
  display: flex;
  justify-content: space-around;
  margin: 0;
  padding: 0;
}

.navbar li {
  padding: 10px 20px;
}

.nav-link {
  text-decoration: none;
  color: var(--fonte-fundo-azul);
  font-weight: 500;
  transition:
    background-color 0.3s,
    color 0.3s;
  padding: 8px 18px;
  font-family: 'Helvetica Neue', sans-serif;
}

.nav-link:hover {
  background-color: #035397;
  color: #fff;
}

.nav-link.router-link-active {
  background-color: var(--preto);
  color: #fff;
}
</style>
