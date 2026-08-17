async function doLogin() {
  const cpf = document.getElementById("login-cpf").value.trim();
  const senha = document.getElementById("login-senha").value.trim();
  const response = await fetch("/api/login.php", {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({cpf, senha})
  });
  const data = await response.json();

  if (response.ok && data.usuario) {
    window.location.href = "home.html";
  } else {
    showToast(data.mensagem || "Erro ao fazer login!");
  }
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') btnLogin.click()
})