function fillProfile(sortedPosts) {
	if (sortedPosts['public'].length > 0) {
		setPosts(sortedPosts['public'], tabPublicPosts);
	}
	if (sortedPosts['private'].length > 0 && tabPrivatePosts !== null && document.getElementById('pseudo') !== null) {
		setPosts(sortedPosts['private'], tabPrivatePosts);
	}
}

function setPosts(posts, blockContent) {
	posts.forEach(post =>{
		switch (post['fileType']) {
			case 'image' :
				setImagePost(post, blockContent);
			break;

			case 'video' :
				setVideoPost(post, blockContent);
			break;

			case 'folder' :
				setFolderPost(post, blockContent);
			break;

			case 'compressed' :
				setCompressedPost(post, blockContent);
			break;

			case 'grouped' :
				setGroupedPost(post, blockContent);
			break;
		}
	});
}

function setImagePost(post, blockContent) {
	let divItem = document.createElement('div');
	divItem.classList.add('post');
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
	blockContent.appendChild(divItem);
}

function setVideoPost(post, blockContent) {
	let divItem = document.createElement('div');
	divItem.classList.add('post');
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
	blockContent.appendChild(divItem);
}

function setFolderPost(post, blockContent, onFolderView = false) {
	//create folder
	let divFolder = document.createElement('div');
	divFolder.classList.add('folder');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemIconeFolder = document.createElement('img');
	elemIconeFolder.src = 'public/images/folder.png';
	elemIconeFolder.alt = 'Publication de type dossier';
	if (post['filePath'] !== null) {
		elemIconeFolder.classList.add('iconeFolder');
		let elemThumbnail = document.createElement('img');
		elemThumbnail.src = post['filePath'];
		if (post['title'] !== null) {
			elemThumbnail.alt = post['title'];
			elemThumbnail.setAttribute('title', post['title']);
		} else {
			elemThumbnail.alt = 'Aperçu de la publication';
		}
		elemA.appendChild(elemThumbnail);
	}
	let elemSpan = document.createElement('span');
	elemSpan.classList.add('previewTitle');
	elemSpan.classList.add('hideUnder600Width');
	elemSpan.textContent = post['title'];
	elemA.appendChild(elemIconeFolder);
	elemA.appendChild(elemSpan);
	elemFigure.appendChild(elemA);
	divFolder.appendChild(elemFigure);
	
	blockContent.appendChild(divFolder);
}

function fillFolder(postId, elemFolder, onFolderView = false) {
	if (sortedPosts['folder'][postId] !== undefined && sortedPosts['folder'][postId].length > 0) {
		sortedPosts['folder'][postId].forEach(post =>{
			switch (post['fileType']) {
				case 'image' :
					setImagePost(post, elemFolder);
				break;

				case 'video' :
					setVideoPost(post, elemFolder);
				break;

				case 'folder' :
					setFolderPost(post, elemFolder, onFolderView);
				break;

				case 'compressed' :
					setCompressedPost(post, elemFolder);
				break;

				case 'grouped' :
					setGroupedPost(post, elemFolder);
				break;
			}
		});
	} else if (onFolderView) {
		elemFolder.style.justifyContent = "center";
		elemFolder.innerHTML = '<p class="emptyFolder">Ce dossier est vide pour le moment</p>';
	}
}

function setCompressedPost(post, blockContent) {
	let divItem = document.createElement('div');
	divItem.classList.add('post');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	elemImg.src = 'public/images/fileOther.png';
	elemImg.alt = 'Publication de type fichier zip / rar';
	let elemSpan = document.createElement('span');
	elemSpan.classList.add('previewTitle');
	elemSpan.textContent = post['title'];
	elemA.appendChild(elemImg);
	elemA.appendChild(elemSpan);
	elemFigure.appendChild(elemA);
	divItem.appendChild(elemFigure);
	blockContent.appendChild(divItem);
}

function setGroupedPost(post, blockContent) {
	let divItem = document.createElement('div');
	divItem.classList.add('post');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemIcone = document.createElement('img');
	elemIcone.src = 'public/images/file.png';
	elemIcone.alt = 'Publication groupé';
	elemIcone.classList.add('iconeFolder');
	let elemImg = document.createElement('img');
	elemImg.src = post['filePath'];
	if (post['title'] !== null) {
		elemImg.alt = post['title'];
		elemImg.setAttribute('title', post['title']);
	} else {
		elemImg.alt = 'Aperçu de la publication';
	}
	elemA.appendChild(elemIcone);
	elemA.appendChild(elemImg);
	elemFigure.appendChild(elemA);
	divItem.appendChild(elemFigure);
	blockContent.appendChild(divItem);
}

let tabPublicPosts = document.querySelector('#tabPublication > div');
let tabPrivatePosts = document.querySelector('#tabPrivatePublication > div');
let sortedPosts;
 
window.addEventListener('load', function(){
	let ajaxUrl = "";
	let url = window.location.search.split('?')[1].split('&');
	let arr = [];
	for (let i=0; i<url.length; i++) {
		let splitUrl = url[i].split('=');
		arr[splitUrl[0]] = splitUrl[1];
	}
	if (arr['action'] === 'post') {
		//folder view
		ajaxUrl = 'index.php?action=getProfilePosts&idFolder=' + arr['id'];
	} else if (arr['action'] === 'schoolProfile') {
		//school profile
		ajaxUrl = 'index.php?action=getSchoolPosts&school=' + arr['school'];
	} else if (arr['action'] === 'userProfile') {
		//user profile
		ajaxUrl = 'index.php?action=getUserPosts&id=' + arr['userId'];
	}
	ajaxGet(ajaxUrl, function(response){
		if (response !== 'false' && response.length > 0) {
			sortedPosts = JSON.parse(response);
			if (arr['action'] === 'post') {
				fillFolder(arr['id'], document.querySelector('#viewFolder > article > section > div'), true);
			} else {
				fillProfile(sortedPosts);
			}
		} else if (arr['action'] === 'post') {
			document.querySelector('#viewFolder > article > section > div').innerHTML = '<p class="emptyFolder">Ce dossier est vide pour le moment</p>';
		}
	});
});
