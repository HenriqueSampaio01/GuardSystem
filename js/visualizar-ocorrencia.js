const token = localStorage.getItem('token');
if (!token) {
    window.location.href = "login.html";
}

const params = new URLSearchParams(window.location.search);
const ocorrenciaId = parseInt(params.get("id"));

const campos = {
  id: "view-id",
  numero_registro: "view-numero-registro",
  natureza: "view-natureza",
  local: "view-local",
  data: "view-data",
  hora: "view-hora",
  solicitante: "view-solicitante",
  conselheiro: "view-conselheiro",
  nome_mae: "view-nome-mae",
  rg_mae: "view-rg-mae",
  cpf_mae: "view-cpf-mae",
  profissao_mae: "view-profissao-mae",
  nome_pai: "view-nome-pai",
  rg_pai: "view-rg-pai",
  cpf_pai: "view-cpf-pai",
  profissao_pai: "view-profissao-pai",
  nome_responsavel: "view-nome-responsavel",
  rg_responsavel: "view-rg-responsavel",
  profissao_responsavel: "view-profissao-responsavel",
  endereco_residencial: "view-endereco-residencial",
  numero_residencial: "view-numero-residencial",
  bairro_residencial: "view-bairro-residencial",
  complemento_residencial: "view-complemento-residencial",
  telefone_residencial: "view-telefone-residencial",
  endereco_comercial: "view-endereco-comercial",
  numero_comercial: "view-numero-comercial",
  bairro_comercial: "view-bairro-comercial",
  complemento_comercial: "view-complemento-comercial",
  telefone_comercial: "view-telefone-comercial",
  historico: "view-historico",
};

async function carregarOcorrencia() {
  const response = await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`, {
    headers: { "Content-Type": "application/json", "Authorization": `Bearer ${token}` }
  });
  const ocorrencia = await response.json();

  if (!response.ok) {
    showToast(ocorrencia.mensagem || "Ocorrência não encontrada!");
    setTimeout(() => { window.location.href = "ocorrencias.html"; }, 1200);
    return;
  }

  // Preenche todos os campos de texto/input
  Object.entries(campos).forEach(([chave, elementoId]) => {
    const elemento = document.getElementById(elementoId);
    if (!elemento) return;
    elemento.value = ocorrencia[chave] ?? "";
  });

  // Assinaturas
  const assinaturas = [
    ["assinatura_conselheiro", "view-assinatura-conselheiro"],
    ["assinatura_responsavel", "view-assinatura-responsavel"],
    ["assinatura_solicitante", "view-assinatura-solicitante"],
  ];
  assinaturas.forEach(([chave, elementoId]) => {
    const img = document.getElementById(elementoId);
    if (!img) return;
    if (ocorrencia[chave]) {
      img.src = "data:image/png;base64," + ocorrencia[chave];
      img.style.display = "block";
    } else {
      img.style.display = "none";
    }
  });
}

async function carregarAdolescentes() {
  const container = document.getElementById("lista-adolescentes");

  try {
    const response = await fetch(`/api/adolescentes.php?ocorrencia_id=${ocorrenciaId}`, {
      headers: { "Authorization": `Bearer ${token}` }
    });
    const adolescentes = await response.json();

    if (!response.ok) {
      container.innerHTML = `<p style="color: var(--text-dim); font-size: 14px; text-align: center;">
        Erro ao carregar adolescentes.
      </p>`;
      return;
    }

    if (adolescentes.length === 0) {
      container.innerHTML = `<p style="color: var(--text-dim); font-size: 14px; text-align: center; margin-top: 12px;">
        Nenhum adolescente vinculado a esta ocorrência.
      </p>`;
      return;
    }

    container.innerHTML = "";
    adolescentes.forEach((a, i) => {
      const card = document.createElement("div");
      card.className = "list-item";
      card.style.marginBottom = "10px";
      card.innerHTML = `
        <span class="list-icon">🧑</span>
        <div class="list-info">
          <div class="name">${a.nome}</div>
          <div class="sub">
            📅 ${a.data_nasc ?? "—"}
            ${a.escola ? ` · 🏫 ${a.escola}` : ""}
          </div>
        </div>
      `;
      container.appendChild(card);
    });

  } catch (error) {
    container.innerHTML = `<p style="color: var(--text-dim); font-size: 14px; text-align: center;">
      Erro ao carregar adolescentes.
    </p>`;
    console.error("Erro ao carregar adolescentes:", error);
  }
}

carregarOcorrencia();
carregarAdolescentes();