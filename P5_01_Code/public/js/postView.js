let formAddComment = document.getElementById('addComment');
let spanMsgComment = document.getElementById('msgComment');
let btnAddComment = document.getElementById('submitComment');
let arrBtnDeleteComment = document.querySelectorAll('.deleteComment');
let arrBtnConfirmDelete = document.querySelectorAll('.confirmDelete');
let blockComments = document.querySelector('#addComment + div');

function nl2br (str, is_xhtml) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Philip Peterson
  // +   improved by: Onno Marsman
  // +   improved by: Atli Þór
  // +   bugfixed by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Maximusya
  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function addComment(content) {
	let divComment = document.createElement('div');
	divComment.classList.add('comment', 'fullWidth');
	let linkPictureProfile = document.createElement('a');
	linkPictureProfile.href = 'index.php?action=userProfile&userId=' + formAddComment.elements.userId.value;
	let imgPictureProfile = document.createElement('img');
	imgPictureProfile.src = formAddComment.elements.userPicture.value;
	linkPictureProfile.appendChild(imgPictureProfile);
	divComment.appendChild(linkPictureProfile);
	let divContent = document.createElement('div');
	let linkUserName = document.createElement('a');
	linkUserName.href = 'index.php?action=userProfile&userId=' + formAddComment.elements.userId.value;
	linkUserName.textContent = formAddComment.elements.userName.value;
	divContent.appendChild(linkUserName);
	let pContent = document.createElement('p');
	pContent.innerHTML = nl2br(content);
	divContent.appendChild(pContent);
	let pDatePublication = document.createElement('p');
	pDatePublication.textContent = 'a l\'instant';
	divContent.appendChild(pDatePublication);
	divComment.appendChild(divContent);
	blockComments.insertBefore(divComment, blockComments.childNodes[0]);
}
//add comment
btnAddComment.addEventListener('click', function(e){
	e.preventDefault();
	if (formAddComment.elements.commentContent.value !== "") {
		let url = 'index.php?action=setComment';
		let data = new FormData(formAddComment);
		ajaxPost(url, data, function(response){
			if (response.length > 0 && response !== "false") {
				//add comment to flux
				addComment(formAddComment.elements.commentContent.value);
				formAddComment.elements.commentContent.value = "";
				spanMsgComment.style.display = "inline";
				spanMsgComment.textContent = "Votre commentaire a bien été ajouté";
				spanMsgComment.style.color = 'green';
				setTimeout(function(){
					spanMsgComment.style.display = "none";
				}, 3000);
			} else {
				formAddComment.elements.commentContent.value = "";
				spanMsgComment.style.display = "inline";
				spanMsgComment.textContent = "Votre commentaire n'a pas pu être ajouté, veuillez réessayer plus tard";
				spanMsgComment.style.color = 'red';
				setTimeout(function(){
					spanMsgComment.style.display = "none";
				}, 3000);
			}
		});
	}
});

//delete comment
for (let i=0;i<arrBtnDeleteComment.length;i++) {
	arrBtnDeleteComment[i].addEventListener('click', function(){
		arrBtnConfirmDelete[i].style.display = "inline";
		arrBtnDeleteComment[i].style.display = "none";
		setTimeout(function(){
			arrBtnConfirmDelete[i].style.display = "none";
			arrBtnDeleteComment[i].style.display = "inline";
		}, 2500);
	});

	let idPost = arrBtnDeleteComment[i].getAttribute('idcomment');
	let url = 'index.php?action=deleteComment&id=' + idPost;
	arrBtnConfirmDelete[i].addEventListener('click', function(){
		ajaxGet(url, function(response){
			if (response) {
				let nodeToRemove = arrBtnConfirmDelete[i].parentNode.parentNode.parentNode;
				blockComments.removeChild(nodeToRemove);
			}
		});
	});
}

//delete post
let btnDeletePost = document.getElementById('deletePost');
let btnConfirmDeletePost = document.getElementById('confirmDeletePost');
let btnLike = document.querySelector('i[class="far fa-heart"]');

btnDeletePost.addEventListener('click', function(){
	btnConfirmDeletePost.style.display = "inline";
	btnDeletePost.style.display = "none";
	btnLike.style.display = "none";
	setTimeout(function(){
		btnConfirmDeletePost.style.display = "none";
		btnDeletePost.style.display = "inline";
		btnLike.style.display = "inline";
	}, 2500);
});
