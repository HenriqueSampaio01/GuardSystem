const params = new URLSearchParams(window.location.search);
const userId = parseInt(params.get("id"));

// ===== PRÉ-PREENCHER FORMULÁRIO =====
document.addEventListener("DOMContentLoaded", async () => {
  const response = await fetch(`https://guardsystem.up.railway.app/api/usuarios.php?id=${userId}`);
  const usuario = await response.json();
  if (usuario) {
    document.getElementById("edit-nome").value = usuario.nome;
    document.getElementById("edit-cpf").value  = usuario.cpf;
  }
});

// ===== SALVAR =====
async function salvarEdicao() {
  const nome = document.getElementById("edit-nome").value.trim();
  const cpf = document.getElementById("edit-cpf").value.trim();
  const senha = document.getElementById("edit-senha").value.trim();

  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }

  await fetch(`https://guardsystem.up.railway.app/api/usuarios.php?id=${userId}`, {
  method: "PUT",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify({nome, cpf, senha})
 });

  showToast("Usuário atualizado!");
  setTimeout(() => {
    window.location.href = "usuarios.html";
  }, 1000);
}
