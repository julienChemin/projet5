let modal = document.getElementById('modal');
let modalInfo = {blockDisplay : '', label : ''};
let spanResult = '';
let previousSpanResult = '';
let timeout = null;
let form = document.querySelector('#modal > form');
let inputElem = form.elements.elem;

let pText = document.getElementById('pText');
let labelPText = document.querySelector('#pText > label');
let btnSubmit = form.elements.submit;
let btnCancel = form.elements.cancel;

let btnWhereInputTextNeeded = document.querySelectorAll("button[inputNeeded='text']");

function ucFirst(str) 
{
    if (str.length > 0) {
        return str[0].toUpperCase() + str.substring(1);
    } else {
        return str;
    } 
}

function getLabel()
{
    switch (inputElem.value) {
        case 'pseudo' :
            return 'Nouvel identifiant : ';
        case 'firstName' :
            return 'Nouveau prénom : ';
        case 'lastName' :
            return 'Nouveau nom : ';
        case 'mail' :
            return 'Nouvelle adresse mail : ';
        default :
            return 'Veuillez recharger la page';
    }
}

function getSuccesMessage()
{
    switch (inputElem.value) {
        case 'pseudo' :
            return "L'identifiant a été modifié";
        case 'firstName' :
            return 'Le prénom a été modifié';
        case 'lastName' :
            return 'Le nom a été modifié';
        case 'mail' :
            return "l'adresse mail a été modifié";
        default :
            return 'Veuillez recharger la page';
    }
}

function getFailureMessage()
{
    switch (inputElem.value) {
        case 'pseudo' :
            return "L'identifiant n'est pas disponible, ou n'est pas correcte";
        case 'firstName' :
            return "Le prénom n'a pas pu être modifié";
        case 'lastName' :
            return "Le nom n'a pas pu être modifié";
        case 'mail' :
            return "l'adresse mail est déjà associé à un compte, ou n'est pas correcte";
        default :
            return 'Veuillez recharger la page';
    }
}

function openModal(btnClicked = null, blockInputToDisplay = null, label = null)
{
    if (btnClicked !== null && blockInputToDisplay !== null && label !== null) {
        // setup modal info
        modalInfo.blockDisplay = blockInputToDisplay;
        modalInfo.label = label;
        inputElem.value = btnClicked.getAttribute('elem');
        spanResult = btnClicked.parentNode.nextElementSibling;
        // display modal
        modal.style.display = "flex";
        // setup label content
        let labelContent = getLabel();
        label.textContent = labelContent;
        // display block input
        blockInputToDisplay.style.display = "block";
    }
}

function closeModal()
{
    // init modal info
    modalInfo.blockDisplay.style.display = "none";
    modalInfo.blockDisplay = '';
    modalInfo.label.textContent = '';
    modalInfo.label = '';
    inputElem.value = '';
    // hide modal
    modal.style.display = "none";
}

/** ---------------------------------------------------- */
/** ----------- BUTTON EDIT ACCOUNT SETTINGS ----------- */
/** ---------------------------------------------------- */
btnWhereInputTextNeeded.forEach(btn => {
    btn.addEventListener(
        'click', function() {
            openModal(btn, pText, labelPText);
        }
    );
});

/** ------------------------------------------------ */
/** ----------- MODAL -> SUBMIT / CANCEL ----------- */
/** ------------------------------------------------ */
btnSubmit.addEventListener(
    'click', function(e){
        e.preventDefault();
        let data = new FormData(form);
        ajaxPost('index.php?action=updateUserInfo', data, function(response){
            // clear previous message
            if (timeout !== null) {
                clearTimeout(timeout);
                previousSpanResult.textContent = '';
            }
            // use response to display adapted message
            if (response.length > 0 && response !== "false") {
                // display success msg
                spanResult.style.color = 'green';
                spanResult.textContent = getSuccesMessage();
                spanResult.previousElementSibling.childNodes[1].textContent = form.elements.textValue.value;
            } else {
                // display failure msg
                spanResult.style.color = 'red';
                spanResult.textContent = getFailureMessage();
            }
            previousSpanResult = spanResult;
            // delete msg after 5s
            timeout = setTimeout(function(){
                spanResult.textContent = '';
            }, 5000);
            // close modal
            closeModal();
        });
    }
);

btnCancel.addEventListener(
    'click', function(e){
        closeModal();
    }
);