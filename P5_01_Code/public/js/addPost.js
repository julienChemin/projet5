let formAddPost;
if (document.getElementById('addSchoolPost') !== null) {
    formAddPost = document.querySelector('#addSchoolPost > form');
} else {
    formAddPost = document.querySelector('#addPost > form');
}
let firstClickOnAddPostOrAddFolder = true;
let blockUploadType = document.getElementById('blockUploadType');
let btnAddFolder = document.getElementById('btnAddFolder');
let btnAddFile = document.getElementById('btnAddFile');
let blockAddFile = document.getElementById('blockAddFile');
let blockFileTypeSelection = document.getElementById('fileTypeSelection');
let blockTitle = document.getElementById('blockTitle');
let inputTitle = document.getElementById('title');
let blockUploadFile = document.getElementById('blockUploadFile');
let labelUploadFile = document.querySelector('label[for="uploadFile"]');
let inputUploadFile = document.querySelector('input[id="uploadFile"]');
let blockVideoLink = document.getElementById('blockVideoLink');
let inputVideoLink = document.getElementById('videoLink');
let preview = document.getElementById('preview');
let blockTinyMce = document.getElementById('blockTinyMce');
let inputSubmit = document.querySelector("input[type='submit']");

let arrGroupedTypes = [];
let blockUploadGroupedFile = document.getElementById('blockUploadGroupedFile');
let fileCountOnGrouped = document.querySelector("input[name='fileCountOnGrouped']");

function addEventPreview(inputFile, blockPreview, imgPreview) {
    inputFile.addEventListener(
        'change', function (e) {
            if (e.target.files && e.target.files[0] && btnActif.btn !== blockTypeOther) {
                let reader = new FileReader();
                reader.addEventListener(
                    'load', function(e) {
                        imgPreview.src = e.target.result;
                        blockPreview.style.display = 'flex';
                    }
                );
                reader.readAsDataURL(e.target.files[0]);
                if (preview.classList.contains('emptyPreview')) {
                    preview.classList.remove('emptyPreview');
                }
            }
        }
    );
}

function addEventVideoPreview(inputText, divId) {
    inputText.addEventListener(
        'change', function(e) {
            let videoPreview = document.querySelector('#' + divId + ' .previewVideo');
            let previewBadLinkWarning = document.querySelector('#' + divId + ' .previewBadLinkWarning');
            let regexUrl = /youtube\.com\/watch\?v\=([_a-zA-Z0-9-]+)/;

            if (regexUrl.test(e.target.value)) {
                videoUrl = regexUrl.exec(e.target.value)[1];

                if (videoPreview === null) {
                    setBlockVideoPreview(divId);
                    videoPreview = document.querySelector('#' + divId + ' .previewVideo');
                    previewBadLinkWarning = document.querySelector('#' + divId + ' .previewBadLinkWarning');
                }

                if (videoPreview.classList.contains('hide')) {
                    videoPreview.classList.remove('hide');
                    previewBadLinkWarning.classList.add('hide');
                }

                videoPreview.src = 'https://www.youtube.com/embed/' + videoUrl;
            } else if (videoPreview !== null) {
                videoPreview.classList.add('hide');
                previewBadLinkWarning.classList.remove('hide');
            }
        }
    );
}

function setBlockVideoPreview(divId) {
    let blockVideo = document.getElementById(divId);
    if (blockVideo !== null) {
        let iframe = document.createElement('iframe');
        iframe.style.height = "90%";
        iframe.classList.add('previewVideo');
        iframe.setAttribute('frameborder', "0");
        iframe.setAttribute('allow', "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture");

        let previewBadLinkWarning = document.createElement('span');
        previewBadLinkWarning.textContent = "Lien incorrect";
        previewBadLinkWarning.classList.add('previewBadLinkWarning');
        previewBadLinkWarning.classList.add('hide');

        blockVideo.appendChild(iframe);
        blockVideo.appendChild(previewBadLinkWarning);

        return iframe;
    }
}

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

