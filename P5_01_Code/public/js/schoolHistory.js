function toggleBorder(elem, activeElem, idActiveElem) {
	if (activeElem.value) {
		activeElem.value.style.border = 'solid 2px #161617';
	}
	elem.style.border = 'solid 2px #FFC652';
	activeElem.value = elem;
	activeElem.id = idActiveElem;
}
function fillContent (data, content) {
	if (data.length > 0) {
		for (let i=0; i<data.length; i++) {
			let div = document.createElement('div');
			let spanDate = document.createElement('span');
			let spanEntry = document.createElement('span');

			spanDate.textContent = data[i][0];
			spanEntry.textContent = data[i][1];
			div.classList.add('entry');

			div.appendChild(spanDate);
			div.appendChild(spanEntry);

			content.appendChild(div);
		}
	} else {
		let div = document.createElement('div');
		let span = document.createElement('span');
		let emptySpan = document.createElement('span');

		span.textContent = "Il n'y a pas d'historique a afficher";
		div.classList.add('entry');

		div.appendChild(emptySpan);
		div.appendChild(span);
		content.appendChild(div);
	}
}
function getHistory(school, content, offset, sortBy = null, sortValue = null, secondSortValue = null, thirdSortValue = null) {
	let url = 'indexAdmin.php?action=getSchoolHistory&school=' + school.id + '&offset=' + offset;

	if (document.getElementById('blockSchools') === null) {
		url += '&schoolName=' + school.name;
	}

	if (sortBy) {
		switch (sortBy) {
			case 'category' :
				if (sortValue) {
					url += '&sortBy=' + sortBy + '&sortValue=' + sortValue;
				}
			break;
			case 'date' :
				if (sortValue && secondSortValue) {
					url += '&sortBy=' + sortBy + '&sortValue=' + sortValue + '&secondSortValue=' + secondSortValue;
				}
			break;
			case 'categoryAndDate' :
				if (sortValue && secondSortValue && thirdSortValue) {
					url += '&sortBy=' + sortBy + '&sortValue=' + sortValue + '&secondSortValue=' 
						+ secondSortValue + '&thirdSortValue=' + thirdSortValue;
				}
			break;
		}
	}
	ajaxGet(url, function(response){
		if (offset === 0) {
			content.innerHTML = '';
		}
		fillContent(JSON.parse(response), content);
	});
}
function toggleClass(elem, classToToggle) {
	if (elem.classList.contains(classToToggle)) {
		elem.classList.remove(classToToggle);
	} else {
		elem.classList.add(classToToggle);
	}
}
function setFocus(buttonToFocus, focusButton) {
	if (focusButton.elem) {
		focusButton.elem.classList.remove('buttonIsFocus');
	}
	buttonToFocus.classList.add('buttonIsFocus');
	focusButton.elem = buttonToFocus;
	focusButton.value = buttonToFocus.value;
}
function cancelFocus(focusButton) {
	if (focusButton.elem) {
		focusButton.elem.classList.remove('buttonIsFocus');
	}
	focusButton.elem = '';
	focusButton.value = '';
}
function menuIsOpen(menu) {
	if (menu.classList.contains('menuIsOpen')) {
		return true;
	} else {
		return false;
	}
}
function toggleButtonIsDisplay(arrButtons) {
	for (let i=0; i<arrButtons.length; i++) {
		if (arrButtons[i].classList.contains('buttonIsDisplay')) {
			arrButtons[i].classList.remove('buttonIsDisplay');
		} else {
			arrButtons[i].classList.add('buttonIsDisplay');
		}
	}
}
function displayInput(button, focusButton, inputCategory, inputDate) {
	switch (button) {
		case 'category':
			switch (focusButton) {
				case 'date':
					inputCategory.classList.add('inputIsDisplay');
					inputDate.classList.remove('inputIsDisplay');
				break;
				case 'categoryAndDate':
					inputDate.classList.remove('inputIsDisplay');
				break;
				default:
					inputCategory.classList.add('inputIsDisplay');
			}
		break;
		case 'date':
			switch (focusButton) {
				case 'category':
					inputCategory.classList.remove('inputIsDisplay');
					inputDate.classList.add('inputIsDisplay');
				break;
				case 'categoryAndDate':
					inputCategory.classList.remove('inputIsDisplay');
				break;
				default:
					inputDate.classList.add('inputIsDisplay');
			}
		break;
		case 'categoryAndDate':
			switch (focusButton) {
				case 'category':
					inputDate.classList.add('inputIsDisplay');
				break;
				case 'date':
					inputCategory.classList.add('inputIsDisplay');
				break;
				default:
				inputCategory.classList.add('inputIsDisplay');
				inputDate.classList.add('inputIsDisplay');
			}
		break;
	}
}
function hideInputs(inputCategory, inputDate, focusButton) {
	inputCategory.classList.remove('inputIsDisplay');
	inputDate.classList.remove('inputIsDisplay');
	focusButton = {elem :'', value :''};
}
function resetFormValue(form){
	form.elements['sortBy'].value = '';
	form.elements['tagCategory'].selectedIndex = '';
	form.elements['firstDate'].value = '';
	form.elements['secondDate'].value = '';
}
function getFormatedDate() {
	//this function is only use to get a default second date value if we don't have one
	//so we add one day to include "today" in the search result 
	let objDate = new Date();
	let year = objDate.getFullYear();
	let month = objDate.getMonth() + 1;
	let day = objDate.getDate() + 1;
	if (month < 10) {
		month = '0' + month;
	}
	if (day < 10) {
		day = '0' + day;
	}
	return year + '-' + month + '-' + day;
}

