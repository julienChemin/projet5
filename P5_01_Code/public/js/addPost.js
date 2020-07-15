let formAddPost;
if (document.getElementById('addSchoolPost') !== null) {
	formAddPost = document.querySelector('#addSchoolPost > form');
} else {
	formAddPost = document.querySelector('#addPost > form');
}
let btnAddFolder = document.getElementById('btnAddFolder');
let btnAddFile = document.getElementById('btnAddFile');
let blockAddFile = document.getElementById('blockAddFile');
let blockTitle = document.getElementById('blockTitle');
let inputTitle = document.getElementById('title');
let blockUploadFile = document.getElementById('blockUploadFile');
let labelUploadFile = document.querySelector('label[for="uploadFile"]');
let blockVideoLink = document.getElementById('blockVideoLink');
let preview = document.getElementById('preview');
let blockTinyMce = document.getElementById('blockTinyMce');
let inputSubmit = document.querySelector("input[type='submit']");

window.addEventListener('load', function(){
	//look if user try to post in folder
	let url = window.location.search.split('&');
	let arr = [];
	for (let i=1; i<url.length; i++) {
		let splitUrl = url[i].split('=');
		arr[splitUrl[0]] = splitUrl[1];
	}
	if (arr['folder'] !== undefined) {
		formAddPost.elements.folder.value = arr['folder'];
	}
});

btnAddFolder.addEventListener('click', function(){
	//display inputs add folder, hide inputs add file
	btnAddFolder.style.backgroundColor = '#222224';
	btnAddFolder.style.border = 'solid 1px #CF8B3F';
	blockTitle.style.display  = "flex";
	inputTitle.placeholder = '';
	blockUploadFile.style.display = 'flex';
	labelUploadFile.textContent = "Thumbnail du dossier (jpg, png, gif) - non obligatoire - (max : 5Mo)";
	blockTinyMce.style.display = 'flex';
	inputSubmit.style.display = "inline-block";

	btnAddFile.style.backgroundColor = '#161617';
	btnAddFile.style.border = 'none';
	blockAddFile.style.display = "none";
	blockTags.style.display = 'none';
	if (btnActif.btn !== "") {
		btnActif.btn.style.backgroundColor = '#161617';
		btnActif.btn.style.border = 'none';
	}
	blockVideoLink.style.display = "none";
	btnActif.btn = "";

	//set upload type
	formAddPost.elements.fileTypeValue.value = 'folder';
});

btnAddFile.addEventListener('click', function(){
	//display inputs add folder, hide inputs add file
	btnAddFile.style.backgroundColor = '#222224';
	btnAddFile.style.border = 'solid 1px #CF8B3F';
	blockAddFile.style.display = "flex";

	btnAddFolder.style.backgroundColor = '#161617';
	btnAddFolder.style.border = 'none';
	blockTags.style.display = 'none';
	if (btnActif.btn !== "") {
		btnActif.btn.style.backgroundColor = '#161617';
		btnActif.btn.style.border = 'none';
	}
	blockTitle.style.display  = "none";
	blockUploadFile.style.display = 'none';
	blockVideoLink.style.display = "none";
	blockTinyMce.style.display = 'none';
	inputSubmit.style.display = "none";
});

