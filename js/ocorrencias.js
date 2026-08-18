let pendingDeleteId = null;

//RENDERIZAR LISTA
async function renderOcorrencias() {
  const response = await fetch("/api/ocorrencias.php");

  if (!response.ok) {
    showToast("Erro ao carregar ocorrências!");
    return;
  }

  const ocorrencias = await response.json();

  const container = document.getElementById("lista-ocorrencias");
  container.innerHTML = "";

  ocorrencias.forEach((o) => {
    const item = document.createElement("div");
    item.className = "list-item";
    item.innerHTML = `
      <span class="list-icon">📄</span>
      <div class="list-info">
        <div class="name">${o.local}</div>
        <div class="sub">📅 ${o.data} · 👤 ${o.conselheiro ?? "Sem conselheiro"}</div>
      </div>
      <div class="list-actions">
        <button class="icon-btn" onclick="visualizarOcorrencia(${o.id})" title="Visualizar">👁️</button>
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

async function confirmarExcluirOcorrencia() {
  await fetch(`/api/ocorrencias.php?id=${pendingDeleteId}`, {method: "DELETE"});
  const data = await response.json();
  closeModal("modal-excluir-ocorrencia");

  if (!response.ok) {
    showToast(data.mensagem || "Erro ao excluir ocorrência!");
    return;
  }

  renderOcorrencias();
  showToast("Ocorrência excluída!");
}

// visualizar
function visualizarOcorrencia(id) {
  window.location.href = `visualizar-ocorrencia.html?id=${id}`;
}

// ===== EDITAR =====
function editarOcorrencia(id) {
  window.location.href = `editar-ocorrencia.html?id=${id}`;
}

// ===== INIT =====
document.addEventListener("DOMContentLoaded", renderOcorrencias);
