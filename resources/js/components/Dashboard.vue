<template>
  <div class="dashboard">
    <!-- Header superior -->
    <header class="top-header">
      <div class="logo">🎵 EntradasYA</div>
      <div class="account-menu">
        <div class="avatar" @click="toggleMenu">
          <span>{{ userInitials }}</span>
        </div>
        <div v-if="showMenu" class="dropdown-menu">
          <div class="dropdown-user">{{ userName }}</div>
          <button class="btn-logout" @click="logout">Cerrar sesión</button>
        </div>
      </div>
    </header>

    <div class="dashboard-body">
      <!-- Sidebar -->
      <aside class="sidebar">
        <nav>
          <ul>
            <li :class="{active: current === 'dashboard'}" @click="current = 'dashboard'">
              <i class="icon">🏠</i> Dashboard
            </li>
            <li :class="{active: current === 'eventos'}" @click="current = 'eventos'">
              <i class="icon">🎫</i> Eventos
            </li>
            <li :class="{active: current === 'ventas'}" @click="current = 'ventas'">
              <i class="icon">💸</i> Ventas
            </li>
            <li :class="{active: current === 'lugares'}" @click="current = 'lugares'">
              <i class="icon">📍</i> Lugares
            </li>
            <li :class="{active: current === 'clientes'}" @click="current = 'clientes'">
              <i class="icon">🧑‍🤝‍🧑</i> Clientes
            </li>
            <li :class="{active: current === 'entradas'}" @click="current = 'entradas'">
              <i class="icon">🎟️</i> Entradas
            </li>
            <li :class="{active: current === 'gastos'}" @click="current = 'gastos'">
              <i class="icon">💼</i> Gastos
            </li>
          </ul>
          <div class="admin-section">
            <div class="admin-title">Administración</div>
            <ul>
              <li :class="{active: current === 'usuarios'}" @click="current = 'usuarios'">
                <i class="icon">👤</i> Usuarios
              </li>
              <li :class="{active: current === 'reportes'}" @click="current = 'reportes'">
                <i class="icon">📊</i> Reportes
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="main-content">
        <header class="main-header">
          <h1 v-if="current === 'dashboard'">Dashboard</h1>
          <h1 v-else-if="current === 'eventos'">Eventos Próximos</h1>
          <h1 v-else-if="current === 'ventas'">Ventas Completadas</h1>
          <h1 v-else-if="current === 'lugares'">Lugares</h1>
          <h1 v-else-if="current === 'clientes'">Clientes</h1>
          <h1 v-else-if="current === 'entradas'">Entradas</h1>
          <h1 v-else-if="current === 'gastos'">Gastos</h1>
          <h1 v-else-if="current === 'usuarios'">Usuarios</h1>
          <h1 v-else-if="current === 'reportes'">Reportes</h1>
        </header>
        <section class="content">
        <EventosModule v-if="current === 'eventos'" />
        <UsuariosModule v-if="current === 'usuarios'" />
        <VentasModule v-if="current === 'ventas'" />
        <LugaresModule v-if="current === 'lugares'" />
        <ClientesModule v-if="current === 'clientes'" />
        <EntradasModule v-if="current === 'entradas'" />
        <GastosModule v-if="current === 'gastos'" />
        <ReportesModule v-if="current === 'reportes'" />
          <!-- Aquí irá el contenido de cada módulo -->
        </section>
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import EventosModule from './EventosModule.vue';
import UsuariosModule from './UsuariosModule.vue';
import VentasModule from './VentasModule.vue';
import LugaresModule from './LugaresModule.vue';
import ClientesModule from './ClientesModule.vue';
import EntradasModule from './EntradasModule.vue';
import GastosModule from './GastosModule.vue';
import ReportesModule from './ReportesModule.vue';

