// Confirmar eliminacion de elementos
function confirmarEliminacion(nombre) {
    return confirm('¿Estas seguro de eliminar a ' + nombre + '?');
}

// Validar formulario de registro
function validarRegistro() {
    var password = document.getElementById('password').value;
    var confirmar = document.getElementById('confirmar_password').value;
    
    console.log("Validando formulario..."); // debug
    
    if (password !== confirmar) {
        alert('Las contraseñas no coinciden');
        return false;
    }
    
    if (password.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    return true;
}

// Validar email
function validarEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

// Mostrar/ocultar contraseña
function togglePassword(inputId) {
    var input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Alerta automatica que desaparece
window.onload = function() {
    var alertas = document.querySelectorAll('.alerta');
    if (alertas.length > 0) {
        console.log("Se encontraron " + alertas.length + " alertas"); // para debug
        setTimeout(function() {
            alertas.forEach(function(alerta) {
                alerta.style.opacity = '0';
                setTimeout(function() {
                    alerta.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
}




