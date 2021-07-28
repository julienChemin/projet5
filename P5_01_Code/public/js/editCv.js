const tagsToReplace = {'<': '&lt;', '>': '&gt;'};

function replaceTag(tag) {
    return tagsToReplace[tag] || tag;
}

function safeTagsReplace(str) {
    return str.replace(/[<>]/g, replaceTag);
}

function ucFirst(string) {
    return string[0].toUpperCase() + string.substring(1);
}

// Source for functions hexToRgb and rgbToHex : 
// https://stackoverflow.com/questions/5623838/rgb-to-hex-and-hex-to-rgb
function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    let shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;

    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });
  
    let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function rgbToHex(r, g, b) {
    return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

function nodeListToArray(nodeList) {
    let result = [];
    if (nodeList && nodeList.length > 0) {
        for (let i = 0; i < nodeList.length; i++)  {
            result.push(nodeList[i]);
        }
    }
    return result;
}

const ownerId = document.getElementById('editCv').getAttribute('ownerId');
const blockSection = document.getElementById('editCv');
let sections = nodeListToArray(document.querySelectorAll('.cvSection'));
const blocks = nodeListToArray(document.querySelectorAll('.cvBlock'));
const menuEditCv = document.getElementById('menuEditCv');
const blockTabs = document.getElementById('tabsEditCv');
const blockTabsContent = document.getElementById('contentTabsEditCv');
const blocksTabsBlock = nodeListToArray(document.querySelectorAll('.tabEditBlock'));

// menu edit cv - size and placement
let navHeight = parseInt(document.getElementById('navbar').getBoundingClientRect().height);
let cvNavHeight = parseInt(document.getElementById('navbarCv').getBoundingClientRect().height);
let screenHeight = parseInt(window.innerHeight);
let maxHeightMenu = screenHeight - (navHeight + cvNavHeight) + 'px';

menuEditCv.style.top = (navHeight + cvNavHeight) + 'px';
menuEditCv.style.maxHeight = maxHeightMenu;
blockTabs.style.maxHeight = maxHeightMenu;
blockTabsContent.style.maxHeight = maxHeightMenu;


/*_____________________________*/
//       WINDOW ON RESIZE
/*_____________________________*/
window.addEventListener(
    'resize', function(e) {
        navHeight = parseInt(document.getElementById('navbar').getBoundingClientRect().height);
        cvNavHeight = parseInt(document.getElementById('navbarCv').getBoundingClientRect().height);
        screenHeight = parseInt(window.innerHeight);
        maxHeightMenu = screenHeight - (navHeight + cvNavHeight) + 'px';

        menuEditCv.style.top = (navHeight + cvNavHeight) + 'px';
        menuEditCv.style.maxHeight = maxHeightMenu;
        blockTabs.style.maxHeight = maxHeightMenu;
        blockTabsContent.style.maxHeight = maxHeightMenu;
    }
);

/*_____________________________*/
//     BUTTON TOGGLE MENU
/*_____________________________*/
const btnToggleMenu = document.getElementById('buttonToggleMenuEditCv');

let tabs = nodeListToArray(document.querySelectorAll('.tabEditCv'));
let tabsAreOpen = false;
let focusedTab = null;
let displayedBlockTabBlocks = null;

let tabsContent = nodeListToArray(document.querySelectorAll('.contentTabEditCv'));
let displayedTabContent = null;

function toggleTabs() {
    if (tabsAreOpen) {
        blockTabs.classList.add('hide');
        tabsAreOpen = false;

        if (displayedTabContent !== null) {
            displayedTabContent.classList.add('hide');
            displayedTabContent = null;
        }

        if (displayedBlockTabBlocks) {
            displayedBlockTabBlocks.style.display = 'none';
            displayedBlockTabBlocks = null;
        }

        if (focusedTabBlock !== null) {
            focusedTabBlock.classList.remove('focusedTab');
            focusedTabBlock = null;
        }

        if (focusedTab !== null) {
            focusedTab.classList.remove('focusedTab');
            focusedTab = null;
        }
    } else {
        blockTabs.classList.remove('hide');
        tabsAreOpen = true;
    }
}

function toggleTabContent(tabContent) {
    if (tabContent === displayedTabContent) {
        displayedTabContent.classList.add('hide');
        displayedTabContent = null;
    } else {
        tabContent.classList.remove('hide');
        displayedTabContent = tabContent;
    }
}

function toggleTabSelection (tab, index = null) {
    // click on tab section -> remove tabBlock focus
    if (focusedTabBlock !== null) {
        focusedTabBlock.classList.remove('focusedTab');
        focusedTabBlock = null;
    }

    if (!focusedTab) {
        // set the focus on tab
        tab.classList.add('focusedTab');
        focusedTab = tab;
        // display tabs for blocks of section focused
        blocksTabsBlock[index].style.display = 'flex';
        displayedBlockTabBlocks = blocksTabsBlock[index];
        // display content tab edit section
        toggleTabContent(tabsContent[index]);
        displayedTabContent = tabsContent[index];
    } else if (tab === focusedTab) {
        if (displayedTabContent === tabsContent[index]) {
            // unset the focus on tab
            tab.classList.remove('focusedTab');
            focusedTab = null;
            // hide tabs for blocks of section previously focused
            displayedBlockTabBlocks.style.display = 'none';
            displayedBlockTabBlocks = null;
            // hide content tab edit section
            toggleTabContent(displayedTabContent);
            displayedTabContent = null;
        } else {
            toggleTabContent(displayedTabContent);
            toggleTabContent(tabsContent[index]);
            displayedTabContent = tabsContent[index];
        }
    } else {
        // unset previous focus, set new
        focusedTab.classList.remove('focusedTab');
        tab.classList.add('focusedTab');
        focusedTab = tab;
        // hide previous tabs for blocks, display new
        if (displayedBlockTabBlocks !== null) {
            displayedBlockTabBlocks.style.display = 'none';
        }
        
        blocksTabsBlock[index].style.display = 'flex';
        displayedBlockTabBlocks = blocksTabsBlock[index];
        // hide previous content tab, display new
        toggleTabContent(displayedTabContent);
        toggleTabContent(tabsContent[index]);
        displayedTabContent = tabsContent[index];
    }
}

btnToggleMenu.addEventListener(
    'click', toggleTabs
);

/*_____________________________*/
//            TABS
/*_____________________________*/
function addEventToggleTab(tab, index) {
    tab.addEventListener(
        'click', function() {
            toggleTabSelection(tab, index);
        }
    );
}

function addEventHoverTab(tab, index) {
    tab.addEventListener(
        'mouseover', function() {
            sections[index].style.borderTop = "solid 3px #CF8B3F";
            sections[index].style.borderBottom = "solid 3px #CF8B3F";
        }
    );

    tab.addEventListener(
        'mouseout', function() {
            sections[index].style.borderTop = "none";
            sections[index].style.borderBottom = "none";
        }
    );
}

for (let i = 0; i < tabs.length; i++) {
    addEventToggleTab(tabs[i], i);
    addEventHoverTab(tabs[i], i);
}

/*_____________________________*/
//       EDIT TABS ORDER
/*_____________________________*/
let chevronsUp = nodeListToArray(document.querySelectorAll('.editSectionOrder .fa-chevron-up'));
let chevronsDown = nodeListToArray(document.querySelectorAll('.editSectionOrder .fa-chevron-down'));

function addEventOrderUp(input) {
    input.addEventListener(
        'click', function(e) {
            e.stopPropagation();
            let currentOrder = parseInt(e.target.parentNode.parentNode.style.order);
            let parsedCurrentOrder = currentOrder/100;
            let currentSection = document.querySelector('#editCv .cvSection[style*="order: ' + parsedCurrentOrder + ';"]');
            let nextSection = document.querySelector('#editCv .cvSection[style*="order: ' + (parsedCurrentOrder+1) + ';"]');
            let currentTab = document.querySelector('#editCv .tabEditCv[style*="order: ' + currentOrder + ';"]');
            let nextTab = document.querySelector('#editCv .tabEditCv[style*="order: ' + (currentOrder+100) + ';"]');
console.log(currentOrder, currentSection,  currentTab,  nextSection,  nextTab);
            if (currentSection !== null && currentTab !== null && nextSection !== null && nextTab !== null) {
                let url = "index.php?action=changeSectionOrder&value=up&currentOrder=" + parsedCurrentOrder + "&ownerId=" + ownerId;

                ajaxGet(url, function(response) {
                    if (response.length > 0 && response === "true") {
                        currentSection.style.order = parsedCurrentOrder + 1;
                        nextSection.style.order = parsedCurrentOrder;
                        currentTab.style.order = currentOrder + 100;
                        nextTab.style.order = currentOrder;
                    }
                });
            }
        }
    );
}

function addEventOrderDown(input) {
    input.addEventListener(
        'click', function(e) {
            e.stopPropagation();
            let currentOrder = parseInt(e.target.parentNode.parentNode.style.order);
            let parsedCurrentOrder = currentOrder/100;
            let currentSection = document.querySelector('#editCv .cvSection[style*="order: ' + parsedCurrentOrder + '"]');
            let previousSection = document.querySelector('#editCv .cvSection[style*="order: ' + (parsedCurrentOrder-1) + '"]');
            let currentTab = document.querySelector('#editCv .tabEditCv[style*="order: ' + currentOrder + '"]');
            let previousTab = document.querySelector('#editCv .tabEditCv[style*="order: ' + (currentOrder-100) + '"]');

            if (currentSection !== null && currentTab !== null && previousSection !== null && previousTab !== null) {
                let url = "index.php?action=changeSectionOrder&value=down&currentOrder=" + parsedCurrentOrder + "&ownerId=" + ownerId;

                ajaxGet(url, function(response) {
                    if (response.length > 0 && response === "true") {
                        currentSection.style.order = parsedCurrentOrder - 1;
                        previousSection.style.order = parsedCurrentOrder;
                        currentTab.style.order = currentOrder - 100;
                        previousTab.style.order = currentOrder;
                    }
                });
            }
        }
    );
}

if (chevronsUp !== null && chevronsUp.length > 0) {
    for (let i = 0; i < chevronsUp.length; i++) {
        addEventOrderDown(chevronsUp[i]);
    }
}

if (chevronsDown !== null && chevronsDown.length > 0) {
    for (let i = 0; i < chevronsDown.length; i++) {
        addEventOrderUp(chevronsDown[i]);
    }
}

/*_____________________________*/
//      EDIT SECTION NAME
/*_____________________________*/
let inputsEditSectionName = nodeListToArray(document.querySelectorAll('.blockEditSectionName input'));
let linksInNavbar = nodeListToArray(document.querySelectorAll('.linkInNavbar'));

function addEventInputName(input, index) {
    input.addEventListener(
        "change", function() {
            inputsEditSectionName[index].value = (safeTagsReplace(inputsEditSectionName[index].value));
            tabs[index].childNodes[1].textContent = inputsEditSectionName[index].value;
            linksInNavbar[index].childNodes[0].textContent = inputsEditSectionName[index].value;
            sectionsValues[index]['newValues']['name'] = inputsEditSectionName[index].value;
        }
    );
}

if (inputsEditSectionName !== null && inputsEditSectionName.length > 0) {
    for (let i = 0; i < inputsEditSectionName.length; i++) {
        addEventInputName(inputsEditSectionName[i], i);
    }
}

/*_____________________________*/
//  EDIT BOOL "LINK IN NAVBAR"
/*_____________________________*/
let inputsEditBoolInNavbar = nodeListToArray(document.querySelectorAll('.blockEditBoolInNavbar input'));

function addEventBoolInNavbar (input, index) {
    input.addEventListener(
        'change', function(e) {
            if (e.currentTarget.checked) {
                linksInNavbar[index].classList.remove('hide');
                sectionsValues[index]['newValues']['linkInNavbar'] = true;
            } else {
                linksInNavbar[index].classList.add('hide');
                sectionsValues[index]['newValues']['linkInNavbar'] = false;
            }
        }
    );
}

if (inputsEditBoolInNavbar !== null && inputsEditBoolInNavbar.length > 0) {
    for (let i = 0; i < inputsEditBoolInNavbar.length; i++) {
        addEventBoolInNavbar(inputsEditBoolInNavbar[i], i);
    }
}

/*_____________________________*/
//    EDIT BACKGROUND COVER
/*_____________________________*/
let inputsEditBoolUseCover = nodeListToArray(document.querySelectorAll('.blockEditBackground .editBoolCover'));
let blocksEditBoolParallax = nodeListToArray(document.querySelectorAll('.blockEditBackground .editBoolParallax'));
let inputsEditBoolParallax = nodeListToArray(document.querySelectorAll('.blockEditBackground .editBoolParallax input'));
let blocksUploadCover = nodeListToArray(document.querySelectorAll('.blockEditBackground form'));
let labelsFileCover = nodeListToArray(document.querySelectorAll('.blockEditBackground form label:first-of-type'));
let inputsFileCover = nodeListToArray(document.querySelectorAll('.blockEditBackground input[type="file"]'));
let previews = nodeListToArray(document.querySelectorAll('.blockEditBackground .preview'));

function addEventBoolUseCover (input, index) {
    input.addEventListener(
        'change', function(e) {
            if (e.currentTarget.checked) {
                if (sectionsValues[index]['newValues']['backgroundCover'] !== null) {
                    sections[index].style.backgroundImage = sectionsValues[index]['newValues']['backgroundCover'];
                }

                blocksUploadCover[index].classList.remove('hide');
                blocksEditBoolParallax[index].classList.remove('hide');
                sectionsValues[index]['newValues']['displayCover'] = true;
            } else {
                sections[index].style.backgroundImage = "";
                blocksUploadCover[index].classList.add('hide');
                blocksEditBoolParallax[index].classList.add('hide');
                sectionsValues[index]['newValues']['displayCover'] = false;
            }
        }
    );
}

function addEventPreview(inputFile, index) {
    inputFile.addEventListener(
        'change', function (e) {
            if (e.currentTarget.files && e.currentTarget.files[0]) {
                if (e.currentTarget.files[0].size < 6000000) {
                    let reader = new FileReader();
                    reader.addEventListener(
                        'load', function(e) {
                            previews[index].childNodes[1].src = e.currentTarget.result;
                            sections[index].style.backgroundImage = "url('" + e.currentTarget.result + "')";
                            sectionsValues[index]['newValues']['backgroundCover'] = "url('" + e.currentTarget.result + "')";

                            if (previews[index].classList.contains('hide')) {
                                previews[index].classList.remove('hide');
                            }
                            
                        }
                    );
                    reader.readAsDataURL(e.currentTarget.files[0]);
                } else {
                    labelsFileCover[index].style.color = "red";
                    labelsFileCover[index].style.fontWeight = "bold";
                    setTimeout(() => {
                        labelsFileCover[index].style.color = "white";
                        setTimeout(() => {
                            labelsFileCover[index].style.color = "red";
                            setTimeout(() => {
                                labelsFileCover[index].style.color = "white";
                                labelsFileCover[index].style.fontWeight = "normal";
                            }, 5000);
                        }, 200);
                    }, 200);
                }
            }
        }
    );
}

function addEventBoolCoverFixed (input, index) {
    input.addEventListener(
        'change', function(e) {
            if (e.currentTarget.checked) {
                sections[index].style.backgroundAttachment = "fixed";
                sectionsValues[index]['newValues']['backgroundFixed'] = true;
            } else {
                sections[index].style.backgroundAttachment = "initial";
                sectionsValues[index]['newValues']['backgroundFixed'] = false;
            }
        }
    );
}

if (inputsEditBoolUseCover !== null && inputsEditBoolUseCover.length > 0) {
    for (let i = 0; i < inputsEditBoolUseCover.length; i++) {
        addEventBoolUseCover(inputsEditBoolUseCover[i], i);
    }
}

if (inputsFileCover !== null && inputsFileCover.length > 0) {
    for (let i = 0; i < inputsFileCover.length; i++) {
        addEventPreview(inputsFileCover[i], i);
    }
}

if (inputsEditBoolParallax !== null && inputsEditBoolParallax.length > 0) {
    for (let i = 0; i < inputsEditBoolParallax.length; i++) {
        addEventBoolCoverFixed(inputsEditBoolParallax[i], i);
    }
}

/*_____________________________*/
//     EDIT SECTION HEIGHT
/*_____________________________*/
let inputsEditBoolHeightAuto = nodeListToArray(document.querySelectorAll('.blockEditHeight input[type="checkbox"]'));
let labelsHeightValue = nodeListToArray(document.querySelectorAll('.blockEditHeight > p label'));
let inputsEditHeightValue = nodeListToArray(document.querySelectorAll('.blockEditHeight input[type="range"]'));

function addEventBoolHeightAuto (input, index) {
    input.addEventListener(
        'change', function(e) {
            if (e.currentTarget.checked) {
                sections[index].style.minHeight = '5vh';
                sections[index].childNodes[1].style.minHeight = '5vh';
                sections[index].style.height = 'auto';
                labelsHeightValue[index].classList.add('elemDisabled');
                inputsEditHeightValue[index].setAttribute('disabled', true);
                sectionsValues[index]['newValues']['heightAuto'] = true;
                sectionsValues[index]['newValues']['heightValue'] = null;
            } else {
                sections[index].style.minHeight = inputsEditHeightValue[index].value + 'vh';
                sections[index].childNodes[1].style.minHeight = inputsEditHeightValue[index].value + 'vh';
                labelsHeightValue[index].classList.remove('elemDisabled');
                inputsEditHeightValue[index].removeAttribute('disabled');
                sectionsValues[index]['newValues']['heightAuto'] = false;
                sectionsValues[index]['newValues']['heightValue'] = inputsEditHeightValue[index].value + 'vh';
            }
        }
    );
}

function addEventBoolHeightValue (input, index) {
    input.addEventListener(
        'input', function(e) {
            sections[index].style.minHeight = inputsEditHeightValue[index].value + 'vh';
            sections[index].childNodes[1].style.minHeight = inputsEditHeightValue[index].value + 'vh';
            sectionsValues[index]['newValues']['heightValue'] = inputsEditHeightValue[index].value + 'vh';
        }
    );
}

if (inputsEditBoolHeightAuto !== null && inputsEditBoolHeightAuto.length > 0) {
    for (let i = 0; i < inputsEditBoolHeightAuto.length; i++) {
        addEventBoolHeightAuto(inputsEditBoolHeightAuto[i], i);
    }
}

if (inputsEditHeightValue !== null && inputsEditHeightValue.length > 0) {
    for (let i = 0; i < inputsEditHeightValue.length; i++) {
        addEventBoolHeightValue(inputsEditHeightValue[i], i);
    }
}

/*________________________________*/
// EDIT HORIZONTAL/ VERTICAL ALIGN
/*________________________________*/
let formEditHorizontalAlign = nodeListToArray(document.querySelectorAll('.blockEditHorizontalAlign form'));
let formEditVerticalAlign = nodeListToArray(document.querySelectorAll('.blockEditVerticalAlign form'));

function addEventEditHorizontalAlign (radioNodeList, index) {
    radioNodeList.forEach(input => {
        input.addEventListener(
            'change', function() {
                sections[index].childNodes[1].classList.remove('horizontalAlign' + ucFirst(sectionsValues[index]['newValues']['horizontalAlign']));
                sections[index].childNodes[1].classList.add('horizontalAlign' + ucFirst(radioNodeList.value));
                sectionsValues[index]['newValues']['horizontalAlign'] = radioNodeList.value;
            }
        );
    });
}

function addEventEditVerticalAlign (radioNodeList, index) {
    radioNodeList.forEach(input => {
        input.addEventListener(
            'change', function() {
                sections[index].childNodes[1].classList.remove('verticalAlign' + ucFirst(sectionsValues[index]['newValues']['verticalAlign']));
                sections[index].childNodes[1].classList.add('verticalAlign' + ucFirst(radioNodeList.value));
                sectionsValues[index]['newValues']['verticalAlign'] = radioNodeList.value;
            }
        );
    });
}

if (formEditHorizontalAlign !== null && formEditHorizontalAlign.length > 0) {
    for (let i = 0; i < formEditHorizontalAlign.length; i++) {
        addEventEditHorizontalAlign(formEditHorizontalAlign[i].elements['horizontalValue' + i], i);
    }
}

if (formEditVerticalAlign !== null && formEditVerticalAlign.length > 0) {
    for (let i = 0; i < formEditVerticalAlign.length; i++) {
        addEventEditVerticalAlign(formEditVerticalAlign[i].elements['verticalValue' + i], i);
    }
}

/*_____________________________*/
//        SAVE SECTION
/*_____________________________*/
let sectionsValues = [];
let buttonsSave = nodeListToArray(document.querySelectorAll('.saveChange'));
let elemWait = nodeListToArray(document.querySelectorAll('.saving'));
let elemSaveSuccess = nodeListToArray(document.querySelectorAll('.saveSuccess'));
let elemSaveFailure = nodeListToArray(document.querySelectorAll('.saveFailure'));

for (let i = 0; i < sections.length; i++) {
    // setup array sections values
    sectionsValues.push(
        {
            'values': getSectionValues(i), 
            'newValues': getSectionValues(i)
        }
    );
}

function getSectionValues(index) {
    return {
        'name': inputsEditSectionName[index].value, 
        'linkInNavbar': inputsEditBoolInNavbar[index].checked, 
        'displayCover': inputsEditBoolUseCover[index].checked, 
        'backgroundCover': sections[index].style.backgroundImage, 
        'backgroundFixed': inputsEditBoolParallax[index].checked, 
        'heightAuto': inputsEditBoolHeightAuto[index].checked, 
        'heightValue': inputsEditHeightValue[index].value + 'vh', 
        'horizontalAlign': formEditHorizontalAlign[index].elements['horizontalValue' + index].value, 
        'verticalAlign': formEditVerticalAlign[index].elements['verticalValue' + index].value
    }
}

function getSectionData(currValue, newValue, index) {
    let data = new FormData();

    data.append('sectionId', tabs[index].getAttribute('idSection'));
    data.append('displayCover', newValue['displayCover']);
    data.append('heightValue', newValue['heightValue']);

    if (inputsFileCover[index].files.length > 0) {
        data.append('file', inputsFileCover[index].files[0]);
    }

    if (currValue['name'] !== newValue['name']) {
        data.append('name', newValue['name']);
    }

    if (currValue['linkInNavbar'] !== newValue['linkInNavbar']) {
        data.append('linkInNavbar', newValue['linkInNavbar']);
    }

    if (currValue['backgroundCover'] !== newValue['backgroundCover']) {
        data.append('backgroundCover', 'new');
    }

    if (currValue['backgroundFixed'] !== newValue['backgroundFixed']) {
        data.append('backgroundFixed', newValue['backgroundFixed']);
    }

    if (currValue['heightAuto'] !== newValue['heightAuto']) {
        data.append('heightAuto', newValue['heightAuto']);
    }

    if (currValue['horizontalAlign'] !== newValue['horizontalAlign']) {
        data.append('horizontalAlign', newValue['horizontalAlign']);
    }

    if (currValue['verticalAlign'] !== newValue['verticalAlign']) {
        data.append('verticalAlign', newValue['verticalAlign']);
    }

    return data;
}

function saveSectionValues(index) {
    let data = getSectionData(sectionsValues[index]['values'], sectionsValues[index]['newValues'], index);
    let url = 'index.php?action=updateCv';
    buttonsSave[index].classList.add('hide');
    elemWait[index].classList.remove('hide');
    let timeout = null;

    ajaxPost(url, data, function(response) {
        if (response.length > 0 && response === 'true') {
            elemWait[index].classList.add('hide');
            addEventQuickCloseSaveResult(elemSaveSuccess[index], index, timeout, 'success');
            elemSaveSuccess[index].classList.remove('hide');

            timeout = setTimeout(() => {
                elemSaveSuccess[index].classList.add('hide');
                buttonsSave[index].classList.remove('hide');
            }, 3000);

            // save new values
            sectionsValues[index]['values'] = getSectionValues(index);
        } else {
            elemWait[index].classList.add('hide');
            addEventQuickCloseSaveResult(elemSaveFailure[index], index, timeout, 'failure');
            elemSaveFailure[index].classList.remove('hide');

            timeout = setTimeout(() => {
                elemSaveFailure[index].classList.add('hide');
                buttonsSave[index].classList.remove('hide');
            }, 5000);
        }
    });
}

function addEventQuickCloseSaveResult(elem, index, timeout, info) {
    elem.addEventListener(
        'click', function() {
            clearTimeout(timeout);

            if (info === "success") {
                elemSaveSuccess[index].classList.add('hide');
                buttonsSave[index].classList.remove('hide');
            } else {
                elemSaveFailure[index].classList.add('hide');
                buttonsSave[index].classList.remove('hide');
            }
        }
    , {'once':true});
}

function addEventSaveSection(input, index) {
    input.addEventListener(
        'click', function() {
            saveSectionValues(index);
        }
    );
}

if (buttonsSave !== null && buttonsSave.length > 0) {
    for (let i = 0; i < buttonsSave.length; i++) {
        addEventSaveSection(buttonsSave[i], i);
    }
}

/*_____________________________*/
//      BUTTON ADD SECTION
/*_____________________________*/
const btnAddSection = document.getElementById('tabAddSection');

function newElem(data) {
    if (!data['type']) {
        return null;
    }

    let elem = document.createElement(data['type']);

    if (data['textContent']) {
        elem.textContent = data['textContent'];
    }

    if (data['innerHTML']) {
        elem.innerHTML = data['innerHTML'];
    }

    if (data['id']) {
        elem.id = data['id'];
    }

    if (data['classes'] && data['classes'].length > 0) {
        for (let i = 0; i < data['classes'].length; i++) {
            elem.classList.add(data['classes'][i]);
        }
    }

    if (data['elemStyle'] && data['elemStyle']['style'] && data['elemStyle']['style'].length > 0) {
        for (let i = 0; i < data['elemStyle']['style'].length; i++) {
            elem.style[data['elemStyle']['style'][i]] = data['elemStyle']['values'][i];
        }
    }

    if (data['elemAttributes'] && data['elemAttributes']['attribute'] && data['elemAttributes']['attribute'].length > 0) {
        for (let i = 0; i < data['elemAttributes']['attribute'].length; i++) {
            elem.setAttribute(data['elemAttributes']['attribute'][i], data['elemAttributes']['values'][i]);
        }
    }

    if (data['childs'] && data['childs'].length > 0) {
        for (let i = 0; i < data['childs'].length; i++) {
            elem.appendChild(data['childs'][i]);
        }
    }

    return elem;
}

function getNewElemSection(order) {
    let divContainer = newElem({
        'type': 'div', 
        'classes': ['container', 'horizontalAlignAround', 'verticalAlignCenter'] 
    });

    let elemSection = newElem({
        'type': 'section', 
        'id': 'anchorsection ' + (order + 1), 
        'classes': ['cvSection'], 
        'elemStyle': {
            'style': ['order'], 
            'values': [(order + 1)]
        }, 
        'childs': [document.createTextNode(""), divContainer]
    });
    sections.push(elemSection);
    
    return elemSection;
}

function getNewElemLink(order) {
    let elemA = newElem({
        'type': 'a', 
        'elemAttributes': {
            'attribute': ['href'], 
            'values': ['anchorsection ' + (order + 1)]
        }, 
        'textContent': 'section ' + (order + 1)
    });

    let elemLi = newElem({
        'type': 'li', 
        'classes': ['linkInNavbar'], 
        'childs': [elemA]
    });
    linksInNavbar.push(elemLi);

    return elemLi;
}

function getNewElemTab(idSection, order) {
    let elemChevronUp = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-chevron-up']
    });
    chevronsUp.push(elemChevronUp);

    let elemChevronDown = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-chevron-down']
    });
    chevronsDown.push(elemChevronDown);

    let divChevrons = newElem({
        'type': 'div', 
        'classes': ['editSectionOrder'], 
        'childs': [elemChevronUp, elemChevronDown]
    });

    let elemP = newElem({
        'type': 'p', 
        'textContent': 'section ' + (order + 1)
    });

    let elemTab = newElem({
        'type': 'div', 
        'classes': ['tabEditCv'], 
        'elemAttributes': {
            'attribute': ['idsection'], 
            'values': [idSection]
        }, 
        'elemStyle': {
            'style': ['order'], 
            'values': [(order+1) + '00']
        }, 
        'childs': [document.createTextNode(""), elemP, divChevrons]
    });
    tabs.push(elemTab);

    return elemTab;
}

