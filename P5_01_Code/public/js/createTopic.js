const regexLetterNumberOnly = /^[a-z0-9]+[a-z0-9 ]*/i;
const formAddTopic = document.getElementById('addForumTopic');
const elemListToSee = formAddTopic.elements.listAuthorizedGroupsToSee;
const elemListToPost = formAddTopic.elements.listAuthorizedGroupsToPost;
const blockAuthorizedGroupsToSee = document.getElementById('blockAuthorizedGroupsToSee');
const blockAuthorizedGroupsToPost = document.getElementById('blockAuthorizedGroupsToPost');

function addEventPreventKeyEnter(inputText) {
    inputText.addEventListener(
        "focus", function(e) {
            e.target.addEventListener('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    e.target.blur();
                }
            }, true);
        }
    );

    inputText.addEventListener(
        "blur", function(e) {
            e.target.removeEventListener('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    e.target.blur();
                }
            });
        }
    );
}

function addGroupToAuthorizedList(group, type)
{
    let blockAuthorizedGroups, listAuthorizedGroups, listGroup, idSelector;

    if (type === 'see') {
        blockAuthorizedGroups = blockAuthorizedGroupsToSee;
        listAuthorizedGroups = elemListToSee;
        listGroup = formAddTopic.elements.authorizedGroupsToSee;
        idSelector = "#authorizedGroupsToSee";
    } else if (type === 'post') {
        blockAuthorizedGroups = blockAuthorizedGroupsToPost;
        listAuthorizedGroups = elemListToPost;
        listGroup = formAddTopic.elements.authorizedGroupsToPost;
        idSelector = "#authorizedGroupsToPost";
    }

    //add elem to list authorized group
    let elemSpan = document.createElement('span');
    elemSpan.textContent = group;
    elemSpan.classList.add('authorizedGroup');

    let elemI = document.createElement('i');
    elemI.classList.add('fas');
    elemI.classList.add('fa-times');
    elemI.classList.add('deleteGroup');
    elemI.addEventListener(
        'click', function () {
            //delete this group from list authorized group
            blockAuthorizedGroups.removeChild(elemSpan);
            //delete group value to input list authorized
            let arrAuthorizedGroups = listAuthorizedGroups.value.split(',');
            arrAuthorizedGroups.shift();
            arrAuthorizedGroups.splice(arrAuthorizedGroups.indexOf(group), 1);
            listAuthorizedGroups.value = "";
            for (let i=0;i<arrAuthorizedGroups.length;i++) {
                listAuthorizedGroups.value += (',' + arrAuthorizedGroups[i]);
            }
            //add this group from list group to add
            let elemOption = document.createElement('option');
            elemOption.value = group;
            elemOption.textContent = group;
            listGroup.appendChild(elemOption);
        }
    );
    elemSpan.appendChild(elemI);
    blockAuthorizedGroups.appendChild(elemSpan);
    //add group value to input list authorized
    listAuthorizedGroups.value += (',' + group);
    //remove this group from list group to add
    listGroup.removeChild(document.querySelector(idSelector + ' option[value="' + group + '"]'));
}

/* select groups */
if (formAddTopic.elements.authorizedGroupsToSee) {
    formAddTopic.elements.authorizedGroupsToSee.addEventListener(
        'change', function(e) {
            if (e.target.value === "all" || e.target.value === "none") {
                blockAuthorizedGroupsToSee.style.display = 'none';
            } else if (e.target.value === "groups") {
                blockAuthorizedGroupsToSee.style.display = 'flex';
            } else {
                blockAuthorizedGroupsToSee.style.display = 'flex';
                addGroupToAuthorizedList(e.target.value, 'see');
            }
        }
    );
}

if (formAddTopic.elements.authorizedGroupsToPost) {
    formAddTopic.elements.authorizedGroupsToPost.addEventListener(
        'change', function(e) {
            if (e.target.value === "all" || e.target.value === "none") {
                blockAuthorizedGroupsToPost.style.display = 'none';
            } else if (e.target.value === "groups") {
                blockAuthorizedGroupsToPost.style.display = 'flex';
            } else {
                blockAuthorizedGroupsToPost.style.display = 'flex';
                addGroupToAuthorizedList(e.target.value, 'post');
            }
        }
    );
}

/****************************************************************************************/
/****************************************************************************************/
/**                                   SUBMIT CHECK                                      */
/****************************************************************************************/
/****************************************************************************************/
const inputTitle = document.querySelector('#blockTitle input');
const inputSubmit = document.querySelector('#blockSubmit input');

inputSubmit.addEventListener(
    'click', function (e) {
        if (!inputTitle.value.trim()) {
            e.preventDefault();
        }
    }
);