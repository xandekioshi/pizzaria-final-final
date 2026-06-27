/**
 * menu.js
 * Alterna as abas do cardápio entre "Pizzas" e "Bebidas".
 */
document.addEventListener('DOMContentLoaded', () => {
  const abas = document.querySelectorAll('.cardapio__aba');
  const categoriaPizzas = document.getElementById('categoria-pizzas');
  const categoriaBebidas = document.getElementById('categoria-bebidas');

  abas.forEach((aba) => {
    aba.addEventListener('click', () => {
      // Marca a aba clicada como ativa.
      abas.forEach((a) => {
        a.classList.remove('is-ativa');
        a.setAttribute('aria-selected', 'false');
      });
      aba.classList.add('is-ativa');
      aba.setAttribute('aria-selected', 'true');

      // Mostra a categoria correspondente.
      const categoria = aba.dataset.categoria;
      categoriaPizzas.hidden = categoria !== 'pizzas';
      categoriaBebidas.hidden = categoria !== 'bebidas';
    });
  });
});