function checkLastAddToGrouped() {
    if (fileCountOnGrouped.value > 0) {
        if (arrGroupedTypes[fileCountOnGrouped.value-1] !== null && arrGroupedTypes[fileCountOnGrouped.value-1] === 'video') {
            let elem = document.querySelector("#newGroupedFile" + (fileCountOnGrouped.value - 1) + " input[type='text']");
            let regexUrl = /youtube\.com\/watch\?v\=([_a-zA-Z0-9-]+)/;

            if (elem && elem.value.trim() !== "" && regexUrl.test(elem.value.trim())) {
                elem.value = regexUrl.exec(elem.value.trim())[1];
            } else {
                deleteLastAddToGrouped();
            }
        } else if (arrGroupedTypes[fileCountOnGrouped.value-1] !== null) {
            let elem = document.querySelector("#newGroupedFile" + (fileCountOnGrouped.value - 1) + " input[type='file']");

            if (!elem || elem && !elem.files[0]) {
                deleteLastAddToGrouped();
            }
        }
    }
}

function deleteLastAddToGrouped() {
    let node = document.getElementById("blockUploadGroupedFile");
    let elem = document.querySelector("#newGroupedFile" + (fileCountOnGrouped.value - 1));
    node.removeChild(elem);

    fileCountOnGrouped.value = fileCountOnGrouped.value - 1;
    arrGroupedTypes.pop();
}

window.addEventListener(
    'load', function () {
        //look if user try to post in folder
        let url = window.location.search.split('&');
        let arr = [];
        for (let i=1; i<url.length; i++) {
            let splitUrl = url[i].split('=');
            arr[splitUrl[0]] = splitUrl[1];
        }
        if (arr['folder'] !== undefined) {
            formAddPost.elements.folder.value = arr['folder'];
        }
    }
);

/****************************************************************************************/
/****************************************************************************************/
/**                                 BTN ADD FOLDER / FILE                               */
/****************************************************************************************/
/****************************************************************************************/

btnAddFolder.addEventListener(
    'click', function () {
        if (firstClickOnAddPostOrAddFolder) {
            firstClickOnAddPostOrAddFolder = false;
            blockUploadType.style.height = "100px";
            blockUploadType.style.borderBottom = "solid 1px #CF8B3F";
        }
        //display inputs add folder, hide inputs add file
        btnAddFolder.style.backgroundColor = '#222224';
        blockTitle.style.display  = "flex";
        inputTitle.placeholder = '';
        blockUploadFile.style.display = 'flex';
        labelUploadFile.textContent = "Thumbnail du dossier (jpg, png, gif) - non obligatoire - (max : 5Mo)";
        blockTinyMce.style.display = 'flex';
        inputSubmit.style.display = "inline-block";

        btnAddFile.style.backgroundColor = '#161617';
        btnAddFile.style.border = 'none';
        blockAddFile.style.display = "none";

        if (blockTags !== null) {
            blockTags.style.display = 'none';
        }

        if (btnActif.btn !== "") {
            btnActif.btn.style.backgroundColor = '#161617';
            btnActif.btn.style.border = 'none';
        }

        blockVideoLink.style.display = "none";
        btnActif.btn = "";

        //set upload type
        formAddPost.elements.fileTypeValue.value = 'folder';
    }
);

