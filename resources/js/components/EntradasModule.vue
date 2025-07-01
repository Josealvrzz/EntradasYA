<template>
  <div class="entradas-module">
    <div class="entradas-header">
      <button class="btn-blue" @click="showCreate = true">
        <span class="icon">➕</span> Nueva Entrada
      </button>
      <input v-model="search" class="search-input" placeholder="Buscar entrada o evento..." />
    </div>
    <div v-if="loading" class="loading">Cargando entradas...</div>
    <div v-else>
      <div v-if="filteredEntradas.length === 0" class="empty">
        <span class="icon">🙁</span> No hay entradas registradas.
      </div>
      <table class="entradas-table" v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Evento</th>
            <th>Tipo</th>
            <th>Precio</th>
            <th>Cantidad Total</th>
            <th>Cantidad Disponible</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="entrada in filteredEntradas" :key="entrada.id">
            <td>{{ entrada.id }}</td>
            <td>{{ entrada.evento?.nombre || 'N/A' }}</td>
            <td>{{ entrada.tipo }}</td>
            <td>${{ entrada.precio }}</td>
            <td>{{ entrada.cantidad_total }}</td>
            <td>{{ entrada.cantidad_disponible }}</td>
            <td>
              <button class="btn-icon btn-blue" @click="editEntrada(entrada)" title="Editar">
                <span class="icon">✏️</span>
              </button>
              <button class="btn-icon btn-red" @click="deleteEntrada(entrada.id)" title="Eliminar">
                <span class="icon">🗑️</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear/Editar Entrada -->
    <div v-if="showCreate || showEdit" class="modal-bg">
      <div class="modal">
        <h2>{{ showEdit ? 'Editar Entrada' : 'Nueva Entrada' }}</h2>
        <form @submit.prevent="showEdit ? updateEntrada() : createEntrada()">
          <select v-model="entradaForm.evento_id" required>
            <option value="">Selecciona un evento</option>
            <option v-for="evento in eventos" :key="evento.id" :value="evento.id">{{ evento.nombre }}</option>
          </select>
          <input v-model="entradaForm.tipo" placeholder="Tipo (General, VIP...)" required />
          <input v-model="entradaForm.precio" type="number" min="0" step="0.01" placeholder="Precio" required />
          <input v-model="entradaForm.cantidad_total" type="number" min="1" placeholder="Cantidad Total" required />
          <input v-model="entradaForm.cantidad_disponible" type="number" min="0" placeholder="Cantidad Disponible" required />
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
  name: 'EntradasModule',
  data() {
    return {
      entradas: [],
      eventos: [],
      loading: false,
      showCreate: false,
      showEdit: false,
      search: '',
      entradaForm: {
        id: null,
        evento_id: '',
        tipo: '',
        precio: '',
        cantidad_total: '',
        cantidad_disponible: ''
      }
    };
  },
  computed: {
    filteredEntradas() {
      if (!this.search) return this.entradas;
      return this.entradas.filter(e =>
        (e.tipo || '').toLowerCase().includes(this.search.toLowerCase()) ||
        (e.evento?.nombre || '').toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchEntradas();
    this.fetchEventos();
  },
  methods: {
    async fetchEntradas() {
      this.loading = true;
      try {
        const res = await axios.get('/api/entradas');
        this.entradas = res.data;
      } catch {
        this.entradas = [];
      }
      this.loading = false;
    },
    async fetchEventos() {
      try {
        const res = await axios.get('/api/eventos');
        this.eventos = res.data;
      } catch {
        this.eventos = [];
      }
    },
    closeModal() {
      this.showCreate = false;
      this.showEdit = false;
      this.resetEntradaForm();
    },
    resetEntradaForm() {
      this.entradaForm = { id: null, evento_id: '', tipo: '', precio: '', cantidad_total: '', cantidad_disponible: '' };
    },
    async createEntrada() {
      try {
        await axios.post('/api/entradas', this.entradaForm);
        this.closeModal();
        this.fetchEntradas();
        alert('Entrada creada correctamente');
      } catch {
        alert('Error al crear la entrada');
      }
    },
    editEntrada(entrada) {
      this.entradaForm = {
        id: entrada.id,
        evento_id: entrada.evento_id,
        tipo: entrada.tipo,
        precio: entrada.precio,
        cantidad_total: entrada.cantidad_total,
        cantidad_disponible: entrada.cantidad_disponible
      };
      this.showEdit = true;
    },
    async updateEntrada() {
      try {
        await axios.put(`/api/entradas/${this.entradaForm.id}`, this.entradaForm);
        this.closeModal();
        this.fetchEntradas();
        alert('Entrada actualizada correctamente');
      } catch {
        alert('Error al actualizar la entrada');
      }
    },
    async deleteEntrada(id) {
      if (confirm('¿Eliminar esta entrada?')) {
        try {
          await axios.delete(`/api/entradas/${id}`);
          this.fetchEntradas();
          alert('Entrada eliminada correctamente');
        } catch {
          alert('Error al eliminar la entrada');
        }
      }
    }
  }
};
</script>

<style scoped>
.entradas-module {
  background: #f9fafb;
  border-radius: 1.2rem;
  padding: 2rem 2.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  max-width: 900px;
  margin: 2rem auto;
}
.entradas-header {
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
.entradas-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.entradas-table th, .entradas-table td {
  padding: 1rem 0.8rem;
  text-align: left;
}
.entradas-table thead {
  background: #f1f5f9;
}
.entradas-table tbody tr {
  transition: background 0.18s;
}
.entradas-table tbody tr:hover {
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
  .entradas-module {
    padding: 1rem 0.3rem;
  }
  .modal {
    padding: 1.2rem 0.7rem;
    min-width: 90vw;
  }
  .entradas-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  .search-input {
    width: 100%;
  }
}
</style>