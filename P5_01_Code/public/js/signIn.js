if (document.getElementById('formConnect') || document.getElementById('signIn')) {
	let modal = document.getElementById('modal');
	let checkbox = document.getElementById('stayConnect');
	let btnOk = document.getElementById('btnAcceptCookie');
	let btnCancel = document.getElementById('btnCancelCookie');

	checkbox.addEventListener("change", function(){
		if (checkbox.checked) {
			modal.style.display = "flex";
		} else {
			modal.style.display = "none";
		}
	});

	btnOk.addEventListener("click", function(){
		modal.style.display = "none";
	});

	btnCancel.addEventListener("click", function(){
		checkbox.checked = false;
		modal.style.display = "none";
	});
}
