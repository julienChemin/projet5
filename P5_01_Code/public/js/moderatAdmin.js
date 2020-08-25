if (document.getElementById('moderatAdmin')) {
    let linksToModo = document.querySelectorAll('.toModerator');
    let linksToAdmin = document.querySelectorAll('.toAdmin');
    let linksToNormalUser = document.querySelectorAll('.toNormalUser');
    let linksToDelete = document.querySelectorAll('.toDelete'); 

    let pathToModo = '&toAdmin=false&toModerator=true';
    let pathToAdmin = '&toAdmin=true&toModerator=false';
    let pathToNormalUser = '&toAdmin=false&toModerator=false';

    let modal = document.getElementById('modal');
    let textModal = document.querySelector('#modal > div > p');
    let btnConfirm = document.querySelector('#modal a');
    let btnCancel = document.querySelector("#modal input[name='cancel']");

    btnCancel.addEventListener(
        'click', function () {
            modal.style.display='none';
        }
    );

    for (let i=0;i<linksToModo.length;i++) {
        linksToModo[i].addEventListener(
            'click', function () {
                let name = linksToModo[i].parentNode.parentNode.childNodes[1].textContent.trim();
                let schoolName = linksToModo[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade de modérateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToModo;
            }
        );
    }

    for (let i=0;i<linksToAdmin.length;i++) {
        linksToAdmin[i].addEventListener(
            'click', function () {
                let name = linksToAdmin[i].parentNode.parentNode.childNodes[1].textContent.trim();
                let schoolName = linksToAdmin[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade d'administrateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToAdmin;
            }
        );
    }

    for (let i=0;i<linksToNormalUser.length;i++) {
        linksToNormalUser[i].addEventListener(
            'click', function () {
                let name = linksToNormalUser[i].parentNode.parentNode.childNodes[1].textContent.trim();
                let schoolName = linksToNormalUser[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade d'utilisateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToNormalUser;
            }
        );
    }

    for (let i=0;i<linksToDelete.length;i++) {
        linksToDelete[i].addEventListener(
            'click', function () {
                let name = linksToDelete[i].parentNode.parentNode.childNodes[1].textContent.trim();
                let schoolName = linksToDelete[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Supprimer définitivement le compte de " + name + "  ?";
                btnConfirm.href = "indexAdmin.php?action=delete&elem=user&userName=" + name + "&schoolName=" + schoolName;
            }
        );
    }

    //form add moderator
    let forms = document.querySelectorAll('.formAddModerator');
    let linksAdd = document.querySelectorAll('.formAddModerator > p:first-of-type');
    let formContent = document.querySelectorAll('.formAddModerator > div:first-of-type');
    let btnCancelAddModerator = document.querySelectorAll('.formAddModerator input[type="button"]')

    for (let i=0; i<forms.length;i++) {
        linksAdd[i].addEventListener(
            'click', function () {
                formContent[i].style.height = "300px";
                forms[i].style.padding = "50px 100px";
            }
        );

        btnCancelAddModerator[i].addEventListener(
            'click', function () {
                formContent[i].style.height = "0px";
                forms[i].style.padding = "0px";
            }
        );
    }

    //message box
    let msgBox = document.querySelectorAll('.blockMsg');
    let btnClose = document.querySelectorAll('.blockMsg .fa-times');

    for (let i=0; i<btnClose.length;i++) {
        btnClose[i].addEventListener(
            'click', function () {
                msgBox[i].style.display = "none";
            }
        );
    }
}
