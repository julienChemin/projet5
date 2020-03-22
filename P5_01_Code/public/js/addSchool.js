if (document.getElementById('addSchool')) {
	let btnNext = document.getElementById('addSchoolBtnNext');
	let btnPrevious = document.getElementById('addSchoolBtnPrevious');
	let btnOk = document.getElementById('addSchoolBtnOk');

	let blockOne = document.querySelector('#formAddSchool > div:nth-of-type(1)');
	let blockTwo = document.querySelector('#formAddSchool > div:nth-of-type(2)');
	let blockMsg = document.querySelector('#formAddSchool > div:nth-of-type(3)');

	let form = document.getElementById('formAddSchool');

	//btn next
	btnNext.addEventListener('click', function(){
		form.style.transform = 'scale(0.9)';
		
		setTimeout(function(){
			blockOne.style.display = 'none';
			blockTwo.style.display = 'flex';
			form.style.transform = 'scale(1)';
		}, 200);
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
