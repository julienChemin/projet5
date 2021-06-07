const modal = document.getElementById('modal');
const modalDeleteReply = document.getElementById('confirmDeleteReply');
const btnDeleteReply = document.querySelectorAll('.replyOption i[class~="fa-trash"]');
const linkDeleteReply = document.querySelector('#confirmDeleteReply a');
const btnCancelDeleteReply = document.querySelector('#confirmDeleteReply .closeModal');

const modalDeleteTopic = document.getElementById('confirmDeleteTopic');
const btnDeleteTopic = document.querySelector('.deleteTopic');
const linkDeleteTopic = document.querySelector('#confirmDeleteTopic a');
const btnCancelDeleteTopic = document.querySelector('#confirmDeleteTopic .closeModal');

let btnToggleCloseTopic = document.querySelector('#blockToggleIsClose > i');
let topicIsClose;

function addEventToggleIsClose(elem) {
    elem.addEventListener(
        'click', function(e) {
            let url = 'index.php?action=toggleTopicIsClose&topicId=' + e.target.getAttribute('topicId');

            ajaxGet(url, function(response) {
                if (response.length > 0 && response !== "false") {
                    if (topicIsClose) {
                        e.target.style.border = "solid 1px red";
                    } else {
                        e.target.style.border = "solid 1px green";
                    }
                    
                    setTimeout(() => {
                        if (topicIsClose) {
                            e.target.classList.add('topicIsOpen');
                            e.target.classList.remove('topicIsClose');
                            e.target.classList.remove('fa-lock');
                            e.target.classList.add('fa-unlock');
                            e.target.title = "Verrouiller le sujet";
                            topicIsClose = false;
                        } else {
                            e.target.classList.remove('topicIsOpen');
                            e.target.classList.add('topicIsClose');
                            e.target.classList.add('fa-lock');
                            e.target.classList.remove('fa-unlock');
                            e.target.title = "déverrouiller le sujet";
                            topicIsClose = true;
                        }
                        e.target.style.border = "none";
                        addEventToggleIsClose(elem);
                    }, 1500);
                }
            });
        }, {'once':true}
    );
}

let btnTogglePinnedTopic = document.querySelector('#blockToggleIsPinned > i');
let topicIsPinned;

function addEventToggleIsPinned(elem) {
    elem.addEventListener(
        'click', function(e) {
            let url = 'index.php?action=toggleTopicIsPinned&topicId=' + e.target.getAttribute('topicId');

            ajaxGet(url, function(response) {
                if (response.length > 0 && response !== "false") {
                    if (topicIsPinned) {
                        e.target.style.color = "red";
                        e.target.style.border = "solid 1px red";
                    } else {
                        e.target.style.color = "green";
                        e.target.style.border = "solid 1px green";
                    }
                    
                    setTimeout(() => {
                        if (topicIsPinned) {
                            e.target.classList.remove('pinnedTopic');
                            e.target.classList.add('nonePinnedTopic');
                            e.target.title = "Épingler le sujet";
                            topicIsPinned = false;
                        } else {
                            e.target.classList.add('pinnedTopic');
                            e.target.classList.remove('nonePinnedTopic');
                            e.target.title = "Désépingler le sujet";
                            topicIsPinned = true;
                        }
                        e.target.style.color = "#CF8B3F";
                        e.target.style.border = "none";
                        addEventToggleIsPinned(elem);
                    }, 1500);
                }
            });
        }, {'once':true}
    );
}

//open modal to delete reply
if (btnDeleteReply && btnDeleteReply.length > 0) {
    btnDeleteReply.forEach(btn => {
        btn.addEventListener(
            'click', function(e) {
                let idReply = e.target.getAttribute('replyId');
                modal.style.display = "flex";
                modalDeleteReply.style.display = "flex";
                linkDeleteReply.href = "index.php?action=deleteReply&replyId=" + idReply;
            }
        );
    });
}

//cancel delete reply and close modal
btnCancelDeleteReply.addEventListener(
    'click', function() {
        modal.style.display = "none";
        modalDeleteReply.style.display = "none";
        linkDeleteReply.href = "";
    }
);

//open modal to delete topic
if (btnDeleteTopic !== null) {
    btnDeleteTopic.addEventListener(
        'click', function(e) {
            let idTopic = e.target.getAttribute('topicId');
            modal.style.display = "flex";
            modalDeleteTopic.style.display = "flex";
            linkDeleteTopic.href = "index.php?action=deleteTopic&topicId=" + idTopic;
        }
    );
}

//cancel delete topic and close modal
btnCancelDeleteTopic.addEventListener(
    'click', function() {
        modal.style.display = "none";
        modalDeleteTopic.style.display = "none";
        linkDeleteTopic.href = "";
    }
);

// toggle bool "topic is close"
if (btnToggleCloseTopic !== null) {
    if (btnToggleCloseTopic.classList.contains('fa-lock')) {
        topicIsClose = true;
    } else {
        topicIsClose = false;
    }

    addEventToggleIsClose(btnToggleCloseTopic);
}

// toggle bool "topic is pinned"
if (btnTogglePinnedTopic !== null) {
    if (btnTogglePinnedTopic.classList.contains('pinnedTopic')) {
        topicIsPinned = true;
    } else {
        topicIsPinned = false;
    }

    addEventToggleIsPinned(btnTogglePinnedTopic);
}