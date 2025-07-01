<template>
  <div class="lugares-module">
    <div class="lugares-header">
      <button class="btn-blue" @click="showCreate = true">
        <span class="icon">➕</span> Nuevo Lugar
      </button>
      <input v-model="search" class="search-input" placeholder="Buscar lugar..." />
    </div>
    <div v-if="loading" class="loading">Cargando lugares...</div>
    <div v-else>
      <div v-if="filteredLugares.length === 0" class="empty">
        <span class="icon">🙁</span> No hay lugares registrados.
      </div>
      <table class="lugares-table" v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Capacidad</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lugar in filteredLugares" :key="lugar.id">
            <td>{{ lugar.id }}</td>
            <td>{{ lugar.nombre }}</td>
            <td>{{ lugar.direccion }}</td>
            <td>{{ lugar.capacidad }}</td>
            <td>
              <button class="btn-icon btn-blue" @click="editLugar(lugar)" title="Editar">
                <span class="icon">✏️</span>
              </button>
              <button class="btn-icon btn-red" @click="deleteLugar(lugar.id)" title="Eliminar">
                <span class="icon">🗑️</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear/Editar Lugar -->
    <div v-if="showCreate || showEdit" class="modal-bg">
      <div class="modal">
        <h2>{{ showEdit ? 'Editar Lugar' : 'Nuevo Lugar' }}</h2>
        <form @submit.prevent="showEdit ? updateLugar() : createLugar()">
          <input v-model="lugarForm.nombre" placeholder="Nombre" required />
          <input v-model="lugarForm.direccion" placeholder="Dirección" required />
          <input v-model="lugarForm.capacidad" type="number" min="1" placeholder="Capacidad" required />
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
  name: 'LugaresModule',
  data() {
    return {
      lugares: [],
      loading: false,
      showCreate: false,
      showEdit: false,
      search: '',
      lugarForm: {
        id: null,
        nombre: '',
        direccion: '',
        capacidad: ''
      }
    };
  },
  computed: {
    filteredLugares() {
      if (!this.search) return this.lugares;
      return this.lugares.filter(l =>
        (l.nombre || '').toLowerCase().includes(this.search.toLowerCase()) ||
        (l.direccion || '').toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchLugares();
  },
  methods: {
    async fetchLugares() {
      this.loading = true;
      try {
        const res = await axios.get('/api/lugares');
        this.lugares = res.data;
      } catch {
        this.lugares = [];
      }
      this.loading = false;
    },
    closeModal() {
      this.showCreate = false;
      this.showEdit = false;
      this.resetLugarForm();
    },
    resetLugarForm() {
      this.lugarForm = { id: null, nombre: '', direccion: '', capacidad: '' };
    },
    async createLugar() {
      try {
        await axios.post('/api/lugares', this.lugarForm);
        this.closeModal();
        this.fetchLugares();
        alert('Lugar creado correctamente');
      } catch {
        alert('Error al crear el lugar');
      }
    },
    editLugar(lugar) {
      this.lugarForm = { ...lugar };
      this.showEdit = true;
    },
    async updateLugar() {
      try {
        await axios.put(`/api/lugares/${this.lugarForm.id}`, this.lugarForm);
        this.closeModal();
        this.fetchLugares();
        alert('Lugar actualizado correctamente');
      } catch {
        alert('Error al actualizar el lugar');
      }
    },
    async deleteLugar(id) {
      if (confirm('¿Eliminar este lugar?')) {
        try {
          await axios.delete(`/api/lugares/${id}`);
          this.fetchLugares();
          alert('Lugar eliminado correctamente');
        } catch {
          alert('Error al eliminar el lugar');
        }
      }
    }
  }
};
</script>

<style scoped>
.lugares-module {
  background: #f9fafb;
  border-radius: 1.2rem;
  padding: 2rem 2.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  max-width: 900px;
  margin: 2rem auto;
}
.lugares-header {
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
.lugares-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.lugares-table th, .lugares-table td {
  padding: 1rem 0.8rem;
  text-align: left;
}
.lugares-table thead {
  background: #f1f5f9;
}
.lugares-table tbody tr {
  transition: background 0.18s;
}
.lugares-table tbody tr:hover {
  background: #e0e7ef;
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
.modal input {
  padding: 0.8rem 1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #f9fafb;
  transition: border 0.2s;
}
.modal input:focus {
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
  .lugares-module {
    padding: 1rem 0.3rem;
  }
  .modal {
    padding: 1.2rem 0.7rem;
    min-width: 90vw;
  }
  .lugares-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  .search-input {
    width: 100%;
  }
}
</style>