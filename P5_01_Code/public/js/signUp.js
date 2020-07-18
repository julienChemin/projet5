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