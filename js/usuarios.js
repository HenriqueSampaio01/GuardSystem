let pendingDeleteId = null;

//RENDERIZAR LISTA
async function renderUsuarios() {
    const response = await fetch("http://localhost/api/usuarios.php");
    const usuarios = await response.json(); // PHP -> JSON -> array JS

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

async function confirmarExcluirUsuario() {
  const response = await fetch(`http://localhost/api/usuarios.php?id=${pendingDeleteId}`, {method: "DELETE"});
  closeModal("modal-excluir-usuario");
  renderUsuarios();
  showToast("Usuário excluído!");
}

// ===== EDITAR (navegar com ID na URL) =====
function irParaEditar(id) {
  window.location.href = `editar-usuario.html?id=${id}`;
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", renderUsuarios);
