if (document.getElementById('moderatUsers') !== null) {
	let linksToModo = document.querySelectorAll('.toModerator');
	let linksToActive = document.querySelectorAll('.toActive');
	let linksToInactive = document.querySelectorAll('.toInactive');
	let linksToDelete = document.querySelectorAll('.toDelete');

	let pathToModo = '&toAdmin=false&toModerator=true';

	let modal = document.getElementById('modal');
	let textModal = document.querySelector('#modal > div > p');
	let btnConfirm = document.querySelector('#modal a');
	let btnCancel = document.querySelector("#modal input[name='cancel']");

	btnCancel.addEventListener('click', function(){
		modal.style.display='none';
	});

	for (let i=0;i<linksToModo.length;i++) {
		linksToModo[i].addEventListener('click', function(){
			let name = linksToModo[i].parentNode.parentNode.childNodes[1].textContent.trim();
			let schoolName = linksToModo[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Passer " + name + " au grade de modérateur ?";
			btnConfirm.href = "indexAdmin.php?action=editGrade&userName=" + name + "&schoolName=" + schoolName + pathToModo;
		});
	}

	for (let i=0;i<linksToActive.length;i++) {
		linksToActive[i].addEventListener('click', function(){
			let name = linksToActive[i].parentNode.parentNode.childNodes[1].textContent.trim();
			let schoolName = linksToActive[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Activer le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=toggleUserIsActive&userName=" + name + "&schoolName=" + schoolName;
		});
	}

	for (let i=0;i<linksToInactive.length;i++) {
		linksToInactive[i].addEventListener('click', function(){
			let name = linksToInactive[i].parentNode.parentNode.childNodes[1].textContent.trim();
			let schoolName = linksToInactive[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Désactiver le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=toggleUserIsActive&userName=" + name + "&schoolName=" + schoolName;
		});
	}

	for (let i=0;i<linksToDelete.length;i++) {
		linksToDelete[i].addEventListener('click', function(){
			let name = linksToDelete[i].parentNode.parentNode.childNodes[1].textContent.trim();
			let schoolName = linksToDelete[i].getAttribute('schoolname');

			modal.style.display = 'flex';
			textModal.textContent = "Supprimer définitivement le compte de " + name + "  ?";
			btnConfirm.href = "indexAdmin.php?action=delete&elem=user&userName=" + name + "&schoolName=" + schoolName;
		});
	}

	//form add group
	let formAddGroup = document.querySelectorAll('.formAddGroup');
	let btnOpenForm  = document.querySelectorAll('.formAddGroup > p');
	let formContent = document.querySelectorAll('.formAddGroup > div:first-of-type');
	let blockListGroup = document.querySelectorAll('.formAddGroup > div:last-of-type');
	let listsGroup = document.querySelectorAll('.listGroup');
	let regexGroupName = /^[a-z0-9]+[a-z0-9-_]*[a-z0-9]+$/i;
	//edit user group
	let btnEditGroup = document.querySelectorAll('.btnEditGroup');
	let listEditGroup = document.querySelectorAll('.listEditGroup');
	let userGroup = document.querySelectorAll('.userGroup');
	let inputListGroup = document.querySelectorAll('.inputListGroup');

	function groupNameOk(str) {
		if (regexGroupName.test(str) && str.length <= 30 && str.trim().toLowerCase() !== 'none') {
			return true;
		} else {
			return false;
		}
	}
	function addGroupToList(group, blockGroup, schoolName) {
		let elemLi = document.createElement('li');

		let elemSpan = document.createElement('span');
		elemSpan.textContent = group;

		let linkConfirmDelete = document.createElement('a');
		linkConfirmDelete.href = 'indexAdmin.php?action=deleteGroup&schoolName=' + schoolName + '&group=' + group;
		linkConfirmDelete.textContent = "Supprimer définitivement '" + group + "' ?";
		linkConfirmDelete.classList.add('confirmDeleteGroup');

		let elemI = document.createElement('i');
		elemI.classList.add('fas');
		elemI.classList.add('fa-times');
		elemI.classList.add('deleteGroup');
		elemI.addEventListener('click', function(){
			//display btnConfirmDelete
			linkConfirmDelete.style.display = "inline";
			elemSpan.style.display = "none";
			elemI.style.display = "none";
			setTimeout(function(){
				linkConfirmDelete.style.display = "none";
				elemSpan.style.display = "inline-block";
				elemI.style.display = "inline-block";
			}, 3000);
		});

		elemLi.appendChild(elemSpan);
		elemLi.appendChild(linkConfirmDelete);
		elemLi.appendChild(elemI);
		blockGroup.appendChild(elemLi);
	}

	//form add group
	for (let i=0;i<formAddGroup.length;i++) {
		//open form and fill list group
		btnOpenForm[i].addEventListener('click', function(){
			formContent[i].style.height = "300px";
			blockListGroup[i].style.display = "flex";
			formAddGroup[i].style.padding = "5px";

			//maj block list groups
			url = 'indexAdmin.php?action=getGroup&schoolName=' + formAddGroup[i].elements.schoolName.value;
			ajaxGet(url, function(response){
				if (response.length > 0 && response !== 'false') {
					let listSchoolGroups = JSON.parse(response);
					listsGroup[i].innerHTML = "";
					if (listSchoolGroups !== null) {
						listSchoolGroups.forEach(group =>{
							addGroupToList(group, listsGroup[i], formAddGroup[i].elements.schoolName.value, i);
						});
					}
				}
			});
		});
		//btn cancel, close form
		formAddGroup[i].elements.cancel.addEventListener('click', function(){
			formContent[i].style.height = "0px";
			blockListGroup[i].style.display = "none";
			formAddGroup[i].style.padding = "0";
		});
		//btn submit, add new group
		formAddGroup[i].elements.submit.addEventListener('click', function(e){
			e.preventDefault();
			if (groupNameOk(formAddGroup[i].elements.addGroup.value)) {
				//creat new group
				let url = 'indexAdmin.php?action=createGroup&schoolName=' + formAddGroup[i].elements.schoolName.value;
				url += '&group=' + formAddGroup[i].elements.addGroup.value;
				ajaxGet(url, function(response){
					if (response.length > 0 && response !== 'false') {
						response = JSON.parse(response);
						//maj list group (to edit user group)
						let listToEdit = document.querySelectorAll('#school' + i + ' .inputListGroup');
						for (let j=0;j<listToEdit.length;j++) {
							let elemOption = document.createElement('option');
							elemOption.value = formAddGroup[i].elements.addGroup.value;
							elemOption.textContent = formAddGroup[i].elements.addGroup.value;
							listToEdit[j].appendChild(elemOption);
						}
						//maj block list groups
						formAddGroup[i].elements.addGroup.value = "";
						url = 'indexAdmin.php?action=getGroup&schoolName=' + formAddGroup[i].elements.schoolName.value;
						ajaxGet(url, function(response){
							if (response.length > 0 && response !== 'false') {
								let listSchoolGroups = JSON.parse(response);
								listsGroup[i].innerHTML = "";
								if (listSchoolGroups !== null) {
									listSchoolGroups.forEach(group =>{
										addGroupToList(group, listsGroup[i], formAddGroup[i].elements.schoolName.value, i);
									});
								}
							}
						});
					}
				});
			} else {
				//group name not valid
				formAddGroup[i].elements.addGroup.style.border = "solid 1px red";
				formAddGroup[i].elements.addGroup.style.color = "red";
				setTimeout(function(){
					formAddGroup[i].elements.addGroup.style.border = "solid 1px #CF8B3F";
					formAddGroup[i].elements.addGroup.style.color = "black";
				}, 1500);
			}
		});
	}

	//edit user group
	for (let i=0;i<btnEditGroup.length;i++) {
		//display list group
		btnEditGroup[i].addEventListener('click', function(){
			listEditGroup[i].style.display = "block";
			btnEditGroup[i].style.display = "none";
		});
		//edit user group
		listEditGroup[i].addEventListener('change', function(){
			listEditGroup[i].style.display = "none";
			btnEditGroup[i].style.display = "inline-block";

			url = 'indexAdmin.php?action=setGroup&userName=' + listEditGroup[i].parentNode.previousElementSibling.textContent;
			url += '&group=' + inputListGroup[i].value;
			ajaxGet(url, function(response){
				if (response.length > 0 && response !== 'false') {
					userGroup[i].textContent = inputListGroup[i].value;
				}
			});
		});
	}
}