function getNewElemTabBlocks(idSection, order) {
    let elemTab = newElem({
        'type': 'div', 
        'classes': ['tabEditBlock', 'belongToSection' + idSection], 
        'elemAttributes': {
            'attribute': ['idsection'], 
            'values': [idSection]
        }, 
        'elemStyle': {
            'style': ['order'], 
            'values': [(order+1) + '50']
        }
    });
    blocksTabsBlock.push(elemTab);

    return elemTab;
}

function getNewElemEditSectionName(order) {
    let elemLabel = newElem({
        'type': 'label', 
        'classes': ['orang'], 
        'textContent': 'Nom de la section'
    });

    let elemInput = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'value'], 
            'values': ['text', 'section ' + (order + 1)]
        }
    });
    inputsEditSectionName.push(elemInput);

    return newElem({
        'type': 'div', 
        'classes': ['blockEditSectionName'], 
        'childs': [elemLabel, elemInput]
    });
}

function getNewElemEditBoolInNavbar() {
    let elemInput = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'checked'], 
            'values': ['checkbox', 'true']
        }
    });
    inputsEditBoolInNavbar.push(elemInput);

    let elemLabel = newElem({
        'type': 'label', 
        'textContent': 'Afficher un lien vers cette section dans la barre de navigation'
    });

    let elemP = newElem({
        'type': 'p', 
        'childs': [elemInput, elemLabel]
    });

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Barre de navigation'
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditBoolInNavbar'], 
        'childs': [elemSpan, elemP]
    });
}

