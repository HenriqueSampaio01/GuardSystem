// usuarios.js — lógica da tela de Usuários

// TODO: substituir por chamada à API
let usuarios = [
  { id: 1, nome: "Alexandre Rodrigues Filho", cpf: "000.111.222-33" },
  { id: 2, nome: "Antonio Augusto Aguiar",    cpf: "208.179.098-23" },
  { id: 3, nome: "Henrique Sampaio Occaso",   cpf: "444.555.666-77" },
  { id: 4, nome: "Diego Henrique Ribeiro",    cpf: "389.190.798-19" },
  { id: 5, nome: "Eduardo Henrique Domingues",cpf: "888.999.111-22" },
];

let pendingDeleteId = null;

// ===== RENDERIZAR LISTA =====
function renderUsuarios() {
  const container = document.getElementById("lista-usuarios");
  container.innerHTML = "";

  usuarios.forEach((u) => {
    const item = document.createElement("div");
    item.className = "list-item";
    item.innerHTML = `
      <span class="list-icon">👤</span>
      <div class="list-info">
        <div class="name">${u.nome}</div>
        <div class="sub">CPF: ${u.cpf}</div>
      </div>
      <div class="list-actions">
        <button class="icon-btn" onclick="irParaEditar(${u.id})" title="Editar">✏️</button>
        <button class="icon-btn" onclick="pedirExcluir(${u.id})" title="Excluir">🗑️</button>
      </div>
    `;
    container.appendChild(item);
  });
}

// ===== EXCLUIR =====
function pedirExcluir(id) {
  pendingDeleteId = id;
  openModal("modal-excluir-usuario");
}

function confirmarExcluirUsuario() {
  usuarios = usuarios.filter((u) => u.id !== pendingDeleteId);
  closeModal("modal-excluir-usuario");
  renderUsuarios();
  showToast("Usuário excluído!");
}

// ===== EDITAR (navegar com ID na URL) =====
function irParaEditar(id) {
  window.location.href = `../editar-usuario/editar-usuario.html?id=${id}`;
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", renderUsuarios);
