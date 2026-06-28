// GRANDE PARTE DO JAVA FOI REVISADO E COMPLEMENTADO PELO CLAUDE OPUS 4.8
document.addEventListener('DOMContentLoaded', () => {
  const formCadastro = document.getElementById('form-cadastro');
  if (!formCadastro) return; 

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