let webmasterSide;
if (document.getElementById('blockSchools') !== null) {
	webmasterSide = true;
} else {
	webmasterSide = false;
}
let schoolsId = document.querySelectorAll('.hide');
let content = document.getElementById('blockEntries');
let menuSearch = document.getElementById('search');
let activeSchool = {id:''};
if (!webmasterSide) {
	activeSchool.id = schoolsId[0].textContent;
	activeSchool.name = document.getElementById('schoolName').value;
}
//menu search stuff - buttons
let buttonToggleMenu = document.querySelector('.fa-search');
let arrButtons = document.querySelectorAll('#search button');
let buttonCategory = document.querySelector('#search button:nth-of-type(1)');
let buttonDate = document.querySelector('#search button:nth-of-type(2)');
let buttonCategoryAndDate = document.querySelector('#search button:nth-of-type(3)');
let focusButton = {elem :'', value :''};
//menu search stuff - inputs
let formSearch = document.querySelector('#schoolHistory form');
let blockInputCategory = document.querySelector('form > div:nth-of-type(1)');
let blockInputDate = document.querySelector('form > div:nth-of-type(2)');
let sortBy = formSearch.elements['sortBy'];
//button showMore
let buttonShowMore = document.getElementById('showMore');
let limit = 10;
let offset = 0;

if (webmasterSide) {
	// all schools are display. display history sort by school and toggle border of school Block
	let blocksSchool = document.querySelectorAll('.blockSchool > div:first-of-type');
	
	for (let i = 0; i < blocksSchool.length; i++){
		blocksSchool[i].addEventListener('click', function(){
			menuSearch.style.display = "flex";
			formSearch.style.display = "flex";
			buttonShowMore.style.display = "block";
			offset = 0;
			hideInputs(blockInputCategory, blockInputDate, focusButton);
			cancelFocus(focusButton);
			resetFormValue(formSearch);
			toggleBorder(blocksSchool[i], activeSchool, schoolsId[i].textContent);
			getHistory(activeSchool, content, offset);
		});
	}
}

//click buttonToggleMenu (iconeSearch) display sorting buttons
buttonToggleMenu.addEventListener('click', function(){
	if (menuIsOpen(buttonToggleMenu)) {
		//close menu, hide all buttons and inputs form, and cancel the focus
		toggleClass(buttonToggleMenu, 'menuIsOpen');
		cancelFocus(focusButton);
		toggleButtonIsDisplay(arrButtons);
		hideInputs(blockInputCategory, blockInputDate, focusButton);
		resetFormValue(formSearch);
		offset = 0;
		getHistory(activeSchool, content, offset);
	} else {
		//display menu and buttons
		toggleClass(buttonToggleMenu, 'menuIsOpen');
		toggleButtonIsDisplay(arrButtons);
	}
});

//click buttons display adapted form
buttonCategory.addEventListener('click', function(){
	displayInput(buttonCategory.value, focusButton.value, blockInputCategory, blockInputDate);
	setFocus(buttonCategory, focusButton);
	sortBy.value = 'category';
});

buttonDate.addEventListener('click', function(){
	displayInput(buttonDate.value, focusButton.value, blockInputCategory, blockInputDate);
	setFocus(buttonDate, focusButton);
	sortBy.value = 'date';
});

buttonCategoryAndDate.addEventListener('click', function(){
	displayInput(buttonCategoryAndDate.value, focusButton.value, blockInputCategory, blockInputDate);
	setFocus(buttonCategoryAndDate, focusButton);
	sortBy.value = 'categoryAndDate';
});