export default {
  name: 'Dashboard',
  components: {
    EventosModule,
    UsuariosModule,
    VentasModule,
    LugaresModule,
    ClientesModule,
    EntradasModule,
    GastosModule,
    ReportesModule
  },
  data() {
    return {
      current: 'dashboard',
      showMenu: false,
      userName: 'Administrador'
    };
  },
  computed: {
    userInitials() {
      return this.userName.split(' ').map(n => n[0]).join('').toUpperCase();
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  },
  methods: {
    logout() {
      localStorage.removeItem('token');
      window.location.reload();
    },
    toggleMenu(event) {
      this.showMenu = !this.showMenu;
      event.stopPropagation();
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.showMenu = false;
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap');

.dashboard {
  min-height: 100vh;
  background: rgb(181, 181, 189);
  font-family: 'Montserrat', Arial, sans-serif;
  display: flex;
  flex-direction: column;
}

.top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f4f4f5;
  color: #111;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid #cbcdd1;
}

.logo {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0ea5e9;
  letter-spacing: 1px;
}

.account-menu {
  position: relative;
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.avatar {
  width: 42px;
  height: 42px;
  background: #3b82f6;
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  font-weight: 700;
  cursor: pointer;
  user-select: none;
  transition: background 0.2s;
  border: 2px solid #e5e7eb;
}

.avatar:hover {
  background: #2563eb;
}

.dropdown-menu {
  position: absolute;
  top: 54px;
  right: 0;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.7rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.06);
  min-width: 180px;
  z-index: 10;
  padding: 0.7rem 0;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.dropdown-user {
  padding: 0.7rem 1.2rem;
  font-weight: 600;
  color: #111;
  border-bottom: 1px solid #e5e7eb;
}

.btn-logout {
  background: #fff;
  color: #ef4444;
  border: none;
  border-radius: 0 0 0.7rem 0.7rem;
  padding: 0.7rem 1.2rem;
  font-weight: 600;
  cursor: pointer;
  text-align: left;
  transition: background 0.2s, color 0.2s;
}

.btn-logout:hover {
  background: #ef4444;
  color: #fff;
}

.dashboard-body {
  display: flex;
  flex: 1;
  min-height: 0;
  background: rgba(215, 215, 219, 0.94);
}

.sidebar {
  width: 220px;
  background: #fff;
  color: #111;
  display: flex;
  flex-direction: column;
  padding: 2rem 1rem 1rem 1rem;
  border-right: 1px solid #e5e7eb;
  min-height: 100%;
}

.sidebar nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar nav li {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding: 1rem 1.2rem;
  margin-bottom: 0.5rem;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 1.1rem;
  transition: background 0.2s, color 0.2s;
  color: #111;
}

.sidebar nav li.active,
.sidebar nav li:hover {
  background: #3b82f6;
  color: #fff;
}

.sidebar .icon {
  font-size: 1.3rem;
}

.admin-section {
  margin-top: 2rem;
  border-top: 1px solid #e5e7eb;
  padding-top: 1.2rem;
}

.admin-title {
  font-size: 0.95rem;
  color: #6b7280;
  font-weight: 700;
  margin-bottom: 0.7rem;
  margin-left: 0.5rem;
  letter-spacing: 1px;
  text-transform: uppercase;
}

.main-content {
  flex: 1;
  padding: 2.5rem 2rem;
  background: #fff;
  min-height: 100vh;
}

.main-header h1 {
  font-size: 2.2rem;
  font-weight: 700;
  color: #111;
  margin-bottom: 2rem;
  letter-spacing: 1px;
}

@media (max-width: 900px) {
  .dashboard-body {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    flex-direction: row;
    border-radius: 0 0 2rem 2rem;
    padding: 1rem 0.5rem;
    justify-content: center;
    min-height: unset;
  }
  .sidebar nav ul {
    display: flex;
    flex-direction: row;
    gap: 0.5rem;
  }
  .sidebar nav li {
    margin-bottom: 0;
    padding: 0.7rem 1rem;
    font-size: 1rem;
  }
  .main-content {
    padding: 1.5rem 0.5rem;
  }
}
</style>