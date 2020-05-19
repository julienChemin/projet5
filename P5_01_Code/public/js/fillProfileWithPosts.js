function fillProfile(sortedPosts) {
	if (sortedPosts['public'].length > 0) {
		setPosts(sortedPosts['public'], tabPublicPosts);
	}
	if (sortedPosts['private'].length > 0 && tabPrivatePosts !== null) {
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
function setFolderPost(post, blockContent) {
	let divFolder = document.createElement('div');
	divFolder.classList.add('folder');
	let divItem = document.createElement('div');
	divItem.classList.add('fullWidth');
	let elemFigure = document.createElement('figure');
	let elemDiv = document.createElement('div');
	//elemA.href = 'index.php?action=post&id=' + post['id'];
	let elemImg = document.createElement('img');
	elemImg.src = 'public/images/folder.png';
	if (post['filePath'] !== null) {
		let elemThumbnail = document.createElement('img');
		elemThumbnail.classList.add('thumbnailFolder');
		elemThumbnail.src = post['filePath'];
		elemDiv.appendChild(elemThumbnail);
	}
	if (post['title'] !== null) {
		elemImg.setAttribute('title', post['title']);
	}
	elemDiv.appendChild(elemImg);
	elemFigure.appendChild(elemDiv);
	divItem.appendChild(elemFigure);
	divFolder.appendChild(divItem);
	fillFolder(post['id'], divFolder);
	blockContent.appendChild(divFolder);
}
function fillFolder(postId, elemFolder) {
	if (sortedPosts['folder'][postId].length > 0) {
		sortedPosts['folder'][postId].forEach(post =>{
			switch (post['fileType']) {
				case 'image' :
					setImagePost(post, elemFolder);
				break;
				case 'video' :
					setVideoPost(post, elemFolder);
				break;
				case 'folder' :
					setFolderPost(post, elemFolder);
				break;
				case 'compressed' :
					setCompressedPost(post, elemFolder);
				break;
			}
		});
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
	if (arr['action'] === 'schoolProfile') {
		//school profile
		ajaxUrl = 'index.php?action=getSchoolPosts&school=' + arr['school'];
	} else {
		//user profile
		ajaxUrl = 'index.php?action=getUserPosts&id=' + arr['userId'];
	}
	ajaxGet(ajaxUrl, function(response){
		if (response !== 'false' && response.length > 0) {
			sortedPosts = JSON.parse(response);
			fillProfile(sortedPosts);
		}
	});
});
