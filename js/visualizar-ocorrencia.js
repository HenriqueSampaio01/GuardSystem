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
  assinatura_conselheiro: "view-assinatura-conselheiro",
  assinatura_responsavel: "view-assinatura-responsavel",
  assinatura_solicitante: "view-assinatura-solicitante",
};

document.addEventListener("DOMContentLoaded", async () => {
  const response = await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`);
  const ocorrencia = await response.json();

  if (!response.ok) {
    showToast(ocorrencia.mensagem || "Ocorrência não encontrada!");
    setTimeout(() => {
      window.location.href = "ocorrencias.html";
    }, 1200);
    return;
  }

  Object.entries(campos).forEach(([chave, elementoId]) => {
    const elemento = document.getElementById(elementoId);
    if (!elemento) return;
    elemento.value = ocorrencia[chave] ?? "";
  });
});