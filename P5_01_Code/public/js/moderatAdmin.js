if (document.getElementById('moderatAdmin')) {
    let linksToModo = document.querySelectorAll('.toModerator');
    let linksToAdmin = document.querySelectorAll('.toAdmin');
    let linksToNormalUser = document.querySelectorAll('.toNormalUser');
    let linksToDelete = document.querySelectorAll('.toDelete');
    let linksToLeaveSchool = document.querySelectorAll('.toLeaveSchool');

    let pathToModo = '&toAdmin=false&toModerator=true';
    let pathToAdmin = '&toAdmin=true&toModerator=false';
    let pathToNormalUser = '&toAdmin=false&toModerator=false';

    let modal = document.getElementById('modal');
    let textModal = document.querySelector('#modal > div > p');
    let btnConfirm = document.querySelector('#modal a');
    let btnCancel = document.querySelector("#modal input[name='cancel']");

    /* BUTTON CANCEL */
    btnCancel.addEventListener(
        'click', function () {
            modal.style.display='none';
        }
    );

    /** ALL BUTTON USER TO MODO */
    for (let i=0;i<linksToModo.length;i++) {
        linksToModo[i].addEventListener(
            'click', function () {
                let name = linksToModo[i].getAttribute('userpseudo');
                let schoolName = linksToModo[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade de modérateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToModo;
            }
        );
    }

    /** ALL BUTTON MODO TO ADMIN */
    for (let i=0;i<linksToAdmin.length;i++) {
        linksToAdmin[i].addEventListener(
            'click', function () {
                let name = linksToAdmin[i].getAttribute('userpseudo');
                let schoolName = linksToAdmin[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade d'administrateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToAdmin;
            }
        );
    }

    /** ALL BUTTON MODO TO USER */
    for (let i=0;i<linksToNormalUser.length;i++) {
        linksToNormalUser[i].addEventListener(
            'click', function () {
                let name = linksToNormalUser[i].getAttribute('userpseudo');
                let schoolName = linksToNormalUser[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Passer " + name + " au grade d'utilisateur ?";
                btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToNormalUser;
            }
        );
    }

    /** ALL BUTTON DELETE USER */
    for (let i=0;i<linksToDelete.length;i++) {
        linksToDelete[i].addEventListener(
            'click', function () {
                let name = linksToDelete[i].getAttribute('userpseudo');
                let schoolName = linksToDelete[i].getAttribute('schoolname');

                modal.style.display = 'flex';
                textModal.textContent = "Supprimer définitivement le compte de " + name + "  ?";
                btnConfirm.href = "indexAdmin.php?action=delete&elem=user&userName=" + name + "&schoolName=" + schoolName;
            }
        );
    }

    /** ALL BUTTON REMOVE USER FROM SCHOOL */
	for (let i=0;i<linksToLeaveSchool.length;i++) {
		linksToLeaveSchool[i].addEventListener('click', function(){
			let name = linksToLeaveSchool[i].getAttribute('userpseudo');
			let schoolName = linksToLeaveSchool[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = name + " ne fera plus parti de votre établissement";
			btnConfirm.href = "indexAdmin.php?action=leaveSchool&userName=" + name + "&schoolName=" + schoolName;
		});
	}

    //form add moderator
    let forms = document.querySelectorAll('.formAddModerator');
    let linksAdd = document.querySelectorAll('.formAddModerator > p:first-of-type');
    let formContent = document.querySelectorAll('.formAddModerator > div:first-of-type');
    let btnCancelAddModerator = document.querySelectorAll('.formAddModerator input[type="button"]');

    for (let i=0; i<forms.length;i++) {
        linksAdd[i].addEventListener(
            'click', function () {
                formContent[i].style.height = "400px";
                forms[i].style.padding = "5px";
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
