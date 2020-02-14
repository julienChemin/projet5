if (document.getElementById('pseudo')) {
	let menuNavbar = document.getElementById('menuNavbar');
	let contentMenuNavbar = menuNavbar.children[0];
	let pseudo = document.getElementById('pseudo');
	let icone = document.querySelector('#pseudo i');
	let rect = icone.getBoundingClientRect();

	window.addEventListener("load", function(){
		menuNavbar.style.right = (window.innerWidth - (rect.x + rect.width)) + 'px';
	});

	window.addEventListener("resize", function(){
		rect = icone.getBoundingClientRect();
		menuNavbar.style.right = (window.innerWidth - (rect.x + rect.width)) + 'px';
	});

	pseudo.addEventListener("click", function(){
		if (icone.classList.contains('fa-sort-down')) {
			icone.classList.replace('fa-sort-down', 'fa-sort-up');
			menuNavbar.style.width = '300px';
			contentMenuNavbar.style.top = '0px';
		} else {
			icone.classList.replace('fa-sort-up', 'fa-sort-down');
			contentMenuNavbar.style.top = '-300px';
			setTimeout(function(){
				menuNavbar.style.width = '0px';
			}, 200);
		}
	});
}
