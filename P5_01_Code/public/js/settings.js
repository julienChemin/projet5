let modal = document.getElementById('modal');
let modalInfo = {blockDisplay : '', label : ''};
let spanResult = '';
let previousSpanResult = '';
let successMsg = '';
let failureMsg = '';
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

function getLabel(elem = null)
{
    let elemValue = '';
    elem === null ? elemValue = inputElem.value : elemValue = elem;
    switch (elemValue) {
        case 'pseudo' :
            return 'Nouvel identifiant : ';
        case 'firstName' :
            return 'Nouveau prénom : ';
        case 'lastName' :
            return 'Nouveau nom : ';
        case 'mail' :
            return 'Nouvelle adresse mail : ';
        case 'leaveSchool' :
            return "Êtes-vous sûr de vouloir quitter votre établissement scolaire ? Entrez le nom de votre établissement scolaire pour confirmer";
        case 'joinSchool' :
            return "Entrez le code fournit par votre établissement scolaire";
        default :
            return 'Veuillez recharger la page';
    }
}

function getSuccesMessage(elem = null)
{
    let elemValue = '';
    elem === null ? elemValue = inputElem.value : elemValue = elem;
    switch (elemValue) {
        case 'pseudo' :
            return "L'identifiant a été modifié";
        case 'firstName' :
            return 'Le prénom a été modifié';
        case 'lastName' :
            return 'Le nom a été modifié';
        case 'mail' :
            return "l'adresse mail a été modifié";
        case 'leaveSchool' :
            return "Vous avez quitté votre établissement scolaire";
        case 'joinSchool' :
            return "Vous avez rejoint l'établissement scolaire";
        default :
            return 'Veuillez recharger la page';
    }
}

function getFailureMessage(elem = null)
{
    let elemValue = '';
    elem === null ? elemValue = inputElem.value : elemValue = elem;
    switch (elemValue) {
        case 'pseudo' :
            return "L'identifiant n'est pas disponible, ou n'est pas correcte";
        case 'firstName' :
            return "Le prénom n'a pas pu être modifié";
        case 'lastName' :
            return "Le nom n'a pas pu être modifié";
        case 'mail' :
            return "l'adresse mail est déjà associé à un compte, ou n'est pas correcte";
        case 'leaveSchool' :
            return "Certaines informations sont incorrectes, vous n'avez pas pu quitter votre établissement scolaire";
        case 'joinSchool' :
            return "Le code est incorrecte, ou l'établissement désigné n'a plus de places";
        default :
            return 'Veuillez recharger la page';
    }
}

function checkInputElemTogglableValue()
{
    if (inputElem.value === 'joinSchool' || inputElem.value === 'leaveSchool') {
        inputElem.value = 'school';
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
        // setup label content and get success/failure msg
        label.textContent = getLabel();
        successMsg = getSuccesMessage();
        failureMsg = getFailureMessage();
        // special treatment for some value which is togglable and have to call the same function
        checkInputElemTogglableValue();
        // display modal
        modal.style.display = "flex";
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
    form.elements.textValue.value = '';
    // hide modal
    modal.style.display = "none";
}

function frontEditing(elem = null)
{
    // editing user information on screen depending on which elem has been edited
    if (elem !== null) {
        if (elem === 'pseudo' || elem === 'firstName' || elem === 'lastName' || elem === 'mail') {
            spanResult.previousElementSibling.childNodes[1].textContent = form.elements.textValue.value;
        } else if (elem === 'school') {
            window.location.reload();
        }
    }
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
                spanResult.textContent = successMsg;
                frontEditing(inputElem.value);
            } else {
                // display failure msg
                spanResult.style.color = 'red';
                spanResult.textContent = failureMsg;
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
