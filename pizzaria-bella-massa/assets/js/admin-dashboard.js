// GRANDE PARTE DO JAVA FOI REVISADO E COMPLEMENTADO PELO CLAUDE OPUS 4.8
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

  
  carregarGrafico(seletor.value);
  seletor.addEventListener('change', () => carregarGrafico(seletor.value));
});
