const params = new URLSearchParams(window.location.search);
const ocorrenciaId = parseInt(params.get("id"));

// ===== PRÉ-PREENCHER FORMULÁRIO =====
document.addEventListener("DOMContentLoaded", async () => {
  const response = await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`);
  const ocorrencia = await response.json();
  if (ocorrencia) {
    document.getElementById("edit-data").value = ocorrencia.data;
    document.getElementById("edit-hora").value  = ocorrencia.hora;
    document.getElementById("edit-rua").value  = ocorrencia.rua;
    document.getElementById("edit-bairro").value  = ocorrencia.bairro;
    document.getElementById("edit-cep").value  = ocorrencia.cep;
    document.getElementById("edit-tipo").value  = ocorrencia.tipo;
    document.getElementById("edit-forma-recebimento").value  = ocorrencia.forma_de_recebimento;
  }
});

// ===== SALVAR =====
async function salvarEdicao() {
  const data = document.getElementById("edit-data").value.trim();
  const hora = document.getElementById("edit-hora").value.trim();
  const rua = document.getElementById("edit-rua").value.trim();
  const bairro = document.getElementById("edit-bairro").value.trim();
  const cep = document.getElementById("edit-cep").value.trim();
  const tipo = document.getElementById("edit-tipo").value.trim();
  const forma_de_recebimento = document.getElementById("edit-forma-recebimento").value.trim();

  if (!data || !hora || !rua || !bairro || !cep || !tipo || !forma_de_recebimento) {
    showToast("Preencha todos os campos!");
    return;
  }

  await fetch(`/api/ocorrencias.php?id=${ocorrenciaId}`, {
  method: "PUT",
  headers: {"Content-Type": "application/json"},
  body: JSON.stringify({data, hora, rua, bairro, cep, tipo, forma_de_recebimento: forma_de_recebimento})
 });

  showToast("Ocorrência atualizada!");
  setTimeout(() => {
    window.location.href = "ocorrencias.html";
  }, 1000);
}
