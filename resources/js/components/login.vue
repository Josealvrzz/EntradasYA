<template>
  <div class="login-container">
    <form class="login-form" @submit.prevent="login" v-if="!autenticado">
      <h2>Iniciar Sesión</h2>
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Contraseña" required />
      <button type="submit" :disabled="loading">
        <span v-if="loading">Entrando...</span>
        <span v-else>Entrar</span>
      </button>
      <div v-if="error" class="error">{{ error }}</div>
    </form>
    <div v-else class="success">
      ¡Usuario autenticado correctamente!
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      autenticado: false,
      loading: false
    };
  },
  methods: {
    async login() {
  this.error = '';
  this.loading = true;
  try {
    await axios.get('/sanctum/csrf-cookie');
    const response = await axios.post('/api/login', {
      email: this.email,
      password: this.password
    });
    // Suponiendo que el backend devuelve { access_token, user: { ... } }
    if (response.data.user && response.data.user.role_id === 1) {
      localStorage.setItem('token', response.data.access_token);
      this.$emit('login-success');
    } else {
      this.error = 'Credenciales incorrectas';
    }
  } catch (e) {
    this.error = 'Credenciales incorrectas';
  } finally {
    this.loading = false;
  }
}
  }
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
}

.login-form {
  background: #111;
  color: #fff;
  padding: 2.5rem 2rem;
  border-radius: 1.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  width: 100%;
  max-width: 350px;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.login-form h2 {
  margin-bottom: 0.5rem;
  font-weight: 700;
  letter-spacing: 1px;
  text-align: center;
}

.login-form input {
  padding: 0.9rem 1rem;
  border: none;
  border-radius: 0.8rem;
  background: #222;
  color: #fff;
  font-size: 1rem;
  outline: none;
  transition: background 0.2s;
}

.login-form input:focus {
  background: #333;
}

.login-form button {
  padding: 0.9rem 1rem;
  border: none;
  border-radius: 0.8rem;
  background: #fff;
  color: #111;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.login-form button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-form button:hover:enabled {
  background: #222;
  color: #fff;
}

.error {
  color: #ff4d4f;
  background: #222;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  text-align: center;
  font-size: 0.95rem;
}

.success {
  background: #111;
  color: #fff;
  padding: 2rem 2.5rem;
  border-radius: 1.5rem;
  font-size: 1.2rem;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

@media (max-width: 500px) {
  .login-form, .success {
    padding: 2rem 1rem;
    max-width: 95vw;
  }
}
</style>