function toggleClass(elem, classToToggle) {
	if (elem.classList.contains(classToToggle)) {
		elem.classList.remove(classToToggle);
	} else {
		elem.classList.add(classToToggle);
	}
}

function slideTo(nbPosition, slides, slideTab) {
	for (let i=0; i<slides.length; i++) {
		slides[i].style.left = nbPosition + '%';
	}
}

let slideTab = document.getElementById('slideTab');
let slides = document.querySelectorAll('#slideTab > div');
let allButtonsTab = document.querySelectorAll('#blockTabs > li');
let focusButton = {elem :allButtonsTab[0]};

for (let i=0; i<allButtonsTab.length; i++) {
	allButtonsTab[i].addEventListener('click', function(){
		if (!allButtonsTab[i].classList.contains('buttonIsFocus')) {
			toggleClass(focusButton.elem, 'buttonIsFocus');
			slideTo(-(i*100), slides, slideTab);
			toggleClass(allButtonsTab[i], 'buttonIsFocus');
			focusButton.elem = allButtonsTab[i];
		}
	});
}

//profile editing
if (document.getElementById('blockTabsEditProfile') !== null) {
	function toggleMenuTop(content, blockMenuTop, modal = null){
		if (content === blockMenuTop.contentDisplay) {
			content.style.top = "-80px";
			blockMenuTop.contentDisplay = "";
			setTimeout(function(){
				blockMenuTop.elem.style.height = "0px";
			}, 100);
		} else {
			if (blockMenuTop.contentDisplay !== "") {
				blockMenuTop.contentDisplay.style.top = "-80px";
			}
			blockMenuTop.elem.style.height = "80px";
			blockMenuTop.contentDisplay = content;
			setTimeout(function(){
				content.style.top = "0px";
			}, 100);
		}

		if (modal !== null) {
			modal.style.display = "flex";
		}
	}

	let buttonMenuEdit = document.querySelector('#blockTabsEditProfile > li > i');
	let editableItems = document.querySelectorAll('.editable');
	let elemMenuEditingTop = document.getElementById('blockMenuEditingTop');
	let blockMenuEditingTop = {elem:elemMenuEditingTop, isOpen:false, contentDisplay:""};
	let allButtonsEditHeader = document.querySelectorAll('.editable > .iconeEditHeader');
	let allContentMenuEditHeader = document.querySelectorAll('.menuEditHeader');

	//open - close edit mode
	buttonMenuEdit.addEventListener('click', function(){
		if (buttonMenuEdit.classList.contains('menuIsOpen')) {
			toggleClass(buttonMenuEdit, 'menuIsOpen');
			for (let i=0; i<editableItems.length; i++) {
				toggleClass(editableItems[i], 'editable');
				toggleClass(editableItems[i], 'editing');
			}

			if (blockMenuEditingTop.contentDisplay !== "") {
				blockMenuEditingTop.contentDisplay.style.top = "-80px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			}
		} else {
			toggleClass(buttonMenuEdit, 'menuIsOpen');
			for (let i=0; i<editableItems.length; i++) {
				toggleClass(editableItems[i], 'editable');
				toggleClass(editableItems[i], 'editing');
			}
		}
	});

	//btn add warning to user
	let btnAddWarning = document.querySelector('.fa-exclamation-triangle');
	let alreadyBan = document.querySelector('.alreadyBan');
	if (btnAddWarning !== null && alreadyBan === null) {
		btnAddWarning.addEventListener('click', function() {
			let url = 'indexAdmin.php?action=addWarning&idUser=' + btnAddWarning.getAttribute('iduser');
			ajaxGet(url, function(response) {
				if (response === "true") {
					btnAddWarning.classList.remove('fa-exclamation-triangle');
					btnAddWarning.classList.add('fa-check');
					btnAddWarning.style.color = 'green';
					setTimeout(function() {
						btnAddWarning.remove();
					}, 1500);
				}
			});
		}, {'once' : true});
	}

	//MENUS EDIT HEADER ( BANNER / PROFILE PICTURE / TEXT)
	for (let i=0; i<allButtonsEditHeader.length; i++) {
		allButtonsEditHeader[i].addEventListener('click', function(){
			toggleMenuTop(allContentMenuEditHeader[i], blockMenuEditingTop);
		});
	}

	//MENU EDIT BANNER
	let formBanner = document.querySelector('#contentMenuEditBanner > form');
	let bannerImg = document.querySelector('#banner img');
	let bannerSrcInBdd = bannerImg.src;
	let noBannerIsChecked = formBanner.elements.noBanner.checked;
	let userId = formBanner.elements.userId.value;

	//input banner picture path
	formBanner.elements.bannerPath.addEventListener('change', function(e){
		if (e.target.value.indexOf('<script') === -1) {
			bannerImg.src = e.target.value;
		} else {
			e.target.value = "";
		}
	});

	//input no banner
	formBanner.elements.noBanner.addEventListener('change', function(e){
		let url = 'index.php?action=upload&elem=banner&noBanner='+formBanner.elements.noBanner.checked;
		formBanner.action = url;
			
		if (e.target.checked) {
			bannerImg.classList.add('hide');
		} else {
			bannerImg.classList.remove('hide');
		}
	});

	//submit
	let noBanner = formBanner.elements.noBanner.checked;
	formBanner.elements.saveBanner.addEventListener('click', function(e){
		if (formBanner.elements.bannerPath.value !== "" && formBanner.elements.bannerPath.value !== bannerSrcInBdd) {
			e.preventDefault();
			let url = 'index.php?action=updateProfile&userId='+userId;
			url += '&elem=profileBanner&value='+formBanner.elements.bannerPath.value;
			url += '&noBanner='+formBanner.elements.noBanner.checked;

			noBanner = formBanner.elements.noBanner.checked;
			
			ajaxGet(url, function(response){
				bannerSrcInBdd = formBanner.elements.bannerPath.value;
				toggleClass(buttonMenuEdit, 'menuIsOpen');
				blockMenuEditingTop.contentDisplay.style.top = "-80px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			});
		} else if (formBanner.elements.dlBanner.value === "") {
			if (noBanner !== formBanner.elements.noBanner.checked) {
				e.preventDefault();
				let url = 'index.php?action=updateProfile&userId='+userId;
				url += '&elem=profileBanner&value='+bannerImg.src;
				url += '&noBanner='+formBanner.elements.noBanner.checked;

				noBanner = formBanner.elements.noBanner.checked;

				ajaxGet(url, function(){
					toggleClass(buttonMenuEdit, 'menuIsOpen');
					blockMenuEditingTop.contentDisplay.style.top = "-80px";
					blockMenuEditingTop.contentDisplay = "";
					setTimeout(function(){
						blockMenuEditingTop.elem.style.height = "0px";
					}, 100);
				});
			} else {
				e.preventDefault();
			}
		} 
	});

	//MENU EDIT PICTURE PROFILE
	let formProfilePicture = document.querySelector('#contentMenuEditProfilePicture > form');
	let blockProfilePicture = document.querySelector('#profile > header > div:first-of-type');
	let profileImg = document.querySelector('#profile img:first-of-type');
	let profileSrcInBdd = profileImg.src;

	//input picture path
	formProfilePicture.elements.picturePath.addEventListener('change', function(e){
		if (e.target.value.indexOf('<script') === -1 || e.target.value.indexOf(' ')) {
			profileImg.src = e.target.value;
		} else {
			e.target.value = "";
		}
	});

	//input orientation
	document.getElementById('widePicture').addEventListener('click', function(e){
		if (!profileImg.classList.contains('widePicture')) {
			profileImg.classList.add('widePicture');
			profileImg.classList.remove('highPicture');
		}

		let url = 'index.php?action=upload&elem=picture&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			formProfilePicture.action = url;
	});

	document.getElementById('highPicture').addEventListener('click', function(e){
		if (!profileImg.classList.contains('highPicture')) {
			profileImg.classList.add('highPicture');
			profileImg.classList.remove('widePicture');
		}

		let url = 'index.php?action=upload&elem=picture&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			formProfilePicture.action = url;
	});

	//input size
	document.getElementById('smallPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('smallPicture')) {
			blockProfilePicture.classList.add('smallPicture');
			blockProfilePicture.classList.remove('mediumPicture');
			blockProfilePicture.classList.remove('bigPicture');
		}

		let url = 'index.php?action=upload&elem=picture&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			formProfilePicture.action = url;
	});

	document.getElementById('mediumPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('mediumPicture')) {
			blockProfilePicture.classList.add('mediumPicture');
			blockProfilePicture.classList.remove('smallPicture');
			blockProfilePicture.classList.remove('bigPicture');
		}

		let url = 'index.php?action=upload&elem=picture&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			formProfilePicture.action = url;
	});

	document.getElementById('bigPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('bigPicture')) {
			blockProfilePicture.classList.add('bigPicture');
			blockProfilePicture.classList.remove('smallPicture');
			blockProfilePicture.classList.remove('mediumPicture');
		}

		let url = 'index.php?action=upload&elem=picture&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			formProfilePicture.action = url;
	});

	//submit
	let pictureOrientation = formProfilePicture.elements.pictureOrientation.value;
	let pictureSize = formProfilePicture.elements.pictureSize.value;

	formProfilePicture.elements.saveProfilePicture.addEventListener('click', function(e){
		if (formProfilePicture.elements.picturePath.value !== "" && formProfilePicture.elements.picturePath.value !== profileSrcInBdd) {
			e.preventDefault();
			let url = 'index.php?action=updateProfile&userId=' + userId;
			url += '&elem=profilePicture&value=' + formProfilePicture.elements.picturePath.value;
			url += '&orientation=' + formProfilePicture.elements.pictureOrientation.value;
			url += '&size=' + formProfilePicture.elements.pictureSize.value;

			pictureOrientation = formProfilePicture.elements.pictureOrientation.value;
			pictureSize = formProfilePicture.elements.pictureSize.value;
			
			ajaxGet(url, function(response){
				profileSrcInBdd = formProfilePicture.elements.picturePath.value;
				toggleClass(buttonMenuEdit, 'menuIsOpen');
				blockMenuEditingTop.contentDisplay.style.top = "-80px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			});
		} else if (formProfilePicture.elements.dlPicture.value === "") {
			if (pictureOrientation !== formProfilePicture.elements.pictureOrientation.value || pictureSize !== formProfilePicture.elements.pictureSize.value) {
				e.preventDefault();
				let url = 'index.php?action=updateProfile&userId=' + userId;
				url += '&elem=profilePicture&value=' + profileImg.src;
				url += '&orientation=' + formProfilePicture.elements.pictureOrientation.value;
				url += '&size=' + formProfilePicture.elements.pictureSize.value;

				pictureOrientation = formProfilePicture.elements.pictureOrientation.value;
				pictureSize = formProfilePicture.elements.pictureSize.value;

				ajaxGet(url, function(){
					toggleClass(buttonMenuEdit, 'menuIsOpen');
					blockMenuEditingTop.contentDisplay.style.top = "-80px";
					blockMenuEditingTop.contentDisplay = "";
					setTimeout(function(){
						blockMenuEditingTop.elem.style.height = "0px";
					}, 100);
				});
			} else {
				e.preventDefault();
			}
		}
	});

	//MENU EDIT TEXT
	let formProfileText = document.querySelector('#contentMenuEditText > form');
	let profileTextBlock = document.querySelector('#profile > header > div:nth-of-type(2)');
	let profilePseudo = document.querySelector('#profile > header > div:nth-of-type(2) > span:nth-of-type(1)');
	let profileSchool = document.querySelector('#profile > header > div:nth-of-type(2) > a:nth-of-type(1)');

	//block position
	document.getElementById('blockTextTop').addEventListener('click', function(){
		if (!profileTextBlock.classList.contains('elemStart')) {
			profileTextBlock.classList.add('elemStart');
			profileTextBlock.classList.remove('elemCenter');
			profileTextBlock.classList.remove('elemEnd');
		}
	});

	document.getElementById('blockTextCenter').addEventListener('click', function(){
		if (!profileTextBlock.classList.contains('elemCenter')) {
			profileTextBlock.classList.add('elemCenter');
			profileTextBlock.classList.remove('elemStart');
			profileTextBlock.classList.remove('elemEnd');
		}
	});

	document.getElementById('blockTextBottom').addEventListener('click', function(){
		if (!profileTextBlock.classList.contains('elemEnd')) {
			profileTextBlock.classList.add('elemEnd');
			profileTextBlock.classList.remove('elemStart');
			profileTextBlock.classList.remove('elemCenter');
		}
	});

	//pseudo position
	document.getElementById('pseudoLeft').addEventListener('click', function(){
		if (!profilePseudo.classList.contains('elemStart')) {
			profilePseudo.classList.add('elemStart');
			profilePseudo.classList.remove('elemCenter');
			profilePseudo.classList.remove('elemEnd');
		}
	});

	document.getElementById('pseudoCenter').addEventListener('click', function(){
		if (!profilePseudo.classList.contains('elemCenter')) {
			profilePseudo.classList.add('elemCenter');
			profilePseudo.classList.remove('elemStart');
			profilePseudo.classList.remove('elemEnd');
		}
	});

	document.getElementById('pseudoRight').addEventListener('click', function(){
		if (!profilePseudo.classList.contains('elemEnd')) {
			profilePseudo.classList.add('elemEnd');
			profilePseudo.classList.remove('elemStart');
			profilePseudo.classList.remove('elemCenter');
		}
	});

	//school name position
	document.getElementById('schoolLeft').addEventListener('click', function(){
		if (!profileSchool.classList.contains('elemStart')) {
			profileSchool.classList.add('elemStart');
			profileSchool.classList.remove('elemCenter');
			profileSchool.classList.remove('elemEnd');
		}
	});

	document.getElementById('schoolCenter').addEventListener('click', function(){
		if (!profileSchool.classList.contains('elemCenter')) {
			profileSchool.classList.add('elemCenter');
			profileSchool.classList.remove('elemStart');
			profileSchool.classList.remove('elemEnd');
		}
	});

	document.getElementById('schoolRight').addEventListener('click', function(){
		if (!profileSchool.classList.contains('elemEnd')) {
			profileSchool.classList.add('elemEnd');
			profileSchool.classList.remove('elemStart');
			profileSchool.classList.remove('elemCenter');
		}
	});

	//submit
	let blockPos = formProfileText.elements.blockTextPosition.value;
	let pseudoPos = formProfileText.elements.pseudoPosition.value;
	let schoolPos = formProfileText.elements.schoolPosition.value;

	formProfileText.elements.saveProfileText.addEventListener('click', function(e){
		e.preventDefault();
		if (blockPos !== formProfileText.elements.blockTextPosition.value || pseudoPos !== formProfileText.elements.pseudoPosition.value 
		|| schoolPos !== formProfileText.elements.schoolPosition.value) {
			let url = 'index.php?action=updateProfile&userId=' + userId;
			url += '&elem=profileText&block=' + formProfileText.elements.blockTextPosition.value;
			url += '&pseudo=' + formProfileText.elements.pseudoPosition.value;
			url += '&school=' + formProfileText.elements.schoolPosition.value;

			blockPos = formProfileText.elements.blockTextPosition.value;
			pseudoPos = formProfileText.elements.pseudoPosition.value;
			schoolPos = formProfileText.elements.schoolPosition.value;
			
			ajaxGet(url, function(response){
				toggleClass(buttonMenuEdit, 'menuIsOpen');
				blockMenuEditingTop.contentDisplay.style.top = "-80px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			});
		}
	});

	//MENU EDIT PROFILE CONTENT
	let modal = document.getElementById('modal');
	let formModal = document.querySelector('#modal form');
	let allButtonsEditProfile = document.querySelectorAll('.editable > .iconeEditProfile');
	let allBlockContentProfile = document.querySelectorAll('.blockContentProfile');
	let allIdBlockContentProfile = document.querySelectorAll('.blockContentProfile + .hide');
	let contentMenuEditBlock = document.getElementById('contentMenuEditBlock');
	let blockProfileListOrder = document.getElementById('blockProfileListOrder');
	let listContentOrderProfile = document.getElementById('profileContentOrder');
	let checkboxAlign = document.getElementById('align');
	let blockAlign = document.querySelector('#contentMenuEditBlock > form > div:nth-of-type(2)');
	let blockToDelete = document.getElementById('blockToDelete');
	//pencil icone (for editing) on tab 'profile'
	for (let i=0; i<allButtonsEditProfile.length; i++) {
		allButtonsEditProfile[i].addEventListener('click', function(){
			let blockOrderValue = allButtonsEditProfile[i].textContent.substring(4, allButtonsEditProfile[i].textContent.length);
			let sizeValue = allButtonsEditProfile[i].getAttribute('atrsize');
			let idCheckboxSize = 'block' + sizeValue.substr(0, 1).toUpperCase() + sizeValue.substr(1);
			let alignValue = allButtonsEditProfile[i].getAttribute('atralign');

			toggleMenuTop(contentMenuEditBlock, blockMenuEditingTop, modal);
			blockProfileListOrder.style.display = "flex";
			//set form modal inputs value
			formModal.elements.idProfileContent.value = allIdBlockContentProfile[i].textContent;
			formModal.elements.type.value = "profile";
			formModal.elements.blockOrderValue.value = blockOrderValue;
			formModal.elements.newOrderValue.value = blockOrderValue;
			document.querySelector('#profileContentOrder > option[value="' + blockOrderValue + '"]').selected = true;
			formModal.elements.sizeValue.value = sizeValue;
			if (formModal.classList.contains('small')) {
				formModal.classList.remove('small');
			}
			if (formModal.classList.contains('medium')) {
				formModal.classList.remove('medium');
			}
			if (formModal.classList.contains('big')) {
				formModal.classList.remove('big');
			}
			formModal.classList.add(sizeValue);
			document.getElementById(idCheckboxSize).checked = true;
			formModal.elements.alignValue.value = alignValue;

			if (alignValue !== "") {
				checkboxAlign.checked = true;
				blockAlign.style.display = "flex";
				switch (alignValue) {
					case 'elemStart' :
						document.getElementById('alignLeft').checked = true;
					break;
					case 'elemCenter' :
						document.getElementById('alignCenter').checked = true;
					break;
					case 'elemEnd' :
						document.getElementById('alignRight').checked = true;
					break;
				}
			}

			let content = allBlockContentProfile[i].innerHTML.split('</i>')[1];
			tinyMCE.get('tinyMCEtextarea').setContent(content);

			if (document.querySelector('#profileContentOrder .lastOption') !== null) {
				listContentOrderProfile.removeChild(document.querySelector('#profileContentOrder .lastOption'));
			}
		});
	}

	//plus icone (for adding content) on tab 'profile'
	document.querySelector('#tabProfile .fa-plus-square').addEventListener('click', function(){
		toggleMenuTop(contentMenuEditBlock, blockMenuEditingTop, modal);
		blockToDelete.style.display = "none";

		if (blockProfileListOrder.style.display !== "flex") {
			blockProfileListOrder.style.display = "flex";
		}
		
		if (!formModal.classList.contains('small')) {
			formModal.classList.add('small');
			formModal.classList.remove('medium');
			formModal.classList.remove('big');
			document.getElementById('blockSmall').checked = true;
		}

		if (document.querySelector('#profileContentOrder .lastOption') === null) {
			let option = document.createElement('option');
			option.classList.add = 'lastOption';
			option.value = "last";
			option.textContent = "Dernier";
			option.selected = true;
			listContentOrderProfile.appendChild(option);
		}

		formModal.elements.idProfileContent.value = 'new';
		formModal.elements.type.value = "profile";
		formModal.elements.blockOrderValue.value = "new";
		formModal.elements.newOrderValue.value = "last";
		formModal.elements.sizeValue.value = "small";
		formModal.elements.alignValue.value = "";
		tinyMCE.get('tinyMCEtextarea').setContent("");
	});

	//block order value
	listContentOrderProfile.addEventListener('change', function(e){
		formModal.elements.newOrderValue.value = e.target.value;
	});

	//block profile size
	document.getElementById('blockSmall').addEventListener('click', function(){
		if (!formModal.classList.contains('small')) {
			formModal.classList.add('small');
			formModal.classList.remove('medium');
			formModal.classList.remove('big');
			formModal.elements.sizeValue.value = "small";
		}
	});

	document.getElementById('blockMedium').addEventListener('click', function(){
		if (!formModal.classList.contains('medium')) {
			formModal.classList.add('medium');
			formModal.classList.remove('small');
			formModal.classList.remove('big');
			formModal.elements.sizeValue.value = "medium";
		}
	});

	document.getElementById('blockBig').addEventListener('click', function(){
		if (!formModal.classList.contains('big')) {
			formModal.classList.add('big');
			formModal.classList.remove('small');
			formModal.classList.remove('medium');
			formModal.elements.sizeValue.value = "big";
		}
	});

	//checkbox 'elem alone in the row'
	checkboxAlign.addEventListener('change', function(e){
		if (e.target.checked) {
			blockAlign.style.display = "flex";
		} else {
			blockAlign.style.display = "none";
			document.getElementById('alignLeft').checked = false;
			document.getElementById('alignCenter').checked = false;
			document.getElementById('alignRight').checked = false;
			formModal.elements.alignValue.value = "";
		}
	});

	//align value
	document.getElementById('alignLeft').addEventListener('click', function(e){
		if (e.target.checked) {
			formModal.elements.alignValue.value = 'elemStart';
		}
	});

	document.getElementById('alignCenter').addEventListener('click', function(e){
		if (e.target.checked) {
			formModal.elements.alignValue.value = 'elemCenter';
		}
	});

	document.getElementById('alignRight').addEventListener('click', function(e){
		if (e.target.checked) {
			formModal.elements.alignValue.value = 'elemEnd';
		}
	});

	//MENU EDIT ABOUT
	let allButtonsEditAbout = document.querySelectorAll('.editable > .iconeEditAbout');
	let allBlockContentAbout = document.querySelectorAll('.blockContentAbout');
	let allIdBlockContentAbout = document.querySelectorAll('.blockContentAbout + .hide');
	let blockAboutListOrder = document.getElementById('blockAboutListOrder');
	let listContentOrderAbout = document.getElementById('aboutContentOrder');

	//pencil icone (for editing) on tab 'about'
	for (let i=0; i<allButtonsEditAbout.length; i++) {
		allButtonsEditAbout[i].addEventListener('click', function(){
			let blockOrderValue = allButtonsEditAbout[i].textContent.substring(4, allButtonsEditAbout[i].textContent.length);
			let sizeValue = allButtonsEditAbout[i].getAttribute('atrsize');
			let idCheckboxSize = 'block' + sizeValue.substr(0, 1).toUpperCase() + sizeValue.substr(1);
			let alignValue = allButtonsEditAbout[i].getAttribute('atralign');

			toggleMenuTop(contentMenuEditBlock, blockMenuEditingTop, modal);
			blockAboutListOrder.style.display = "flex";
			//set form modal inputs value
			formModal.elements.idProfileContent.value = allIdBlockContentAbout[i].textContent;
			formModal.elements.type.value = "about";
			formModal.elements.blockOrderValue.value = blockOrderValue;
			formModal.elements.newOrderValue.value = blockOrderValue;
			document.querySelector('#aboutContentOrder > option[value="' + blockOrderValue + '"]').selected = true;
			formModal.elements.sizeValue.value = sizeValue;
			if (formModal.classList.contains('small')) {
				formModal.classList.remove('small');
			}
			if (formModal.classList.contains('medium')) {
				formModal.classList.remove('medium');
			}
			if (formModal.classList.contains('big')) {
				formModal.classList.remove('big');
			}
			formModal.classList.add(sizeValue);
			document.getElementById(idCheckboxSize).checked = true;
			formModal.elements.alignValue.value = alignValue;

			if (alignValue !== "") {
				checkboxAlign.checked = true;
				blockAlign.style.display = "flex";
				switch (alignValue) {
					case 'elemStart' :
						document.getElementById('alignLeft').checked = true;
					break;
					case 'elemCenter' :
						document.getElementById('alignCenter').checked = true;
					break;
					case 'elemEnd' :
						document.getElementById('alignRight').checked = true;
					break;
				}
			}

			let content = allBlockContentAbout[i].innerHTML.split('</i>')[1];
			tinyMCE.get('tinyMCEtextarea').setContent(content);

			if (document.querySelector('#aboutContentOrder .lastOption') !== null) {
				listContentOrderAbout.removeChild(document.querySelector('#aboutContentOrder .lastOption'));
			}
		});
	}

	//plus icone (for adding content) on tab 'about'
	document.querySelector('#tabAbout .fa-plus-square').addEventListener('click', function(){
		toggleMenuTop(contentMenuEditBlock, blockMenuEditingTop, modal);
		blockToDelete.style.display = "none";

		if (blockAboutListOrder.style.display !== "flex") {
			blockAboutListOrder.style.display = "flex";
		}
		
		if (!formModal.classList.contains('small')) {
			formModal.classList.add('small');
			formModal.classList.remove('medium');
			formModal.classList.remove('big');
			document.getElementById('blockSmall').checked = true;
		}

		if (document.querySelector('#aboutContentOrder .lastOption') === null) {
			let option = document.createElement('option');
			option.classList.add = 'lastOption';
			option.value = "last";
			option.textContent = "Dernier";
			option.selected = true;
			listContentOrderAbout.appendChild(option);
		}

		formModal.elements.idProfileContent.value = 'new';
		formModal.elements.type.value = "about";
		formModal.elements.blockOrderValue.value = "new";
		formModal.elements.newOrderValue.value = "last";
		formModal.elements.sizeValue.value = "small";
		formModal.elements.alignValue.value = "";
		tinyMCE.get('tinyMCEtextarea').setContent("");
	});

	//block order value
	listContentOrderAbout.addEventListener('change', function(e){
		formModal.elements.newOrderValue.value = e.target.value;
	});

	//BUTTON DELETE CONTENT
	let buttonDelete = document.querySelector('#blockToDelete i');
	let warningBeforeDelete = document.getElementById('warningBeforeDelete');

	buttonDelete.addEventListener('click', function(){
		formModal.elements.deleteBlock.value = formModal.elements.blockOrderValue.value;
		document.querySelector('#tinyMCEtextarea + .tox-tinymce').style.display = "none";
		warningBeforeDelete.style.display = "flex";
	});

	//MODAL

	//button cancel
	formModal.elements.cancel.addEventListener('click', function(e){
		e.preventDefault();
		if (document.querySelector('#tinyMCEtextarea + .tox-tinymce').style.display === "none") {
			formModal.elements.deleteBlock.value = "";
			document.querySelector('#tinyMCEtextarea + .tox-tinymce').style.display = "flex";
			warningBeforeDelete.style.display = "none";
		} else {
			modal.style.display = "none";
			toggleMenuTop(contentMenuEditBlock, blockMenuEditingTop);

			blockAlign.style.display = "none";
			checkboxAlign.checked = false;

			if (blockProfileListOrder.style.display === "flex") {
				blockProfileListOrder.style.display = "none";
			}

			if (blockAboutListOrder.style.display === "flex") {
				blockAboutListOrder.style.display = "none";
			}

			if (blockToDelete.style.display === 'none') {
				blockToDelete.style.display = "flex";
			}
		}
	});
}
