// ===================================================
//  GuardSystem — Utilitários Compartilhados
//  Funções usadas em múltiplas páginas
// ===================================================

// ===== MÁSCARA CPF =====
function maskCPF(input) {
  let v = input.value.replace(/\D/g, "");
  if (v.length > 9)
    v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{0,2}).*/, "$1.$2.$3-$4");
  else if (v.length > 6)
    v = v.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, "$1.$2.$3");
  else if (v.length > 3)
    v = v.replace(/^(\d{3})(\d{0,3}).*/, "$1.$2");
  input.value = v;
}

// ===== TOAST =====
function showToast(msg) {
  const t = document.getElementById("toast");
  if (!t) return;
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 2500);
}

// ===== MODAL =====
function openModal(id) {
  document.getElementById(id).classList.add("open");
}

function closeModal(id) {
  document.getElementById(id).classList.remove("open");
}

// Fechar modal ao clicar fora
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".modal-overlay").forEach((overlay) => {
    overlay.addEventListener("click", function (e) {
      if (e.target === this) this.classList.remove("open");
    });
  });
});
