let form = document.getElementById('formAdvancedSearch');
let inputSchoolFilter = document.querySelectorAll('input[name="schoolFilter"] + label');
let focusedInputSchoolFilter = "";
let inputSortBy = document.querySelectorAll('input[name="sortBy"] + label');
let focusedInputSortBy = "";
let existingTags = [];
let inputTag = document.getElementById('tagName');
let inputListTags = document.getElementById('listTags');
let blockRecommendedTags = document.getElementById('recommendedTags');
let blockSelectedTags = document.getElementById('selectedTags');
let divRecommendedTags = document.querySelector('#recommendedTags div');
let divSelectedTags = document.querySelector('#selectedTags div');
let blockFilterAdvancedSearch = document.getElementById('blockFilterAdvancedSearch');

if (blockFilterAdvancedSearch === null) {
	//advancedsearch home (select filter for advanced search)
	function setFocus(inputType = null, input = null) {
		if (inputType !== null && input !== null){
			switch (inputType) {
				case 'schoolFilter' :
					if (focusedInputSchoolFilter !== '') {
						focusedInputSchoolFilter.style.border = 'none';
						focusedInputSchoolFilter.style.color = 'white';
					}
					input.style.border = 'solid 1px #CF8B3F';
					input.style.color = '#CF8B3F';
					focusedInputSchoolFilter = input;
				break;
				case 'sortBy' :
					if (focusedInputSortBy !== '') {
						focusedInputSortBy.style.border = 'none';
						focusedInputSortBy.style.color = 'white';
					}
					input.style.border = 'solid 1px #CF8B3F';
					input.style.color = '#CF8B3F';
					focusedInputSortBy = input;
				break;
			}
		}
	}

	inputSchoolFilter.forEach(input => {
		input.addEventListener('click', function() {
			setFocus('schoolFilter', input);
		});
	});
	inputSortBy.forEach(input => {
		input.addEventListener('click', function() {
			setFocus('sortBy', input);
		});
	});

	//TAGS
	function createTag(tagValue, selected = false){
		if (selected) {
			//selected tags
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
			//recommended tags
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
	function deleteSpace(str){
		let arr = str.split(" ");
		let string = "";
		arr.forEach(char => {
			string += char;
		});
		return string;
	}
	inputTag.addEventListener('input', function(){
		divRecommendedTags.innerHTML = "";
		if (inputTag.value.length <= 1) {
			blockRecommendedTags.style.display = "none";
		} else if (inputTag.value.length > 1) {
			existingTags.forEach(tag => {
				if (tag.toLowerCase().indexOf(inputTag.value.toLowerCase()) !== -1) {
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
	window.addEventListener('load', function() {
		setFocus('schoolFilter', inputSchoolFilter[0]);
		form.elements.schoolFilter[0].checked = true;
		setFocus('sortBy', inputSortBy[0]);
		form.elements.sortBy[0].checked = true;
		form.elements.tagName.value = '';
		ajaxGet('index.php?action=getTags', function(response){
			if (response.length > 0) {
				existingTags = JSON.parse(response);
			}
		});
	});
} else {
	//advanced search result
	let form = document.querySelector('form');
	let pages = document.querySelectorAll('#pagingQuickSearch li');

	pages.forEach(page =>{
		page.addEventListener('click', function() {
			form.elements.pageToGo.value = page.textContent;
			form.elements.submit.click();
		});
	});
}
