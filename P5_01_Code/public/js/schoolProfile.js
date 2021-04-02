function getImgPath(elem) {
	let backgroundValue = elem.style.backgroundImage;
	let regex = /url\(['"`](.+)['"`]\)/;
	let result;
	
	if (result = regex.exec(backgroundValue)) {
		return result[1];
	}
	
	return null;
}

function toggleClass(elem, classToToggle) {
	if (elem.classList.contains(classToToggle)) {
		elem.classList.remove(classToToggle);
	} else {
		elem.classList.add(classToToggle);
	}
}

function slideTo(nbPosition, slides) {
	for (let i=0; i<slides.length; i++) {
		slides[i].style.left = nbPosition + '%';
	}
}

let modal = document.getElementById('modal');
let slideTab = document.getElementById('slideTab');
let slides = document.querySelectorAll('#slideTab > div');
let allButtonsTab = document.querySelectorAll('#blockTabs > li');
let focusElem = { buttonTab: allButtonsTab[0], slide: slides[0] };

/*------------------------
 --------- TABS ----------
 -----------------------*/
for (let i=0; i<allButtonsTab.length; i++) {
	allButtonsTab[i].addEventListener('click', function(){
		if (!allButtonsTab[i].classList.contains('buttonIsFocus')) {
			toggleClass(focusElem.buttonTab, 'buttonIsFocus');
			toggleClass(slides[i], "noHeight");
			slideTo(-(i*100), slides);
			toggleClass(allButtonsTab[i], 'buttonIsFocus');
			toggleClass(focusElem.slide, "noHeight");
			focusElem.buttonTab = allButtonsTab[i];
			focusElem.slide = slides[i];
		}
	});
}

//profile editing
if (document.getElementById('blockTabsEditProfile') !== null) {
	function toggleModal(blockMenu, modal, formDisplay = null){
		if (formDisplay === null) {
			modal.style.display = "none";
			contentMenuEditBlock.style.display = "none";
			blockMenu.contentDisplay.style.display = "none";
		} else {
			modal.style.display = "flex";
			formDisplay.style.display = "flex";
			if (formDisplay === formModal) {
				contentMenuEditBlock.style.display = "flex";
			}
			blockMenu.contentDisplay = formDisplay;
		}
	}

	let buttonMenuEdit = document.querySelector('#blockTabsEditProfile > li > i');
	let editableItems = document.querySelectorAll('.editable');
	let blockMenuEditing = {elem:modal, isOpen:false, contentDisplay:""};
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

			if (blockMenuEditing.contentDisplay !== "") {
				blockMenuEditing.contentDisplay.style.display = "none";
				blockMenuEditing.contentDisplay = "";
			}
		} else {
			toggleClass(buttonMenuEdit, 'menuIsOpen');
			for (let i=0; i<editableItems.length; i++) {
				toggleClass(editableItems[i], 'editable');
				toggleClass(editableItems[i], 'editing');
			}
		}
	});

	//MENUS EDIT HEADER ( BANNER / PROFILE PICTURE / TEXT)
	for (let i=0; i<allButtonsEditHeader.length; i++) {
		allButtonsEditHeader[i].addEventListener('click', function(){
			toggleModal(blockMenuEditing, modal, allContentMenuEditHeader[i]);
		});
	}

	//MENU EDIT BANNER
	let formBanner = document.querySelector('#contentMenuEditBanner');
	let school = formBanner.elements.school.value;
	let banner = document.querySelector('#banner');
	let bannerImgPath = getImgPath(banner);
	let newBannerImgPath = "";

	//input no banner
	formBanner.elements.noBanner.addEventListener('change', function(e){
		let url = 'indexAdmin.php?action=upload&elem=banner&noBanner='+ formBanner.elements.noBanner.checked + '&school=' + school;
		formBanner.action = url;
			
		if (e.target.checked) {
			banner.style.backgroundImage = "";
			newBannerImgPath = "";
			banner.classList.add("noBanner");
		} else {
			banner.style.backgroundImage = "url('" + bannerImgPath + "')";
			newBannerImgPath = "url('" + bannerImgPath + "')";
			banner.classList.remove("noBanner");
		}
	});

	//submit
	let noBanner = formBanner.elements.noBanner.checked;
	formBanner.elements.saveBanner.addEventListener('click', function(e){
		if (formBanner.elements.dlBanner.value === "") {
			//banner img don't change
			e.preventDefault();
			if (noBanner !== formBanner.elements.noBanner.checked) {
				//toggle "noBanner" value
				let url = 'indexAdmin.php?action=updateProfile&school='+school;
				url += '&elem=profileBanner&value='+newBannerImgPath;
				url += '&noBanner='+formBanner.elements.noBanner.checked;

				//edit actual "noBanner" value
				noBanner = formBanner.elements.noBanner.checked;

				if (formBanner.elements.noBanner.checked) {
					banner.classList.add("noBanner");
				} else {
					banner.classList.remove("noBanner");
				}

				ajaxGet(url, function(){
					toggleClass(buttonMenuEdit, 'menuIsOpen');
				});
			}
		}
		toggleModal(blockMenuEditing, modal);
	});

	//MENU EDIT PICTURE PROFILE
	let formProfilePicture = document.querySelector('#contentMenuEditProfilePicture');
	let blockProfilePicture = document.querySelector('#profilePicture');
	let profileImgPath = getImgPath(blockProfilePicture);

	//input size
	document.getElementById('smallPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('smallPicture')) {
			blockProfilePicture.classList.add('smallPicture');
			blockProfilePicture.classList.remove('mediumPicture');
			blockProfilePicture.classList.remove('bigPicture');
		}

		let url = 'indexAdmin.php?action=upload&elem=picture';
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			url += '&school=' + school;
			formProfilePicture.action = url;
	});

	document.getElementById('mediumPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('mediumPicture')) {
			blockProfilePicture.classList.add('mediumPicture');
			blockProfilePicture.classList.remove('smallPicture');
			blockProfilePicture.classList.remove('bigPicture');
		}

		let url = 'indexAdmin.php?action=upload&elem=picture';
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			url += '&school=' + school;
			formProfilePicture.action = url;
	});

	document.getElementById('bigPicture').addEventListener('click', function(e){
		if (!blockProfilePicture.classList.contains('bigPicture')) {
			blockProfilePicture.classList.add('bigPicture');
			blockProfilePicture.classList.remove('smallPicture');
			blockProfilePicture.classList.remove('mediumPicture');
		}

		let url = 'indexAdmin.php?action=upload&elem=picture';
			url += '&size=' + formProfilePicture.elements.pictureSize.value;
			url += '&school=' + school;
			formProfilePicture.action = url;
	});

	//submit
	let pictureSize = formProfilePicture.elements.pictureSize.value;

	formProfilePicture.elements.saveProfilePicture.addEventListener('click', function(e){
		if (formProfilePicture.elements.dlPicture.value === "") {
			// profile picture don't change
			e.preventDefault();
			if (pictureSize !== formProfilePicture.elements.pictureSize.value) {
				// toggle "size" value
				let url = 'indexAdmin.php?action=updateProfile&school=' + school;
				url += '&elem=profilePicture&value=' + profileImgPath;
				url += '&size=' + formProfilePicture.elements.pictureSize.value;

				// edit actual "size" value
				pictureSize = formProfilePicture.elements.pictureSize.value;

				ajaxGet(url, function(){
					toggleClass(buttonMenuEdit, 'menuIsOpen');
				});
			}
		}
		toggleModal(blockMenuEditing, modal);
	});

	//MENU EDIT TEXT (block text, pseudo, school name)
	let formProfileText = document.querySelector('#contentMenuEditText');
	let profileTextBlock = document.querySelector('#profile > header > div:nth-of-type(2)');
	let profileSchool = document.querySelector('#profile > header > div:nth-of-type(2) > span:nth-of-type(1)');

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
	let schoolPos = formProfileText.elements.schoolPosition.value;

	formProfileText.elements.saveProfileText.addEventListener('click', function(e){
		e.preventDefault();
		if (blockPos !== formProfileText.elements.blockTextPosition.value || schoolPos !== formProfileText.elements.schoolPosition.value) {
			let url = 'indexAdmin.php?action=updateProfile&school=' + school;
			url += '&elem=profileText&block=' + formProfileText.elements.blockTextPosition.value;
			url += '&schoolPos=' + formProfileText.elements.schoolPosition.value;

			blockPos = formProfileText.elements.blockTextPosition.value;
			schoolPos = formProfileText.elements.schoolPosition.value;
			
			ajaxGet(url, function(response){
				toggleClass(buttonMenuEdit, 'menuIsOpen');
			});
		}
		toggleModal(blockMenuEditing, modal);
	});

	//MENU EDIT PROFILE CONTENT
	let formModal = document.querySelector('#modal form:last-of-type');
	let allButtonsEditProfile = document.querySelectorAll('.editable > .iconeEditProfile');
	let allBlockContentProfile = document.querySelectorAll('.blockContentProfile');
	let allIdBlockContentProfile = document.querySelectorAll('.blockContentProfile + .hide');
	let contentMenuEditBlock = document.getElementById('contentMenuEditBlock');
	let blockProfileListOrder = document.getElementById('blockProfileListOrder');
	let listContentOrderProfile = document.getElementById('profileContentOrder');
	let checkboxAlign = document.getElementById('align');
	let blockAlign = document.querySelector('#contentMenuEditBlock > div:nth-of-type(2)');
	let blockToDelete = document.getElementById('blockToDelete');
	//pencil icone (for editing) on tab 'profile'
	for (let i=0; i<allButtonsEditProfile.length; i++) {
		allButtonsEditProfile[i].addEventListener('click', function(){
			let blockOrderValue = allButtonsEditProfile[i].textContent.substring(4, allButtonsEditProfile[i].textContent.length);
			let sizeValue = allButtonsEditProfile[i].getAttribute('atrsize');
			let idCheckboxSize = 'block' + sizeValue.substr(0, 1).toUpperCase() + sizeValue.substr(1);
			let alignValue = allButtonsEditProfile[i].getAttribute('atralign');

			toggleModal(blockMenuEditing, modal, formModal);
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
		toggleModal(blockMenuEditing, modal, formModal);
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
			option.classList.add('lastOption');
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

	//MENU EDIT NEWS
	let allButtonsEditNews = document.querySelectorAll('.editable > .iconeEditNews');
	let allBlockContentNews = document.querySelectorAll('.blockContentNews');
	let allIdBlockContentNews = document.querySelectorAll('.blockContentNews + .hide');
	let blockNewsListOrder = document.getElementById('blockNewsListOrder');
	let listContentOrderNews = document.getElementById('newsContentOrder');

	//pencil icone (for editing) on tab 'news'
	for (let i=0; i<allButtonsEditNews.length; i++) {
		allButtonsEditNews[i].addEventListener('click', function(){
			let blockOrderValue = allButtonsEditNews[i].textContent.substring(4, allButtonsEditNews[i].textContent.length);
			let sizeValue = allButtonsEditNews[i].getAttribute('atrsize');
			let idCheckboxSize = 'block' + sizeValue.substr(0, 1).toUpperCase() + sizeValue.substr(1);
			let alignValue = allButtonsEditNews[i].getAttribute('atralign');

			toggleModal(blockMenuEditing, modal, formModal);
			blockNewsListOrder.style.display = "flex";
			//set form modal inputs value
			formModal.elements.idProfileContent.value = allIdBlockContentNews[i].textContent;
			formModal.elements.type.value = "news";
			formModal.elements.blockOrderValue.value = blockOrderValue;
			formModal.elements.newOrderValue.value = blockOrderValue;
			document.querySelector('#newsContentOrder > option[value="' + blockOrderValue + '"]').selected = true;
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

			let content = allBlockContentNews[i].innerHTML.split('</i>')[1];
			tinyMCE.get('tinyMCEtextarea').setContent(content);

			if (document.querySelector('#newsContentOrder .lastOption') !== null) {
				listContentOrderNews.removeChild(document.querySelector('#newsContentOrder .lastOption'));
			}
		});
	}

	//plus icone (for adding content) on tab 'news'
	document.querySelector('#tabNews .fa-plus-square').addEventListener('click', function(){
		toggleModal(blockMenuEditing, modal, formModal);
		blockToDelete.style.display = "none";

		if (blockNewsListOrder.style.display !== "flex") {
			blockNewsListOrder.style.display = "flex";
		}
		
		if (!formModal.classList.contains('small')) {
			formModal.classList.add('small');
			formModal.classList.remove('medium');
			formModal.classList.remove('big');
			document.getElementById('blockSmall').checked = true;
		}

		if (document.querySelector('#newsContentOrder .lastOption') === null) {
			let option = document.createElement('option');
			option.classList.add('lastOption');
			option.value = "last";
			option.textContent = "Dernier";
			option.selected = true;
			listContentOrderNews.appendChild(option);
		}

		formModal.elements.idProfileContent.value = 'new';
		formModal.elements.type.value = "news";
		formModal.elements.blockOrderValue.value = "new";
		formModal.elements.newOrderValue.value = "last";
		formModal.elements.sizeValue.value = "small";
		formModal.elements.alignValue.value = "";
		tinyMCE.get('tinyMCEtextarea').setContent("");
	});

	//block order value
	listContentOrderNews.addEventListener('change', function(e){
		formModal.elements.newOrderValue.value = e.target.value;
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

			toggleModal(blockMenuEditing, modal, formModal);
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
		toggleModal(blockMenuEditing, modal, formModal);
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
			option.classList.add('lastOption');
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
			toggleModal(blockMenuEditing, modal);

			blockAlign.style.display = "none";
			checkboxAlign.checked = false;

			if (blockProfileListOrder.style.display === "flex") {
				blockProfileListOrder.style.display = "none";
			}
			if (blockNewsListOrder.style.display === "flex") {
				blockNewsListOrder.style.display = "none";
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
