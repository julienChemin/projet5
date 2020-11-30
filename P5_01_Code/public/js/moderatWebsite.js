function ucFirst(str) 
{
    if (str.length > 0) {
        return str[0].toUpperCase() + str.substring(1);
    } else {
        return str;
    } 
}

function getDateWithoutTime(date) 
{
    let dateWithoutTime = new Date(date.setHours(0, 0, 0, 0));
    return dateWithoutTime;
}

function getDateCheck(type) 
{
    type = ucFirst(type);
    return new Date(localStorage.getItem('year' + type + 'Check'), localStorage.getItem('month' + type + 'Check'), localStorage.getItem('day' + type + 'Check'));
}

function setDateCheck(type, date)
{
    type = ucFirst(type);
    localStorage.setItem('year' + type + 'Check', date.getFullYear());
    localStorage.setItem('month' + type + 'Check', date.getMonth());
    localStorage.setItem('day' + type + 'Check', date.getDate());
}

function setMsgBox(msg, block, temporary = false)
{
    if (document.querySelector('.temporaryMsg') !== null) {
        document.querySelector('.temporaryMsg').parentNode.removeChild(document.querySelector('.temporaryMsg'));
    }
    let elemP = document.createElement('p');
    elemP.textContent = msg;
    if (temporary) {
        elemP.classList.add('temporaryMsg');
    }
    block.appendChild(elemP);
}

function btnCheckContract(type) {
    setMsgBox('traitement en cours..', msgBoxContract, true);
    ajaxGet('indexAdmin.php?action=checkContract&type=' + type, function(response) {
        if (response.length > 0 && response !== 'false') {
            response = JSON.parse(response);
            setMsgBox('- ' + response['nbRemind'] + ' contrat(s) ont été traitée(s). ' + 
                response['nbRemindDone'] + ' rappel(s) ont été envoyé(s)', msgBoxContract);
            setDateCheck(type + 'Contract', today);
        }
    });
}

const today = getDateWithoutTime(new Date());

const buttonUserContractCheck = document.getElementById('userContractCheck');
const buttonSchoolContractCheck = document.getElementById('schoolContractCheck');
const msgBoxContract = document.querySelector('#blockModeratWebsite > article:nth-child(1)');
let userContractChecked = false;
let schoolContractChecked = false;

const buttonWarnCheck = document.getElementById('warnCheck');
const buttonBanCheck = document.getElementById('banCheck');
const msgBoxWarnBan = document.querySelector('#blockModeratWebsite > article:nth-child(2)');
let warnChecked = false;
let banChecked = false;

const buttonImgCheck = document.getElementById('imgCheck');
const buttonTagCheck = document.getElementById('tagCheck');
const msgBoxImgTag = document.querySelector('#blockModeratWebsite > article:nth-child(3)');

