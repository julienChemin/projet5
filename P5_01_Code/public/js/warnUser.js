let form = document.getElementById('formWarnUser');
let btnSubmit = document.getElementById('submit');
let msgBox = document.getElementById('msgBox');
btnSubmit.addEventListener(
    'click', function(e) {
        e.preventDefault();
        msgBox.classList.remove('hide');
        msgBox.textContent = "Traitement en cours..";
        if (!sessionStorage.hasOwnProperty('warnDone') && form.elements.reasonWarn.value !== '') {
            let data = new FormData(form);
            ajaxPost('indexAdmin.php?action=addWarning', data, function(response) {
                if (response === 'true') {
                    msgBox.textContent = "L'utilisateur a été averti";
                    sessionStorage.setItem('warnDone', true);
                } else if (response !== 'false') {
                    msgBox.textContent = "Le compte a reçu un avertissement, mais le mail n'a pas pu etre envoyé car l'adresse renseignée n'existe pas";
                    sessionStorage.setItem('warnDone', true);
                } else {
                    msgBox.textContent = 'L\'utilisateur n\'existe pas';
                }
            });
        } else if (sessionStorage.hasOwnProperty('warnDone')) {
            msgBox.textContent = 'L\'avertissement a déja été envoyé';
        } else if (form.elements.reasonWarn.value === '') {
            msgBox.textContent = 'Vous devez indiquer la raison de l\'avertissement';
        }
    }
);
window.addEventListener(
    'load', function() {
        sessionStorage.clear();   
    }
);