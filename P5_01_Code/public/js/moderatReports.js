let btnReportPost = document.getElementById('btnReportPost');
let btnReportComment = document.getElementById('btnReportComment');
let btnReportOther = document.getElementById('btnReportOther');
let blockContent = document.querySelector('#content');
let blockNoContent = document.getElementById('noContent');
let content = document.querySelector('#content tbody');
let paging = document.getElementById('paging');
let elemCount = "";
let elem = '';
let url = '';
let limit = 10;
let pageDisplay = {'i' : 0, 'elem' : ""};

function getOffset() {
	return (pageDisplay.i - 1) * limit;
}
function addEventPaging(elemLi, i) {
	elemLi.addEventListener('click', function() {
		if (pageDisplay.elem !== elemLi) {
			pageDisplay.elem.style.color = 'white';
			pageDisplay.elem = elemLi;
			pageDisplay.elem.style.color = '#CF8B3F';
			pageDisplay.i = i;
			fillContentReport();
		}
	});
}
function addEventDeleteReport(elemI, idElem, idUser = 0) {
	elemI.addEventListener('click', function() {
		url = 'indexAdmin.php?action=deleteReport&elem=' + elem + '&idElem=' + idElem + '&idUser=' + idUser;
		ajaxGet(url, function(response) {
			if (response === '1') {
				let tableBody = document.querySelector('tbody');
				tableBody.removeChild(elemI.parentNode.parentNode);
				if (tableBody.childNodes.length <= 0) {
					blockNoContent.style.display = 'flex';
					blockContent.style.display = "none";
				}
			}
		});
	});
}
function fillContentReport() {
	url = 'indexAdmin.php?action=getReports&elem=' + elem + '&offset=' + getOffset();
	ajaxGet(url, function(response) {
		if (JSON.parse(response).length > 0 && response !== 'false') {
			blockNoContent.style.display = 'none';
			blockContent.style.display = "flex";
			content.innerHTML = "";
			let rows = JSON.parse(response);
			rows.forEach(row => {
				let elemTr = document.createElement('tr');
				let tdName = document.createElement('td');
				let elemLinkName = document.createElement('a');
				elemLinkName.href = 'index.php?action=userProfile&userId=' + row['idUser'];
				elemLinkName.textContent = row['userName'];
				tdName.appendChild(elemLinkName);
				let tdContent = document.createElement('td');
				tdContent.innerHTML = row['content'];
				let elemDate = document.createElement('p');
				elemDate.textContent = row['dateReport'];
				tdContent.appendChild(elemDate);
				let tdAction = document.createElement('td');
				let elemI = document.createElement('i');
				if (elem === 'post' || elem === 'comment') {
					row['idPost'] !== undefined ? idElem = row['idPost'] : idElem = row['idComment'];
					let elemLinkElem = document.createElement('a');
					elemLinkElem.href = 'indexAdmin.php?action=moderatReports&elem=' + elem + '&idElem=' + idElem;
					elemI.classList.add('far');
					elemI.classList.add('fa-eye');
					elemLinkElem.appendChild(elemI);
					tdAction.appendChild(elemLinkElem);
				} else if (elem === 'other') {
					addEventDeleteReport(elemI, row['id']);
					elemI.classList.add('fas');
					elemI.classList.add('fa-trash-alt');
					tdAction.appendChild(elemI);
				}
				elemTr.appendChild(tdName);
				elemTr.appendChild(tdContent);
				elemTr.appendChild(tdAction);
				content.appendChild(elemTr);
			});
		} else {
			blockContent.style.display = "none";
			blockNoContent.style.display = 'flex';
		}
	});
}
function fillContentReportFromElem(idElem) {
	url = 'indexAdmin.php?action=getReportsFromElem&elem=' + elem + '&idElem=' + idElem;
	ajaxGet(url, function(response) {
		if (JSON.parse(response).length > 0 && response !== 'false') {
			blockNoContent.style.display = 'none';
			blockContent.style.display = "flex";
			content.innerHTML = "";
			let rows = JSON.parse(response);
			rows.forEach(row => {
				let elemTr = document.createElement('tr');
				let tdName = document.createElement('td');
				let elemLinkName = document.createElement('a');
				elemLinkName.href = 'index.php?action=userProfile&userId=' + row['idUser'];
				elemLinkName.textContent = row['userName'];
				tdName.appendChild(elemLinkName);
				let tdContent = document.createElement('td');
				tdContent.innerHTML = row['content'];
				let elemDate = document.createElement('p');
				elemDate.textContent = row['dateReport'];
				tdContent.appendChild(elemDate);
				let tdDeleteReport = document.createElement('td');
				let elemI = document.createElement('i');
				elemI.classList.add('fas');
				elemI.classList.add('fa-trash-alt');
				addEventDeleteReport(elemI, idElem, row['idUser']);
				tdDeleteReport.appendChild(elemI);
				elemTr.appendChild(tdName);
				elemTr.appendChild(tdContent);
				elemTr.appendChild(tdDeleteReport);
				content.appendChild(elemTr);
			});
		} else {
			blockContent.style.display = "none";
			blockNoContent.style.display = 'flex';
		}
	});
}

