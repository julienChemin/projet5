if (document.getElementById('addSchool')) {
	let btnNext = document.getElementById('addSchoolBtnNext');
	let btnPrevious = document.getElementById('addSchoolBtnPrevious');
	let btnOk = document.getElementById('addSchoolBtnOk');

	let blockOne = document.querySelector('#formAddSchool > div:nth-of-type(1)');
	let blockTwo = document.querySelector('#formAddSchool > div:nth-of-type(2)');
	let blockMsg = document.querySelector('#formAddSchool > div:nth-of-type(3)');

	let form = document.getElementById('formAddSchool');
	let inputSchoolName = form.elements.schoolName;
	let inputSchoolCode = form.elements.schoolCode;
	let inputSchoolNbEleve = form.elements.schoolNbEleve;

	function blink(elem){
		elem.style.border = 'solid 2px red';
		setTimeout(function(){
			elem.style.border = 'solid 2px #CF8B3F';
			setTimeout(function(){
				elem.style.border = 'solid 2px red';
				setTimeout(function(){
					elem.style.border = 'solid 2px #CF8B3F';
				}, 2000);
			}, 150);
		}, 150);
	}

	//btn next
	btnNext.addEventListener('click', function(){
		if (inputSchoolName.value !== "" && inputSchoolCode.value !== "" && inputSchoolNbEleve.value !== "") {
			form.style.transform = 'scale(0.9)';
			setTimeout(function(){
				blockOne.style.display = 'none';
				blockTwo.style.display = 'flex';
				form.style.transform = 'scale(1)';
			}, 200);
		} else {
			if (inputSchoolName.value === "") {
				blink(inputSchoolName);
			} else if (inputSchoolCode.value === "") {
				blink(inputSchoolCode);
			} else if (inputSchoolNbEleve.value === "") {
				blink(inputSchoolNbEleve);
			}
		}
		
	});

	//btn previous
	btnPrevious.addEventListener('click', function(){
		form.style.transform = 'scale(0.9)';
		
		setTimeout(function(){
			blockTwo.style.display = 'none';
			blockOne.style.display = 'flex';
			form.style.transform = 'scale(1)';
		}, 200);
	});

	//display msg
	if (document.querySelector('.msg')) {
		let msg = document.querySelector('.msg').parentNode;
		blockOne.style.display = 'none';
		msg.style.display = 'flex';

		//btn ok
		btnOk.addEventListener('click', function(){
			form.style.transform = 'scale(0.9)';

			setTimeout(function(){
				blockMsg.style.display = 'none';
				blockOne.style.display = 'flex';
				form.style.transform = 'scale(1)';
			}, 200);
		});
	}
}
