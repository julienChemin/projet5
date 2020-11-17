// checkbox to add an affiliation code
let form = document.getElementById('formSignUp');
let checkbox = document.getElementById('isAffiliated');
let input = document.getElementById('postAffiliationCode');

checkbox.addEventListener(
    "change", function () {
        if (this.checked) {
            input.style.height = '50px';
            input.style.margin = "15px";
        
        } else {
            input.style.height = '0px';
            input.style.margin = "0px";
        }
    }
);

// toggle information message depending on the input focused
let inputIdentifier = form.elements.signUpPseudo;
let identifierMsg = document.getElementById('idMsg');
let inputMail = form.elements.signUpMail;
let mailMsg = document.getElementById('mailMsg');
let inputFirstName = form.elements.signUpFirstName;
let inputLastName = form.elements.signUpLastName;
let firstLastNameMsg = document.getElementById('nameMsg');
let inputPassword = form.elements.password;
let inputConfirmPassword = form.elements.confirmPassword;
let passwordMsg = document.getElementById('passwordMsg');
let inputCode = form.elements.affiliationCode;
let codeMsg = document.getElementById('codeMsg');
let lastMsgDisplay = "";

inputIdentifier.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        identifierMsg.classList.remove('hide');
        lastMsgDisplay = identifierMsg;
    }
)
inputMail.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        mailMsg.classList.remove('hide');
        lastMsgDisplay = mailMsg;
    }
)
inputFirstName.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        firstLastNameMsg.classList.remove('hide');
        lastMsgDisplay = firstLastNameMsg;
    }
)
inputLastName.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        firstLastNameMsg.classList.remove('hide');
        lastMsgDisplay = firstLastNameMsg;
    }
)
inputPassword.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        passwordMsg.classList.remove('hide');
        lastMsgDisplay = passwordMsg;
    }
)
inputConfirmPassword.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        passwordMsg.classList.remove('hide');
        lastMsgDisplay = passwordMsg;
    }
)
inputCode.addEventListener(
    "focus", function() {
        if (lastMsgDisplay !== '') {
            lastMsgDisplay.classList.add('hide');
        }
        codeMsg.classList.remove('hide');
        lastMsgDisplay = codeMsg;
    }
)