btnAddFile.addEventListener(
    'click', function () {
        if (firstClickOnAddPostOrAddFolder) {
            firstClickOnAddPostOrAddFolder = false;
            blockUploadType.style.height = "100px";
            blockUploadType.style.borderBottom = "solid 1px #CF8B3F";
        }
        //display inputs add folder, hide inputs add file
        btnAddFile.style.backgroundColor = '#222224';
        blockAddFile.style.height = "200px";
        blockAddFile.style.borderBottom = "none";
        blockAddFile.style.display = "flex";

        btnAddFolder.style.backgroundColor = '#161617';
        btnAddFolder.style.border = 'none';
        if (blockTags !== null) {
            blockTags.style.display = 'none';
        }
        if (btnActif.btn !== "") {
            btnActif.btn.style.backgroundColor = '#161617';
            btnActif.btn.style.border = 'none';
        }
        blockTitle.style.display  = "none";
        blockUploadFile.style.display = 'none';
        blockVideoLink.style.display = "none";
        blockTinyMce.style.display = 'none';
        inputSubmit.style.display = "none";
    }
);

//public / private publication
if (document.getElementById('addSchoolPost') !== null) {
    let checkboxIsPrivate = formAddPost.elements.isPrivate;
    let blockListGroup = document.querySelector('#blockIsPrivate > div > div:nth-of-type(2)');
    let listGroup = document.querySelector('#blockIsPrivate select');
    let listAuthorizedGroups = formAddPost.elements.listAuthorizedGroups;
    let blockAuthorizedGroups = document.getElementById('authorizedGroups');

    function addGroupToAuthorizedList(group)
    {
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
        listGroup.removeChild(document.querySelector('option[value="' + group + '"]'));
    }

    if (checkboxIsPrivate !== undefined) {
        //display / hide private mode
        checkboxIsPrivate.addEventListener(
            'change', function (e) {
                if (formAddPost.elements.fileTypeValue.value === 'compressed' && !checkboxIsPrivate.checked) {
                    //compressed file can't be public
                    e.preventDefault();
                    checkboxIsPrivate.checked = true;
                }
                if (!checkboxIsPrivate.checked) {
                    blockListGroup.style.display = 'none';
                    blockAuthorizedGroups.style.display = 'none';
                    formAddPost.action = 'indexAdmin.php?action=uploadSchoolPost&type=onSchoolProfile';
                    if (document.getElementById('addSchoolPost') === null 
                    && (formAddPost.elements.fileTypeValue.value === 'video' || formAddPost.elements.fileTypeValue.value === 'image')) {
                        blockTags.style.display = 'flex';
                    }
                } else {
                    blockListGroup.style.display = 'inline-block';
                    formAddPost.action = 'indexAdmin.php?action=uploadSchoolPost&type=private';
                    if (listGroup.value !== "all") {
                        blockAuthorizedGroups.style.display = 'flex';
                    }
                    if (blockTags !== null && blockTags.style.display === 'flex') {
                        blockTags.style.display = 'none';
                    }
                }
            }
        );

        //add group to list authorized group
        listGroup.addEventListener(
            'change', function (e) {
                if (e.target.value === "all") {
                    blockAuthorizedGroups.style.display = 'none';
                } else if (e.target.value === "") {
                    blockAuthorizedGroups.style.display = 'flex';
                } else {
                    blockAuthorizedGroups.style.display = 'flex';
                    addGroupToAuthorizedList(e.target.value);
                }
            }
        );
    }
}

/****************************************************************************************/
/****************************************************************************************/
/**                                   BTN TYPE FILE                                     */
/****************************************************************************************/
/****************************************************************************************/

let blockTypeImg = document.querySelector('#fileTypeSelection > figure:first-of-type');
let btnImg = document.getElementById('typeImage');

let blockTypeVideo = document.querySelector('#fileTypeSelection > figure:nth-of-type(2)');
let btnVideo = document.getElementById('typeVideo');

let blockTypeGrouped = document.querySelector('#fileTypeSelection > figure:nth-of-type(3)');
let btnGrouped = document.getElementById('typeGrouped');
let btnAddImgToGrouped = document.getElementById('groupedTypeImage');
let btnAddVideoToGrouped = document.getElementById('groupedTypeVideo');
let btnAddOtherToGrouped = document.getElementById('groupedTypeOther');

let blockTypeOther = document.querySelector('#fileTypeSelection > figure:nth-of-type(4)');
let btnOther = document.getElementById('typeOther');