//public / private publication
if (document.getElementById('addSchoolPost') !== null) {
	let checkboxIsPrivate = formAddPost.elements.isPrivate;
	let blockListGroup = document.querySelector('#blockIsPrivate > div > div:nth-of-type(2)');
	let listGroup = document.querySelector('#blockIsPrivate select');
	let listAuthorizedGroups = formAddPost.elements.listAuthorizedGroups;
	let blockAuthorizedGroups = document.getElementById('authorizedGroups');

	function addGroupToAuthorizedList(group) {
		//add elem to list authorized group
		let elemSpan = document.createElement('span');
		elemSpan.textContent = group;
		elemSpan.classList.add('authorizedGroup');

		let elemI = document.createElement('i');
		elemI.classList.add('fas');
		elemI.classList.add('fa-times');
		elemI.classList.add('deleteGroup');
		elemI.addEventListener('click', function(){
			//delete this group from list authorized group
			blockAuthorizedGroups.removeChild(elemSpan);
			//delete group value to input list authorized
			let arrAuthorizedGroups = listAuthorizedGroups.value.split(',');
			arrAuthorizedGroups.shift();
			arrAuthorizedGroups.splice(arrAuthorizedGroups.indexOf(group), 1);
			listAuthorizedGroups.value = "";
			for (let i=0;i<arrAuthorizedGroups.length;i++) {
				listAuthorizedGroups.value += (',' + arrAuthorizedGroups[i]);
			}
			//add this group from list group to add
			let elemOption = document.createElement('option');
			elemOption.value = group;
			elemOption.textContent = group;
			listGroup.appendChild(elemOption);
		});
		elemSpan.appendChild(elemI);
		blockAuthorizedGroups.appendChild(elemSpan);
		//add group value to input list authorized
		listAuthorizedGroups.value += (',' + group);
		//remove this group from list group to add
		listGroup.removeChild(document.querySelector('option[value="' + group + '"]'));
		//select empty option on list group
		document.querySelector('option[value=""]').selected = true;
	}

	if (formAddPost.elements.isStudent.value === 'false') {
		//display / hide private mode
		checkboxIsPrivate.addEventListener('change', function(e){
			if (formAddPost.elements.fileTypeValue.value === 'compressed' && !checkboxIsPrivate.checked) {
				//compressed file can't be public
				e.preventDefault();
				checkboxIsPrivate.checked = true;
			}
			if (!checkboxIsPrivate.checked) {
				blockListGroup.style.display = 'none';
				blockAuthorizedGroups.style.display = 'none';
				formAddPost.elements.uploadType.value = 'public';
				if (document.getElementById('addSchoolPost') === null && (formAddPost.elements.fileTypeValue.value === 'video' || formAddPost.elements.fileTypeValue.value === 'image')) {
					blockTags.style.display = 'flex';
				}
			} else {
				blockListGroup.style.display = 'inline-block';
				formAddPost.elements.uploadType.value = 'private';
				if (listGroup.value !== "all") {
					blockAuthorizedGroups.style.display = 'flex';
				}
				if (blockTags.style.display === 'flex') {
					blockTags.style.display = 'none';
				}
			}
		});

		//add group to list authorized group
		listGroup.addEventListener('change', function(e){
			if (e.target.value === "all") {
				blockAuthorizedGroups.style.display = 'none';
			} else if (e.target.value === "") {
				blockAuthorizedGroups.style.display = 'flex';
			} else {
				blockAuthorizedGroups.style.display = 'flex';
				addGroupToAuthorizedList(e.target.value);
			}
		});
	}
}

//button type file
let blockTypeImg = document.querySelector('#fileTypeSelection > figure:first-of-type');
let btnImg = document.getElementById('typeImage');
let blockTypeVideo = document.querySelector('#fileTypeSelection > figure:nth-of-type(2)');
let btnVideo = document.getElementById('typeVideo');
let blockTypeOther = document.querySelector('#fileTypeSelection > figure:last-of-type');
let btnOther = document.getElementById('typeOther');
let btnActif = {btn:""};
let inputFile = document.querySelector('#blockUploadFile input[type="file"]');
let blockTags = document.getElementById('blockTags');

btnImg.addEventListener('click', function(){
	blockTitle.style.display  = "flex";
	inputTitle.placeholder = 'Champ non obligatoire';
	blockUploadFile.style.display = 'flex';
	labelUploadFile.textContent = "Fichier jpg, png, gif - (max : 5Mo)";
	blockTinyMce.style.display = 'flex';
	if (formAddPost.elements.uploadType.value === 'public' && formAddPost.elements.postType.value === 'userPost' && formAddPost.elements.isStudent.value === 'true') {
		blockTags.style.display = 'flex';
	}
	inputSubmit.style.display = "inline-block";
	formAddPost.elements.fileTypeValue.value = 'image';
	//set focus
	blockTypeImg.style.backgroundColor = '#222224';
	blockTypeImg.style.border = 'solid 1px #CF8B3F';
	if (btnActif.btn !== "" && btnActif.btn !== blockTypeImg) {
		//unset previous focus
		btnActif.btn.style.backgroundColor = '#161617';
		btnActif.btn.style.border = 'none';
	}
	blockVideoLink.style.display = "none";
	btnActif.btn = blockTypeImg;
});

btnVideo.addEventListener('click', function(){
	blockTitle.style.display  = "flex";
	inputTitle.placeholder = 'Champ non obligatoire';
	blockUploadFile.style.display = 'flex';
	labelUploadFile.textContent = "Thumbnail de la vidéo (jpg, png, gif) - non obligatoire - (max : 5Mo)";
	blockTinyMce.style.display = 'flex';
	if (formAddPost.elements.uploadType.value === 'public' && formAddPost.elements.postType.value === 'userPost' && formAddPost.elements.isStudent.value === 'true') {
		blockTags.style.display = 'flex';
	}
	inputSubmit.style.display = "inline-block";
	blockVideoLink.style.display = "flex";
	formAddPost.elements.fileTypeValue.value = 'video';
	//set focus
	blockTypeVideo.style.backgroundColor = '#222224';
	blockTypeVideo.style.border = 'solid 1px #CF8B3F';
	if (btnActif.btn !== "" && btnActif.btn !== blockTypeVideo) {
		//unset previous focus
		btnActif.btn.style.backgroundColor = '#161617';
		btnActif.btn.style.border = 'none';
	}
	btnActif.btn = blockTypeVideo;
});

