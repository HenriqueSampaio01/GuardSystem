// ocorrencias.js — lógica da tela de Ocorrências

// TODO: substituir por chamada à API
let ocorrencias = [
  { id: 1, conselheiro: "Diego" },
  { id: 2, conselheiro: "Henrique" },
  { id: 3, conselheiro: "Alexandre" },
  { id: 4, conselheiro: "Augusto" },
  { id: 5, conselheiro: "Eduardo" },
];

let pendingDeleteId = null;

// ===== RENDERIZAR LISTA =====
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
        <button class="icon-btn" onclick="pedirExcluir(${o.id})" title="Excluir">🗑️</button>
      </div>
    `;
    container.appendChild(item);
  });
}

// ===== EXCLUIR =====
function pedirExcluir(id) {
  pendingDeleteId = id;
  openModal("modal-excluir-ocorrencia");
}

function confirmarExcluirOcorrencia() {
  ocorrencias = ocorrencias.filter((o) => o.id !== pendingDeleteId);
  closeModal("modal-excluir-ocorrencia");
  renderOcorrencias();
  showToast("Ocorrência excluída!");
}

// ===== EDITAR =====
function editarOcorrencia(id) {
  // TODO: navegar para tela de editar ocorrência
  showToast("Edição de ocorrência em breve!");
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", renderOcorrencias);
