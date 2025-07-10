<template>
  <div class="reportes-module">
    <div class="header-container">
      <h2 class="titulo">Reportes Gerenciales</h2>
      <button @click="generarReportePDF" class="btn-generar-pdf" :disabled="loadingPDF">
        <i class="fas fa-file-pdf"></i> 
        {{ loadingPDF ? 'Generando...' : 'Generar PDF' }}
      </button>
    </div>
    <div class="filtros">
      <label>Seleccionar reporte:</label>
<select v-model="reporteSeleccionado">
  <option value="resumen">Resumen General</option>
  <option value="ventas_evento">Ventas por Evento</option>
  <option value="ingresos_gastos">Ingresos vs Gastos</option>
  <option value="clientes_frecuentes">Clientes Frecuentes</option>
  <option value="tipos_entradas">Tipos de Entradas Vendidas</option>
</select>

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
      tiposEntradas: [],
      ventasEventoChart: null,
      ingresosGastosChart: null,
      clientesFrecuentesChart: null,
      tiposEntradasChart: null,
      loadingPDF: false,
      reporteSeleccionado: 'resumen', // valor por defecto
      
      
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
      const ventasEvento = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const evento = v.entrada?.evento?.nombre || 'Sin evento';
        ventasEvento[evento] = (ventasEvento[evento] || 0) + parseFloat(v.total);
      });
      this.ventasPorEvento = Object.entries(ventasEvento);

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

      const clientesMap = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const nombre = v.cliente?.nombre || v.cliente?.email || 'Sin cliente';
        clientesMap[nombre] = (clientesMap[nombre] || 0) + 1;
      });
      this.clientesFrecuentes = Object.entries(clientesMap)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 7);

      const tiposMap = {};
      this.ventas.forEach(v => {
        if (this.eventoSeleccionado && v.entrada?.evento?.id != this.eventoSeleccionado) return;
        const tipo = v.entrada?.tipo || 'Sin tipo';
        tiposMap[tipo] = (tiposMap[tipo] || 0) + parseInt(v.cantidad);
      });
      this.tiposEntradas = Object.entries(tiposMap);
    },
    renderGraficos() {
      if (this.ventasEventoChart) this.ventasEventoChart.destroy();
      if (this.ingresosGastosChart) this.ingresosGastosChart.destroy();
      if (this.clientesFrecuentesChart) this.clientesFrecuentesChart.destroy();
      if (this.tiposEntradasChart) this.tiposEntradasChart.destroy();

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
    },
    loadJSDF() {
      return new Promise((resolve, reject) => {
        if (window.jspdf) {
          resolve(window.jspdf);
          return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = () => {
          resolve(window.jspdf);
        };
        script.onerror = () => {
          reject(new Error('Failed to load jsPDF'));
        };
        document.head.appendChild(script);
      });
    },
    loadHtml2Canvas() {
      return new Promise((resolve, reject) => {
        if (window.html2canvas) {
          resolve(window.html2canvas);
          return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
        script.onload = () => {
          resolve(window.html2canvas);
        };
        script.onerror = () => {
          reject(new Error('Failed to load html2canvas'));
        };
        document.head.appendChild(script);
      });
    },

// MÉTODO FINAL - RESUMEN GENERAL GERENCIAL REDISEÑADO CON TOTAL LIBERTAD CREATIVA
async generarReportePDF() {
  this.loadingPDF = true;
  try {
    const { jsPDF } = await this.loadJSDF();
    const pdf = new jsPDF('landscape', 'mm', 'a4');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const margin = 15;
    const fecha = new Date().toLocaleDateString();
    const usuario = this.usuario?.nombre || 'Administrador';
    const nombreEvento = this.eventoSeleccionado
      ? (this.eventos.find(e => e.id === this.eventoSeleccionado)?.nombre || '')
      : 'Todos los eventos';

    const insertarGrafico = (canvasId, yPos, widthMm = 200, xPos = margin + 10) => {
      const canvas = document.getElementById(canvasId);
      const tempCanvas = document.createElement('canvas');
      tempCanvas.width = canvas.width * 2;
      tempCanvas.height = canvas.height * 2;
      const ctx = tempCanvas.getContext('2d');
      ctx.scale(2, 2);
      ctx.drawImage(canvas, 0, 0);
      const imgData = tempCanvas.toDataURL('image/png');
      const aspectRatio = tempCanvas.height / tempCanvas.width;
      const heightMm = widthMm * aspectRatio;
      pdf.addImage(imgData, 'PNG', xPos, yPos, widthMm, heightMm);
      return heightMm;
    };

    const textoAnalitico = {
      ventas_evento: `El análisis de ventas por evento permite identificar cuáles generaron mayores ingresos, siendo clave para la planificación de futuros espectáculos o actividades.`,
      ingresos_gastos: `Comparar ingresos y gastos permite evaluar la eficiencia financiera mensual. Se observan meses con rentabilidad alta y otros con desbalance presupuestario.`,
      clientes_frecuentes: `Detectar clientes frecuentes ayuda a identificar patrones de fidelidad. Se pueden generar campañas de retención o recompensas personalizadas.`,
      tipos_entradas: `Los tipos de entradas preferidos por los asistentes revelan segmentos de precio y formato con mayor aceptación. Esto guía decisiones comerciales futuras.`
    };

    const kpis = [
      { label: 'Total Ventas', value: `$${this.totalVentas}` },
      { label: 'Total Gastos', value: `$${this.totalGastos}` },
      { label: 'Entradas Vendidas', value: `${this.totalEntradasVendidas}` },
      { label: 'Clientes', value: `${this.totalClientes}` }
    ];

    // PORTADA
    pdf.setFontSize(20);
    pdf.setTextColor(33);
    pdf.text('Resumen Gerencial de Eventos', pageWidth / 2, 22, { align: 'center' });
    pdf.setFontSize(11);
    pdf.setTextColor(90);
    pdf.text(`Generado por: ${usuario}`, margin, 30);
    pdf.text(`Fecha: ${fecha}`, margin, 36);
    pdf.text(`Evento seleccionado: ${nombreEvento}`, margin, 42);

    pdf.setDrawColor(200);
    pdf.line(margin, 48, pageWidth - margin, 48);

    // KPIs
    pdf.setFontSize(13);
    pdf.setTextColor(44);
    pdf.text('Indicadores Clave (KPIs):', margin, 58);
    let x = margin;
    let y = 64;
    const kpiWidth = (pageWidth - margin * 2 - 30) / 2;
    const kpiHeight = 18;
    kpis.forEach((kpi, i) => {
      if (i % 2 === 0 && i !== 0) {
        y += kpiHeight + 8;
        x = margin;
      }
      pdf.setFillColor(240);
      pdf.roundedRect(x, y, kpiWidth, kpiHeight, 3, 3, 'F');
      pdf.setTextColor(70);
      pdf.setFontSize(10);
      pdf.text(kpi.label, x + 4, y + 6);
      pdf.setFontSize(12);
      pdf.setTextColor(20);
      pdf.text(kpi.value, x + 4, y + 14);
      x += kpiWidth + 10;
    });

    // GRÁFICOS CON ANALÍTICA
    const graficos = [
      { id: 'ventasEventoChart', titulo: 'Ventas por Evento', texto: textoAnalitico.ventas_evento },
      { id: 'ingresosGastosChart', titulo: 'Ingresos vs Gastos', texto: textoAnalitico.ingresos_gastos },
      { id: 'clientesFrecuentesChart', titulo: 'Clientes Frecuentes', texto: textoAnalitico.clientes_frecuentes },
      { id: 'tiposEntradasChart', titulo: 'Tipos de Entradas Vendidas', texto: textoAnalitico.tipos_entradas }
    ];

    graficos.forEach((grafico) => {
      pdf.addPage();
      pdf.setFontSize(14);
      pdf.setTextColor(33);
      pdf.text(grafico.titulo, margin, 20);
      const altura = insertarGrafico(grafico.id, 25, 180); // reducido para evitar desbordamiento
      pdf.setFontSize(11);
      pdf.setTextColor(66);
      const lines = pdf.splitTextToSize(grafico.texto, pageWidth - margin * 2);
      pdf.text(lines, margin, 30 + altura);
    });

    // CONCLUSIÓN FINAL EXTENDIDA
    pdf.addPage();
    pdf.setFontSize(14);
    pdf.setTextColor(30);
    pdf.text('Conclusiones Gerenciales y Recomendaciones', margin, 20);

    const conclusion = `Este reporte sintetiza el comportamiento financiero y comercial de los eventos desarrollados durante el periodo evaluado. A partir de los gráficos analizados, se destacan oportunidades de mejora operativa y comercial.

Principales hallazgos:
- La variabilidad de ingresos por evento permite detectar cuáles son más rentables.
- Los gastos deben ser monitoreados más estrictamente en los meses con baja rentabilidad.
- Existen clientes frecuentes que representan un valor estratégico para la organización.
- Las entradas más vendidas indican las preferencias del público objetivo.

Recomendaciones estratégicas:
- Potenciar la promoción de los eventos con mejores ventas.
- Negociar mejores condiciones con proveedores en meses críticos.
- Implementar programas de fidelización.
- Diversificar los tipos de entradas con base en la demanda real.

Este informe debe servir como base para la toma de decisiones en futuras ediciones de eventos, ayudando a maximizar la rentabilidad y optimizar la experiencia del cliente.`;

    const parrafos = pdf.splitTextToSize(conclusion, pageWidth - margin * 2);
    pdf.setFontSize(11);
    pdf.setTextColor(70);
    pdf.text(parrafos, margin, 30);

    // Paginación
    const totalPages = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
      pdf.setPage(i);
      pdf.setFontSize(9);
      pdf.setTextColor(120);
      pdf.text(`Página ${i} de ${totalPages}`, pageWidth - margin, pageHeight - 7, { align: 'right' });
    }

    pdf.save(`Resumen_Gerencial_${new Date().toISOString().split('T')[0]}.pdf`);
  } catch (e) {
    console.error('Error PDF:', e);
    alert('Error al generar PDF');
  } finally {
    this.loadingPDF = false;
  }
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
.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}
.btn-generar-pdf {
  background-color: #e53e3e;
  color: white;
  border: none;
  padding: 0.7rem 1.5rem;
  border-radius: 0.5rem;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.btn-generar-pdf:hover {
  background-color: #c53030;
}
.btn-generar-pdf:disabled {
  background-color: #cbd5e0;
  cursor: not-allowed;
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
@media (max-width: 600px) {
  .header-container {
    flex-direction: column;
    align-items: center;
  }
  .btn-generar-pdf {
    width: 100%;
    justify-content: center;
  }
}
</style>