async function doLogin() {
  const response = await fetch(`https://guardsystem.up.railway.app/api/login.php`);
  const usuario = await response.json();

  if (usuario) {
    document.getElementById("login-cpf").value = usuario.cpf;
    document.getElementById("login-senha").value  = usuario.senha;
  }
}