function getNewElemEditBackground(order) {
    // span
    let elemSpan1 = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Image de fond'
    });

    // p
    let elemInputBoolCover = newElem({
        'type': 'input', 
        'classes': ['editBoolCover'], 
        'elemAttributes': {
            'attribute': ['type'], 
            'values': ['checkbox']
        }
    });
    inputsEditBoolUseCover.push(elemInputBoolCover);

    let elemLabelBoolCover = newElem({
        'type': 'label', 
        'textContent': 'Utiliser une image de fond'
    });

    let elemP1 = newElem({
        'type': 'p', 
        'childs': [elemInputBoolCover, elemLabelBoolCover]
    });

    // form
    let elemLabelUploadFile = newElem({
        'type': 'label', 
        'textContent': '(max : 5Mo)', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['uploadFile' + (order - 1)]
        }
    });
    labelsFileCover.push(elemLabelUploadFile);

    let elemInputHidden = newElem({
        'type': 'input', 
        'id': 'uploadFile' + (order - 1), 
        'elemAttributes': {
            'attribute': ['type', 'name', 'max-size', 'accept'], 
            'values': ['hidden', 'uploadFile' + (order - 1), '6000000', '.jpeg, .jpg, .jfif, .png, .gif']
        }
    });

    let elemInputFile = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['file', 'MAX_FILE_SIZE', '6000000']
        }
    });
    inputsFileCover.push(elemInputFile);

    let elemP2 = newElem({
        'type': 'p', 
        'childs': [elemLabelUploadFile, elemInputHidden, elemInputFile]
    });

    let elemImg = newElem({
        'type': 'img', 
        'elemAttributes': {
            'attribute': ['alt', 'title'], 
            'values': ['Aperçu', 'Preview']
        }
    });

    let elemFigurePreview = newElem({
        'type': 'figure', 
        'classes': ['preview', 'hide'], 
        'childs': [document.createTextNode(""), elemImg]
    });
    previews.push(elemFigurePreview);

    let elemForm = newElem({
        'type': 'form', 
        'classes': ['hide'], 
        'elemAttributes': {
            'attribute': ['method', 'enctype'], 
            'values': ['POST', 'multipart/form-data']
        }, 
        'childs': [elemP2, elemFigurePreview]
    });
    blocksUploadCover.push(elemForm);

    // p
    let elemInputParallax = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type'], 
            'values': ['checkbox']
        }
    });
    inputsEditBoolParallax.push(elemInputParallax);

    let elemLabelParallax = newElem({
        'type': 'label', 
        'textContent': 'Effet de parallaxe'
    });

    let elemP3 = newElem({
        'type': 'p', 
        'classes': ['editBoolParallax', 'hide'], 
        'childs': [elemInputParallax, elemLabelParallax]
    });
    blocksEditBoolParallax.push(elemP3);

    return newElem({
        'type': 'div', 
        'classes': ['blockEditBackground'], 
        'childs': [
            newElem({
                'type': 'div', 
                'childs': [elemSpan1, elemP1, elemForm, elemP3]
            })
        ]
    });
}

