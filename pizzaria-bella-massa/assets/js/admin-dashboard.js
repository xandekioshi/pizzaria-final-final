/**
 * admin-dashboard.js  (Extra 2)
 * Busca os dados no PHP (GROUP BY/SUM) e desenha o gráfico com Chart.js.
 */
document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('grafico-vendas');
  const seletor = document.getElementById('seletor-tipo-grafico');
  const titulo = document.getElementById('titulo-grafico');
  let grafico = null;

  async function carregarGrafico(tipo) {
    const resposta = await fetch(`../actions/dados_grafico.php?tipo=${tipo}`);
    const dados = await resposta.json();

    titulo.textContent = tipo === 'faturamento'
      ? 'Faturamento por produto'
      : 'Pizzas mais vendidas';

    // Se já existe um gráfico, destrói antes de criar outro.
    if (grafico) grafico.destroy();

    grafico = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: dados.labels,
        datasets: [{
          label: titulo.textContent,
          data: dados.valores,
          backgroundColor: '#c0392b',
        }],
      },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
      },
    });
  }

  // Primeira carga e troca pelo seletor.
  carregarGrafico(seletor.value);
  seletor.addEventListener('change', () => carregarGrafico(seletor.value));
});