let btnActif = {btn:""};
let inputFile = document.querySelector('#blockUploadFile input[type="file"]');
let blockTags = document.getElementById('blockTags');

btnImg.addEventListener(
    'click', function () {
        //scale block fileType
        blockAddFile.style.height = "100px";
        blockAddFile.style.borderBottom = "solid 1px #CF8B3F";
        //display block add image
        blockTitle.style.display  = "flex";
        inputTitle.placeholder = 'Champ non obligatoire';
        blockUploadFile.style.display = 'flex';
        labelUploadFile.textContent = "Fichier jpg, png, gif - (max : 5Mo)";
        inputUploadFile.setAttribute("accept", ".jpeg, .jpg, .jfif, .png, .gif");
        blockTinyMce.style.display = 'flex';
        if (blockTags !== null) {
            blockTags.style.display = 'flex';
        }
        if (!preview.classList.contains('emptyPreview')) {
            preview.style.display = 'flex';
        }
        inputSubmit.style.display = "inline-block";
        formAddPost.elements.fileTypeValue.value = 'image';
        blockUploadGroupedFile.style.display = 'none';
        //set focus
        blockTypeImg.style.backgroundColor = '#222224';
        if (btnActif.btn !== "" && btnActif.btn !== blockTypeImg) {
            //unset previous focus
            btnActif.btn.style.backgroundColor = '#161617';
            btnActif.btn.style.border = 'none';
        }
        blockVideoLink.style.display = "none";
        btnActif.btn = blockTypeImg;
    }
);

addEventPreventKeyEnter(inputVideoLink);
addEventVideoPreview(inputVideoLink, 'blockVideoLink');

btnVideo.addEventListener(
    'click', function () {
        //scale block fileType
        blockAddFile.style.height = "100px";
        blockAddFile.style.borderBottom = "solid 1px #CF8B3F";
        //display block add video
        blockTitle.style.display  = "flex";
        inputTitle.placeholder = 'Champ non obligatoire';
        blockUploadFile.style.display = 'flex';
        labelUploadFile.textContent = "Thumbnail de la vidéo (jpg, png, gif - non obligatoire - max : 5Mo)";
        inputUploadFile.setAttribute("accept", ".jpeg, .jpg, .jfif, .png, .gif");
        blockTinyMce.style.display = 'flex';
        if (blockTags !== null) {
            blockTags.style.display = 'flex';
        }
        if (!preview.classList.contains('emptyPreview')) {
            preview.style.display = 'flex';
        }
        inputSubmit.style.display = "inline-block";
        blockVideoLink.style.display = "flex";
        formAddPost.elements.fileTypeValue.value = 'video';
        blockUploadGroupedFile.style.display = 'none';
        //set focus
        blockTypeVideo.style.backgroundColor = '#222224';
        if (btnActif.btn !== "" && btnActif.btn !== blockTypeVideo) {
            //unset previous focus
            btnActif.btn.style.backgroundColor = '#161617';
            btnActif.btn.style.border = 'none';
        }
        btnActif.btn = blockTypeVideo;
    }
);

btnGrouped.addEventListener(
    'click', function () {
        //scale block fileType
        blockAddFile.style.height = "100px";
        blockAddFile.style.borderBottom = "solid 1px #CF8B3F";
        //display block add Grouped file
        blockTitle.style.display  = "flex";
        inputTitle.placeholder = 'Champ non obligatoire';
        blockUploadGroupedFile.style.display = 'flex';
        blockTinyMce.style.display = 'flex';
        if (blockTags !== null) {
            blockTags.style.display = 'flex';
        }
        if (!preview.classList.contains('emptyPreview')) {
            preview.style.display = 'flex';
        }
        inputSubmit.style.display = "inline-block";
        formAddPost.elements.fileTypeValue.value = 'grouped';
        blockUploadFile.style.display = 'flex';
        labelUploadFile.textContent = "Thumbnail de la publication (jpg, png, gif - max : 5Mo)";
        inputUploadFile.setAttribute("accept", ".jpeg, .jpg, .jfif, .png, .gif");
        blockVideoLink.style.display = "none";
        //set focus
        blockTypeGrouped.style.backgroundColor = '#222224';
        if (btnActif.btn !== "" && btnActif.btn !== blockTypeGrouped) {
            //unset previous focus
            btnActif.btn.style.backgroundColor = '#161617';
            btnActif.btn.style.border = 'none';
        }
        btnActif.btn = blockTypeGrouped;
    }
);

