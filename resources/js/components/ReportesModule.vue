<template>
  <div class="reportes-module">
    <h2 class="titulo">Reportes Gerenciales</h2>
    <div class="filtros">
      <label>Filtrar por evento:</label>
      <select v-model="eventoSeleccionado">
        <option value="">Todos</option>
        <option v-for="evento in eventos" :key="evento.id" :value="evento.id">{{ evento.nombre }}</option>
      </select>
    </div>
    <div class="kpi-mosaico">
      <div class="kpi-box">
        <div class="kpi-titulo">Total Ventas</div>
        <div class="kpi-valor">${{ totalVentas }}</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-titulo">Total Gastos</div>
        <div class="kpi-valor">${{ totalGastos }}</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-titulo">Clientes</div>
        <div class="kpi-valor">{{ totalClientes }}</div>
      </div>
      <div class="kpi-box">
        <div class="kpi-titulo">Entradas Vendidas</div>
        <div class="kpi-valor">{{ totalEntradasVendidas }}</div>
      </div>
    </div>

    <div class="graficos-mosaico">
      <div class="grafico-box">
        <h3>Ventas por Evento</h3>
        <canvas id="ventasEventoChart"></canvas>
      </div>
      <div class="grafico-box">
        <h3>Ingresos vs Gastos</h3>
        <canvas id="ingresosGastosChart"></canvas>
      </div>
      <div class="grafico-box">
        <h3>Clientes Frecuentes</h3>
        <canvas id="clientesFrecuentesChart"></canvas>
      </div>
      <div class="grafico-box">
        <h3>Tipos de Entradas Vendidas</h3>
        <canvas id="tiposEntradasChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Chart from 'chart.js/auto';

