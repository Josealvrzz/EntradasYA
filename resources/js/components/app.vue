<template>
  <div>
    <Login v-if="!autenticado" @login-success="autenticado = true" />
    <Dashboard v-else />
  </div>
</template>

<script>
import Login from './Login.vue';
import Dashboard from './Dashboard.vue';

export default {
  components: { Login, Dashboard },
  data() {
    return {
      autenticado: !!localStorage.getItem('token') // persiste autenticación tras recarga
    };
  }
};
</script>