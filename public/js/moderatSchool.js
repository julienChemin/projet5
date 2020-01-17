if (document.getElementById('moderatSchool')) {
	let arrBlockSchoolDiv = document.querySelectorAll('.blockSchool > div:nth-child(2)');
	let arrArrowDown = document.querySelectorAll('.fa-caret-square-down');
	let arrArrowUp = document.querySelectorAll('.fa-caret-square-up');

	for (let i=0; i<arrBlockSchoolDiv.length; i++) {
		arrArrowDown[i].addEventListener('click', function(){
			arrArrowDown[i].style.display = 'none';
			arrArrowUp[i].style.display = 'inline-block';
			arrBlockSchoolDiv[i].style.display = 'flex';
		});

		arrArrowUp[i].addEventListener('click', function(){
			arrArrowDown[i].style.display = 'inline-block';
			arrArrowUp[i].style.display = 'none';
			arrBlockSchoolDiv[i].style.display = 'none';
		});
	}

	//display msg
	if (document.querySelector('.msg')) {
		let blockOne = document.querySelector('#moderatSchool form');
		let msg = document.querySelector('.msg').parentNode;
		let btnOk = document.getElementById('moderatSchool');

		blockOne.style.display = 'none';
		msg.style.display = 'flex';

		//btn ok
		btnOk.addEventListener('click', function(){
			msg.style.transform = 'scale(0.9)';
		});
	}
}