if (btnOther !== null) {
    btnOther.addEventListener(
        'click', function () {
            //scale block fileType
            blockAddFile.style.height = "100px";
            blockAddFile.style.borderBottom = "solid 1px #CF8B3F";
            //display block add other
            if (formAddPost.elements.isPrivate !== undefined && !formAddPost.elements.isPrivate.checked) {
                formAddPost.elements.isPrivate.click();
            }
            blockTitle.style.display  = "flex";
            inputTitle.placeholder = '';
            blockUploadFile.style.display = 'flex';
            labelUploadFile.innerHTML = "Fichier zip, rar - (max : 5Mo)";
            inputUploadFile.setAttribute('accept', '.zip,.rar,.7zip,.7z');
            preview.style.display = 'none';
            blockUploadGroupedFile.style.display = 'none';
            blockTinyMce.style.display = 'flex';
            if (blockTags !== null) {
                blockTags.style.display = 'none';
            }
            inputSubmit.style.display = "inline-block";
            formAddPost.elements.fileTypeValue.value = 'compressed';
            //set focus
            blockTypeOther.style.backgroundColor = '#222224';
            if (btnActif.btn !== "" && btnActif.btn !== blockTypeOther) {
                //unset previous focus
                btnActif.btn.style.backgroundColor = '#161617';
                btnActif.btn.style.border = 'none';
            }
            blockVideoLink.style.display = "none";
            btnActif.btn = blockTypeOther;
        }
    );
}

//set preview
addEventPreview(inputFile, preview, document.querySelector('.preview img'));

/****************************************************************************************/
/****************************************************************************************/
/**                              BTN ADD FILE TO GROUPED                                */
/****************************************************************************************/
/****************************************************************************************/

btnAddImgToGrouped.addEventListener(
    'click', function() {
        checkLastAddToGrouped();
        
        let divContent = document.createElement('div');
        divContent.classList.add('newGroupedFile');
        divContent.classList.add('newGroupedFileImg');
        divContent.id = "newGroupedFile" + fileCountOnGrouped.value;

            let divInput = document.createElement('div');

                let label = document.createElement('label');
                label.setAttribute('for', 'uploadFile' + fileCountOnGrouped.value);
                label.textContent = "Fichier jpg, png, gif - (max : 5Mo)";
                divInput.appendChild(label);

                let inputHidden = document.createElement('input');
                inputHidden.setAttribute("type", "hidden");
                inputHidden.setAttribute("name", "MAX_FILE_SIZE");
                inputHidden.value = "6000000";
                divInput.appendChild(inputHidden);

                let inputFile = document.createElement('input');
                inputFile.setAttribute("type", "file");
                inputFile.setAttribute("name", 'uploadFile' + fileCountOnGrouped.value);
                inputFile.setAttribute("accept", ".jpeg, .jpg, .jfif, .png, .gif");
                inputFile.id = 'uploadFile' + fileCountOnGrouped.value;
                divInput.appendChild(inputFile);

            let figurePreview = document.createElement('figure');
            figurePreview.classList.add('preview');

                let imgPreview = document.createElement('img');
                imgPreview.title = "Preview";
                imgPreview.alt = "Aperçu";
                figurePreview.appendChild(imgPreview);

                
        divContent.appendChild(document.createElement('hr'));
        divContent.appendChild(divInput);
        divContent.appendChild(figurePreview);
        blockUploadGroupedFile.insertBefore(divContent, blockUploadGroupedFile.childNodes[fileCountOnGrouped.value]);

        addEventPreview(inputFile, figurePreview, imgPreview);
        arrGroupedTypes.push('image');
        fileCountOnGrouped.value++;
    }
);

