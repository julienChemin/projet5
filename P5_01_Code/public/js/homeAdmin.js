function createItemImage(post, divSlide){
	let divItem = document.createElement('div');
	divItem.classList.add('items');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	elemImg.src = post['filePath'];
	if (post['title'] !== null) {
		elemImg.alt = post['title'];
		elemImg.setAttribute('title', post['title']);
	} else {
		elemImg.alt = 'Aperçu de la publication';
	}
	elemA.appendChild(elemImg);
	elemFigure.appendChild(elemA);
	divItem.appendChild(elemFigure);
	divSlide.appendChild(divItem);
}

function createItemVideo(post, divSlide){
	let divItem = document.createElement('div');
	divItem.classList.add('items');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	if (post['filePath'] !== null) {
		elemImg.src = post['filePath'];
		let elemIconeVid = document.createElement('img');
		elemIconeVid.classList.add('iconeVideo');
		elemIconeVid.src = 'public/images/defaultVideoThumbnail.png';
		elemA.appendChild(elemIconeVid);
	} else {
		elemImg.src = 'public/images/defaultVideoThumbnail.png';
	}
	if (post['title'] !== null) {
		elemImg.alt = post['title'];
		elemImg.setAttribute('title', post['title']);
	} else {
		elemImg.alt = 'Aperçu de la publication';
	}
	elemA.appendChild(elemImg);
	elemFigure.appendChild(elemA);
	divItem.appendChild(elemFigure);
	divSlide.appendChild(divItem);
}

function createSlide(posts){
	let divSlide = document.createElement('div');
	divSlide.classList.add('slide');
	posts.forEach(post =>{
		switch (post['fileType']) {
			case 'image' :
				createItemImage(post, divSlide);
			break;
			case 'video' :
				createItemVideo(post, divSlide);
			break;
		}
	});
	blockSlides.appendChild(divSlide);
}

let addSlide = (slide) => {
	url = 'index.php?action=getPostsBySchool&offset=' + (slide.positionSlider * slide.nbItemBySlide);
	url += '&limit=' + slide.nbItemBySlide + '&school=' + document.getElementById('schoolName').value;
	ajaxGet(url, function(response){
		if (response !== 'false' && response.length > 0) {
			createSlide(JSON.parse(response));
			slide.nbSlide += 1;
			slide.positionSlider += 1;
			slide.blockSlides.style.left = '-' + ((slide.positionSlider - 1) * 100) + '%';
		}
	});
}
let blockSlides = document.querySelector('.slider');
const slide = new Slide(blockSlides, 
						document.querySelector('.arrowLeft'), 
						document.querySelector('.arrowRight'), 
						5, 
						addSlide);
window.addEventListener("load", function(){
	slide.init();
	url = 'index.php?action=getPostsBySchool&school=' + document.getElementById('schoolName').value + '&limit=5';
	ajaxGet(url, function(response){
		if (response !== 'false' && response.length > 0) {
			createSlide(JSON.parse(response));
		} else {
			let divSlide = document.createElement('div');
			divSlide.classList.add('slide');
			divSlide.textContent = "Il n'y a aucune publication pour l'instant";
			blockSlides.appendChild(divSlide);
		}
	});
});