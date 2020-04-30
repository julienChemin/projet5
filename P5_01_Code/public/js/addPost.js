let formAddPost = document.querySelector('#addSchoolPost > form');
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

//button type file
let blockTypeImg = document.querySelector('#fileTypeSelection > p:first-of-type');
let btnImg = document.getElementById('typeImage');
let blockTypeVideo = document.querySelector('#fileTypeSelection > p:nth-of-type(2)');
let btnVideo = document.getElementById('typeVideo');
let blockTypeOther = document.querySelector('#fileTypeSelection > p:last-of-type');
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
	blockTags.style.display = 'flex';
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
	blockTags.style.display = 'flex';
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

btnOther.addEventListener('click', function(){
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
		existingTags = JSON.parse(response);
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
	console.log(inputFile.files);
	switch (formAddPost.elements.fileTypeValue.value) {
		case 'image' :
			if (inputListTags.value === "") {
				e.preventDefault();
				setErrorMsg('Vous devez choisir au moins un tag');
			}
			if (!inputFile.files.length > 0) {
				e.preventDefault();
				setErrorMsg('Vous devez sélectionner une image a télécharger');
			}
		break;
		case 'video' :
			if (inputListTags.value === "") {
				e.preventDefault();
				setErrorMsg('Vous devez choisir au moins un tag');
			}
			if (formAddPost.elements.videoLink.value === "") {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter un lien pour la vidéo');
			}
		break;
		case 'compressed' :
			if (!inputFile.files.length > 0) {
				e.preventDefault();
				setErrorMsg('Vous devez sélectionner une fichier a télécharger');
			}
			if (formAddPost.elements.title.value === "") {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter un titre');
			}
		break;
		case 'folder' :
			if (formAddPost.elements.title.value === "") {
				e.preventDefault();
				setErrorMsg('Vous devez ajouter un titre');
			}
		break;

	}
});
