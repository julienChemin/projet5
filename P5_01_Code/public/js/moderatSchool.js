if (document.getElementById('blockModeratSchool') !== null) {
    let modal = document.getElementById('modal');
    let formModal = document.querySelector('#modal > form');
    
    let blockName = document.getElementById('blockName');
    let blockAdmin = document.getElementById('blockAdmin');
    let blockCode = document.getElementById('blockCode');
    let blockNbEleve = document.getElementById('blockNbEleve');
    let blockLogo = document.getElementById('blockLogo');
    let blockMail = document.getElementById('blockMail');
    let blockToActive = document.getElementById('blockToActive');
    let blockToInactive = document.getElementById('blockToInactive');

    let btnEditName = document.querySelectorAll('.btnEditName');
    let btnEditAdmin = document.querySelectorAll('.btnEditAdmin');
    let btnEditCode = document.querySelectorAll('.btnEditCode');
    let btnEditNbEleve = document.querySelectorAll('.btnEditNbEleve');
    let btnEditLogo = document.querySelectorAll('.btnEditLogo');
    let btnEditMail = document.querySelectorAll('.btnEditMail');
    let btnEditToActive = document.querySelectorAll('.btnEditToActive');
    let btnEditToInactive = document.querySelectorAll('.btnEditToInactive');

    let blockSchool = document.querySelectorAll('.blockSchool');

    if (blockSchool.length > 1) {
        //all schools are display, add event to know which school is selected
        let allSchoolName = document.querySelectorAll('.blockSchool h1');
        let allBtnDisplaySchool = document.querySelectorAll('.blockSchool i[class~="fa-caret-square-down"]');

        for (let i=0;i<blockSchool.length;i++) {
            allBtnDisplaySchool[i].addEventListener(
                'click', function () {
                    formModal.elements.schoolName.value = allSchoolName[i].textContent;
                }
            );
        }
    }

    btnEditName.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "name";
                blockName.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditAdmin.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "admin";
                blockAdmin.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditCode.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "code";
                blockCode.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditNbEleve.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "nbEleve";
                blockNbEleve.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditLogo.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "logo";
                blockLogo.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditMail.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "mail";
                blockMail.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditToActive.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "toActive";
                blockToActive.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    btnEditToInactive.forEach(btn => {
        btn.addEventListener(
            'click', function () {
                formModal.elements.elem.value = "toInactive";
                blockToInactive.style.display = "flex";
                modal.style.display = "flex";
            }
        );
    });

    formModal.elements.cancel.addEventListener(
        'click', function () {
            switch (formModal.elements.elem.value) {
                case 'name' :
                    blockName.style.display = "none";
                    break;
                case 'admin' :
                    blockAdmin.style.display = "none";
                    break;
                case 'code' :
                    blockCode.style.display = "none";
                    break;
                case 'nbEleve' :
                    blockNbEleve.style.display = "none";
                    break;
                case 'logo' :
                    blockLogo.style.display = "none";
                    break;
                case 'mail' :
                    blockMail.style.display = "none";
                    break;
                case 'toActive' :
                    blockToActive.style.display = "none";
                    break;
                case 'toInactive' :
                    blockToInactive.style.display = "none";
                    break;
            }
            modal.style.display = "none";
        }
    );
}
