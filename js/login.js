async function doLogin() {
  const cpf = document.getElementById("login-cpf").value.trim();
  const senha = document.getElementById("login-senha").value.trim();
  const response = await fetch(`https://guardsystem.up.railway.app/api/login.php`, {
    method: "POST",
    headers: {"Content-Type": "application/json"},
    body: JSON.stringify({cpf, senha})
  });
  const usuario = await response.json();

  if (usuario.usuario) {
    window.location.href = "home.html";
} else {
    showToast(usuario.mensagem);
}
}