function getNewElemEditHeight() {
    let elemInputAuto = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'checked'], 
            'values': ['checkbox', 'true']
        }
    });
    inputsEditBoolHeightAuto.push(elemInputAuto);

    let elemLabelAuto = newElem({
        'type': 'label', 
        'textContent': 'Auto'
    });

    let elemP1 = newElem({
        'type': 'p', 
        'childs': [elemInputAuto, elemLabelAuto]
    });

    let elemSpan1 = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Hauteur de la section'
    });

    let elemDiv1 = newElem({
        'type': 'div', 
        'childs': [elemSpan1, elemP1]
    });

    let elemLabelHeightValue = newElem({
        'type': 'label', 
        'classes': ['elemDisabled'], 
        'textContent': 'Taille en pourcentage de l\'écran'
    });
    labelsHeightValue.push(elemLabelHeightValue);

    let elemInputHeightValue = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'min', 'max', 'step', 'value', 'disabled'], 
            'values': ['range', '5', '100', '5', '50', 'true']
        }
    });
    inputsEditHeightValue.push(elemInputHeightValue);

    let elemP2 = newElem({
        'type': 'p', 
        'childs': [elemLabelHeightValue, elemInputHeightValue]
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditHeight'], 
        'childs': [elemDiv1, elemP2]
    });
}

function getNewElemEditHorizontalAlign(order) {
    let elemInputBetween = newElem({
        'type': 'input', 
        'id': 'horizontalBetween' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'horizontalValue' + order, 'between']
        }
    });

    let spanBetween = newElem({
        'type': 'span', 
        'textContent': 'Au bord'
    });

    let labelBetween = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['horizontalBetween' + order]
        }, 
        'childs': [elemInputBetween, spanBetween]
    });

    let elemInputAround = newElem({
        'type': 'input', 
        'id': 'horizontalAround' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value', 'checked'], 
            'values': ['radio', 'horizontalValue' + order, 'around', 'true']
        }
    });

    let spanAround = newElem({
        'type': 'span', 
        'textContent': 'Espacé'
    });

    let labelAround = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['horizontalAround' + order]
        }, 
        'childs': [elemInputAround, spanAround]
    });

    let elemInputRight = newElem({
        'type': 'input', 
        'id': 'horizontalRight' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'horizontalValue' + order, 'right']
        }
    });

    let spanRight = newElem({
        'type': 'span', 
        'textContent': 'Droite'
    });

    let labelRight = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['horizontalRight' + order]
        }, 
        'childs': [elemInputRight, spanRight]
    });

    let elemInputCenter = newElem({
        'type': 'input', 
        'id': 'horizontalCenter' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'horizontalValue' + order, 'center']
        }
    });

    let spanCenter = newElem({
        'type': 'span', 
        'textContent': 'Centre'
    });

    let labelCenter = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['horizontalCenter' + order]
        }, 
        'childs': [elemInputCenter, spanCenter]
    });

    let elemInputLeft = newElem({
        'type': 'input', 
        'id': 'horizontalLeft' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'horizontalValue' + order, 'left']
        }
    });

    let spanLeft = newElem({
        'type': 'span', 
        'textContent': 'Gauche'
    });

    let labelLeft = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['horizontalLeft' + order]
        }, 
        'childs': [elemInputLeft, spanLeft]
    });

    let elemForm = newElem({
        'type': 'form', 
        'childs': [labelLeft, labelCenter, labelRight, labelAround, labelBetween]
    });
    formEditHorizontalAlign.push(elemForm);

    return newElem({
        'type': 'div', 
        'classes': ['blockEditHorizontalAlign'], 
        'childs': [elemForm]
    });
}