export default {
  name: 'ReportesModule',
  data() {
    return {
      ventas: [],
      eventos: [],
      gastos: [],
      clientes: [],
      entradas: [],
      totalVentas: 0,
      totalGastos: 0,
      totalClientes: 0,
      totalEntradasVendidas: 0,
      eventoSeleccionado: '',
      ventasPorEvento: [],
      ingresosPorMes: [],
      gastosPorMes: [],
      clientesFrecuentes: [],
      tiposEntradas: []
    };
  },
  async mounted() {
    await Promise.all([
      this.fetchVentas(),
      this.fetchEventos(),
      this.fetchGastos(),
      this.fetchClientes(),
      this.fetchEntradas()
    ]);
    this.actualizarReportes();
  },
  watch: {
    eventoSeleccionado() {
      this.actualizarReportes();
    }
  },
  methods: {
    async fetchVentas() {
      const res = await axios.get('/api/ventas');
      this.ventas = res.data;
    },
    async fetchEventos() {
      const res = await axios.get('/api/eventos');
      this.eventos = res.data;
    },
    async fetchGastos() {
      const res = await axios.get('/api/gastos');
      this.gastos = res.data;
    },
    async fetchClientes() {
      const res = await axios.get('/api/clientes');
      this.clientes = res.data;
    },
    async fetchEntradas() {
      const res = await axios.get('/api/entradas');
      this.entradas = res.data;
    },
    actualizarReportes() {
      this.calcularKPIs();
      this.prepararDatosGraficos();
      this.renderGraficos();
    },
    calcularKPIs() {
      // Filtrar ventas y gastos por evento si corresponde
      let ventasFiltradas = this.ventas;
      let gastosFiltrados = this.gastos;
      let entradasFiltradas = this.entradas;

      if (this.eventoSeleccionado) {
        ventasFiltradas = ventasFiltradas.filter(v => v.entrada?.evento?.id == this.eventoSeleccionado);
        gastosFiltrados = gastosFiltrados.filter(g => g.evento_id == this.eventoSeleccionado);
        entradasFiltradas = entradasFiltradas.filter(e => e.evento_id == this.eventoSeleccionado);
      }

      this.totalVentas = ventasFiltradas.reduce((sum, v) => sum + parseFloat(v.total), 0).toFixed(2);
      this.totalGastos = gastosFiltrados.reduce((sum, g) => sum + parseFloat(g.monto), 0).toFixed(2);
      this.totalClientes = [...new Set(ventasFiltradas.map(v => v.cliente?.id))].filter(Boolean).length;
      this.totalEntradasVendidas = ventasFiltradas.reduce((sum, v) => sum + parseInt(v.cantidad), 0);
    },
    prepararDatosGraficos() {
      // Ventas por Evento
      const ventasEvento = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const evento = v.entrada?.evento?.nombre || 'Sin evento';
        ventasEvento[evento] = (ventasEvento[evento] || 0) + parseFloat(v.total);
      });
      this.ventasPorEvento = Object.entries(ventasEvento);

      // Ingresos y Gastos por Mes
      const ingresosMes = {};
      const gastosMes = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const mes = v.created_at ? v.created_at.substring(0, 7) : 'Sin fecha';
        ingresosMes[mes] = (ingresosMes[mes] || 0) + parseFloat(v.total);
      });
      this.gastos.forEach(g => {
        if (this.eventoSeleccionado && g.evento_id != this.eventoSeleccionado) return;
        const mes = g.created_at ? g.created_at.substring(0, 7) : 'Sin fecha';
        gastosMes[mes] = (gastosMes[mes] || 0) + parseFloat(g.monto);
      });
      this.ingresosPorMes = ingresosMes;
      this.gastosPorMes = gastosMes;

      // Clientes Frecuentes
      const clientesMap = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const nombre = v.cliente?.nombre || v.cliente?.email || 'Sin cliente';
        clientesMap[nombre] = (clientesMap[nombre] || 0) + 1;
      });
      this.clientesFrecuentes = Object.entries(clientesMap)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 7);

      // Tipos de Entradas Vendidas
      const tiposMap = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const tipo = v.entrada?.tipo || 'Sin tipo';
        tiposMap[tipo] = (tiposMap[tipo] || 0) + parseInt(v.cantidad);
      });
      this.tiposEntradas = Object.entries(tiposMap);
    },
    renderGraficos() {
      // Limpia gráficos anteriores si existen
      if (this.ventasEventoChart) this.ventasEventoChart.destroy();
      if (this.ingresosGastosChart) this.ingresosGastosChart.destroy();
      if (this.clientesFrecuentesChart) this.clientesFrecuentesChart.destroy();
      if (this.tiposEntradasChart) this.tiposEntradasChart.destroy();

      // Ventas por Evento
      const ctx1 = document.getElementById('ventasEventoChart').getContext('2d');
      this.ventasEventoChart = new Chart(ctx1, {
        type: 'bar',
        data: {
          labels: this.ventasPorEvento.map(e => e[0]),
          datasets: [{
            label: 'Total vendido ($)',
            data: this.ventasPorEvento.map(e => e[1]),
            backgroundColor: '#2563eb'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } }
        }
      });

      // Ingresos vs Gastos por Mes
      const meses = Array.from(new Set([
        ...Object.keys(this.ingresosPorMes),
        ...Object.keys(this.gastosPorMes)
      ])).sort();
      const ctx2 = document.getElementById('ingresosGastosChart').getContext('2d');
      this.ingresosGastosChart = new Chart(ctx2, {
        type: 'line',
        data: {
          labels: meses,
          datasets: [
            {
              label: 'Ingresos',
              data: meses.map(m => this.ingresosPorMes[m] || 0),
              borderColor: '#22c55e',
              backgroundColor: 'rgba(34,197,94,0.1)',
              tension: 0.3
            },
            {
              label: 'Gastos',
              data: meses.map(m => this.gastosPorMes[m] || 0),
              borderColor: '#ef4444',
              backgroundColor: 'rgba(239,68,68,0.1)',
              tension: 0.3
            }
          ]
        },
        options: {
          responsive: true
        }
      });

      // Clientes Frecuentes
      const ctx3 = document.getElementById('clientesFrecuentesChart').getContext('2d');
      this.clientesFrecuentesChart = new Chart(ctx3, {
        type: 'bar',
        data: {
          labels: this.clientesFrecuentes.map(c => c[0]),
          datasets: [{
            label: 'Compras',
            data: this.clientesFrecuentes.map(c => c[1]),
            backgroundColor: '#f59e42'
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } }
        }
      });

      // Tipos de Entradas Vendidas
      const ctx4 = document.getElementById('tiposEntradasChart').getContext('2d');
      this.tiposEntradasChart = new Chart(ctx4, {
        type: 'pie',
        data: {
          labels: this.tiposEntradas.map(t => t[0]),
          datasets: [{
            label: 'Entradas vendidas',
            data: this.tiposEntradas.map(t => t[1]),
            backgroundColor: ['#2563eb', '#f59e42', '#22c55e', '#ef4444', '#a78bfa', '#f472b6', '#facc15']
          }]
        },
        options: {
          responsive: true
        }
      });
    }
  }
};
</script>

<style scoped>
.reportes-module {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 2rem 1.5rem;
  background: #f9fafb;
  border-radius: 1.2rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
}
.titulo {
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #2563eb;
  margin-bottom: 2rem;
}
.filtros {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  justify-content: center;
}
.filtros select {
  padding: 0.5rem 1rem;
  border-radius: 0.7rem;
  border: 1px solid #d1d5db;
  font-size: 1rem;
  background: #fff;
}
.kpi-mosaico {
  display: flex;
  gap: 2rem;
  justify-content: center;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
}
.kpi-box {
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  padding: 1.5rem 2.5rem;
  min-width: 180px;
  text-align: center;
  margin-bottom: 1rem;
}
.kpi-titulo {
  color: #64748b;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}
.kpi-valor {
  font-size: 2rem;
  font-weight: 700;
  color: #2563eb;
}
.graficos-mosaico {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  justify-content: center;
}
.grafico-box {
  background: #fff;
  border-radius: 1rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
  padding: 1.5rem 1.5rem 2.5rem 1.5rem;
  min-width: 340px;
  max-width: 400px;
  flex: 1 1 340px;
  margin-bottom: 2rem;
}
.grafico-box h3 {
  text-align: center;
  color: #2563eb;
  font-size: 1.15rem;
  margin-bottom: 1rem;
  font-weight: 600;
}
@media (max-width: 900px) {
  .kpi-mosaico, .graficos-mosaico {
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
  }
  .grafico-box {
    min-width: 90vw;
    max-width: 98vw;
  }
}
</style>