function login(){
  
  var A = document.getElementById("input").value
  var B = document.querySelector(".input2").value

        // Verificar combinações válidas usando ||
        if (
            (A === "Fernando" && B === "1234") || 
            (A === "Gustavo" && B === "abcd") || 
            (A === "Maria" && B === "teste123")
        ) {
            alert("Logado");
        } else {
            alert("Usuário ou senha erradas");
        }
 
};

var senha = $('#senha');
var olho= $("#olho");

olho.mousedown(function() {
senha.attr("type", "text");
});

olho.mouseup(function() {
senha.attr("type", "password");
});
// para evitar o problema de arrastar a imagem e a senha continuar exposta,
//citada pelo nosso amigo nos comentários
$( "#olho" ).mouseout(function() {
$("#senha").attr("type", "password");
});