function getNewElemEditVerticalAlign(order) {
    let elemInputBottom = newElem({
        'type': 'input', 
        'id': 'verticalBottom' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'verticalValue' + order, 'bottom']
        }
    });

    let spanBottom = newElem({
        'type': 'span', 
        'textContent': 'Bas'
    });

    let labelBottom = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['verticalBottom' + order]
        }, 
        'childs': [elemInputBottom, spanBottom]
    });

    let elemInputCenter = newElem({
        'type': 'input', 
        'id': 'verticalCenter' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value', 'checked'], 
            'values': ['radio', 'verticalValue' + order, 'center', 'true']
        }
    });

    let spanCenter = newElem({
        'type': 'span', 
        'textContent': 'Centre'
    });

    let labelCenter = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['verticalCenter' + order]
        }, 
        'childs': [elemInputCenter, spanCenter]
    });

    let elemInputTop = newElem({
        'type': 'input', 
        'id': 'verticalTop' + order, 
        'elemAttributes': {
            'attribute': ['type', 'name', 'value'], 
            'values': ['radio', 'verticalValue' + order, 'top']
        }
    });

    let spanTop = newElem({
        'type': 'span', 
        'textContent': 'Haut'
    });

    let labelTop = newElem({
        'type': 'label', 
        'elemAttributes': {
            'attribute': ['for'], 
            'values': ['verticalTop' + order]
        }, 
        'childs': [elemInputTop, spanTop]
    });

    let elemForm = newElem({
        'type': 'form', 
        'childs': [labelTop, labelCenter, labelBottom]
    });
    formEditVerticalAlign.push(elemForm);

    return newElem({
        'type': 'div', 
        'classes': ['blockEditVerticalAlign'], 
        'childs': [elemForm]
    });
}

function getNewElemEditAlign(order) {
    let elemVerticalAlign = getNewElemEditVerticalAlign(order);

    let elemVerticalP = newElem({
        'type': 'p', 
        'elemStyle': {
            'style': ['textAlign'], 
            'values': ['left']
        }, 
        'textContent': 'Alignement vertical'
    });

    let elemHorizontalAlign = getNewElemEditHorizontalAlign(order);

    let elemHorizontalP = newElem({
        'type': 'p', 
        'elemStyle': {
            'style': ['textAlign'], 
            'values': ['left']
        }, 
        'textContent': 'Alignement horizontal'
    });

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Alignement des blocs'
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditAlign'], 
        'childs': [
            elemSpan, 
            elemHorizontalP, 
            elemHorizontalAlign, 
            elemVerticalP, 
            elemVerticalAlign
        ]
    });
}

function getNewElemSaveSection() {
    let elemP = newElem({
        'type': 'p', 
        'classes': ['saveFailure', 'red', 'hide'], 
        'textContent': 'Certaines modifications n\'ont pas pu être enregistrées, réessayez ou rechargez la page'
    });
    elemSaveFailure.push(elemP);

    let elemIcone = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-check', 'saveSuccess', 'green', 'hide']
    });
    elemSaveSuccess.push(elemIcone);

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['saving', 'orang', 'hide'], 
        'textContent': '. . .'
    });
    elemWait.push(elemSpan);

    let elemButton = newElem({
        'type': 'button', 
        'classes': ['saveChange'], 
        'textContent': 'Enregistrer les modifications'
    });
    buttonsSave.push(elemButton);

    return newElem({
        'type': 'div', 
        'classes': ['blockSaveSection'], 
        'childs': [elemButton, elemSpan, elemIcone, elemP]
    });
}

function getNewElemAddNewBlock() {
    let elemButton = newElem({
        'type': 'button', 
        'classes': ['addNewBlock'], 
        'textContent': 'Ajouter un nouveau bloc'
    });
    buttonsAddNewBlock.push(elemButton);

    return newElem({
        'type': 'div', 
        'classes': ['blockAddNewBlock'], 
        'childs': [elemButton]
    });
}

function getNewElemDeleteSection() {
    let elemButton = newElem({
        'type': 'button', 
        'classes': ['deleteSection', 'red'], 
        'textContent': 'Supprimer la section'
    });
    buttonsDelete.push(elemButton);

    return newElem({
        'type': 'div', 
        'classes': ['blockDeleteSection'], 
        'childs': [elemButton]
    });
}

function getNewElemTabContent(order) {
    let elemTabContent = newElem({
        'type': 'div', 
        'classes': ['contentTabEditCv', 'hide'], 
        'childs': [
            getNewElemEditSectionName(order), 
            getNewElemEditBoolInNavbar(), 
            getNewElemEditBackground(order), 
            getNewElemEditHeight(), 
            getNewElemEditAlign(order), 
            getNewElemSaveSection(), 
            getNewElemAddNewBlock(), 
            getNewElemDeleteSection()
        ]
    });
    tabsContent.push(elemTabContent);

    return elemTabContent;
}

function addAllEventForNewSection(index) {
    addEventToggleTab(tabs[index], index);
    addEventHoverTab(tabs[index], index);
    addEventOrderUp(chevronsDown[index]);
    addEventOrderDown(chevronsUp[index]);
    addEventInputName(inputsEditSectionName[index], index);
    addEventBoolInNavbar(inputsEditBoolInNavbar[index], index);
    addEventBoolUseCover(inputsEditBoolUseCover[index], index);
    addEventPreview(inputsFileCover[index], index);
    addEventBoolCoverFixed(inputsEditBoolParallax[index], index);
    addEventBoolHeightAuto(inputsEditBoolHeightAuto[index], index);
    addEventBoolHeightAuto(inputsEditBoolHeightAuto[index], index);
    addEventBoolHeightValue(inputsEditHeightValue[index], index);
    addEventEditHorizontalAlign(formEditHorizontalAlign[index].elements['horizontalValue' + index], index);
    addEventEditVerticalAlign(formEditVerticalAlign[index].elements['verticalValue' + index], index);
    addEventSaveSection(buttonsSave[index], index);
    addEventAddNewBlock(buttonsAddNewBlock[index], index);
    addEventDeleteSection(buttonsDelete[index], index);
}

function getElemsForNewSection(idSection, order) {
    let elemSection = getNewElemSection(parseInt(order));
    let elemTab = getNewElemTab(idSection, parseInt(order));
    let elemTabBlocks = getNewElemTabBlocks(idSection, parseInt(order));
    let elemTabContent = getNewElemTabContent(parseInt(order));
    let elemLink = getNewElemLink(parseInt(order));

    sectionsValues.push(
        {
            'values': getSectionValues(parseInt(order) - 1), 
            'newValues': getSectionValues(parseInt(order) - 1)
        }
    );

    addAllEventForNewSection(parseInt(order));

    return {
        'elemSection': elemSection, 
        'elemTab': elemTab, 
        'elemTabBlocks': elemTabBlocks, 
        'elemTabContent': elemTabContent, 
        'elemLink': elemLink
    };
}

const navbarLinkList = document.querySelector('#navbarCv > ul');

if (btnAddSection !== null) {
    btnAddSection.addEventListener(
        'click', function() {
            btnAddSection.classList.add('hide');
            let url = 'index.php?action=addNewSection&ownerId=' + ownerId;
    
            ajaxGet(url, function(response) {
                if (response.length > 0 && response !== 'false') {
                    response = JSON.parse(response);
                    let elemsNewSection = getElemsForNewSection(response['idSection'], response['order']);
                    blockTabs.insertBefore(elemsNewSection['elemTab'], btnAddSection);
                    blockTabs.insertBefore(elemsNewSection['elemTabBlocks'], btnAddSection);
                    blockTabsContent.appendChild(elemsNewSection['elemTabContent']);
                    navbarLinkList.appendChild(elemsNewSection['elemLink']);
                    blockSection.appendChild(elemsNewSection['elemSection']);
    
                    if (sections.length < 15) {
                        btnAddSection.classList.remove('hide');
                    } else {
                        blockTabs.removeChild(btnAddSection);
                    }
                }
            });
        }
    );
}

/*_____________________________*/
//    BUTTON DELETE SECTION
/*_____________________________*/
let buttonsDelete = nodeListToArray(document.querySelectorAll('.deleteSection'));

function addEventDeleteSection(input, index) {
    input.addEventListener(
        'click', function() {
            let url = 'index.php?action=deleteSection&ownerId=' + ownerId + '&sectionId=' + tabs[index].getAttribute('idSection');
            linkDeleteSection.href = url;
            modal.style.display = "flex";
            modalDeleteSection.style.display = "flex";
            displayedModal = modalDeleteSection;
        }
    );
}

if (buttonsDelete !== null && buttonsDelete.length > 0) {
    for (let i = 0; i < buttonsDelete.length; i++) {
        addEventDeleteSection(buttonsDelete[i], i);
    }
}

/*__________________________________________________________*/
/*__________________________________________________________*/
/*__________________________________________________________*/
//                       BLOCKS STUFF
/*__________________________________________________________*/
/*__________________________________________________________*/
/*__________________________________________________________*/

/*________________________________________*/
//       TOGGLE TAB MENU BLOCK CONTENT
/*________________________________________*/
const tabsBlock = nodeListToArray(document.querySelectorAll('.tabEditBlock > div'));
const tabsBlockContent = nodeListToArray(document.querySelectorAll('.contentTabEditBlock'));
let focusedTabBlock = null;

function addEventToggleTabBlock(tab, index) {
    tab.addEventListener(
        'click', function() {
            if (!focusedTabBlock) {
                // set the focus on tab
                tab.classList.add('focusedTab');
                focusedTabBlock = tab;
                // display tabBlock content
                if (displayedTabContent !== null) {
                    toggleTabContent(displayedTabContent);
                }
                toggleTabContent(tabsBlockContent[index]);
                displayedTabContent = tabsBlockContent[index];
            } else if (tab === focusedTabBlock) {
                // unset the focus on tabBlock
                tab.classList.remove('focusedTab');
                focusedTabBlock = null;
                // unset the focus on tabSection
                focusedTab.classList.remove('focusedTab');
                focusedTab = null;
                // hide content tab edit section
                toggleTabContent(displayedTabContent);
                displayedTabContent = null;
                // hide blockTabBlock
                displayedBlockTabBlocks.style.display = "none";
                displayedBlockTabBlocks = null;
            } else {
                // unset previous focus, set new
                focusedTabBlock.classList.remove('focusedTab');
                tab.classList.add('focusedTab');
                focusedTabBlock = tab;
                // hide previous content tab, display new
                toggleTabContent(displayedTabContent);
                toggleTabContent(tabsBlockContent[index]);
                displayedTabContent = tabsBlockContent[index];
            }
        }
    );
}