if (document.querySelector('.moderatReportsFromElem') === null) {
	//all reports
	btnReportPost.addEventListener('click', function() {
		if (elem !== 'post') {
			//display block content, set focus btn
			btnReportOther.style.color = 'white';
			btnReportOther.style.backgroundColor = 'transparent';
			btnReportComment.style.color = 'white';
			btnReportComment.style.backgroundColor = 'transparent';
			btnReportPost.style.color = '#CF8B3F';
			btnReportPost.style.backgroundColor = '#161617';
			elem = 'post';
			pageDisplay.i = 1;
			//paging
			paging.innerHTML = "";
			url = 'indexAdmin.php?action=getCountReports&elem=' + elem;
			ajaxGet(url, function(response) {
				if (response.length > 0) {
					elemCount = parseInt(JSON.parse(response));
					let nbPage = Math.ceil(elemCount / limit);
					for (let i=1; i < nbPage + 1; i++) {
						let elemLi = document.createElement('li');
						elemLi.textContent = i;
						if (i === 1) {
							pageDisplay.elem = elemLi;
							elemLi.style.color = '#CF8B3F';
						}
						addEventPaging(elemLi, i);
						paging.appendChild(elemLi);
					}
				}
			});
			//fill content
			fillContentReport();
		}
	});

	btnReportComment.addEventListener('click', function() {
		if (elem !== 'comment') {
			//display block content, set focus btn
			btnReportPost.style.color = 'white';
			btnReportPost.style.backgroundColor = 'transparent';
			btnReportOther.style.color = 'white';
			btnReportOther.style.backgroundColor = 'transparent';
			btnReportComment.style.color = '#CF8B3F';
			btnReportComment.style.backgroundColor = '#161617';
			elem = 'comment';
			pageDisplay.i = 1;
			//paging
			paging.innerHTML = "";
			url = 'indexAdmin.php?action=getCountReports&elem=' + elem;
			ajaxGet(url, function(response) {
				if (response.length > 0) {
					elemCount = parseInt(JSON.parse(response));
					let nbPage = Math.ceil(elemCount / limit);
					for (let i=1; i < nbPage + 1; i++) {
						let elemLi = document.createElement('li');
						elemLi.textContent = i;
						if (i === 1) {
							pageDisplay.elem = elemLi;
							elemLi.style.color = '#CF8B3F';
						}
						addEventPaging(elemLi, i);
						paging.appendChild(elemLi);
					}
				}
			});
			//fill content
			fillContentReport();
		}
	});

	btnReportOther.addEventListener('click', function() {
		if (elem !== 'other') {
			//display block content, set focus btn
			btnReportPost.style.color = 'white';
			btnReportPost.style.backgroundColor = 'transparent';
			btnReportComment.style.color = 'white';
			btnReportComment.style.backgroundColor = 'transparent';
			btnReportOther.style.color = '#CF8B3F';
			btnReportOther.style.backgroundColor = '#161617';
			elem = 'other';
			pageDisplay.i = 1;
			//paging
			paging.innerHTML = "";
			url = 'indexAdmin.php?action=getCountReports&elem=' + elem;
			ajaxGet(url, function(response) {
				if (response.length > 0) {
					elemCount = parseInt(JSON.parse(response));
					let nbPage = Math.ceil(elemCount / limit);
					for (let i=1; i < nbPage + 1; i++) {
						let elemLi = document.createElement('li');
						elemLi.textContent = i;
						if (i === 1) {
							pageDisplay.elem = elemLi;
							elemLi.style.color = '#CF8B3F';
						}
						addEventPaging(elemLi, i);
						paging.appendChild(elemLi);
					}
				}
			});
			//fill content
			fillContentReport();
		}
	});
} else {
	let btnDeleteReportsFromElem = document.getElementById('btnDeleteReportsFromElem');
	let textBtnDelete = document.querySelector('#btnDeleteReportsFromElem > p');
	let btnConfirmDeleteAll = document.querySelector('#btnDeleteReportsFromElem > i');
	let arrUrlVar = [];
	//reports from one comment / post
	window.addEventListener('load', function() {
		let url = window.location.search.split('&');
		for (let i=1; i<url.length; i++) {
			let splitUrl = url[i].split('=');
			arrUrlVar[splitUrl[0]] = splitUrl[1];
		}
		elem = arrUrlVar['elem'];
		fillContentReportFromElem(arrUrlVar['idElem']);
	});

	btnDeleteReportsFromElem.addEventListener('click', function() {
		if (btnConfirmDeleteAll.style.right !== '-60px') {
			btnConfirmDeleteAll.style.right = '-60px';
			textBtnDelete.textContent = 'Confirmez ->';
			setTimeout(function() {
				btnConfirmDeleteAll.style.right = '5px';
				textBtnDelete.textContent = 'Définir ce contenu comme traité';
			}, 2000);
		}
	});

	btnConfirmDeleteAll.addEventListener('click', function() {
		url = 'indexAdmin.php?action=deleteReportsFromElem&elem=' + elem + '&idElem=' + arrUrlVar['idElem'];
		ajaxGet(url, function(response) {
			if (response === '1') {
				document.querySelector('tbody').innerHTML = '';
				blockContent.style.display = 'none';
				blockNoContent.style.display = 'flex';
			}
		})
	});
}
