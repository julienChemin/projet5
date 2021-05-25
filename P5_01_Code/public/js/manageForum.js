const regexLetterNumberOnly = /^[a-z0-9]+[a-z0-9 ]*/i;
const btnAddCategory = document.querySelector('#addNewCategory > h2 .iconeEdit');
const formAddCategory = document.getElementById('addForumCategory');
const elemListToSee = formAddCategory.elements.listAuthorizedGroupsToSee;
const elemListToPost = formAddCategory.elements.listAuthorizedGroupsToPost;
const blockAuthorizedGroupsToSee = document.getElementById('blockAuthorizedGroupsToSee');
const blockAuthorizedGroupsToPost = document.getElementById('blockAuthorizedGroupsToPost');
const btnSubmitNewCategory = document.querySelector('#addForumCategory input[type="button"]');
const btnCancelAddCategory = document.getElementById('cancelAddCategory');
const inputNewCategoryName = document.querySelector('#addForumCategory input[type="text"]');
const inputNewCategoryDescription = document.querySelector('#addForumCategory textarea');
const blockCategories = document.getElementById("forumCategories");
const addNewCategoryCheck = document.getElementById("addNewCategoryCheck");

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

function addElemCategory(name, idCategory, description = null) {
    let elemContainer = document.createElement('section');
    elemContainer.classList.add('container');
    elemContainer.classList.add('forumCategory');
    elemContainer.setAttribute('style', 'order: ' + (blockCategories.children.length) + ';');
    
        let elemHeader = document.createElement('header');

            let divChangOrder = document.createElement('div');
            divChangOrder.classList.add('changCategoryOrder');

                let chevronUp = document.createElement('i');
                chevronUp.classList.add('fas');
                chevronUp.classList.add('fa-chevron-up');
                addEventCategoryUp(chevronUp);

                let chevronDown = document.createElement('i');
                chevronDown.classList.add('fas');
                chevronDown.classList.add('fa-chevron-down');
                addEventCategoryDown(chevronDown);

                divChangOrder.appendChild(chevronUp);
                divChangOrder.appendChild(chevronDown);

            let divContent = document.createElement('div');

                let elemH2 = document.createElement('h2');
                elemH2.textContent = name;
                divContent.appendChild(elemH2);

                if (description !== null) {
                    let elemDescription = document.createElement('p');
                    elemDescription.classList.add('categoryDescription');
                    elemDescription.textContent = description;
                    divContent.appendChild(elemDescription);
                }

            let divDeleteCategory = document.createElement('div');
            divDeleteCategory.classList.add('deleteCategory');

                let elemPencil = document.createElement('i');
                elemPencil.classList.add('fas');
                elemPencil.classList.add('fa-pencil-alt');
                elemPencil.setAttribute('categoryId', idCategory);
                addEventEditNewCategory(elemPencil);

                let elemTrash = document.createElement('i');
                elemTrash.classList.add('fas');
                elemTrash.classList.add('fa-trash');
                elemTrash.setAttribute('categoryId', idCategory);
                addEventDeleteCategory(elemTrash);

                divDeleteCategory.appendChild(elemPencil);
                divDeleteCategory.appendChild(elemTrash);

            elemHeader.appendChild(divChangOrder);
            elemHeader.appendChild(divContent);
            elemHeader.appendChild(divDeleteCategory);

        let divTopics = document.createElement('div');
        divTopics.classList.add('container');
        divTopics.classList.add('blockStyleOne');

            let elemSpan = document.createElement('span');
            elemSpan.textContent = "Il n'y a aucun sujet d'ouvert pour l'instant";
            divTopics.appendChild(elemSpan);

        elemContainer.appendChild(elemHeader);
        elemContainer.appendChild(divTopics);
    
    blockCategories.append(elemContainer);
}

