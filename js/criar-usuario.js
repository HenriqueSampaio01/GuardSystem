document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    document.getElementById('btnCriar').click();
  }
});

// ===== SALVAR =====
async function adicionarUsuario() {
  const nome = document.getElementById("add-nome").value.trim();
  const cpf = document.getElementById("add-cpf").value.trim();
  const senha = document.getElementById("add-senha").value.trim();

  // ===== VALIDAÇÕES =====
  if (!nome || !cpf || !senha) {
    showToast("Preencha todos os campos!");
    return;
  }

  // Nome: mínimo 2 partes, cada uma com pelo menos 3 letras
  const partes = nome.split(" ").filter(p => p.length > 0);
  if (partes.length < 2 || partes[0].length < 3 || partes[1].length < 3) {
    showToast("Informe nome e sobrenome, ambos com no mínimo 3 letras.");
    return;
  }

  if (cpf.length !== 14) {
    showToast("Preencha o CPF corretamente.");
    return;
  }

  const senhaValida = senha.length >= 8 && /[A-Z]/.test(senha) && /[a-z]/.test(senha) && /[0-9]/.test(senha);
  if (!senhaValida) {
    showToast("A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula e número.");
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