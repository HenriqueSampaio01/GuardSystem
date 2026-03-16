// editar-usuario.js — lógica da tela Editar Usuário

// Lê o ?id=X da URL para saber qual usuário editar
const params = new URLSearchParams(window.location.search);
const userId = parseInt(params.get("id"));

// TODO: substituir por chamada à API (GET /usuarios/:id)
// Dados mockados — mesma lista de usuarios.js
const usuariosMock = [
  { id: 1, nome: "Alexandre Rodrigues Filho", cpf: "000.111.222-33" },
  { id: 2, nome: "Antonio Augusto Aguiar",    cpf: "208.179.098-23" },
  { id: 3, nome: "Henrique Sampaio Occaso",   cpf: "444.555.666-77" },
  { id: 4, nome: "Diego Henrique Ribeiro",    cpf: "389.190.798-19" },
  { id: 5, nome: "Eduardo Henrique Domingues",cpf: "888.999.111-22" },
];

// ===== PRÉ-PREENCHER FORMULÁRIO =====
document.addEventListener("DOMContentLoaded", () => {
  const usuario = usuariosMock.find((u) => u.id === userId);
  if (usuario) {
    document.getElementById("edit-nome").value = usuario.nome;
    document.getElementById("edit-cpf").value  = usuario.cpf;
  }
});

// ===== SALVAR =====
function salvarEdicao() {
  const nome  = document.getElementById("edit-nome").value.trim();
  const cpf   = document.getElementById("edit-cpf").value.trim();
  const senha = document.getElementById("edit-senha").value.trim();

  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }

  // TODO: chamar API (PUT /usuarios/:id)
  showToast("Usuário atualizado!");
  setTimeout(() => {
    window.location.href = "../usuarios/usuarios.html";
  }, 1000);
}