if (tabsBlock !== null && tabsBlock.length > 0) {
    for (let i = 0; i < tabsBlock.length; i++) {
        addEventToggleTabBlock(tabsBlock[i], i);
    }
}

/*________________________________________*/
//              BLOCKS SIZE
/*________________________________________*/
const inputsEditBlockSize = nodeListToArray(document.querySelectorAll('.blockEditSize input'));

function addEventEditBlockSize(input, index) {
    input.addEventListener(
        'input', function(e) {
            let newSize = sizeIntValueToString(e.currentTarget.value);

            if (newSize !== blocksValues[index]['newValues']['size']) {
                let classSize = 'block' + ucFirst(newSize);
                let classToRemove = 'block' + ucFirst(blocksValues[index]['newValues']['size']);

                blocks[index].classList.remove(classToRemove);
                blocks[index].classList.add(classSize);

                blocksValues[index]['newValues']['size'] = newSize;
            }
        }
    );
}

function sizeIntValueToString(value = 0) {
    if (value == 1) {
        return 'small';
    } else if (value == 2) {
        return 'medium';
    } else {
        return 'large';
    }
}

function sizeStringValueToInt(value = null) {
    if (value === 'small') {
        return 1;
    } else if (value === 'medium') {
        return 2;
    } else {
        return 3;
    }
}

if (inputsEditBlockSize && inputsEditBlockSize.length > 0) {
    for (let i = 0; i < inputsEditBlockSize.length; i++) {
        addEventEditBlockSize(inputsEditBlockSize[i], i);
    }
}

/*________________________________________*/
//              BLOCKS STYLE
/*________________________________________*/
const inputsEditBlockBackOpacity = nodeListToArray(document.querySelectorAll('.editBackgroundOpacity input'));
const inputsEditBlockBackColor = nodeListToArray(document.querySelectorAll('.editBackgroundColor input'));

function addEventEditBlockBackColor(input, index) {
    input.addEventListener(
        'input', function(e) {
            let rgbObject = hexToRgb(e.currentTarget.value);
            let rgbValue = rgbObject.r + ", " + rgbObject.g + ", " + rgbObject.b;
            
            if (rgbValue !== blocksValues[index]['newValues']['color']) {
                let newBackColor = 'rgba(' + rgbValue + ', ' + blocksValues[index]['newValues']['opacity'] + ')';
                blocks[index].style.backgroundColor = newBackColor;
                blocksValues[index]['newValues']['color'] = rgbValue;
            }
        }
    );
}

function addEventEditBlockBackOpacity(input, index) {
    input.addEventListener(
        'input', function(e) {
            if (e.currentTarget.value !== blocksValues[index]['newValues']['opacity']) {
                let newBackColor = 'rgba(' + blocksValues[index]['newValues']['color'] + ', ' + e.currentTarget.value + ')';
                blocks[index].style.backgroundColor = newBackColor;
                blocksValues[index]['newValues']['opacity'] = e.currentTarget.value;
            }
        }
    );
}

if (inputsEditBlockBackColor && inputsEditBlockBackColor.length > 0) {
    for (let i = 0; i < inputsEditBlockBackColor.length; i++) {
        // set current value on input color
        if (blocks[i].getAttribute('backColor')) {
            let result = blocks[i].getAttribute('backColor').split(", ");
            inputsEditBlockBackColor[i].value = rgbToHex(parseInt(result[0]), parseInt(result[1]), parseInt(result[2]));
        }
        // add event
        addEventEditBlockBackColor(inputsEditBlockBackColor[i], i);
    }
}

if (inputsEditBlockBackOpacity && inputsEditBlockBackOpacity.length > 0) {
    for (let i = 0; i < inputsEditBlockBackOpacity.length; i++) {
        addEventEditBlockBackOpacity(inputsEditBlockBackOpacity[i], i);
    }
}

/*________________________________________*/
//              BLOCKS BORDER
/*________________________________________*/
const inputsEditBlockBorderWidth = nodeListToArray(document.querySelectorAll('.editBorderWidth input'));
const inputsEditBlockBorderRadius = nodeListToArray(document.querySelectorAll('.editBorderRadius input'));
const inputsEditBlockBorderColor = nodeListToArray(document.querySelectorAll('.editBorderColor input'));

function addEventEditBlockBorderColor(input, index) {
    input.addEventListener(
        'input', function(e) {
            let rgbObject = hexToRgb(e.currentTarget.value);
            let rgbValue = rgbObject.r + ", " + rgbObject.g + ", " + rgbObject.b;
            
            if (rgbValue !== blocksValues[index]['newValues']['borderColor']) {
                let newBorderColor = 'rgb(' + rgbValue + ')';
                blocks[index].style.borderColor = newBorderColor;
                blocksValues[index]['newValues']['borderColor'] = rgbValue;
            }
        }
    );
}

function addEventEditBlockBorderWidth(input, index) {
    input.addEventListener(
        'input', function(e) {
            if (e.currentTarget.value !== blocksValues[index]['newValues']['borderWidth']) {
                blocks[index].style.borderWidth = e.currentTarget.value + "px";
                blocksValues[index]['newValues']['borderWidth'] = e.currentTarget.value;
            }
        }
    );
}

function addEventEditBlockBorderRadius(input, index) {
    input.addEventListener(
        'input', function(e) {
            if (e.currentTarget.value !== blocksValues[index]['newValues']['borderRadius']) {
                blocks[index].style.borderRadius = e.currentTarget.value + "px";
                blocksValues[index]['newValues']['borderRadius'] = e.currentTarget.value;
            }
        }
    );
}

if (inputsEditBlockBorderColor && inputsEditBlockBorderColor.length > 0) {
    for (let i = 0; i < inputsEditBlockBorderColor.length; i++) {
        // set current value on input color
        if (blocks[i].getAttribute('borderColor')) {
            let result = blocks[i].getAttribute('borderColor').split(", ");
            inputsEditBlockBorderColor[i].value = rgbToHex(parseInt(result[0]), parseInt(result[1]), parseInt(result[2]));
        }
        // add event
        addEventEditBlockBorderColor(inputsEditBlockBorderColor[i], i);
    }
}

if (inputsEditBlockBorderWidth && inputsEditBlockBorderWidth.length > 0) {
    for (let i = 0; i < inputsEditBlockBorderWidth.length; i++) {
        addEventEditBlockBorderWidth(inputsEditBlockBorderWidth[i], i);
    }
}

if (inputsEditBlockBorderRadius && inputsEditBlockBorderRadius.length > 0) {
    for (let i = 0; i < inputsEditBlockBorderRadius.length; i++) {
        addEventEditBlockBorderRadius(inputsEditBlockBorderRadius[i], i);
    }
}

/*_____________________________*/
//        SAVE BLOCKS
/*_____________________________*/
let blocksValues = [];
let buttonsSaveBlock = nodeListToArray(document.querySelectorAll('.saveBlockChange'));
let elemWaitBlock = nodeListToArray(document.querySelectorAll('.savingBlock'));
let elemSaveBlockSuccess = nodeListToArray(document.querySelectorAll('.saveBlockSuccess'));
let elemSaveBlockFailure = nodeListToArray(document.querySelectorAll('.saveBlockFailure'));

for (let i = 0; i < blocks.length; i++) {
    // setup array blocks values
    blocksValues.push(
        {
            'values': getBlockValues(i), 
            'newValues': getBlockValues(i)
        }
    );
}

function getBlockValues(index) {
    let rgbObjectBackColor = hexToRgb(inputsEditBlockBackColor[index].value);
    let backColorValue = rgbObjectBackColor.r + ", " + rgbObjectBackColor.g + ", " + rgbObjectBackColor.b;
    let rgbObjectBorderColor = hexToRgb(inputsEditBlockBorderColor[index].value);
    let borderColorValue = rgbObjectBorderColor.r + ", " + rgbObjectBorderColor.g + ", " + rgbObjectBorderColor.b;

    return {
        'size': sizeIntValueToString(inputsEditBlockSize[index].value), 
        'color': backColorValue, 
        'opacity': inputsEditBlockBackOpacity[index].value, 
        'borderWidth': inputsEditBlockBorderWidth[index].value, 
        'borderColor': borderColorValue, 
        'borderRadius': inputsEditBlockBorderRadius[index].value
    }
}

function getBlockData(currValue, newValue, index) {
    let data = new FormData();
    data.append('idBlock', blocks[index].getAttribute('idBlock'));

    if (currValue['size'] !== newValue['size']) {
        data.append('size', newValue['size']);
    }

    if (currValue['color'] !== newValue['color']) {
        data.append('color', newValue['color']);
    }

    if (currValue['opacity'] !== newValue['opacity']) {
        data.append('opacity', newValue['opacity']);
    }

    if (currValue['borderWidth'] !== newValue['borderWidth']) {
        data.append('borderWidth', newValue['borderWidth']);
    }

    if (currValue['borderColor'] !== newValue['borderColor']) {
        data.append('borderColor', newValue['borderColor']);
    }

    if (currValue['borderRadius'] !== newValue['borderRadius']) {
        data.append('borderRadius', newValue['borderRadius']);
    }

    return data;
}