btnAddVideoToGrouped.addEventListener(
    'click', function() {
        checkLastAddToGrouped();
        
        let divContent = document.createElement('div');
        divContent.classList.add('newGroupedFile');
        divContent.classList.add('newGroupedFileVideo');
        divContent.id = "newGroupedFile" + fileCountOnGrouped.value;

            let divInput = document.createElement('div');

                let h2 = document.createElement('h2');
                h2.textContent = "Adresse de la vidéo youtube";

                let inputText = document.createElement('input');
                inputText.setAttribute("type", "text");
                inputText.setAttribute("name", 'uploadFile' + fileCountOnGrouped.value);
                addEventPreventKeyEnter(inputText);
                addEventVideoPreview(inputText, divContent.id);
                divInput.appendChild(h2);
                divInput.appendChild(inputText);

        divContent.appendChild(document.createElement('hr'));
        divContent.appendChild(divInput);
        blockUploadGroupedFile.insertBefore(divContent, blockUploadGroupedFile.childNodes[fileCountOnGrouped.value]);

        arrGroupedTypes.push('video');
        fileCountOnGrouped.value++;
    }
);

if (btnAddOtherToGrouped !== null) {
    btnAddOtherToGrouped.addEventListener(
        'click', function() {
            checkLastAddToGrouped();
            let divContent = document.createElement('div');
            divContent.classList.add('newGroupedFile');
            divContent.classList.add('newGroupedFileOther');
            divContent.id = "newGroupedFile" + fileCountOnGrouped.value;

                let divInput = document.createElement('div');

                    let label = document.createElement('label');
                    label.setAttribute('for', 'uploadFile' + fileCountOnGrouped.value);
                    label.textContent = "Fichier zip, rar - (max : 5Mo)";
                    divInput.appendChild(label);

                    let inputHidden = document.createElement('input');
                    inputHidden.setAttribute("type", "hidden");
                    inputHidden.setAttribute("name", "MAX_FILE_SIZE");
                    inputHidden.value = "6000000";
                    divInput.appendChild(inputHidden);

                    let inputFile = document.createElement('input');
                    inputFile.setAttribute("type", "file");
                    inputFile.setAttribute("name", 'uploadFile' + fileCountOnGrouped.value);
                    inputFile.setAttribute("accept", ".zip,.rar,.7zip,.7z");
                    inputFile.id = 'uploadFile' + fileCountOnGrouped.value;
                    divInput.appendChild(inputFile);
                    
            divContent.appendChild(document.createElement('hr'));
            divContent.appendChild(divInput);
            blockUploadGroupedFile.insertBefore(divContent, blockUploadGroupedFile.childNodes[fileCountOnGrouped.value]);

            arrGroupedTypes.push('compressed');
            fileCountOnGrouped.value++;
        }
    );
}

/****************************************************************************************/
/****************************************************************************************/
/**                                      TAGS STUFF                                     */
/****************************************************************************************/
/****************************************************************************************/

let errorMsg = document.getElementById('errorMsg');
let blockRecommendedTags = document.getElementById('recommendedTags');
let blockSelectedTags = document.getElementById('selectedTags');
let divRecommendedTags = document.querySelector('#recommendedTags div');
let divSelectedTags = document.querySelector('#selectedTags div');
let regexTag = /^[a-z0-9]+[a-z0-9 ]*[a-z0-9]+$/i;
let inputTag = document.getElementById('tags');
let inputListTags = document.getElementById('listTags');
let btnAddTag = document.querySelector('#tags + button');
let textTagRules = document.querySelector('.tagRules');
let existingTags;
window.addEventListener(
    'load', function () {
        ajaxGet( 'index.php?action=getTags', function (response) {
            if (response.length > 0) {
                existingTags = JSON.parse(response);
            }
        });
    }
);