window.addEventListener(
    'load', function() {
        ///////////////////////////////////////////
        //******* checking user contract *******//
        /////////////////////////////////////////
        if (!localStorage.hasOwnProperty('yearUserContractCheck') || today > getDateCheck('userContract')) {
            // a day has passed since user contract check or local storage is empty
            buttonUserContractCheck.addEventListener(
                'click', function() {
                    btnCheckContract('user');
                }
            , {'once' : true});
        } else {
            // user contract check already done today
            setMsgBox('- Les contrats utilisateurs ont déja été vérifiés aujourd\'hui', msgBoxContract, true);
            buttonUserContractCheck.classList.add('inactifLink');
            userContractChecked = true;
        }

        /////////////////////////////////////////////
        //******* checking school contract *******//
        ///////////////////////////////////////////
        if (!localStorage.hasOwnProperty('yearSchoolContractCheck') || today > getDateCheck('schoolContract')) {
            // a day has passed since school contract check or local storage is empty
            buttonSchoolContractCheck.addEventListener(
                'click', function() {
                    btnCheckContract('school');
                }
            , {'once' : true});
        } else {
            // school contract check already done today
            setMsgBox('- Les contrats établissements ont déja été vérifiés aujourd\'hui', msgBoxContract, true);
            buttonSchoolContractCheck.classList.add('inactifLink');
            schoolContractChecked = true;
        }

        /////////////////////////////////////
        //******* checking warning *******//
        ///////////////////////////////////
        if (!localStorage.hasOwnProperty('yearWarnCheck') || today > getDateCheck('warn')) {
            // a day has passed since warning check or local storage is empty
            buttonWarnCheck.addEventListener(
                'click', function() {
                    setMsgBox('traitement en cours..', msgBoxWarnBan, true);
                    ajaxGet('indexAdmin.php?action=checkWarnings', function(response) {
                        if (response.length > 0 && response !== 'false') {
                            response = JSON.parse(response);
                            setMsgBox('- ' + response['nbActiveWarn'] + ' avertissement(s) ont été traitée(s). ' + 
                                response['nbEntriesUnwarned'] + ' avertissement(s) ont été désactivé(s)', msgBoxWarnBan);
                            setDateCheck('warn', today);
                        }
                    });
                }
            , {'once' : true});
        } else {
            // warning check already done today
            setMsgBox('- Les avertissements ont déja été vérifiés aujourd\'hui', msgBoxWarnBan, true);
            buttonWarnCheck.classList.add('inactifLink');
            warnChecked = true;
        }
        ////////////////////////////////////////
        //******* checking banishment *******//
        //////////////////////////////////////
        if (!localStorage.hasOwnProperty('yearBanCheck') || today > getDateCheck('ban')) {
            // a day has passed since banishment check or local storage is empty
            buttonBanCheck.addEventListener(
                'click', function() {
                    setMsgBox('traitement en cours..', msgBoxWarnBan, true);
                    ajaxGet('indexAdmin.php?action=checkBanishments', function(response) {
                        if (response.length > 0 && response !== 'false') {
                            response = JSON.parse(response);
                            setMsgBox('- ' + response['nbActiveBan'] + ' suspension(s) ont été traitée(s). ' + 
                                response['nbEntriesUnbanished'] + ' suspension(s) ont été désactivé(s)', msgBoxWarnBan);
                            setDateCheck('ban', today);
                        }
                    });
                }
            , {'once' : true});
        } else {
            // banishment check already done today
            setMsgBox('- Les suspensions de compte ont déja été vérifiés aujourd\'hui', msgBoxWarnBan, true);
            buttonBanCheck.classList.add('inactifLink');
            banChecked = true;
        }

        ////////////////////////////////////////
        //******* checking unused img *******//
        //////////////////////////////////////
        buttonImgCheck.addEventListener(
            'click', function() {
                setMsgBox('traitement en cours..', msgBoxImgTag, true);
                ajaxGet('indexAdmin.php?action=checkUnusedImg', function(response) {
                    if (response.length > 0 && response !== 'false') {
                        response = JSON.parse(response);
                        setMsgBox('- profileContent entries : ' + response['profileContent'], msgBoxImgTag);
                        setMsgBox('- temp folder : ' + response['tempFolder'], msgBoxImgTag);
                    }
                });
            }
        , {'once' : true});

        ////////////////////////////////////////
        //******* checking unused tag *******//
        //////////////////////////////////////
        buttonTagCheck.addEventListener(
            'click', function() {
                setMsgBox('traitement en cours..', msgBoxImgTag, true);
                ajaxGet('indexAdmin.php?action=checkUnusedTag', function(response) {
                    if (response.length > 0 && response !== 'false') {
                        setMsgBox('- Les tags inutilisés ont été supprimées.', msgBoxImgTag);
                    }
                });
            }
        , {'once' : true});

        if (warnChecked && banChecked) {
            setMsgBox('- Les avertissements et suspensions de compte ont déja été vérifiés aujourd\'hui', msgBoxWarnBan);
        }
        if (userContractChecked && schoolContractChecked) {
            setMsgBox('- Les contrats établissements et utilisateurs ont déja été vérifiés aujourd\'hui', msgBoxContract);
        }
    });