function addGroupToAuthorizedList(group, type)
{
    let blockAuthorizedGroups, listAuthorizedGroups, listGroup, idSelector;

    if (type === 'see') {
        blockAuthorizedGroups = blockAuthorizedGroupsToSee;
        listAuthorizedGroups = elemListToSee;
        listGroup = formAddCategory.elements.authorizedGroupsToSee;
        idSelector = "#authorizedGroupsToSee";
    } else if (type === 'post') {
        blockAuthorizedGroups = blockAuthorizedGroupsToPost;
        listAuthorizedGroups = elemListToPost;
        listGroup = formAddCategory.elements.authorizedGroupsToPost;
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

/******************** */
/** add new category */
/****************** */

addEventPreventKeyEnter(inputNewCategoryName);

btnAddCategory.addEventListener(
    'click', function() {
        formAddCategory.style.display = "flex";
        btnAddCategory.style.display = "none";
    }
);

btnSubmitNewCategory.addEventListener(
    'click', function(e) {
        e.preventDefault();
        let name = inputNewCategoryName.value.trim();
        let description = inputNewCategoryDescription.value.trim();

        if (regexLetterNumberOnly.test(name) && (description === "" || regexLetterNumberOnly.test(description))) {
            let url = "indexAdmin.php?action=setNewCategory";
            let data = new FormData(formAddCategory);

            ajaxPost(url, data, function(response) {
                if (response.length > 0 && response !== "false" && parseInt(response) > 0) {
                    addElemCategory(inputNewCategoryName.value.trim(), parseInt(response), inputNewCategoryDescription.value.trim());
                    formAddCategory.style.display = "none";
                    addNewCategoryCheck.style.display = "block";
                    inputNewCategoryName.value = "";
                    inputNewCategoryDescription.value = "";
                    setTimeout(function() {
                        addNewCategoryCheck.style.display = "none";
                        btnAddCategory.style.display = "inline-block";
                    }, 2000);
                }
            });
        }
    }
);

btnCancelAddCategory.addEventListener(
    'click', function() {
        formAddCategory.style.display = "none";
        btnAddCategory.style.display = "inline-block";
        elemListToSee.value = "";
        elemListToPost.value = "";
    }
)

/* select groups */
formAddCategory.elements.authorizedGroupsToSee.addEventListener(
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

formAddCategory.elements.authorizedGroupsToPost.addEventListener(
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

/************************ */
/** chang category order */
/********************** */

const schoolName = document.getElementById('manageForum').getAttribute('schoolName');
const categories = document.querySelectorAll('.forumCategory');

let arrayBtnOrderUp = document.querySelectorAll('.changCategoryOrder i[class~="fa-chevron-down"]');
let arrayBtnOrderDown = document.querySelectorAll('.changCategoryOrder i[class~="fa-chevron-up"]');
let order = "";
let previousCategory = "";
let currentCategory = "";
let nextCategory = "";
let urlChangOrder = "";

function addEventCategoryUp(elem) {
    elem.addEventListener(
        'click', function(e) {
            order = e.target.parentNode.parentNode.parentNode.style.order;
            currentCategory = document.querySelector('#forumCategories > section[style="order: ' + order + ';"]');
            nextCategory = document.querySelector('#forumCategories > section[style="order: ' + (parseInt(order)+1) + ';"]');
            urlChangOrder = 'indexAdmin.php?action=changCategoryOrder&value=up&schoolName=' + schoolName + '&currentOrder=' + order;

            if (currentCategory !== null && nextCategory !== null) {
                ajaxGet(urlChangOrder, function(response) {
                    if (response.length > 0 && response !== 'false') {
                        currentCategory.style.order = (parseInt(order)+1);
                        nextCategory.style.order = order;
                    }
                });
            }
        }
    );
}

function addEventCategoryDown(elem) {
    elem.addEventListener(
        'click', function(e) {
            order = e.target.parentNode.parentNode.parentNode.style.order;
            currentCategory = document.querySelector('#forumCategories > section[style="order: ' + order + ';"]');
            previousCategory = document.querySelector('#forumCategories > section[style="order: ' + (parseInt(order)-1) + ';"]');
            urlChangOrder = 'indexAdmin.php?action=changCategoryOrder&value=down&schoolName=' + schoolName + '&currentOrder=' + order;

            if (currentCategory !== null && previousCategory !== null) {
                ajaxGet(urlChangOrder, function(response) {
                    if (response.length > 0 && response !== 'false') {
                        currentCategory.style.order = (parseInt(order)-1);
                        previousCategory.style.order = order;
                    }
                });
            }
        }
    );
}

if (arrayBtnOrderUp.length > 0) {
    arrayBtnOrderUp.forEach(btn => {
        addEventCategoryUp(btn);
    });
}

if (arrayBtnOrderDown.length > 0) {
    arrayBtnOrderDown.forEach(btn => {
        addEventCategoryDown(btn);
    });
}

/******************* */
/** delete category */
/*******************/

const arrayBtnDeleteCategory = document.querySelectorAll('.deleteCategory i[class~="fa-trash"]');
const modal = document.getElementById('modal');
const blockConfirmDelete = document.getElementById('confirmDeleteCategory');
const elemLinkDelete = document.querySelector('#confirmDeleteCategory a');
const btnCancelDeleteCategory = document.getElementById('closeModalDelete');

function addEventDeleteCategory(btn) {
    btn.addEventListener(
        'click', function() {
            elemLinkDelete.href = "indexAdmin.php?action=deleteCategory&idCategory=" + btn.getAttribute('categoryId');
            modal.style.display = "flex";
            blockConfirmDelete.style.display = "flex";
        }
    );
}

if (arrayBtnDeleteCategory !== null && arrayBtnDeleteCategory.length > 0) {
    arrayBtnDeleteCategory.forEach(btn => {
        addEventDeleteCategory(btn);
    });
}

if (btnCancelDeleteCategory !== null) {
    btnCancelDeleteCategory.addEventListener(
        'click', function() {
            elemLinkDelete.href = "";
            blockConfirmDelete.style.display = "none";
            modal.style.display = "none";
        }
    );
}

/***************** */
/** edit category */
/*****************/

const arrayBtnEditCategory = document.querySelectorAll('.deleteCategory i[class~="fa-pencil-alt"]');
const formEditCategory = document.getElementById('formEditCategory');
const blockConfirmEdit = document.getElementById('confirmEditCategory');
const btnCancelEditCategory = document.getElementById('closeModalEdit');
const btnConfirmEditCategory = document.getElementById('btnConfirmEdit');

function addEventEditCategory(btn) {
    btn.addEventListener(
        'click', function() {
            let blockCategory = btn.parentNode.parentNode.childNodes[3];
            formEditCategory.elements.title.value = blockCategory.childNodes[1].textContent;
            formEditCategory.elements.content.value = blockCategory.childNodes[3].textContent;
            formEditCategory.elements.idCategory.value = btn.getAttribute('categoryId');
            modal.style.display = "flex";
            blockConfirmEdit.style.display = "flex";
        }
    );
}

function addEventEditNewCategory(btn) {
    btn.addEventListener(
        'click', function() {
            let blockCategory = btn.parentNode.parentNode.childNodes[1];
            formEditCategory.elements.title.value = blockCategory.childNodes[0].textContent;
            formEditCategory.elements.content.value = blockCategory.childNodes[1].textContent;
            formEditCategory.elements.idCategory.value = btn.getAttribute('categoryId');
            modal.style.display = "flex";
            blockConfirmEdit.style.display = "flex";
        }
    );
}

if (arrayBtnEditCategory !== null && arrayBtnEditCategory.length > 0) {
    arrayBtnEditCategory.forEach(btn => {
        addEventEditCategory(btn);
    });
}

btnCancelEditCategory.addEventListener(
    'click', function() {
        formEditCategory.elements.idCategory.value = "";
        formEditCategory.elements.title.value = "";
        formEditCategory.elements.content.value = "";
        blockConfirmEdit.style.display = "none";
        modal.style.display = "none";
    }
);

btnConfirmEditCategory.addEventListener(
    'click', function() {
        if (formEditCategory.elements.title.value.trim() !== "") {
            formEditCategory.submit();
        }
    }
);