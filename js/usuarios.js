const token = localStorage.getItem('token');
if (!token) {
    window.location.href = "login.html";
}

let pendingDeleteId = null;

//RENDERIZAR LISTA
async function renderUsuarios() {
  const token = localStorage.getItem('token');
  const response = await fetch("/api/usuarios.php", {
      headers: {"Content-Type": "application/json", "Authorization": `Bearer ${token}`},
  });

    if (!response.ok) {
      showToast("Erro ao carregar usuários!");
      return;
    }

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
  const token = localStorage.getItem('token');
  const response = await fetch(`/api/usuarios.php?id=${pendingDeleteId}`, {
    method: "DELETE",
    headers: {"Authorization": `Bearer ${token}`}
});
  const data = await response.json();
  closeModal("modal-excluir-usuario");

  if (!response.ok) {
    showToast(data.mensagem || "Erro ao excluir usuário!");
    return;
  }

  renderUsuarios();
  showToast("Usuário excluído!");
}

// ===== EDITAR (navegar com ID na URL) =====
function irParaEditar(id) {
  window.location.href = `editar-usuario.html?id=${id}`;
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", renderUsuarios);
