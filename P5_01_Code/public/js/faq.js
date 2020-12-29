let arrayElemH3 = document.querySelectorAll('#summary > h3');
let arrayElemI = document.querySelectorAll('#summary > h3 > i');
let arrayElemUl = document.querySelectorAll('#summary > ul');
let arrayElemA = document.querySelectorAll('#summary li > a');

function toggleList(list, iconeArrow, previousIconeArrow = null, nextIconeArrow = null)
{
    let toggledH3 = iconeArrow.parentNode;
    let previousH3 = null
    if (previousIconeArrow !== null) {
        previousH3 = previousIconeArrow.parentNode;
    }
    let nextH3 = null
    if (nextIconeArrow !== null) {
        nextH3 = nextIconeArrow.parentNode;
    }
    if (iconeArrow.classList.contains('fa-sort-down')) {
        // open
        iconeArrow.classList.remove('fa-sort-down');
        iconeArrow.classList.add('fa-sort-up');
        toggledH3.classList.add('categorySummaryOpen');
        if (previousH3 !== null) {
            previousH3.classList.add('bottomRadius');
        }
        if (nextH3 !== null) {
            nextH3.classList.add('topRadius');
        }
        list.style.display = 'block';
    } else {
        // close
        iconeArrow.classList.remove('fa-sort-up');
        iconeArrow.classList.add('fa-sort-down');
        toggledH3.classList.remove('categorySummaryOpen');
        if (previousH3 !== null) {
            previousH3.classList.remove('bottomRadius');
        }
        if (nextH3 !== null) {
            nextH3.classList.remove('topRadius');
        }
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

for (let i=0; i < arrayElemH3.length; i++) {
    arrayElemH3[i].addEventListener('click', function() {
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