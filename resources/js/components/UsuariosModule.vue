<template>
  <div class="usuarios-module">
    <div class="usuarios-header">
      <button class="btn-blue" @click="showCreate = true">
        <span class="icon">➕</span> Nuevo Usuario
      </button>
      <input v-model="search" class="search-input" placeholder="Buscar usuario..." />
    </div>
    <div v-if="loading" class="loading">Cargando usuarios...</div>
    <div v-else>
      <div v-if="filteredUsuarios.length === 0" class="empty">
        <span class="icon">🙁</span> No hay usuarios registrados.
      </div>
      <table class="usuarios-table" v-else>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in filteredUsuarios" :key="usuario.id">
            <td>{{ usuario.name }}</td>
            <td>{{ usuario.email }}</td>
            <td>
              <span class="badge">
                {{ usuario.role?.nombre || getRoleName(usuario.role_id) || usuario.role_id }}
              </span>
            </td>
            <td>
              <button class="btn-icon btn-blue" @click="editUsuario(usuario)" title="Editar">
                <span class="icon">✏️</span>
              </button>
              <button class="btn-icon btn-red" @click="deleteUsuario(usuario.id)" title="Eliminar">
                <span class="icon">🗑️</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear/Editar Usuario -->
    <div v-if="showCreate || showEdit" class="modal-bg">
      <div class="modal">
        <h2>{{ showEdit ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
        <form @submit.prevent="showEdit ? updateUsuario() : createUsuario()">
          <input v-model="usuarioForm.name" placeholder="Nombre" required />
          <input v-model="usuarioForm.email" type="email" placeholder="Email" required />
          <input v-model="usuarioForm.password" type="password" placeholder="Contraseña" :required="!showEdit" />
          <select v-model="usuarioForm.role_id" required>
            <option value="">Selecciona un rol</option>
            <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.nombre }}</option>
          </select>
          <div class="modal-actions">
            <button class="btn-blue" type="submit">{{ showEdit ? 'Actualizar' : 'Crear' }}</button>
            <button class="btn-red" type="button" @click="closeModal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: 'UsuariosModule',
  data() {
    return {
      usuarios: [],
      roles: [],
      loading: false,
      showCreate: false,
      showEdit: false,
      search: '',
      usuarioForm: {
        id: null,
        name: '',
        email: '',
        password: '',
        role_id: ''
      }
    };
  },
  computed: {
    filteredUsuarios() {
      if (!this.search) return this.usuarios;
      return this.usuarios.filter(u =>
        u.name.toLowerCase().includes(this.search.toLowerCase()) ||
        u.email.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchUsuarios();
    this.fetchRoles();
  },
  methods: {
    async fetchUsuarios() {
      this.loading = true;
      try {
        const res = await axios.get('/api/users');
        this.usuarios = res.data;
      } catch {
        this.usuarios = [];
      }
      this.loading = false;
    },
    async fetchRoles() {
      try {
        const res = await axios.get('/api/roles');
        this.roles = res.data;
      } catch {
        this.roles = [];
      }
    },
    getRoleName(role_id) {
      const rol = this.roles.find(r => r.id === role_id);
      return rol ? rol.nombre : '';
    },
    closeModal() {
      this.showCreate = false;
      this.showEdit = false;
      this.resetUsuarioForm();
    },
    resetUsuarioForm() {
      this.usuarioForm = { id: null, name: '', email: '', password: '', role_id: '' };
    },
    async createUsuario() {
      try {
        await axios.post('/api/users', this.usuarioForm);
        this.closeModal();
        this.fetchUsuarios();
        alert('Usuario creado correctamente');
      } catch (e) {
        alert('Error al crear el usuario');
      }
    },
    editUsuario(usuario) {
      this.usuarioForm = { ...usuario, password: '' };
      this.showEdit = true;
    },
    async updateUsuario() {
      try {
        const data = { ...this.usuarioForm };
        if (!data.password) delete data.password;
        await axios.put(`/api/users/${this.usuarioForm.id}`, data);
        this.closeModal();
        this.fetchUsuarios();
        alert('Usuario actualizado correctamente');
      } catch (e) {
        alert('Error al actualizar el usuario');
      }
    },
    async deleteUsuario(id) {
      if (confirm('¿Eliminar este usuario?')) {
        try {
          await axios.delete(`/api/users/${id}`);
          this.fetchUsuarios();
          alert('Usuario eliminado correctamente');
        } catch {
          alert('Error al eliminar el usuario');
        }
      }
    }
  }
};
</script>

<style scoped>
.usuarios-module {
  background: #f9fafb;
  border-radius: 1.2rem;
  padding: 2rem 2.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  max-width: 900px;
  margin: 2rem auto;
}

.usuarios-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.btn-blue {
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.7rem 1.3rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-blue:hover {
  background: #1d4ed8;
}

.btn-red {
  background: #ef4444;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.7rem 1.3rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-red:hover {
  background: #b91c1c;
}

.btn-icon {
  padding: 0.4rem 0.7rem;
  font-size: 1.1rem;
  border-radius: 0.5rem;
  margin-right: 0.2rem;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.search-input {
  padding: 0.7rem 1.1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #fff;
  transition: border 0.2s;
  width: 220px;
}

.search-input:focus {
  border: 1.5px solid #2563eb;
  outline: none;
}

.usuarios-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}

.usuarios-table th, .usuarios-table td {
  padding: 1rem 0.8rem;
  text-align: left;
}

.usuarios-table thead {
  background: #f1f5f9;
}

.usuarios-table tbody tr {
  transition: background 0.18s;
}

.usuarios-table tbody tr:hover {
  background: #e0e7ef;
}

.badge {
  background: #e0e7ef;
  color: #2563eb;
  padding: 0.3rem 0.8rem;
  border-radius: 0.7rem;
  font-size: 0.95rem;
  font-weight: 600;
}

.empty {
  text-align: center;
  color: #6b7280;
  font-size: 1.1rem;
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.7rem;
}

.loading {
  text-align: center;
  color: #2563eb;
  font-size: 1.2rem;
  padding: 2rem 0;
}

.icon {
  font-size: 1.2rem;
  vertical-align: middle;
}

.modal-bg {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(30, 41, 59, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}

.modal {
  background: #fff;
  border-radius: 1.2rem;
  padding: 2.2rem 2.5rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.13);
  min-width: 340px;
  max-width: 95vw;
}

.modal h2 {
  margin-bottom: 1.2rem;
  font-size: 1.4rem;
  font-weight: 700;
  color: #2563eb;
  text-align: center;
}

.modal form {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.modal input,
.modal select {
  padding: 0.8rem 1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #f9fafb;
  transition: border 0.2s;
}

.modal input:focus,
.modal select:focus {
  border: 1.5px solid #2563eb;
  outline: none;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 0.5rem;
}

@media (max-width: 600px) {
  .usuarios-module {
    padding: 1rem 0.3rem;
  }
  .modal {
    padding: 1.2rem 0.7rem;
    min-width: 90vw;
  }
  .usuarios-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  .search-input {
    width: 100%;
  }
}
</style>