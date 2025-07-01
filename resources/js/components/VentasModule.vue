<template>
  <div class="ventas-module">
    <div class="ventas-header">
      <input v-model="search" class="search-input" placeholder="Buscar por cliente o evento..." />
    </div>
    <div v-if="loading" class="loading">Cargando ventas...</div>
    <div v-else>
      <div v-if="filteredVentas.length === 0" class="empty">
        <span class="icon">🙁</span> No hay ventas registradas.
      </div>
      <table class="ventas-table" v-else>
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Evento</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="venta in filteredVentas" :key="venta.id">
            <td>{{ venta.id }}</td>
            <td>{{ venta.cliente?.nombre || venta.cliente?.email || 'N/A' }}</td>
            <td>{{ venta.entrada?.evento?.nombre || 'N/A' }}</td>
            <td>{{ venta.cantidad }}</td>
            <td>${{ venta.total }}</td>
            <td>{{ formatFecha(venta.created_at) }}</td>
            <td>
              <button class="btn-icon btn-blue" @click="verDetalle(venta)" title="Ver detalle">
                <span class="icon">🔍</span>
              </button>
              <button class="btn-icon btn-red" @click="eliminarVenta(venta.id)" title="Eliminar">
                <span class="icon">🗑️</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Detalle Venta -->
    <div v-if="showDetalle" class="modal-bg">
      <div class="modal">
        <h2>Detalle de Venta #{{ detalleVenta.id }}</h2>
        <ul class="detalle-list">
            <li><b>Cliente:</b> {{ detalleVenta.cliente?.nombre || detalleVenta.cliente?.email }}</li>
            <li><b>Evento:</b> {{ detalleVenta.entrada?.evento?.nombre }}</li>
            <li><b>Cantidad:</b> {{ detalleVenta.cantidad }}</li>
            <li><b>Total:</b> ${{ detalleVenta.total }}</li>
            <li><b>Fecha:</b> {{ formatFecha(detalleVenta.created_at) }}</li>
            <li><b>Método de pago:</b> {{ detalleVenta.metodo_pago || 'N/A' }}</li>
        </ul>
        <div class="modal-actions">
          <button class="btn-blue" @click="showDetalle = false">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: 'VentasModule',
  data() {
    return {
      ventas: [],
      loading: false,
      search: '',
      showDetalle: false,
      detalleVenta: {}
    };
  },
  computed: {
    filteredVentas() {
      if (!this.search) return this.ventas;
      return this.ventas.filter(v =>
        (v.cliente?.nombre || '').toLowerCase().includes(this.search.toLowerCase()) ||
        (v.cliente?.email || '').toLowerCase().includes(this.search.toLowerCase()) ||
        (v.evento?.nombre || '').toLowerCase().includes(this.search.toLowerCase())
      );
    }
  },
  mounted() {
    this.fetchVentas();
  },
  methods: {
    async fetchVentas() {
      this.loading = true;
      try {
        const res = await axios.get('/api/ventas');
        this.ventas = res.data;
      } catch {
        this.ventas = [];
      }
      this.loading = false;
    },
    formatFecha(fecha) {
      if (!fecha) return '';
      return new Date(fecha).toLocaleString();
    },
    verDetalle(venta) {
      this.detalleVenta = venta;
      this.showDetalle = true;
    },
    async eliminarVenta(id) {
      if (confirm('¿Eliminar esta venta?')) {
        try {
          await axios.delete(`/api/ventas/${id}`);
          this.fetchVentas();
          alert('Venta eliminada correctamente');
        } catch {
          alert('Error al eliminar la venta');
        }
      }
    }
  }
};
</script>

<style scoped>
.ventas-module {
  background: #f9fafb;
  border-radius: 1.2rem;
  padding: 2rem 2.5rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  max-width: 900px;
  margin: 2rem auto;
}
.ventas-header {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 1.5rem;
}
.search-input {
  padding: 0.7rem 1.1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #fff;
  transition: border 0.2s;
  width: 260px;
}
.search-input:focus {
  border: 1.5px solid #2563eb;
  outline: none;
}
.ventas-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.ventas-table th, .ventas-table td {
  padding: 1rem 0.8rem;
  text-align: left;
}
.ventas-table thead {
  background: #f1f5f9;
}
.ventas-table tbody tr {
  transition: background 0.18s;
}
.ventas-table tbody tr:hover {
  background: #e0e7ef;
}
.btn-blue {
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.4rem 0.9rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
  display: inline-flex;
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
  padding: 0.4rem 0.9rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}
.btn-red:hover {
  background: #b91c1c;
}
.btn-icon {
  font-size: 1.1rem;
  border-radius: 0.5rem;
  margin-right: 0.2rem;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
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
.detalle-list {
  list-style: none;
  padding: 0;
  margin: 0 0 1.2rem 0;
}
.detalle-list li {
  margin-bottom: 0.6rem;
  font-size: 1.05rem;
}
.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 0.5rem;
}
@media (max-width: 600px) {
  .ventas-module {
    padding: 1rem 0.3rem;
  }
  .modal {
    padding: 1.2rem 0.7rem;
    min-width: 90vw;
  }
  .ventas-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  .search-input {
    width: 100%;
  }
}
</style>