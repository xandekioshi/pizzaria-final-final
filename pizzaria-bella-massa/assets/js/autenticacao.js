/**
 * autenticacao.js
 * Pequena validação no navegador antes de enviar o cadastro.
 * (A validação "de verdade" também acontece no PHP.)
 */
document.addEventListener('DOMContentLoaded', () => {
  const formCadastro = document.getElementById('form-cadastro');
  if (!formCadastro) return; // só roda na página de cadastro

  formCadastro.addEventListener('submit', (e) => {
    const senha = document.getElementById('senha-cadastro').value;
    const confirmar = document.getElementById('confirmar-senha-cadastro').value;
    const mensagem = document.getElementById('mensagem-erro-cadastro');

    if (senha !== confirmar) {
      e.preventDefault();
      mensagem.textContent = 'As senhas não coincidem.';
      mensagem.hidden = false;
    }
  });
});
