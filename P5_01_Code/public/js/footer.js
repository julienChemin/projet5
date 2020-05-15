let linksList = document.querySelector('#linksList ul');

function addLinkToFooter(arrayLinks) {
	for (let i=0; i<arrayLinks.length; i++) {
		let elemLi = document.createElement('li');
		let elemLink = document.createElement('a');
		elemLink.href = 'index.php?action=schoolProfile&school=' + arrayLinks[i];
		elemLink.textContent = arrayLinks[i];

		elemLi.appendChild(elemLink);
		linksList.appendChild(elemLi);
	}
}

window.addEventListener('load', function(){
	ajaxGet('index.php?action=getSchools', function(response){
		if (response !== "") {
			addLinkToFooter(JSON.parse(response));
		}
	});
});