let arrayElemH2 = document.querySelectorAll('#summary > h2');
let arrayElemI = document.querySelectorAll('#summary > h2 > i');
let arrayElemUl = document.querySelectorAll('#summary > ul');
let arrayElemA = document.querySelectorAll('#summary li > a');

function toggleList(list, iconeArrow)
{
    if (iconeArrow.classList.contains('fa-sort-down')) {
        iconeArrow.classList.remove('fa-sort-down');
        iconeArrow.classList.add('fa-sort-up');
        list.style.display = 'block';
    } else {
        iconeArrow.classList.remove('fa-sort-up');
        iconeArrow.classList.add('fa-sort-down');
        list.style.display = 'none';
    }
}
function blink(elem)
{
    elem.style.backgroundColor = '#bb0b0b';
    elem.style.opacity = '0.4';
    setTimeout(function(){
        elem.style.backgroundColor = '#161617';
        elem.style.opacity = '1';
    }, 300);
}
function checkUrl()
{
    let anchor = window.location.href.split('#')[1];
    if (anchor !== undefined) {
        blink(document.getElementById(anchor));
    }
}

window.addEventListener('load', function() {
    checkUrl();
});

for (let i=0; i < arrayElemH2.length; i++) {
    arrayElemH2[i].addEventListener('click', function() {
        toggleList(arrayElemUl[i], arrayElemI[i]);
    });
}
for (let i=0; i < arrayElemA.length; i++) {
    arrayElemA[i].addEventListener('click', function() {
        setTimeout(function(){
            checkUrl();
        }, 300);
    });
}