function createItemImage(post, divSlide){
	let elemFigure = document.createElement('figure');
	elemFigure.classList.add('items');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	elemImg.src = post['filePath'];
	if (post['title'] !== null) {
		elemImg.setAttribute('title', post['title']);
	}
	elemA.appendChild(elemImg);
	elemFigure.appendChild(elemA);
	divSlide.appendChild(elemFigure);
}

function createItemVideo(post, divSlide){
	let elemFigure = document.createElement('figure');
	elemFigure.classList.add('items');
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
		elemImg.setAttribute('title', post['title']);
	}
	elemA.appendChild(elemImg);
	elemFigure.appendChild(elemA);
	divSlide.appendChild(elemFigure);
}

function createSlide(posts, blockSlider){
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
	blockSlider.appendChild(divSlide);
}
if (document.getElementById('homeAdmin') !== null) {
	//admin side
	let blockSlider = document.querySelector('.slider');
	let addSlide = (slide) => {
		myUrl = slide.url + '&offset=' + (slide.positionSlider * slide.nbItemBySlide);
		myUrl += '&limit=' + slide.nbItemBySlide + '&school=' + document.getElementById('schoolName').value;
		ajaxGet(myUrl, function(response){
			if (response !== 'false' && response.length > 0) {
				createSlide(JSON.parse(response), slide.blockSlider);
				slide.nbSlide += 1;
				slide.positionSlider += 1;
				slide.blockSlider.style.left = '-' + ((slide.positionSlider - 1) * 100) + '%';
			} else {slide.goToFirstSlide();}
		});
	};
	const slide = new Slide(blockSlider, 
							document.querySelector('.arrowLeft'), 
							document.querySelector('.arrowRight'), 
							6, 
							'index.php?action=getPostsBySchool', 
							addSlide);
	if (blockSlider !== null) {
		window.addEventListener("load", function(){
			slide.init();
			let url = 'index.php?action=getPostsBySchool&school=' + document.getElementById('schoolName').value + '&limit=6';
			ajaxGet(url, function(response){
				if (response !== 'false' && response.length > 0) {
					createSlide(JSON.parse(response), blockSlider);
				} else {
					let divSlide = document.createElement('div');
					divSlide.classList.add('slide');
					let elemP = document.createElement('p');
					elemP.textContent = "Il n'y a aucune publication pour l'instant";
					divSlide.appendChild(elemP);
					blockSlider.appendChild(divSlide);
				}
			});
		});
	}
} else if (document.getElementById('home') !== null) {
	//user side
	let arrBlockSlider = document.querySelectorAll('.slider');
	let arrArrowLeft = document.querySelectorAll('.arrowLeft');
	let arrArrowRight = document.querySelectorAll('.arrowRight');
	let arrSlides = [];
	let url = '';
	for (let i=0; i<arrBlockSlider.length; i++) {
		switch (arrBlockSlider[i].getAttribute('slidetype')) {
			case 'lastPosted' :
				url = 'index.php?action=getLastPosted';
			break;
			case 'mostLiked' :
				url = 'index.php?action=getMostLikedPosts';
			break;
			case 'bySchool' :
				let school = arrBlockSlider[i].getAttribute('slidevalue');
				url = 'index.php?action=getPostsBySchool&school=' + school;
			break;
			case 'withTag' :
				let tag = arrBlockSlider[i].getAttribute('slidevalue');
				url = 'index.php?action=getPostsByTag&tag=' + tag;
			break;
		}
		let addSlide = (slide) => {
			let myUrl = slide.url += '&offset=' + (slide.positionSlider * slide.nbItemBySlide);
			myUrl += '&limit=' + slide.nbItemBySlide;
			ajaxGet(myUrl, function(response){
				if (response !== 'false' && response.length > 0) {
					createSlide(JSON.parse(response), slide.blockSlider);
					slide.nbSlide += 1;
					slide.positionSlider += 1;
					slide.blockSlider.style.left = '-' + ((slide.positionSlider - 1) * 100) + '%';
				} else {slide.goToFirstSlide();}
			});
		};
		arrSlides[i] = new Slide(arrBlockSlider[i], 
								arrArrowLeft[i], 
								arrArrowRight[i], 
								6, 
								url, 
								addSlide);
	}
	window.addEventListener("load", function(){
		arrSlides.forEach(slide => {
			slide.init();
		});
	});
}