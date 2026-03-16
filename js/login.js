// login.js — lógica da tela de Login

function doLogin() {
  const cpf = document.getElementById("login-cpf").value.trim();
  const senha = document.getElementById("login-senha").value.trim();

  if (!cpf || !senha) {
    showToast("Preencha CPF e senha!");
    return;
  }

  // TODO: validar credenciais com a API
  window.location.href = "/HTML/home.html";
}
