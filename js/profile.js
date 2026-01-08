document.getElementById('toggle-password').addEventListener('click', function(e) {
  e.preventDefault();
  
  var passwordElement = document.getElementById('password-value');
  var toggleElement = document.getElementById('toggle-password');
  var iconElement = toggleElement.querySelector('i');
  
  if (passwordElement.textContent === '••••••••') {
    // Mostrar senha real (substitua pelo valor real da senha)
    passwordElement.textContent = '<?php echo htmlspecialchars($linha["senha"]); ?>';
    toggleElement.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar senha';
  } else {
    // Ocultar senha
    passwordElement.textContent = '••••••••';
    toggleElement.innerHTML = '<i class="fas fa-eye"></i> Mostrar senha';
  }
}); 