function saveBlockValues(index) {
    let data = getBlockData(blocksValues[index]['values'], blocksValues[index]['newValues'], index);
    let url = 'index.php?action=updateCvBlock';
    buttonsSaveBlock[index].classList.add('hide');
    elemWaitBlock[index].classList.remove('hide');
    let timeout = null;

    ajaxPost(url, data, function(response) {
        if (response.length > 0 && response === 'true') {
            elemWaitBlock[index].classList.add('hide');
            addEventQuickCloseSaveBlockResult(elemSaveBlockSuccess[index], index, timeout, 'success');
            elemSaveBlockSuccess[index].classList.remove('hide');

            timeout = setTimeout(() => {
                elemSaveBlockSuccess[index].classList.add('hide');
                buttonsSaveBlock[index].classList.remove('hide');
            }, 3000);

            // save new values
            blocksValues[index]['values'] = getBlockValues(index);
        } else {
            elemWaitBlock[index].classList.add('hide');
            addEventQuickCloseSaveBlockResult(elemSaveBlockFailure[index], index, timeout, 'failure');
            elemSaveBlockFailure[index].classList.remove('hide');

            timeout = setTimeout(() => {
                elemSaveBlockFailure[index].classList.add('hide');
                buttonsSaveBlock[index].classList.remove('hide');
            }, 5000);
        }
    });
}

function addEventQuickCloseSaveBlockResult(elem, index, timeout, info) {
    elem.addEventListener(
        'click', function() {
            clearTimeout(timeout);

            if (info === "success") {
                elemSaveBlockSuccess[index].classList.add('hide');
                buttonsSaveBlock[index].classList.remove('hide');
            } else {
                elemSaveBlockFailure[index].classList.add('hide');
                buttonsSaveBlock[index].classList.remove('hide');
            }
        }
    , {'once':true});
}

function addEventSaveBlock(input, index) {
    input.addEventListener(
        'click', function() {
            saveBlockValues(index);
        }
    );
}

if (buttonsSaveBlock !== null && buttonsSaveBlock.length > 0) {
    for (let i = 0; i < buttonsSaveBlock.length; i++) {
        addEventSaveBlock(buttonsSaveBlock[i], i);
    }
}

/*_____________________________*/
//    BUTTON DELETE SECTION
/*_____________________________*/
let buttonsDeleteBlock = nodeListToArray(document.querySelectorAll('.deleteBlock'));

function addEventDeleteBlock(input, index) {
    input.addEventListener(
        'click', function() {
            let url = 'index.php?action=deleteBlock&ownerId=' + ownerId + '&blockId=' + blocks[index].getAttribute('idBlock');
            linkDeleteBlock.href = url;
            modal.style.display = "flex";
            modalDeleteBlock.style.display = "flex";
            displayedModal = modalDeleteBlock;
        }
    );
}

if (buttonsDeleteBlock !== null && buttonsDeleteBlock.length > 0) {
    for (let i = 0; i < buttonsDeleteBlock.length; i++) {
        addEventDeleteBlock(buttonsDeleteBlock[i], i);
    }
}

/*_____________________________*/
//    EDIT TABS BLOCK ORDER
/*_____________________________*/
let chevronsBlockUp = nodeListToArray(document.querySelectorAll('.editBlockOrder .fa-chevron-up'));
let chevronsBlockDown = nodeListToArray(document.querySelectorAll('.editBlockOrder .fa-chevron-down'));

function addEventOrderBlockUp(input, index) {
    input.addEventListener(
        'click', function(e) {
            e.stopPropagation();
            let idSection = blocks[index].getAttribute('idSection');
            let idBlock = e.target.parentNode.parentNode.getAttribute('idBlock');
            let currentOrder = parseInt(e.target.parentNode.parentNode.style.order);

            let currentTab = document.querySelector('#editCv .belongToSection' + idSection + ' > div[style*="order: ' + currentOrder + '"]');
            let currentBlock = document.querySelector('#editCv .cvBlock[idBlock="' + idBlock + '"]');
            let nextTab = document.querySelector('#editCv .belongToSection' + idSection + ' > div[style*="order: ' + (currentOrder+1) + '"]');
            if (!nextTab) {
                return;
            }
            let idNextBlock = nextTab.getAttribute('idBlock');
            let nextBlock = document.querySelector('#editCv .cvBlock[idBlock="' + idNextBlock + '"]');
            
            if (currentBlock !== null && currentTab !== null && nextBlock !== null && nextTab !== null) {
                let url = "index.php?action=changeCvBlockOrder&value=up&currentOrder=" + currentOrder + "&idOwner=" + ownerId + "&idSection=" + idSection;

                ajaxGet(url, function(response) {
                    if (response.length > 0 && response === "true") {
                        currentBlock.style.order = (currentOrder + 1);
                        nextBlock.style.order = currentOrder;
                        currentTab.style.order = (currentOrder + 1);
                        nextTab.style.order = currentOrder;
                    }
                });
            }
        }
    );
}

function addEventOrderBlockDown(input, index) {
    input.addEventListener(
        'click', function(e) {
            e.stopPropagation();
            let idSection = blocks[index].getAttribute('idSection');
            let idBlock = e.target.parentNode.parentNode.getAttribute('idBlock');
            let currentOrder = parseInt(e.target.parentNode.parentNode.style.order);

            let currentTab = document.querySelector('#editCv .belongToSection' + idSection + ' > div[style*="order: ' + currentOrder + '"]');
            let currentBlock = document.querySelector('#editCv .cvBlock[idBlock="' + idBlock + '"]');
            let previousTab = document.querySelector('#editCv .belongToSection' + idSection + ' > div[style*="order: ' + (currentOrder-1) + '"]');
            if (!previousTab) {
                return;
            }
            let idPreviousBlock = previousTab.getAttribute('idBlock');
            let previousBlock = document.querySelector('#editCv .cvBlock[idBlock="' + idPreviousBlock + '"]');

            if (currentBlock !== null && currentTab !== null && previousBlock !== null && previousTab !== null) {
                let url = "index.php?action=changeCvBlockOrder&value=down&currentOrder=" + currentOrder + "&idOwner=" + ownerId + "&idSection=" + idSection;

                ajaxGet(url, function(response) {
                    if (response.length > 0 && response === "true") {
                        currentBlock.style.order = currentOrder - 1;
                        previousBlock.style.order = currentOrder;
                        currentTab.style.order = currentOrder - 1;
                        previousTab.style.order = currentOrder;
                    }
                });
            }
        }
    );
}

if (chevronsBlockUp !== null && chevronsBlockUp.length > 0) {
    for (let i = 0; i < chevronsBlockUp.length; i++) {
        addEventOrderBlockDown(chevronsBlockUp[i], i);
    }
}

if (chevronsBlockDown !== null && chevronsBlockDown.length > 0) {
    for (let i = 0; i < chevronsBlockDown.length; i++) {
        addEventOrderBlockUp(chevronsBlockDown[i], i);
    }
}

/*_____________________________*/
//         ADD BLOCK
/*_____________________________*/
let buttonsAddNewBlock = nodeListToArray(document.querySelectorAll('.addNewBlock'));

function getNewBlock(idBlock, idSection, order) {
    let newBlock = newElem({
        'type': 'div', 
        'classes': ['cvBlock', 'blockLarge'], 
        'elemStyle': {
            'style': ['order', 'backgroundColor'], 
            'values': [order, 'rgba(0, 0, 0, 0.5)']
        }, 
        'elemAttributes': {
            'attribute': ['idblock', 'idsection'], 
            'values': [idBlock, idSection]
        }, 
        'innerHTML': '<p>Nouveau bloc</p>'
    });
    blocks.push(newBlock);

    return newBlock;
}

function getNewTabBlock(idBlock, order) {
    let elemChevronUp = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-chevron-up']
    });
    chevronsBlockUp.push(elemChevronUp);

    let elemChevronDown = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-chevron-down']
    });
    chevronsBlockDown.push(elemChevronDown);

    let divChevrons = newElem({
        'type': 'div', 
        'classes': ['editBlockOrder'], 
        'childs': [elemChevronUp, elemChevronDown]
    });

    let elemP = newElem({
        'type': 'p', 
        'textContent': 'Bloc ' + order
    });

    let elemTab = newElem({
        'type': 'div', 
        'elemAttributes': {
            'attribute': ['idblock'], 
            'values': [idBlock]
        }, 
        'elemStyle': {
            'style': ['order'], 
            'values': [order]
        }, 
        'childs': [document.createTextNode(""), elemP, divChevrons]
    });
    tabsBlock.push(elemTab);

    return elemTab;
}

function getElemEditBlockContent(idBlock) {
    let elemButton = newElem({
        'type': 'button', 
        'elemAttributes': {
            'attribute': ['idblock'], 
            'values': [idBlock]
        }, 
        'textContent': 'Modifier le contenu du bloc'
    });
    inputsEditBlockContent.push(elemButton);

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Contenu'
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditBlockContent'], 
        'childs': [elemSpan, elemButton]
    });
}

function getElemEditBlockSize() {
    let elemInput = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'min', 'max', 'step', 'value'], 
            'values': ['range', 1, 3, 1, 3]
        }
    });
    inputsEditBlockSize.push(elemInput);

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Taille du bloc'
    });

    let elemP = newElem({
        'type': 'p', 
        'childs': [elemSpan, elemInput]
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditSize'], 
        'childs': [elemP]
    });
}

