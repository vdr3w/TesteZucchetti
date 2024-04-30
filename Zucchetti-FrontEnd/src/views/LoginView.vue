<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="login">
      <input v-model="username" type="text" placeholder="Username" required>
      <input v-model="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script>
import { ref } from 'vue';
import router from '../router';

export default {
  setup() {
    const username = ref('');
    const password = ref('');

    const login = async () => {
      try {
        const response = await fetch('http://localhost:8000/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            username: username.value,
            password: password.value
          })
        });

        if (response.ok) {
          const data = await response.json();
          localStorage.setItem('@tokenzucchetti', data.token);
          router.push('/clientes');
        } else if (response.status === 401) {
          // Redireciona para a página de Não Autorizado em caso de credenciais inválidas
          router.push('/unauthorized');
        } else {
          alert('Login failed!');
        }
      } catch (error) {
        console.error('Error during login:', error);
        alert('Network error or server is down');
      }
    };

    return {
      username,
      password,
      login
    };
  }
}
</script>

<style scoped>
.login-container {
  max-width: 300px;
  margin: auto;
  padding: 20px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
</style>
