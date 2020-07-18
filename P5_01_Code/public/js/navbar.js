if (document.getElementById('pseudo')) {
    let menuNavbar = document.getElementById('menuNavbar');
    let contentMenuNavbar = menuNavbar.children[0];
    let pseudo = document.getElementById('pseudo');
    let marginPseudo = parseInt(getComputedStyle(pseudo).marginLeft);
    let icone = document.querySelector('#pseudo i');
    let rect = icone.getBoundingClientRect();

    window.addEventListener(
        "load", function () {
            menuNavbar.style.right = (window.innerWidth - (rect.x + rect.width + (marginPseudo*2))) + 'px';
        }
    );

    window.addEventListener(
        "resize", function () {
            rect = icone.getBoundingClientRect();
            //i don't know why but menuNavbar loose "marginPseudo"px on resize. This is why i do marginPseudo*3, and it fit perfectly
            menuNavbar.style.right = (window.innerWidth - (rect.x + rect.width + (marginPseudo*3))) + 'px';
        }
    );

    pseudo.addEventListener(
        "click", function () {
            if (icone.classList.contains('fa-sort-down')) {
                icone.classList.replace('fa-sort-down', 'fa-sort-up');
                contentMenuNavbar.style.top = '0px';
                menuNavbar.style.height = 'auto';
            } else {
                icone.classList.replace('fa-sort-up', 'fa-sort-down');
                contentMenuNavbar.style.top = '-300px';
                setTimeout(function () {
                    menuNavbar.style.height = '0px';
                }, 200);
            }
        }
    );
}