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

  // ===== VALIDAÇÕES =====
  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }

  // Nome: mínimo 2 partes, cada uma com pelo menos 3 letras
  const partes = nome.split(" ").filter(p => p.length > 0);
  if (partes.length < 2 || partes[0].length < 3 || partes[1].length < 3) {
    showToast("Informe nome e sobrenome, ambos com no mínimo 3 letras.");
    return;
  }

  // CPF: deve estar com máscara completa (14 chars: 000.000.000-00)
  if (cpf.length !== 14) {
    showToast("Preencha o CPF corretamente.");
    return;
  }

  // Senha: validar apenas se o usuário preencheu o campo
  if (senha !== '') {
    const senhaValida = senha.length >= 8 && /[A-Z]/.test(senha) && /[a-z]/.test(senha) && /[0-9]/.test(senha);
    if (!senhaValida) {
      showToast("A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula e número.");
      return;
    }
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

document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    document.getElementById('btnEditar').click();
  }
});