function blink(elem, border = false)
{
    elem.style.color = 'red';
    inputTag.style.border = 'solid 2px red';
    border ? (elem.style.border = 'solid 2px red') : border = false;
    setTimeout(function() {
		elem.style.color = 'white';
		border ? (elem.style.border = 'solid 2px #CF8B3F') : border = false;
		setTimeout(function() {
			elem.style.color = 'red';
			border ? (elem.style.border = 'solid 2px red') : border = false;
			setTimeout(function() {
				elem.style.color = 'white';
				inputTag.style.border = 'none';
				border ? (elem.style.border = 'solid 2px #CF8B3F') : border = false;
			}, 2000);
		}, 150);
	}, 150);
}
function deleteSpace(str)
{
    let arr = str.split(" ");
    let string = "";
    arr.forEach(char => {
        string += char;
    });
    return string;
}
function tagIsValid(tag)
{
    if (regexTag.test(tag) && tag.length <= 30) {
        return true;
    } else {
        return false;
    }
}
function createTag(tagValue, selected = false)
{
    if (selected) {
        blockSelectedTags.style.display = 'block';
        let valueForListTags = ',' + tagValue;
        inputListTags.value += valueForListTags;

        let tag = document.createElement('span');
        tag.textContent = tagValue;
        tag.classList.add('tag');
        tag.id = 'tag' + tagValue[0].toUpperCase() + deleteSpace(tagValue.substring(1));

        let cross = document.createElement('i');
        cross.classList.add('fas');
        cross.classList.add('fa-times');
        tag.appendChild(cross);

        tag.addEventListener(
            'click', function () {
                inputListTags.value = inputListTags.value.replace(valueForListTags, "");
                divSelectedTags.removeChild(tag);
                if (inputListTags.value === "") {
                    blockSelectedTags.style.display = 'none';
                }
            }
        );
        divSelectedTags.appendChild(tag);
    } else {
        let tag = document.createElement('span');
        tag.textContent = tagValue;
        tag.classList.add('tag');
        tag.id = 'tag' + tagValue[0].toUpperCase() + deleteSpace(tagValue.substring(1));

        tag.addEventListener(
            'click', function () {
                divRecommendedTags.removeChild(tag);
                createTag(tagValue, true);
            }
        );
        divRecommendedTags.appendChild(tag);
    }
}

function setErrorMsg(msg)
{
    errorMsg.style.opacity = '1';
    errorMsg.textContent = msg;
    setTimeout(function () {
        errorMsg.style.opacity = '0';
    }, 5000);
}

//button add tag
if (btnAddTag !== null) {
    btnAddTag.addEventListener(
        'click', function (e) {
            e.preventDefault();
            if (tagIsValid(inputTag.value)) {
                let tagId = 'tag' + inputTag.value[0].toUpperCase() + deleteSpace(inputTag.value.substring(1));
                if (document.getElementById(tagId) === null) {
                    createTag(inputTag.value, true);
                    inputTag.value = "";
                } else {
                    blink(document.getElementById(tagId), true);
                }
            } else {
                blink(textTagRules);
            }
        }
    );
}

//recommended tags
if (inputTag !== null) {
    inputTag.addEventListener(
        'input', function () {
            divRecommendedTags.innerHTML = "";
            if (inputTag.value.length <= 1) {
                blockRecommendedTags.style.display = "none";
            } else if (inputTag.value.length > 1) {
                existingTags.forEach(tag => {
                    if (tag.toLowerCase().indexOf(inputTag.value.toLowerCase()) !== -1) {
                        if (document.getElementById('tag' + tag[0].toUpperCase() + deleteSpace(tag.substring(1))) === null) {
                            blockRecommendedTags.style.display = "block";
                            createTag(tag);
                        }
                    }
                });
            }
            if (divRecommendedTags.innerHTML === "") {
                blockRecommendedTags.style.display = "none";
            }
        }
    );
}

