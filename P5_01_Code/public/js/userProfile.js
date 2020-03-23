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
let buttonProfile = document.querySelector('#blockTabs > li:nth-of-type(1)');
let buttonPublication = document.querySelector('#blockTabs > li:nth-of-type(2)');
let buttonAbout = document.querySelector('#blockTabs > li:nth-of-type(3)');
let focusButton = {elem :buttonProfile};

buttonProfile.addEventListener('click', function(){
	if (!buttonProfile.classList.contains('buttonIsFocus')) {
		toggleClass(focusButton.elem, 'buttonIsFocus');
		slideTo(0, slides, slideTab);
		toggleClass(buttonProfile, 'buttonIsFocus');
		focusButton.elem = buttonProfile;
	}
});

buttonPublication.addEventListener('click', function(){
	if (!buttonPublication.classList.contains('buttonIsFocus')) {
		toggleClass(focusButton.elem, 'buttonIsFocus');
		slideTo(-100, slides, slideTab);
		toggleClass(buttonPublication, 'buttonIsFocus');
		focusButton.elem = buttonPublication;
	}
});

buttonAbout.addEventListener('click', function(){
	if (!buttonAbout.classList.contains('buttonIsFocus')) {
		toggleClass(focusButton.elem, 'buttonIsFocus');
		slideTo(-200, slides, slideTab);
		toggleClass(buttonAbout, 'buttonIsFocus');
		focusButton.elem = buttonAbout;
	}
});


//profile editing
if (document.getElementById('blockTabsEditProfile') !== null) {
	function toggleMenuTop(content, blockMenuTop){
		if (content === blockMenuTop.contentDisplay) {
			content.style.top = "-160px";
			blockMenuTop.contentDisplay = "";
			setTimeout(function(){
				blockMenuTop.elem.style.height = "0px";
			}, 100);
		} else {
			if (blockMenuTop.contentDisplay !== "") {
				blockMenuTop.contentDisplay.style.top = "-160px";
			}
			blockMenuTop.elem.style.height = "80px";
			blockMenuTop.contentDisplay = content;
			setTimeout(function(){
				content.style.top = "0px";
			}, 100);
		}
	}

	let buttonMenuEdit = document.querySelector('#blockTabsEditProfile > li > i');
	let editableItems = document.querySelectorAll('.editable');
	let elemMenuEditingTop = document.getElementById('blockMenuEditingTop');
	let blockMenuEditingTop = {elem:elemMenuEditingTop, isOpen:false, contentDisplay:""};
	let allButtonsEdit = document.querySelectorAll('.editable i');
	let allContentMenuEdit = document.querySelectorAll('.contentMenuEdit');

	//open - close edit mode
	buttonMenuEdit.addEventListener('click', function(){
		if (buttonMenuEdit.classList.contains('menuIsOpen')) {
			toggleClass(buttonMenuEdit, 'menuIsOpen');
			for (let i=0; i<editableItems.length; i++) {
				toggleClass(editableItems[i], 'editable');
				toggleClass(editableItems[i], 'editing');
			}

			if (blockMenuEditingTop.contentDisplay !== "") {
				blockMenuEditingTop.contentDisplay.style.top = "-160px";
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

	//menus edit
	for (let i=0; i<allButtonsEdit.length; i++) {
		allButtonsEdit[i].addEventListener('click', function(){
			toggleMenuTop(allContentMenuEdit[i], blockMenuEditingTop);
		});
	}

	//menu edit banner
	let formBanner = document.querySelector('#contentMenuEditBanner > form');
	let bannerImg = document.querySelector('#banner img');

	formBanner.elements.bannerPath.addEventListener('change', function(e){
		if (e.target.value.indexOf('<script') === -1) {
			bannerImg.src = e.target.value;
		} else {
			e.target.value = "";
		}
	});

	formBanner.elements.noBanner.addEventListener('change', function(e){
		if (e.target.checked) {
			bannerImg.classList.add('hide');
		} else {
			bannerImg.classList.remove('hide');
		}
	});

	formBanner.elements.saveBanner.addEventListener('click', function(e){
		if (formBanner.elements.bannerPath.value !== "") {
			e.preventDefault();
			let url = 'index.php?action=updateProfile&userId='+formBanner.elements.userId.value;
			url += '&elem=profileBanner&value=';
			url += formBanner.elements.bannerPath.value;
			
			ajaxGet(url, function(response){
				toggleClass(buttonMenuEdit, 'menuIsOpen');
				blockMenuEditingTop.contentDisplay.style.top = "-160px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			});
		} else if (formBanner.elements.dlBanner.value === "") {
			e.preventDefault();
			let url = 'index.php?action=updateProfile&userId='+formBanner.elements.userId.value;
			url += '&elem=noBanner&value=';
			url += formBanner.elements.noBanner.checked;

			ajaxGet(url, function(response){
				toggleClass(buttonMenuEdit, 'menuIsOpen');
				blockMenuEditingTop.contentDisplay.style.top = "-160px";
				blockMenuEditingTop.contentDisplay = "";
				setTimeout(function(){
					blockMenuEditingTop.elem.style.height = "0px";
				}, 100);
			});
		}
	});

	//menu edit picture profile
	let buttonSaveProfilePicture = document.getElementById('saveProfilePicture');
	buttonSaveProfilePicture.addEventListener('click', function(e){
		e.preventDefault();
	});

	//menu edit pseudo
	let buttonSavePseudo = document.getElementById('savePseudo');
	buttonSavePseudo.addEventListener('click', function(e){
		e.preventDefault();
	});

	//menu edit profile

	//menu edit publication

	//menu edit about
}
