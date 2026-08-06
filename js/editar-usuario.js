const params = new URLSearchParams(window.location.search);
const userId = parseInt(params.get("id"));

// ===== PRÉ-PREENCHER FORMULÁRIO =====
document.addEventListener("DOMContentLoaded", async () => {
  const response = await fetch(`/api/usuarios.php?id=${userId}`);
  const usuario = await response.json();

  if (!response.ok) {
    showToast(usuario.mensagem || "Usuário não encontrado!");
    setTimeout(() => { window.location.href = "usuarios.html"; }, 1200);
    return;
  }

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

  const payload = {nome, cpf};
  if (senha) payload.senha = senha; // senha em branco mantém a atual

  const response = await fetch(`/api/usuarios.php?id=${userId}`, {
  method: "PUT",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify(payload)
 });

  const data = await response.json();

  if (!response.ok) {
    showToast(data.mensagem || "Erro ao atualizar usuário!");
    return;
  }

  showToast("Usuário atualizado!");
  setTimeout(() => {
    window.location.href = "usuarios.html";
  }, 1000);
}
