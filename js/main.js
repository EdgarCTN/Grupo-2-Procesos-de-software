window.onload = function() {
    var errorMessage = document.getElementById('error-message');
    if (errorMessage.textContent !== "") {
        errorMessage.style.display = "block";

        // Ocultar el mensaje de error después de 2 segundos
        setTimeout(function() {
            errorMessage.style.display = "none";
        }, 2000);
    }
}
