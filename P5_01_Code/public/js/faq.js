let arrayElemH2 = document.querySelectorAll('#summary > h2');
let arrayElemI = document.querySelectorAll('#summary > h2 > i');
let arrayElemUl = document.querySelectorAll('#summary > ul');
let arrayElemA = document.querySelectorAll('#summary li > a');

function toggleList(list, iconeArrow, previousIconeArrow = null, nextIconeArrow = null)
{
    let toggledH2 = iconeArrow.parentNode;
    let previousH2 = null
    if (previousIconeArrow !== null) {
        previousH2 = previousIconeArrow.parentNode;
    }
    let nextH2 = null
    if (nextIconeArrow !== null) {
        nextH2 = nextIconeArrow.parentNode;
    }
    if (iconeArrow.classList.contains('fa-sort-down')) {
        // open
        iconeArrow.classList.remove('fa-sort-down');
        iconeArrow.classList.add('fa-sort-up');
        toggledH2.classList.add('categorySummaryOpen');
        if (previousH2 !== null) {
            previousH2.classList.add('bottomRadius');
        }
        if (nextH2 !== null) {
            nextH2.classList.add('topRadius');
        }
        list.style.display = 'block';
    } else {
        // close
        iconeArrow.classList.remove('fa-sort-up');
        iconeArrow.classList.add('fa-sort-down');
        toggledH2.classList.remove('categorySummaryOpen');
        if (previousH2 !== null) {
            previousH2.classList.remove('bottomRadius');
        }
        if (nextH2 !== null) {
            nextH2.classList.remove('topRadius');
        }
        list.style.display = 'none';
    }
}
function blink(elem)
{
    elem.style.color = '#ff5000';
    elem.style.opacity = '0.9';
    setTimeout(function(){
        elem.style.color = '#CF8B3F';
        elem.style.opacity = '1';
    }, 600);
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
        toggleList(arrayElemUl[i], arrayElemI[i], arrayElemI[i-1], arrayElemI[i+1]);
    });
}
for (let i=0; i < arrayElemA.length; i++) {
    arrayElemA[i].addEventListener('click', function() {
        setTimeout(function(){
            checkUrl();
        }, 300);
    });
}