/****************************************************************************************/
/****************************************************************************************/
/**                                   SUBMIT CHECK                                      */
/****************************************************************************************/
/****************************************************************************************/

inputSubmit.addEventListener(
    'click', function (e) {
        switch (formAddPost.elements.fileTypeValue.value) {
            case 'image' :
                if (inputListTags !== null && inputListTags.value === "") {
                    //tag is only for referenced post
                    e.preventDefault();
                    setErrorMsg('Vous devez choisir au moins un tag');
                }

                if (!inputFile.files.length > 0) {
                    e.preventDefault();
                    setErrorMsg('Vous devez sélectionner une image a télécharger');
                }

                if (formAddPost.elements.title.value !== "" && formAddPost.elements.title.value.length > 30) {
                    e.preventDefault();
                    setErrorMsg('Le titre ne peut pas comporter plus de 30 caractères');
                }
            break;

            case 'video' :
                if (inputListTags !== null && inputListTags.value === "") {
                    //tag is only for referenced post
                    e.preventDefault();
                    setErrorMsg('Vous devez choisir au moins un tag');
                }

                let regexUrl = /youtube\.com\/watch\?v\=([_a-zA-Z0-9-]+)/;
                if (formAddPost.elements.videoLink.value === "" || !regexUrl.test(formAddPost.elements.videoLink.value)) {
                    e.preventDefault();
                    setErrorMsg('Vous devez ajouter une adresse youtube pour la vidéo');
                } else {
                    //set url video
                    formAddPost.elements.videoLink.value = regexUrl.exec(formAddPost.elements.videoLink.value)[1];
                }

                if (formAddPost.elements.title.value !== "" && formAddPost.elements.title.value.length > 30) {
                    e.preventDefault();
                    setErrorMsg('Le titre ne peut pas comporter plus de 30 caractères');
                }
            break;

            case 'compressed' :
                if (!inputFile.files.length > 0) {
                    e.preventDefault();
                    setErrorMsg('Vous devez sélectionner une fichier a télécharger');
                }

                if (formAddPost.elements.title.value === "" || formAddPost.elements.title.value.length > 30) {
                    e.preventDefault();
                    setErrorMsg('Vous devez ajouter un titre (30 caractères max.)');
                }
            break;

            case 'grouped' :
                if (inputListTags !== null && inputListTags.value === "") {
                    //tag is only for referenced post
                    e.preventDefault();
                    setErrorMsg('Vous devez choisir au moins un tag');
                }

                if (!inputFile.files.length > 0) {
                    e.preventDefault();
                    setErrorMsg('Vous devez sélectionner une image a télécharger');
                }

                if (formAddPost.elements.title.value !== "" && formAddPost.elements.title.value.length > 30) {
                    e.preventDefault();
                    setErrorMsg('Le titre ne peut pas comporter plus de 30 caractères');
                }

                checkLastAddToGrouped();
                if (arrGroupedTypes.length > 0) {
                    let listTypeGroupedFile = document.querySelector("input[name='listTypeGroupedFile']");

                    for (let i = 0; i < arrGroupedTypes.length; i++) {
                        listTypeGroupedFile.value += ',' + arrGroupedTypes[i];
                    }
                } else {
                    e.preventDefault();
                    setErrorMsg('Une publication groupé doit contenir au moins un élément en plus de la thumbnail');
                }
            break;

            case 'folder' :
                if (formAddPost.elements.title.value === "" || formAddPost.elements.title.value.length > 30) {
                    e.preventDefault();
                    setErrorMsg('Vous devez ajouter un titre (30 caractères max.)');
                }
            break;
        }
    }
);
