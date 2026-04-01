let ocorrencias = [
  { id: 1, conselheiro: "Diego" },
  { id: 2, conselheiro: "Henrique" },
  { id: 3, conselheiro: "Alexandre" },
  { id: 4, conselheiro: "Augusto" },
  { id: 5, conselheiro: "Eduardo" },
];

let pendingDeleteId = null;
let editingUserId = null;

// ===== NAVIGATION =====
function goTo(screenId) {
  document
    .querySelectorAll(".screen")
    .forEach((s) => s.classList.remove("active"));
  document.getElementById(screenId).classList.add("active");

  if (screenId === "screen-usuarios") renderUsuarios();
  if (screenId === "screen-ocorrencias") renderOcorrencias();
}

// ===== LOGIN =====
function doLogin() {
  const cpf = document.getElementById("login-cpf").value.trim();
  const senha = document.getElementById("login-senha").value.trim();
  if (!cpf || !senha) {
    showToast("Preencha CPF e senha!");
    return;
  }
  goTo("screen-home");
}

// ===== MASK CPF =====
function maskCPF(input) {
  let v = input.value.replace(/\D/g, "");
  if (v.length > 9)
    v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, "$1.$2.$3-$4");
  else if (v.length > 6)
    v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, "$1.$2.$3");
  else if (v.length > 3) v = v.replace(/^(\d{3})(\d{0,3}).*/, "$1.$2");
  input.value = v;
}

// ===== RENDER USUÁRIOS =====
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
          <button class="icon-btn" onclick="abrirEdicaoUsuario(${u.id})" title="Editar">✏️</button>
          <button class="icon-btn" onclick="pedirExcluirUsuario(${u.id})" title="Excluir">🗑️</button>
        </div>
      `;
    container.appendChild(item);
  });
}

function pedirExcluirUsuario(id) {
  pendingDeleteId = id;
  document.getElementById("modal-excluir-usuario").classList.add("open");
}

function confirmarExcluirUsuario() {
  usuarios = usuarios.filter((u) => u.id !== pendingDeleteId);
  closeModal("modal-excluir-usuario");
  renderUsuarios();
  showToast("Usuário excluído!");
}

function abrirEdicaoUsuario(id) {
  const u = usuarios.find((x) => x.id === id);
  editingUserId = id;
  document.getElementById("edit-nome").value = u.nome;
  document.getElementById("edit-cpf").value = u.cpf;
  document.getElementById("edit-senha").value = "";
  goTo("screen-editar-usuario");
}

function salvarEdicaoUsuario() {
  const nome = document.getElementById("edit-nome").value.trim();
  const cpf = document.getElementById("edit-cpf").value.trim();
  if (!nome || !cpf) {
    showToast("Preencha nome e CPF!");
    return;
  }
  const idx = usuarios.findIndex((u) => u.id === editingUserId);
  if (idx > -1) {
    usuarios[idx].nome = nome;
    usuarios[idx].cpf = cpf;
  }
  showToast("Usuário atualizado!");
  goTo("screen-usuarios");
}

// ===== RENDER OCORRÊNCIAS =====
function renderOcorrencias() {
  const container = document.getElementById("lista-ocorrencias");
  container.innerHTML = "";
  ocorrencias.forEach((o) => {
    const item = document.createElement("div");
    item.className = "list-item";
    item.innerHTML = `
        <span class="list-icon">📄</span>
        <div class="list-info">
          <div class="name">Ocorrência: ${o.id}</div>
          <div class="sub">Conselheiro: ${o.conselheiro}</div>
        </div>
        <div class="list-actions">
          <button class="icon-btn" onclick="editarOcorrencia(${o.id})" title="Editar">✏️</button>
          <button class="icon-btn" onclick="pedirExcluirOcorrencia(${o.id})" title="Excluir">🗑️</button>
        </div>
      `;
    container.appendChild(item);
  });
}

function pedirExcluirOcorrencia(id) {
  pendingDeleteId = id;
  document.getElementById("modal-excluir-ocorrencia").classList.add("open");
}

function confirmarExcluirOcorrencia() {
  ocorrencias = ocorrencias.filter((o) => o.id !== pendingDeleteId);
  closeModal("modal-excluir-ocorrencia");
  renderOcorrencias();
  showToast("Ocorrência excluída!");
}

function editarOcorrencia(id) {
  showToast("Edição de ocorrência em breve!");
}

// ===== MODAL =====
function closeModal(id) {
  document.getElementById(id).classList.remove("open");
  pendingDeleteId = null;
}

// click outside modal to close
document.querySelectorAll(".modal-overlay").forEach((overlay) => {
  overlay.addEventListener("click", function (e) {
    if (e.target === this) this.classList.remove("open");
  });
});

// ===== TOAST =====
function showToast(msg) {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2500);
}
