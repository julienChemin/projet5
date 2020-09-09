let formLogin = document.getElementById("formConnect");
let buttonForgetPassword = document.getElementById("buttonForgetPassword");
let formForgetPassword = document.getElementById("formForgetPassword");
let buttonCancel = document.getElementById("buttonCancel");

if (formLogin) {
    buttonForgetPassword.addEventListener(
        "click", function () {
            formLogin.style.display = "none";
            formForgetPassword.style.display = "block";
        }
    );

    buttonCancel.addEventListener(
        "click", function () {
            formLogin.style.display = "block";
            formForgetPassword.style.display = "none";
        }
    );
}