function getElemEditBlockBackground() {
    let elemInput1 = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type'], 
            'values': ['color']
        }
    });
    inputsEditBlockBackColor.push(elemInput1);

    let elemLabel1 = newElem({
        'type': 'label', 
        'textContent': 'Couleur'
    });

    let elemP1 = newElem({
        'type': 'p', 
        'classes': ['editBackgroundColor'], 
        'childs': [elemLabel1, elemInput1]
    });

    let elemInput2 = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'min', 'max', 'step', 'value'], 
            'values': ['range', 0, 1, 0.1, 0.5]
        }
    });
    inputsEditBlockBackOpacity.push(elemInput2);

    let elemLabel2 = newElem({
        'type': 'label', 
        'textContent': 'Transparence'
    });

    let elemP2 = newElem({
        'type': 'p', 
        'classes': ['editBackgroundOpacity'], 
        'childs': [elemLabel2, elemInput2]
    });

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Apparence du fond'
    });

    return newElem({
        'type': 'div', 
        'childs': [elemSpan, elemP2, elemP1]
    });
}

function getElemEditBlockBorder() {
    let elemInput1 = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type'], 
            'values': ['color']
        }
    });
    inputsEditBlockBorderColor.push(elemInput1);

    let elemLabel1 = newElem({
        'type': 'label', 
        'textContent': 'Couleur'
    });

    let elemP1 = newElem({
        'type': 'p', 
        'classes': ['editBorderColor'], 
        'childs': [elemLabel1, elemInput1]
    });

    let elemInput2 = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'min', 'max', 'step', 'value'], 
            'values': ['range', 0, 25, 1, 0]
        }
    });
    inputsEditBlockBorderRadius.push(elemInput2);

    let elemLabel2 = newElem({
        'type': 'label', 
        'textContent': 'Arrondie des coins'
    });

    let elemP2 = newElem({
        'type': 'p', 
        'classes': ['editBorderRadius'], 
        'childs': [elemLabel2, elemInput2]
    });

    let elemInput3 = newElem({
        'type': 'input', 
        'elemAttributes': {
            'attribute': ['type', 'min', 'max', 'step', 'value'], 
            'values': ['range', 0, 5, 1, 0]
        }
    });
    inputsEditBlockBorderWidth.push(elemInput3);

    let elemLabel3 = newElem({
        'type': 'label', 
        'textContent': 'Taille'
    });

    let elemP3 = newElem({
        'type': 'p', 
        'classes': ['editBorderWidth'], 
        'childs': [elemLabel3, elemInput3]
    });

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['orang'], 
        'textContent': 'Bordure du bloc'
    });

    return newElem({
        'type': 'div', 
        'classes': ['blockEditBackgroundBorder'], 
        'childs': [elemSpan, elemP3, elemP2, elemP1]
    });
}

function getElemSaveBlock() {
    let elemP = newElem({
        'type': 'p', 
        'classes': ['saveBlockFailure', 'red', 'hide'], 
        'textContent': 'Certaines modifications n\'ont pas pu être enregistrées, réessayez ou rechargez la page'
    });
    elemSaveBlockFailure.push(elemP);

    let elemI = newElem({
        'type': 'i', 
        'classes': ['fas', 'fa-check', 'saveBlockSuccess', 'green', 'hide']
    });
    elemSaveBlockSuccess.push(elemI);

    let elemSpan = newElem({
        'type': 'span', 
        'classes': ['savingBlock', 'orang', 'hide'], 
        'textContent': '. . .'
    });
    elemWaitBlock.push(elemSpan);

    let elemButton = newElem({
        'type': 'button', 
        'classes': ['saveBlockChange'], 
        'textContent': 'Enregistrer les modifications'
    });
    buttonsSaveBlock.push(elemButton);

    return newElem({
        'type': 'div', 
        'classes': ['blockSaveBlock'], 
        'childs': [elemButton, elemSpan, elemI, elemP]
    });
}

function getElemDeleteBlock() {
    let elemButton = newElem({
        'type': 'button', 
        'classes': ['deleteBlock', 'red'], 
        'textContent': 'Supprimer le bloc'
    });
    buttonsDeleteBlock.push(elemButton);

    return newElem({
        'type': 'div', 
        'classes': ['blockDeleteBlock'], 
        'childs': [elemButton]
    });
}

function getNewTabContentBlock(idBlock) {
    let tabContentBlock = newElem({
        'type': 'div', 
        'classes': ['contentTabEditBlock', 'hide'], 
        'childs': [
            getElemEditBlockContent(idBlock), 
            getElemEditBlockSize(), 
            getElemEditBlockBackground(), 
            getElemEditBlockBorder(), 
            getElemSaveBlock(), 
            getElemDeleteBlock()
        ]
    });
    tabsBlockContent.push(tabContentBlock);

    return tabContentBlock;
}

function addAllEventForNewBlock(index) {
    addEventOrderBlockDown(chevronsBlockUp[index], index);
    addEventOrderBlockUp(chevronsBlockDown[index], index);
    addEventToggleTabBlock(tabsBlock[index], index);
    addEventEditBlockContent(inputsEditBlockContent[index], index);
    addEventEditBlockSize(inputsEditBlockSize[index], index);
    addEventEditBlockBackColor(inputsEditBlockBackColor[index], index);
    addEventEditBlockBackOpacity(inputsEditBlockBackOpacity[index], index);
    addEventEditBlockBorderColor(inputsEditBlockBorderColor[index], index);
    addEventEditBlockBorderWidth(inputsEditBlockBorderWidth[index], index);
    addEventEditBlockBorderRadius(inputsEditBlockBorderRadius[index], index);
    addEventSaveBlock(buttonsSaveBlock[index], index);
    addEventDeleteBlock(buttonsDeleteBlock[index], index);
}

function addEventAddNewBlock(input, index) {
    input.addEventListener(
        'click', function() {
            input.classList.add('hide');
            let idSection = tabs[index].getAttribute('idSection');
            let url = 'index.php?action=addNewCvBlock&idOwner=' + ownerId + '&idSection=' + idSection;
            
            ajaxGet(url, function(response) {
                if (response.length > 0 && response !== 'false') {
                    response = JSON.parse(response);
                    let idnewBlock = response.idBlock;
                    let countBlock = response['countBlock'];

                    let newBlock = getNewBlock(idnewBlock, idSection, countBlock);
                    let newBlockTab = getNewTabBlock(idnewBlock, countBlock);
                    let newBlockTabContent = getNewTabContentBlock(idnewBlock);
                    let indexBlock = blocks.length - 1;
                    addAllEventForNewBlock(indexBlock);

                    blocksValues.push({
                        'values': getBlockValues(indexBlock), 
                        'newValues': getBlockValues(indexBlock)
                    });

                    sections[index].childNodes[1].appendChild(newBlock);
                    blocksTabsBlock[index].appendChild(newBlockTab);
                    blockTabsContent.appendChild(newBlockTabContent);

                    if (blocks.length < 6) {
                        input.classList.remove('hide');
                    }
                }
            });
        }
    );
}

if (buttonsAddNewBlock !== null && buttonsAddNewBlock.length > 0) {
    for (let i = 0; i < buttonsAddNewBlock.length; i++) {
        addEventAddNewBlock(buttonsAddNewBlock[i], i);
    }
}

/*_____________________________*/
//           MODAL
/*_____________________________*/
let modal = document.getElementById('modal');
const buttonsCloseModal = document.querySelectorAll('.closeModal');
let displayedModal = null;

for (let i = 0; i < buttonsCloseModal.length; i++) {
    buttonsCloseModal[i].addEventListener(
        'click', function() {
            if (displayedModal !== null) {
                displayedModal.style.display = "none";
                displayedModal = null;
            }

            modal.style.display = "none";
        }
    );
}

const modalDeleteSection = document.getElementById('confirmDeleteSection');
const modalDeleteBlock = document.getElementById('confirmDeleteBlock');
const linkDeleteSection = document.getElementById('linkDeleteInModal');
const linkDeleteBlock = document.getElementById('linkDeleteBlockInModal');

/*________________________________________*/
//              BLOCKS CONTENT
/*________________________________________*/
const inputsEditBlockContent = nodeListToArray(document.querySelectorAll('.blockEditBlockContent button'));
const modalTinyMce = document.getElementById('modalTinyMce');
const linkConfirmTinyMce = document.getElementById('linkConfirmTinyMce');

function addEventEditBlockContent(input, index) {
    input.addEventListener(
        'click', function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
            if (modalTinyMce.classList.contains('blockSmall')) {
                modalTinyMce.classList.remove('blockSmall');
            } else if (modalTinyMce.classList.contains('blockMedium')) {
                modalTinyMce.classList.remove('blockMedium');
            } else if (modalTinyMce.classList.contains('blockLarge')) {
                modalTinyMce.classList.remove('blockLarge');
            }
            modalTinyMce.classList.add('block' + ucFirst(blocksValues[index]['newValues']['size']));
            modalTinyMce.style.display = 'flex';
            displayedModal = modalTinyMce;
            modalTinyMce.action = 'index.php?action=updateCvBlockContent&idBlock=' + blocks[index].getAttribute('idBlock') + '&idOwner=' + ownerId;
            tinyMCE.get('tinyMCEtextarea').setContent(blocks[index].innerHTML);
        }
    );
}

if (inputsEditBlockContent !== null && inputsEditBlockContent.length > 0) {
    for (let i = 0; i < inputsEditBlockContent.length; i++) {
        addEventEditBlockContent(inputsEditBlockContent[i], i);
    }
}