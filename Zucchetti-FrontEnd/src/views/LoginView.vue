<template>
    <div class="login-wrapper">
  <div class="login-container">
    <img src="https://www.zucchettibrasil.com.br/templates/website/img/logo.png" alt="Zucchetti Logo" class="logo">
    <h1>#tamojunto</h1>
    <form @submit.prevent="login" class="form-container">
      <input v-model="username" type="text" placeholder="Username (ADMIN) *case sensitive" required class="search-input">
      <input v-model="password" type="password" placeholder="Password (ADMIN) *case sensitive" required class="search-input">
      <button class="buttons" type="submit">Login</button>
    </form>
  </div>
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
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #ADD8E6;
}

.login-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 100%;
  max-width: 400px;
}

.logo {
  width: 80%;
  max-height: 120px;
  margin-bottom: 20px;
}

.form-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.search-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid var(--preto);
  border-radius: 4px;
}

.buttons {
  width: 100%;
  padding: 10px;
  background-color: var(--azul-zucchetti);
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.3s;
}

.buttons:hover {
  background-color: darken(var(--azul-zucchetti), 10%);
}
</style>