const token = localStorage.getItem('token');
if (!token) {
    window.location.href = "login.html";
}

const params = new URLSearchParams(window.location.search);
const ocorrenciaId = parseInt(params.get("id"));

document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const tab = btn.dataset.tab;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
  });
});

// Mapeamento: chave da API → id do elemento HTML
const campos = {
  numero_registro:          "edit-numero-registro",
  natureza:                 "edit-natureza",
  local:                    "edit-local",
  data:                     "edit-data",
  hora:                     "edit-hora",
  solicitante:              "edit-solicitante",
  nome_mae:                 "edit-nome-mae",
  rg_mae:                   "edit-rg-mae",
  cpf_mae:                  "edit-cpf-mae",
  profissao_mae:            "edit-profissao-mae",
  nome_pai:                 "edit-nome-pai",
  rg_pai:                   "edit-rg-pai",
  cpf_pai:                  "edit-cpf-pai",
  profissao_pai:            "edit-profissao-pai",
  nome_responsavel:         "edit-nome-responsavel",
  rg_responsavel:           "edit-rg-responsavel",
  profissao_responsavel:    "edit-profissao-responsavel",
  endereco_residencial:     "edit-endereco-residencial",
  numero_residencial:       "edit-numero-residencial",
  bairro_residencial:       "edit-bairro-residencial",
  complemento_residencial:  "edit-complemento-residencial",
  telefone_residencial:     "edit-telefone-residencial",
  endereco_comercial:       "edit-endereco-comercial",
  numero_comercial:         "edit-numero-comercial",
  bairro_comercial:         "edit-bairro-comercial",
  complemento_comercial:    "edit-complemento-comercial",
  telefone_comercial:       "edit-telefone-comercial",
  historico:                "edit-historico",
};

// ===== PRÉ-PREENCHER FORMULÁRIO =====
async function carregarOcorrencia() {
  if (!ocorrenciaId || isNaN(ocorrenciaId)) {
    showToast("ID da ocorrência inválido!");
    return;
  }

  try {
    const response = await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`, {
      headers: {"Content-Type": "application/json", "Authorization": `Bearer ${token}`}
    });
    const ocorrencia = await response.json();

    if (!response.ok) {
      showToast(ocorrencia.mensagem || "Ocorrência não encontrada!");
      setTimeout(() => { window.location.href = "ocorrencias.html"; }, 1200);
      return;
    }

    Object.entries(campos).forEach(([chave, elementoId]) => {
      const el = document.getElementById(elementoId);
      if (el) {
        el.value = ocorrencia[chave] ?? "";
      }
    });
  } catch (error) {
    console.error("Erro ao carregar ocorrência:", error);
  }
}

// ===== SALVAR =====
async function salvarEdicao() {
  const natureza   = document.getElementById("edit-natureza")?.value.trim();
  const local      = document.getElementById("edit-local")?.value.trim();
  const data       = document.getElementById("edit-data")?.value.trim();
  const solicitante = document.getElementById("edit-solicitante")?.value.trim();
  const nome_mae   = document.getElementById("edit-nome-mae")?.value.trim();
  const historico  = document.getElementById("edit-historico")?.value.trim();

  if (!natureza || !local || !data || !solicitante || !nome_mae || !historico) {
    showToast("Preencha os campos obrigatórios: natureza, local, data, solicitante, nome da mãe e histórico.");
    return;
  }

  const payload = {};
  Object.entries(campos).forEach(([chave, elementoId]) => {
    const el = document.getElementById(elementoId);
    payload[chave] = el ? el.value.trim() : "";
  });

  const response = await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`, {
    method: "PUT",
    headers: {"Content-Type": "application/json", "Authorization": `Bearer ${token}`},
    body: JSON.stringify(payload),
  });

  const resposta = await response.json();

  if (!response.ok) {
    showToast(resposta.mensagem || "Erro ao atualizar ocorrência!");
    return;
  }

  showToast("Ocorrência atualizada!");
  setTimeout(() => { window.location.href = "ocorrencias.html"; }, 1000);
}

// Inicializa quando o DOM estiver totalmente carregado
document.addEventListener("DOMContentLoaded", carregarOcorrencia);