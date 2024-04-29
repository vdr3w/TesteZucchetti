<template>
    <nav class="navbar">
      <ul>
        <li><router-link to="/clientes" class="nav-link">Clientes</router-link></li>
        <li><router-link to="/produtos" class="nav-link">Produtos</router-link></li>
        <li><router-link to="/metodos-de-pagamento" class="nav-link">Métodos de Pagamento</router-link></li>
        <li><router-link to="/vendas" class="nav-link">Vendas</router-link></li>
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
  background-color: #007BFF; /* Azul primário */
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
  color: white;
  font-weight: 500;
  transition: background-color 0.3s, color 0.3s; /* Suaviza a transição de cores */
  padding: 8px 18px; /* Preenchimento consistente */
}

.nav-link:hover, .nav-link.router-link-active {
  background-color: #035397; /* Azul mais escuro para fundo e borda ao passar o mouse */
  color: #FFF; /* Mantém o texto branco para alta legibilidade */
}

.nav-link.router-link-active {
  background-color: #33AFFF; /* Azul mais claro para o item ativo */
  color: #FFF; /* Mantém o texto branco para alta legibilidade */
}
</style>
