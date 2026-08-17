// ===== SALVAR =====
async function adicionarUsuario() {
  const nome = document.getElementById("add-nome").value.trim();
  const cpf = document.getElementById("add-cpf").value.trim();
  const senha = document.getElementById("add-senha").value.trim();

  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }

  const response = await fetch("/api/usuarios.php", {
  method: "POST",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify({nome, cpf, senha})
 });

  const data = await response.json();

  if (!response.ok) {
    showToast(data.mensagem || "Erro ao adicionar usuário!");
    return;
  }

  showToast("Usuário adicionado!");
  setTimeout(() => {
    window.location.href = "usuarios.html";
  }, 1000);
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    document.getElementById('btnCriar').click();
  }
});
