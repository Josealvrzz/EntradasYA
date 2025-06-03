<template>
  <div class="eventos-module">
    <div class="eventos-header">
      <button class="btn-blue" @click="showCreate = true">+ Nuevo Evento</button>
      <input v-model="search" class="search-input" placeholder="Buscar evento..." />
    </div>
    <div v-if="loading" class="loading">Cargando eventos...</div>
    <div v-else>
      <div v-if="filteredEventos.length === 0" class="empty">No hay eventos disponibles.</div>
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
            <td>{{ evento.fecha }}</td>
            <td>{{ evento.lugar?.nombre || 'Sin asignar' }}</td>
            <td>{{ evento.lugar.capacidad || 'N/A' }}</td>
            <td>{{ evento.estado || 'Activo' }}</td>
            <td>
              <button class="btn-blue" @click="editEvento(evento)">Editar</button>
              <button class="btn-red" @click="deleteEvento(evento.id)">Eliminar</button>
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
          <input v-model="eventoForm.capacidad" type="number" min="1" placeholder="Capacidad" required />
          <input v-model="eventoForm.lugar" placeholder="Lugar" required />
          <textarea v-model="eventoForm.descripcion" placeholder="Descripción"></textarea>
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
      loading: false,
      showCreate: false,
      showEdit: false,
      search: '',
      eventoForm: {
        id: null,
        nombre: '',
        fecha: '',
        capacidad: '',
        lugar: '',
        descripcion: ''
      }
    };
  },
  computed: {
    filteredEventos() {
      if (!this.search) return this.eventos;
      return this.eventos.filter(e =>
        e.nombre.toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchEventos();
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
    closeModal() {
      this.showCreate = false;
      this.showEdit = false;
      this.resetEventoForm();
    },
    resetEventoForm() {
      this.eventoForm = { id: null, nombre: '', fecha: '', capacidad: '', lugar: '', descripcion: '' };
    },
    async createEvento() {
      try {
        await axios.post('/api/eventos', this.eventoForm);
        this.closeModal();
        this.fetchEventos();
      } catch {}
    },
    editEvento(evento) {
      this.eventoForm = { ...evento };
      this.showEdit = true;
    },
    async updateEvento() {
      try {
        await axios.put(`/api/eventos/${this.eventoForm.id}`, this.eventoForm);
        this.closeModal();
        this.fetchEventos();
      } catch {}
    },
    async deleteEvento(id) {
      if (confirm('¿Eliminar este evento?')) {
        try {
          await axios.delete(`/api/eventos/${id}`);
          this.fetchEventos();
        } catch {}
      }
    }
  }
};
</script>

<style scoped>
.eventos-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 1rem;
}
.search-input {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  background: #f9fafb;
  color: #111;
}
.eventos-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border: 1px solid #e5e7eb;
}
.eventos-table th,
.eventos-table td {
  padding: 0.8rem 1rem;
  text-align: left;
  font-size: 1rem;
  border-bottom: 1px solid #eee;
}
.eventos-table th {
  background: #f3f4f6;
  color: #111;
}
.eventos-table tr:last-child td {
  border-bottom: none;
}
.btn-blue {
  background: #3b82f6;
  color: #fff;
  border: none;
  border-radius: 0.6rem;
  padding: 0.5rem 1rem;
  margin-right: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-blue:hover {
  background: #2563eb;
}
.btn-red {
  background: #ef4444;
  color: #fff;
  border: none;
  border-radius: 0.6rem;
  padding: 0.5rem 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-red:hover {
  background: #b91c1c;
}
.modal-bg {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
}
.modal {
  background: #fff;
  padding: 2rem 2rem 1.5rem 2rem;
  border-radius: 1rem;
  min-width: 320px;
  max-width: 95vw;
  box-shadow: 0 4px 24px rgba(0,0,0,0.10);
}
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.2rem;
}
.modal input, .modal textarea {
  width: 100%;
  margin-bottom: 1rem;
  padding: 0.7rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  background: #f9fafb;
  color: #111;
}
.loading {
  color: #3b82f6;
  font-size: 1.2rem;
  text-align: center;
  margin-top: 2rem;
}
.empty {
  color: #6b7280;
  font-size: 1.1rem;
  text-align: center;
  margin-top: 2rem;
}
</style>