if (document.getElementById('addSchoolPost') !== null) {
	btnOther.addEventListener('click', function(){
		if (formAddPost.elements.isStudent.value === 'false' && !formAddPost.elements.isPrivate.checked) {
			formAddPost.elements.isPrivate.click();
		}
		blockTitle.style.display  = "flex";
		inputTitle.placeholder = '';
		blockUploadFile.style.display = 'flex';
		labelUploadFile.innerHTML = "Fichier zip, rar - (max : 5Mo)";
		preview.style.display = 'none';
		blockTinyMce.style.display = 'flex';
		blockTags.style.display = 'none';
		inputSubmit.style.display = "inline-block";
		formAddPost.elements.fileTypeValue.value = 'compressed';
		//set focus
		blockTypeOther.style.backgroundColor = '#222224';
		blockTypeOther.style.border = 'solid 1px #CF8B3F';
		if (btnActif.btn !== "" && btnActif.btn !== blockTypeOther) {
			//unset previous focus
			btnActif.btn.style.backgroundColor = '#161617';
			btnActif.btn.style.border = 'none';
		}
		blockVideoLink.style.display = "none";
		btnActif.btn = blockTypeOther;
	});
}

//set preview
inputFile.addEventListener('change', function(e){
	if (e.target.files && e.target.files[0] && btnActif.btn !== blockTypeOther) {
		let reader = new FileReader();

		reader.addEventListener('load', function(e){
			document.querySelector('#preview img').src = e.target.result;
			preview.style.display = 'flex';
		});

        reader.readAsDataURL(e.target.files[0]);
    }
});

//tags
let errorMsg = document.getElementById('errorMsg');
let blockRecommendedTags = document.getElementById('recommendedTags');
let blockSelectedTags = document.getElementById('selectedTags');
let divRecommendedTags = document.querySelector('#recommendedTags div');
let divSelectedTags = document.querySelector('#selectedTags div');
let regexTag = /^[a-z0-9]+[a-z0-9 ]*[a-z0-9]+$/i;
let inputTag = document.getElementById('tags');
let inputListTags = document.getElementById('listTags');
let btnAddTag = document.querySelector('#tags + button');
let textTagRules = document.querySelector('.tagRules');
let existingTags;
window.addEventListener('load', function(){
	ajaxGet('index.php?action=getTags', function(response){
		if (response.length > 0) {
			existingTags = JSON.parse(response);
		}
	});
});

function blink(elem, border = false){
	elem.style.color = 'red';
	inputTag.style.border = 'solid 2px red';
	border ? (elem.style.border = 'solid 2px red') : border = false;
	setTimeout(function(){
		elem.style.color = 'white';
		border ? (elem.style.border = 'solid 2px #CF8B3F') : border = false;
		setTimeout(function(){
			elem.style.color = 'red';
			border ? (elem.style.border = 'solid 2px red') : border = false;
			setTimeout(function(){
				elem.style.color = 'white';
				inputTag.style.border = 'none';
				border ? (elem.style.border = 'solid 2px #CF8B3F') : border = false;
			}, 2000);
		}, 150);
	}, 150);
}
function deleteSpace(str){
	let arr = str.split(" ");
	let string = "";
	arr.forEach(char => {
		string += char;
	});
	return string;
}
function tagIsValid(tag){
	if (regexTag.test(tag) && tag.length <= 30){
		return true;
	} else {
		return false;
	}
}
function createTag(tagValue, selected = false){
	if (selected) {
		blockSelectedTags.style.display = 'block';
		let valueForListTags = ',' + tagValue;
		inputListTags.value += valueForListTags;

		let tag = document.createElement('span');
		tag.textContent = tagValue;
		tag.classList.add('tag');
		tag.id = 'tag' + tagValue[0].toUpperCase() + deleteSpace(tagValue.substring(1));

		let cross = document.createElement('i');
		cross.classList.add('fas');
		cross.classList.add('fa-times');
		tag.appendChild(cross);

		tag.addEventListener('click', function(){
			inputListTags.value = inputListTags.value.replace(valueForListTags, "");
			divSelectedTags.removeChild(tag);
			if (inputListTags.value === "") {
				blockSelectedTags.style.display = 'none';
			}
		});
		divSelectedTags.appendChild(tag);
	} else {
		let tag = document.createElement('span');
		tag.textContent = tagValue;
		tag.classList.add('tag');
		tag.id = 'tag' + tagValue[0].toUpperCase() + deleteSpace(tagValue.substring(1));

		tag.addEventListener('click', function(){
			divRecommendedTags.removeChild(tag);
			createTag(tagValue, true);
		});
		divRecommendedTags.appendChild(tag);
	}
}
function setErrorMsg(msg){
	errorMsg.style.opacity = '1';
	errorMsg.textContent = msg;
	setTimeout(function(){
		errorMsg.style.opacity = '0';
	}, 5000);
}