//change the inputs values will update the content
formSearch.elements['tagCategory'].addEventListener('change', function(){
	let category = formSearch.elements['tagCategory'].value;
	let firstDate = formSearch.elements['firstDate'].value;
	let secondDate = formSearch.elements['secondDate'].value;
	offset = 0;

	//if second date is set, add one day to include this date to the result
	if (secondDate) {
		let arr = secondDate.split('-');
		secondDate = arr[0] + '-' + arr[1] + '-' + (parseInt(arr[2]) + 1);
	}

	switch (sortBy.value) {
		case 'category' :
			getHistory(activeSchool, content, offset, sortBy.value, category);
		break;
		case 'categoryAndDate' :
			if (category) {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, date);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, '2020-01-01', secondDate);
				} else {
					getHistory(activeSchool, content, offset, 'category', category);
				}
			} else {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, 'date', firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, 'date', firstDate, date);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, 'date', '2020-01-01', secondDate);
				} else {
					getHistory(activeSchool, content, offset);
				}
			}
		break;
	}
});

formSearch.elements['firstDate'].addEventListener('change', function(){
	let category = formSearch.elements['tagCategory'].value;
	let firstDate = formSearch.elements['firstDate'].value;
	let secondDate = formSearch.elements['secondDate'].value;
	offset = 0;

	//if second date is set, add one day to include this date to the result
	if (secondDate) {
		let arr = secondDate.split('-');
		secondDate = arr[0] + '-' + arr[1] + '-' + (parseInt(arr[2]) + 1);
	}

	switch (sortBy.value) {
		case 'date' :
			if (firstDate && secondDate) {
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, secondDate);
			} else if (firstDate) {
				let date = getFormatedDate();
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, date);
			}
		break;
		case 'categoryAndDate' :
			if (category) {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, date);
				}
			} else {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, 'date', firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, 'date', firstDate, date);
				}
			}
		break;
	}
});

formSearch.elements['secondDate'].addEventListener('change', function(){
	let category = formSearch.elements['tagCategory'].value;
	let firstDate = formSearch.elements['firstDate'].value;
	let secondDate = formSearch.elements['secondDate'].value;
	offset = 0;

	//if second date is set, add one day to include this date to the result
	if (secondDate) {
		let arr = secondDate.split('-');
		secondDate = arr[0] + '-' + arr[1] + '-' + (parseInt(arr[2]) + 1);
	}

	switch (sortBy.value) {
		case 'date' :
			if (firstDate && secondDate) {
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, secondDate);
			} else if (secondDate) {
				getHistory(activeSchool, content, offset, sortBy.value, '2020-01-01', secondDate);
			} else if (firstDate) {
				let date = getFormatedDate();
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, date);
			}
		break;
		case 'categoryAndDate' :
			if (category) {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, secondDate);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, '2020-01-01', secondDate);
				}
			} else {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, 'date', firstDate, secondDate);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, 'date', '2020-01-01', secondDate);
				}
			}
		break;
	}
});

//button showMore
buttonShowMore.addEventListener('click', function(){
	let category = formSearch.elements['tagCategory'].value;
	let firstDate = formSearch.elements['firstDate'].value;
	let secondDate = formSearch.elements['secondDate'].value;
	offset += limit;

	switch (sortBy.value) {
		case 'category' :
			getHistory(activeSchool, content, offset, sortBy.value, category);
		break;
		case 'date' :
			if (firstDate && secondDate) {
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, secondDate);
			} else if (secondDate) {
				getHistory(activeSchool, content, offset, sortBy.value, '2020-01-01', secondDate);
			} else if (firstDate) {
				let date = getFormatedDate();
				getHistory(activeSchool, content, offset, sortBy.value, firstDate, date);
			} else {
				getHistory(activeSchool, content, offset);
			}
		break;
		case 'categoryAndDate' :
			if (category) {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, sortBy.value, category, firstDate, date);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, sortBy.value, category, '2020-01-01', secondDate);
				} else {
					getHistory(activeSchool, content, offset, 'category', category);
				}
			} else {
				if (firstDate && secondDate) {
					getHistory(activeSchool, content, offset, 'date', firstDate, secondDate);
				} else if (firstDate) {
					let date = getFormatedDate();
					getHistory(activeSchool, content, offset, 'date', firstDate, date);
				} else if (secondDate) {
					getHistory(activeSchool, content, offset, 'date', '2020-01-01', secondDate);
				} else {
					getHistory(activeSchool, content, offset);
				}
			}
		break;
		default :
			getHistory(activeSchool, content, offset);
	}
});

