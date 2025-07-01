<template>
  <div class="eventos-module">
    <h2 class="titulo">Gestión de Eventos</h2>
    <div class="eventos-header">
      <button class="btn-blue" @click="showCreate = true">
        <span class="icon">➕</span> Nuevo Evento
      </button>
      <input v-model="search" class="search-input" placeholder="Buscar evento..." />
    </div>
    <div v-if="loading" class="loading">Cargando eventos...</div>
    <div v-else>
      <div v-if="filteredEventos.length === 0" class="empty">
        <span class="icon">🙁</span> No hay eventos disponibles.
      </div>
      <table class="eventos-table" v-else>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>Lugar</th>
            <th>Capacidad</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="evento in filteredEventos" :key="evento.id">
            <td>{{ evento.nombre }}</td>
            <td>{{ evento.fecha ? evento.fecha.substring(0, 10) : '' }}</td>
            <td>{{ evento.lugar?.nombre || 'Sin asignar' }}</td>
            <td>{{ evento.capacidad }}</td>
            <td>{{ evento.estado || 'Activo' }}</td>
            <td>
              <button class="btn-icon btn-blue" @click="editEvento(evento)" title="Editar">
                <span class="icon">✏️</span>
              </button>
              <button class="btn-icon btn-red" @click="deleteEvento(evento.id)" title="Eliminar">
                <span class="icon">🗑️</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear/Editar Evento -->
    <div v-if="showCreate || showEdit" class="modal-bg">
      <div class="modal">
        <h2>{{ showEdit ? 'Editar Evento' : 'Nuevo Evento' }}</h2>
        <form @submit.prevent="showEdit ? updateEvento() : createEvento()">
          <input v-model="eventoForm.nombre" placeholder="Nombre del evento" required />
          <input v-model="eventoForm.fecha" type="date" required />
          <select v-model.number="eventoForm.lugar_id" required>
            <option value="">Selecciona un lugar</option>
            <option v-for="lugar in lugares" :key="lugar.id" :value="lugar.id">
              {{ lugar.nombre }} (Capacidad: {{ lugar.capacidad }})
            </option>
          </select>
          <input v-model="eventoForm.capacidad" type="number" min="1" placeholder="Capacidad" required />
          <textarea v-model="eventoForm.descripcion" placeholder="Descripción"></textarea>
          <select v-model="eventoForm.estado" required>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
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
  name: 'EventosModule',
  data() {
    return {
      eventos: [],
      lugares: [],
      loading: false,
      showCreate: false,
      showEdit: false,
      search: '',
      eventoForm: {
        id: null,
        nombre: '',
        fecha: '',
        lugar_id: null,
        capacidad: '',
        descripcion: '',
        estado: 'Activo'
      }
    };
  },
  computed: {
    filteredEventos() {
      if (!this.search) return this.eventos;
      return this.eventos.filter(e =>
        (e.nombre || '').toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchEventos();
    this.fetchLugares();
  },
  methods: {
    async fetchEventos() {
      this.loading = true;
      try {
        const res = await axios.get('/api/eventos');
        this.eventos = res.data;
      } catch {
        this.eventos = [];
      }
      this.loading = false;
    },
    async fetchLugares() {
      try {
        const res = await axios.get('/api/lugares');
        this.lugares = res.data;
      } catch {
        this.lugares = [];
      }
    },
    closeModal() {
      this.showCreate = false;
      this.showEdit = false;
      this.resetEventoForm();
    },
    resetEventoForm() {
      this.eventoForm = {
        id: null,
        nombre: '',
        fecha: '',
        lugar_id: null,
        capacidad: '',
        descripcion: '',
        estado: 'Activo'
      };
    },
    async createEvento() {
      if (!this.eventoForm.lugar_id || isNaN(this.eventoForm.lugar_id)) {
        alert('Debes seleccionar un lugar');
        return;
      }
      try {
        const payload = {
          nombre: this.eventoForm.nombre,
          fecha: this.eventoForm.fecha,
          lugar_id: this.eventoForm.lugar_id,
          capacidad: this.eventoForm.capacidad,
          descripcion: this.eventoForm.descripcion,
          estado: this.eventoForm.estado
        };
        await axios.post('/api/eventos', payload);
        this.closeModal();
        this.fetchEventos();
      } catch (e) {
        console.log(e.response?.data);
        alert('Error al crear el evento');
      }
    },
    editEvento(evento) {
      this.eventoForm = {
        id: evento.id,
        nombre: evento.nombre,
        fecha: evento.fecha ? evento.fecha.substring(0, 10) : '',
        lugar_id: evento.lugar?.id ?? null,
        capacidad: evento.capacidad,
        descripcion: evento.descripcion,
        estado: evento.estado || 'Activo'
      };
      this.showEdit = true;
    },
    async updateEvento() {
      if (!this.eventoForm.lugar_id || isNaN(this.eventoForm.lugar_id)) {
        alert('Debes seleccionar un lugar');
        return;
      }
      try {
        const payload = {
          nombre: this.eventoForm.nombre,
          fecha: this.eventoForm.fecha,
          lugar_id: this.eventoForm.lugar_id,
          capacidad: this.eventoForm.capacidad,
          descripcion: this.eventoForm.descripcion,
          estado: this.eventoForm.estado
        };
        await axios.put(`/api/eventos/${this.eventoForm.id}`, payload);
        this.closeModal();
        this.fetchEventos();
      } catch (e) {
        console.log(e.response?.data);
        alert('Error al actualizar el evento');
      }
    },
    async deleteEvento(id) {
      if (confirm('¿Eliminar este evento?')) {
        try {
          await axios.delete(`/api/eventos/${id}`);
          this.fetchEventos();
        } catch {
          alert('Error al eliminar el evento');
        }
      }
    }
  }
};
</script>

<style scoped>
.eventos-module {
  background: #f9fafb;
  border-radius: 1.2rem;
  padding: 2rem 2.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  max-width: 900px;
  margin: 2rem auto;
}
.titulo {
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
  color: #2563eb;
  margin-bottom: 2rem;
}
.eventos-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 1rem;
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
.eventos-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.eventos-table th, .eventos-table td {
  padding: 1rem 0.8rem;
  text-align: left;
}
.eventos-table thead {
  background: #f1f5f9;
}
.eventos-table tbody tr {
  transition: background 0.18s;
}
.eventos-table tbody tr:hover {
  background: #e0e7ef;
}
.loading {
  color: #2563eb;
  font-size: 1.2rem;
  text-align: center;
  margin-top: 2rem;
}
.empty {
  color: #6b7280;
  font-size: 1.1rem;
  text-align: center;
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.7rem;
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
.modal input, .modal textarea, .modal select {
  padding: 0.8rem 1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #f9fafb;
  transition: border 0.2s;
}
.modal input:focus, .modal textarea:focus, .modal select:focus {
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
  .eventos-module {
    padding: 1rem 0.3rem;
  }
  .modal {
    padding: 1.2rem 0.7rem;
    min-width: 90vw;
  }
  .eventos-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  .search-input {
    width: 100%;
  }
}
</style>