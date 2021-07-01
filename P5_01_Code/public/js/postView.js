let formAddComment = document.getElementById('addComment');
let spanMsgComment = document.getElementById('msgComment');
let btnAddComment = document.getElementById('submitComment');
let arrBtnDeleteComment = document.querySelectorAll('.deleteComment');
let arrBtnConfirmDelete = document.querySelectorAll('.confirmDelete');
let blockComments = document.querySelector('#addComment + div');
const regexNumberUnderQuote = /^\"([0-9]+)\"$/i;

function nl2br(str, is_xhtml)
{
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

function addComment(content, idComment)
{
    let divComment = document.createElement('div');
    divComment.classList.add('comment', 'fullWidth');
    let linkPictureProfile = document.createElement('a');
    linkPictureProfile.href = 'index.php?action=userProfile&userId=' + formAddComment.elements.userId.value;
    linkPictureProfile.setAttribute('style', "background-image: url('" + formAddComment.elements.userPicture.value + "');");

    divComment.appendChild(linkPictureProfile);
    let divContent = document.createElement('div');

    let linkUserName = document.createElement('a');
    linkUserName.href = 'index.php?action=userProfile&userId=' + formAddComment.elements.userId.value;
    linkUserName.textContent = formAddComment.elements.userName.value;
    linkUserName.style.color = formAddComment.elements.userColor.value;
    divContent.appendChild(linkUserName);

    let pContent = document.createElement('p');
    pContent.innerHTML = nl2br(content);
    divContent.appendChild(pContent);

    let pDatePublication = document.createElement('p');
    pDatePublication.textContent = 'a l\'instant - ';

    let spanDelete = document.createElement('span');
    spanDelete.classList.add('deleteComment');
    spanDelete.setAttribute('idComment', idComment);
    spanDelete.textContent = "Supprimer le commentaire";

    let spanConfirmDelete = document.createElement('span');
    spanConfirmDelete.classList.add('confirmDelete');
    spanConfirmDelete.textContent = "Supprimer définitivement ?";
    addEventDeleteComment(spanDelete, spanConfirmDelete);

    pDatePublication.appendChild(spanDelete);
    pDatePublication.appendChild(spanConfirmDelete);
    divContent.appendChild(pDatePublication);
    divComment.appendChild(divContent);
    blockComments.insertBefore(divComment, blockComments.childNodes[0]);
}

function displayComment(comment)
{
    let userColor = comment['authorIsAdmin'] ||comment['authorIsModerator'] ? "#de522f" : "#CF8B3F";

    let divComment = document.createElement('div');
    divComment.classList.add('comment', 'fullWidth');
    let linkPictureProfile = document.createElement('a');
    linkPictureProfile.href = 'index.php?action=userProfile&userId=' + comment['idAuthor'];
    linkPictureProfile.setAttribute('style', "background-image: url('" + comment['profilePictureAuthor'] + "');");

    divComment.appendChild(linkPictureProfile);
    let divContent = document.createElement('div');

    let linkUserName = document.createElement('a');
    linkUserName.href = 'index.php?action=userProfile&userId=' + comment['idAuthor'];
    linkUserName.textContent = comment['firstNameAuthor'] + " " + comment['lastNameAuthor'];
    linkUserName.style.color = userColor;
    divContent.appendChild(linkUserName);

    let pContent = document.createElement('p');
    pContent.innerHTML = nl2br(comment['content']);
    divContent.appendChild(pContent);

    let pDatePublication = document.createElement('p');
    pDatePublication.textContent = comment['datePublication'] + " - ";

    let spanDelete = document.createElement('span');
    spanDelete.classList.add('deleteComment');
    spanDelete.setAttribute('idComment', comment['id']);
    spanDelete.textContent = "Supprimer le commentaire";

    let spanConfirmDelete = document.createElement('span');
    spanConfirmDelete.classList.add('confirmDelete');
    spanConfirmDelete.textContent = "Supprimer définitivement ?";
    addEventDeleteComment(spanDelete, spanConfirmDelete);

    pDatePublication.appendChild(spanDelete);
    pDatePublication.appendChild(spanConfirmDelete);
    divContent.appendChild(pDatePublication);
    divComment.appendChild(divContent);
    blockComments.insertBefore(divComment, blockShowMoreComments);
}

function addEventDeleteComment(btnDelete, btnConfirm) {
    btnDelete.addEventListener(
        'click', function () {
            btnConfirm.style.display = "inline";
            btnDelete.style.display = "none";
            setTimeout(function () {
                btnConfirm.style.display = "none";
                btnDelete.style.display = "inline";
            }, 2500);
        }
    );

    let idComment = btnDelete.getAttribute('idcomment');
    let url = 'index.php?action=deleteComment&id=' + idComment;
    btnConfirm.addEventListener(
        'click', function () {
            ajaxGet(url, function (response) {
                if (response !== 'false') {
                    let nodeToRemove = btnConfirm.parentNode.parentNode.parentNode;
                    blockComments.removeChild(nodeToRemove);
                }
            });
        }
    );
}

//add comment
if (btnAddComment !== null) {
    btnAddComment.addEventListener(
        'click', function (e) {
            e.preventDefault();

            if (formAddComment.elements.commentContent.value !== "") {
                let url = 'index.php?action=setComment';
                let data = new FormData(formAddComment);
                ajaxPost(url, data, function (response) {
                    if (response.length > 0 && response !== "false" && regexNumberUnderQuote.test(response)) {
                        //add comment to flux
                        idComment = regexNumberUnderQuote.exec(response)[1];
                        addComment(formAddComment.elements.commentContent.value, idComment);
                        formAddComment.elements.commentContent.value = "";
                        spanMsgComment.style.display = "inline";
                        spanMsgComment.textContent = "Votre commentaire a bien été ajouté";
                        spanMsgComment.style.color = 'green';
                        setTimeout(function () {
                            spanMsgComment.style.display = "none";
                        }, 3000);
                    } else {
                        formAddComment.elements.commentContent.value = "";
                        spanMsgComment.style.display = "inline";
                        spanMsgComment.textContent = "Votre commentaire n'a pas pu être ajouté, veuillez réessayer plus tard";
                        spanMsgComment.style.color = 'red';
                        setTimeout(function () {
                            spanMsgComment.style.display = "none";
                        }, 3000);
                    }
                });
            }
        }
    );
}

//event delete comment
for (let i=0;i<arrBtnDeleteComment.length;i++) {
    addEventDeleteComment(arrBtnDeleteComment[i], arrBtnConfirmDelete[i]);
}

// button show more comments
const blockShowMoreComments = document.getElementById('showMoreComments');
const btnShowMoreComments = document.querySelector('#showMoreComments > span');

if (blockShowMoreComments !== null) {
    const idElem = blockShowMoreComments.getAttribute('idElem');
    const limitComments = parseInt(blockShowMoreComments.getAttribute('limitComments'));
    const totalComments = parseInt(blockShowMoreComments.getAttribute('totalComments'));
    let offsetComments = limitComments;


    btnShowMoreComments.addEventListener(
        'click', function() {
            urlGetMoreComments = 'index.php?action=getCommentsFromPosts&idElem=' + idElem + '&limit=' + limitComments + '&offset=' + offsetComments;
            ajaxGet(urlGetMoreComments, function(response) {
                if (response.length > 0 && response !== 'false') {
                    response = JSON.parse(response);
                    response.forEach(comment => {
                        displayComment(comment);
                    });

                    offsetComments += limitComments;

                    if (offsetComments >= totalComments) {
                        blockShowMoreComments.style.display = "none";
                    }
                }
            });
        }
    );
}

//like/unlike the post
let btnLike = document.querySelector('#heart > i');

if (btnLike !== null) {
    let idPost = btnLike.getAttribute('idPost');
    let blockNbLike = document.querySelector('#heart > span');
    let nbLike = parseInt(blockNbLike.textContent);
    let urlAlreadyLike = 'index.php?action=userAlreadyLikePost&idPost=' + idPost;
    let urlToggleLike = 'index.php?action=toggleLikePost&idPost=' + idPost;

    function likePost()
    {
        btnLike.style.transform = 'scale(0.5)';
        setTimeout(function () {
            btnLike.classList.remove('far');
            btnLike.classList.add('fas');
            btnLike.style.transform = 'scale(1)';
        }, 200);
        ajaxGet(urlToggleLike, function (response) {
            if (response === 'true') {
                blockNbLike.textContent = (nbLike += 1);
                urlToggleLike = 'index.php?action=toggleLikePost&idPost=' + idPost;
                setTimeout(function () {
                    btnLike.addEventListener(
                        'click', function () {
                            unlikePost();
                        }, {once : true}
                    );
                }, 400);
            }
        });
    }
    function unlikePost()
    {
        btnLike.style.transform = 'scale(0.5)';
        setTimeout(function () {
            btnLike.classList.remove('fas');
            btnLike.classList.add('far');
            btnLike.style.transform = 'scale(1)';
        }, 200);
        ajaxGet(urlToggleLike, function (response) {
            if (response === 'true') {
                blockNbLike.textContent = (nbLike -= 1);
                urlToggleLike = 'index.php?action=toggleLikePost&idPost=' + idPost;
                setTimeout(function () {
                    btnLike.addEventListener(
                        'click', function () {
                            likePost();
                        }, {once : true}
                    );
                }, 400);
            }
        });
    }
    window.addEventListener(
        'load', function () {
            //set like btn
            setTimeout(function () {
                ajaxGet(urlAlreadyLike, function (response) {
                    if (response === 'true') {
                        btnLike.classList.remove('far');
                        btnLike.classList.add('fas');
                        btnLike.style.transform = 'scale(1.4)';
                        setTimeout(function () {
                            btnLike.style.transform = 'scale(1)';
                            btnLike.addEventListener(
                                'click', function () {
                                    unlikePost();
                                }, {once : true}
                            );
                        }, 200);
                    } else {
                        btnLike.style.transform = 'scale(1.4)';
                        setTimeout(function () {
                            btnLike.style.transform = 'scale(1)';
                            btnLike.addEventListener(
                                'click', function () {
                                    likePost();
                                }, {once : true}
                            );
                        }, 200);
                    }
                });
            }, 500);
        }
    );
}
//////////////////////////////////////
        // GROUPED POSTS
//////////////////////////////////////

let elemPosts = document.querySelectorAll('#viewPost > article > section > .container > *');
let elemThumbnail = document.querySelectorAll('#listGroupPosts > .container > p');
let blockDisplayedContent = document.querySelector('#viewPost > article > section');

if (elemThumbnail !== null && elemThumbnail.length > 0) {
    let displayedElem = elemPosts[0];
    let displayedThumbnail = elemThumbnail[0];
    displayedThumbnail.classList.add('displayedThumbnail');

    function displayElem(elem, thumbnail) {
        if (displayedThumbnail !== thumbnail) {
            displayedElem.classList.remove('groupedPostDisplay');
            displayedElem.classList.add('groupedPostHidden');
            elem.classList.remove('groupedPostHidden');
            elem.classList.add('groupedPostDisplay');
            displayedElem = elem;
            if (thumbnail.getAttribute('postType') === 'compressed' && !blockDisplayedContent.classList.contains('showCompressedFile')) {
                blockDisplayedContent.classList.add('showCompressedFile');
            } else if (thumbnail.getAttribute('postType') !== 'compressed' && blockDisplayedContent.classList.contains('showCompressedFile')) {
                blockDisplayedContent.classList.remove('showCompressedFile');
            }
        
            displayedThumbnail.classList.remove('displayedThumbnail');
            thumbnail.classList.add('displayedThumbnail');
            displayedThumbnail = thumbnail;
        }
    }

    for (let i = 0; i < elemThumbnail.length; i++) {
        elemThumbnail[i].addEventListener(
            'click', function() {
                displayElem(elemPosts[i], elemThumbnail[i]);
            }
        );
    }
}

//////////////////////////////////////
        // MODAL
//////////////////////////////////////

const modal = document.getElementById('modal');

if (modal !== null) {
    const modalContent = document.querySelector('#modal > div');
    const btnCloseModal = document.querySelectorAll('.closeModal');

    //delete post
    let btnDeletePost = document.getElementById('deletePost');
    let blockConfirmDeletePost = document.getElementById('confirmDeletePost');

    if (btnDeletePost !== null) {
        btnDeletePost.addEventListener(
            'click', function () {
                modal.style.display = "flex";
                blockConfirmDeletePost.style.display = "flex";
            }
        );
    }

    //close modal
    for (btnClose of btnCloseModal) {
        btnClose.addEventListener(
            'click', function() {
                modal.style.display = "none";
                blockConfirmDeletePost.style.display = "none";
            }
        )
    }
}