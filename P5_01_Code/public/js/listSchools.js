let blockSchools = document.querySelectorAll('.blockSchool > div');
let schoolNames = document.querySelectorAll('.blockSchool h2');
let schoolInformation = document.getElementById('schoolInformation');
let linkSchoolProfile = document.querySelector('#linkSchoolProfile a');
let schoolNameInLink = document.querySelector('#linkSchoolProfile span');
let titlesOfSections = document.querySelectorAll('#schoolInformation > div > h2');
let adminSection = document.getElementById('adminSection');
let moderatorSection = document.getElementById('moderatorSection');
let studentSection = document.getElementById('studentSection');
let focusSchool = null;

function fillUsersSection(users)
{
    adminSection.innerHTML = '';
    moderatorSection.innerHTML = '';
    studentSection.innerHTML = '';
    if (users['admin'].length > 0) {
        setTimeout(function () {
            titlesOfSections[0].style.opacity = '1';
        }, 100);
        users['admin'].forEach(user => {
            insertUser(user, adminSection);
        });
    } else {
        titlesOfSections[0].style.opacity = '0';
    }

    if (users['moderator'].length > 0) {
        setTimeout(function () {
            titlesOfSections[1].style.opacity = '1';
        }, 100);
        users['moderator'].forEach(user => {
            insertUser(user, moderatorSection);
        });
    } else {
        titlesOfSections[1].style.opacity = '0';
    }

    if (users['student'].length > 0) {
        setTimeout(function () {
            titlesOfSections[2].style.opacity = '1';
        }, 100);
        users['student'].forEach(user => {
            insertUser(user, studentSection);
        });
    } else {
        titlesOfSections[2].style.opacity = '0';
    }
}
function insertUser(user, block)
{
    let elemDiv = document.createElement('div');
    let elemA = document.createElement('a');
    elemA.href = 'index.php?action=userProfile&userId=' + user['id'];
    let elemFigure = document.createElement('figure');
    elemFigure.classList.add('figureProfilePicture');
    elemFigure.classList.add('fullWidth');
    let elemDivOnFigure = document.createElement('div');
    let elemImg = document.createElement('img');
    elemImg.src = user['profilePicture'];
    elemDivOnFigure.appendChild(elemImg);
    elemFigure.appendChild(elemDivOnFigure);
    let elemFigCaption = document.createElement('figcaption');
    let elemP = document.createElement('p');
    elemP.textContent = user['name'];
    elemFigCaption.appendChild(elemP);
    elemFigure.appendChild(elemFigCaption);
    elemA.appendChild(elemFigure);
    elemDiv.appendChild(elemA);
    block.appendChild(elemDiv);
}
for (let i=0; i<blockSchools.length; i++) {
    blockSchools[i].addEventListener(
        'click', function () {
            if (focusSchool !== blockSchools[i]) {
                //display informations
                if (schoolInformation.style.display !== 'block') {
                    schoolInformation.style.display = 'block';
                }
                schoolNameInLink.textContent = schoolNames[i].textContent;
                linkSchoolProfile.href = 'index.php?action=schoolProfile&school=' + schoolNames[i].textContent;
                //set focus on clicked school and unset focus on the previous
                if (focusSchool !== null) {
                    focusSchool.style.borderColor = '#161617';
                } else {
                    titlesOfSections.forEach(title => {
                        title.style.opacity = '1';
                    });
                }
                focusSchool = blockSchools[i];
                focusSchool.style.borderColor = '#CF8B3F';
                //get users of this school
                let url = 'index.php?action=getUsersBySchool&school=' + schoolNames[i].textContent;
                ajaxGet(url, function (response) {
                    if (response.length > 0 && response !== 'false') {
                        response = JSON.parse(response);
                        fillUsersSection(response);
                    }
                });
            }
        }
    );
}