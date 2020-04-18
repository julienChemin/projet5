if (document.getElementById('moderatSchool') || document.getElementById('moderatAdmin') 
	|| document.getElementById('moderatUsers') || document.getElementById('schoolHistory')) {
	let arrBlockSchoolDiv = document.querySelectorAll('.blockSchool > div:nth-child(2)');
	let arrArrowDown = document.querySelectorAll('.fa-caret-square-down');
	let arrArrowUp = document.querySelectorAll('.fa-caret-square-up');

	if (arrArrowDown.length > 0) {
		for (let i=0; i<arrBlockSchoolDiv.length; i++) {
			arrBlockSchoolDiv[i].style.display = 'none';

			arrArrowDown[i].addEventListener('click', function(){
				arrArrowDown[i].style.display = 'none';
				arrArrowUp[i].style.display = 'inline-block';
				for (let j=0; j<arrBlockSchoolDiv.length; j++) {
					if (arrBlockSchoolDiv[j].style.display === 'flex') {
						arrArrowDown[j].style.display = 'inline-block';
						arrArrowUp[j].style.display = 'none';
						arrBlockSchoolDiv[j].style.display = 'none';
					}
				}
				arrBlockSchoolDiv[i].style.display = 'flex';
			});

			arrArrowUp[i].addEventListener('click', function(){
				arrArrowDown[i].style.display = 'inline-block';
				arrArrowUp[i].style.display = 'none';
				arrBlockSchoolDiv[i].style.display = 'none';
			});
		}
	}
}