//button add tag
btnAddTag.addEventListener('click', function(e){
	e.preventDefault();
	if (tagIsValid(inputTag.value)) {
		let tagId = 'tag' + inputTag.value[0].toUpperCase() + deleteSpace(inputTag.value.substring(1));
		if (document.getElementById(tagId) === null) {
			createTag(inputTag.value, true);
			inputTag.value = "";
		} else {
			blink(document.getElementById(tagId), true);
		}
	} else {
		//make the text 'tag rules' blink
		blink(textTagRules);
	}
});

//recommended tags
inputTag.addEventListener('input', function(){
	divRecommendedTags.innerHTML = "";
	if (inputTag.value.length <= 1) {
		blockRecommendedTags.style.display = "none";
	} else if (inputTag.value.length > 1) {
		existingTags.forEach(tag => {
			if (tag.indexOf(inputTag.value) !== -1) {
				if (document.getElementById('tag' + tag[0].toUpperCase() + deleteSpace(tag.substring(1))) === null) {
					blockRecommendedTags.style.display = "block";
					createTag(tag);
				}
			}
		});
	}
	if (divRecommendedTags.innerHTML === "") {
		blockRecommendedTags.style.display = "none";
	}
});

//button submit
inputSubmit.addEventListener('click', function(e){
	switch (formAddPost.elements.fileTypeValue.value) {
		case 'image' :
			if (formAddPost.elements.uploadType.value === 'public' && formAddPost.elements.postType.value === 'userPost' 
			&& formAddPost.elements.isStudent.value === 'true' && inputListTags.value === "") {
				//tag is only for public student post
				e.preventDefault();
				setErrorMsg('Vous devez choisir au moins un tag');
			}
			if (!inputFile.files.length > 0) {
				e.preventDefault();
				setErrorMsg('Vous devez sélectionner une image a télécharger');
			}
			if (formAddPost.elements.title.value !== "" && formAddPost.elements.title.value.length > 30) {
				e.preventDefault();
				setErrorMsg('Le titre ne peut pas comporter plus de 30 caractères');
			}
		break;
		case 'video' :
			if (formAddPost.elements.uploadType.value === 'public' && formAddPost.elements.postType.value === 'userPost' 
			&& formAddPost.elements.isStudent.value === 'true' && inputListTags.value === "") {
				//tag is only for public student post
				e.preventDefault();
				setErrorMsg('Vous devez choisir au moins un tag');
			}
			let regexUrl = /youtube\.com\/watch\?v\=([a-zA-Z0-9]+)$/;
			if (formAddPost.elements.videoLink.value === "" || !regexUrl.test(formAddPost.elements.videoLink.value)) {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter une adresse youtube pour la vidéo');
			} else {
				//set url video
				formAddPost.elements.videoLink.value = regexUrl.exec(formAddPost.elements.videoLink.value)[1];
			}
			if (formAddPost.elements.title.value !== "" && formAddPost.elements.title.value.length > 30) {
				e.preventDefault();
				setErrorMsg('Le titre ne peut pas comporter plus de 30 caractères');
			}
		break;
		case 'compressed' :
			if (!inputFile.files.length > 0) {
				e.preventDefault();
				setErrorMsg('Vous devez sélectionner une fichier a télécharger');
			}
			if (formAddPost.elements.title.value === "" || formAddPost.elements.title.value.length > 30) {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter un titre (30 caractères max.)');
			}
		break;
		case 'folder' :
			if (formAddPost.elements.title.value === "" || formAddPost.elements.title.value.length > 30) {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter un titre (30 caractères max.)');
			}
		break;
	}
	if (document.getElementById('addSchoolPost') !== null && formAddPost.elements.isPrivate.checked && 
	formAddPost.elements.listGroup.value !== 'all' && formAddPost.elements.listAuthorizedGroups.value === '') {
		//no group selected for private school post
		e.preventDefault();
		setErrorMsg('Pour une publication privée, vous devez choisir au moins un groupe');
	}
});
