let modal = document.getElementById('modal');
let textModal = document.querySelector('#modal > div > p');
let btnConfirm = document.querySelector('#modal a');
let btnCancel = document.querySelector("#modal input[name='cancel']");

btnCancel.addEventListener('click', function(){
	modal.style.display='none';
});

//moderatAdminView
if (document.getElementById('moderatAdmin')) {
	let linksToModo = document.querySelectorAll('.toModerator');
	let linksToAdmin = document.querySelectorAll('.toAdmin');
	let linksToNormalUser = document.querySelectorAll('.toNormalUser'); 

	let pathToModo = '&isAdmin=false&isModerator=true';
	let pathToAdmin = '&isAdmin=true&isModerator=false';
	let pathToNormalUser = '&isAdmin=false&isModerator=false';

	for (let i=0;i<linksToModo.length;i++) {
		linksToModo[i].addEventListener('click', function(){
			let name = linksToModo[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToModo[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Passer " + name + " au grade de modérateur ?";
			btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToModo;
		});
	}

	for (let i=0;i<linksToAdmin.length;i++) {
		linksToAdmin[i].addEventListener('click', function(){
			let name = linksToAdmin[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToAdmin[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Passer " + name + " au grade d'administrateur ?";
			btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToAdmin;
		});
	}

	for (let i=0;i<linksToNormalUser.length;i++) {
		linksToNormalUser[i].addEventListener('click', function(){
			let name = linksToNormalUser[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToNormalUser[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Passer " + name + " au grade d'utilisateur ?";
			btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToNormalUser;
		});
	}
}

//moderatUsersView
if (document.getElementById('moderatUsers')) {
	let linksToModo = document.querySelectorAll('.toModerator');
	let linksToActive = document.querySelectorAll('.toActive');
	let linksToInactive = document.querySelectorAll('.toInactive');
	let linksToDelete = document.querySelectorAll('.toDelete');

	let pathToModo = '&isAdmin=false&isModerator=true';

	for (let i=0;i<linksToModo.length;i++) {
		linksToModo[i].addEventListener('click', function(){
			let name = linksToModo[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToModo[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Passer " + name + " au grade de modérateur ?";
			btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToModo;
		});
	}

	for (let i=0;i<linksToActive.length;i++) {
		linksToActive[i].addEventListener('click', function(){
			let name = linksToActive[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToModo[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Activer le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=toggleIsActive&userName=" + name + "&schoolName=" + schoolName;
		});
	}

	for (let i=0;i<linksToInactive.length;i++) {
		linksToInactive[i].addEventListener('click', function(){
			let name = linksToInactive[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToModo[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Désactiver le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=toggleIsActive&userName=" + name + "&schoolName=" + schoolName;
		});
	}

	for (let i=0;i<linksToDelete.length;i++) {
		linksToDelete[i].addEventListener('click', function(){
			let name = linksToDelete[i].parentNode.parentNode.childNodes[1].textContent;
			let schoolName = linksToDelete[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Supprimer définitivement le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=delete&elem=user&userName=" + name + "&schoolName=" + schoolName;
		});
	}
}
