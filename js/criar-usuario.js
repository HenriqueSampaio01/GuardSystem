// ===== SALVAR =====
async function adicionarUsuario() {
  const nome = document.getElementById("add-nome").value.trim();
  const cpf = document.getElementById("add-cpf").value.trim();
  const senha = document.getElementById("add-senha").value.trim();

  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }

  await fetch(`https://guardsystem.up.railway.app/api/usuarios.php`, {
  method: "POST",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify({nome, cpf, senha})
 });

  showToast("Usuário adicionado!");
  setTimeout(() => {
    window.location.href = "usuarios.html";
  }, 1000);
}
