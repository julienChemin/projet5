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
		elemImg.setAttribute('title', post['title']);
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
		elemImg.setAttribute('title', post['title']);
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
	let divItem = document.createElement('div');
	divItem.classList.add('fullWidth');
	let elemFigure = document.createElement('figure');
	let elemDiv = document.createElement('div');
	let elemImg = document.createElement('img');
	elemImg.src = 'public/images/folder.png';
	if (post['filePath'] !== null) {
		let elemThumbnail = document.createElement('img');
		elemThumbnail.classList.add('thumbnailFolder');
		elemThumbnail.src = post['filePath'];
		elemDiv.appendChild(elemThumbnail);
	}
	let elemSpan = document.createElement('span');
	elemSpan.classList.add('previewTitle');
	elemSpan.textContent = post['title'];
	elemDiv.appendChild(elemImg);
	elemDiv.appendChild(elemSpan);
	elemFigure.appendChild(elemDiv);
	divItem.appendChild(elemFigure);
	if (onFolderView) {
		//event to fill folder on click
		divItem.addEventListener('click', function() {
			ajaxUrl = 'index.php?action=getProfilePosts&idFolder=' + post['id'];
			ajaxGet(ajaxUrl, function(response){
				if (response !== 'false' && response.length > 0) {
					sortedPosts = JSON.parse(response);
					fillFolder(post['id'], divFolder);
					toggleFolder(divFolder);
					divItem.addEventListener('click', function(){
						toggleFolder(divFolder);
					});
				}
			});
		}, {'once' : true});
	} else {
		divItem.addEventListener('click', function(){
			toggleFolder(divFolder);
		});
	}
	divFolder.appendChild(divItem);
	//create post folder (link to consult post)
	let div = document.createElement('div');
	div.classList.add('post');
	let figure = document.createElement('figure');
	let link = document.createElement('a');
	link.href = 'index.php?action=post&id=' + post['id'];
	let img = document.createElement('img');
	img.src = 'public/images/folder.png';
	let span = document.createElement('span');
	span.classList.add('previewTitle');
	span.textContent = 'Consulter le dossier';
	link.appendChild(img);
	link.appendChild(span);
	figure.appendChild(link);
	div.appendChild(figure);
	divFolder.appendChild(div);
	if (!onFolderView) {
		fillFolder(post['id'], divFolder);
	}
	
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
			}
		});
	} else if (onFolderView) {
		elemFolder.style.justifyContent = "center";
		elemFolder.innerHTML = '<p class="emptyFolder">Ce dossier est vide pour le moment</p>';
	}
}
function toggleFolder(folder, action = null) {
	let childs = folder.childNodes;
	if (folderIsOpen(folder)) {
		folder.style.width = "33.3%";
		folder.style.border = "none";
		folder.style.margin = '0px';
		for (let i=1; i<childs.length;i++) {
			if (childs[i].classList.contains('folder') && folderIsOpen(childs[i])) {
				toggleFolder(childs[i]);
			}
			childs[i].style.width = "0%";
		}
	} else {
		folder.style.width = "98%";
		folder.style.border = "solid 2px #CF8B3F";
		folder.style.margin = 'auto';
		for (let i=1; i<childs.length;i++) {
			childs[i].style.width = "33.3%";
		}
	}
}
function folderIsOpen(folder) {
	if (folder.childNodes[1].style.width === "33.3%") {
		return true;
	} else { return false;}
}
function setCompressedPost(post, blockContent) {
	let divItem = document.createElement('div');
	divItem.classList.add('post');
	let elemFigure = document.createElement('figure');
	let elemA = document.createElement('a');
	elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	elemImg.src = 'public/images/fileOther.png';
	let elemSpan = document.createElement('span');
	elemSpan.classList.add('previewTitle');
	elemSpan.textContent = post['title'];
	elemA.appendChild(elemImg);
	elemA.appendChild(elemSpan);
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
	} else {
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
