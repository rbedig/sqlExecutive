/* 
* SQL Executive
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: sqlExec.js
*/

// makes the ajax request for SQL query
function executeQuery() {

	setModalMessage('executing query', 'cancel');
	document.getElementById('result').innerHTML = '';

	// push query onto qHistory stack
	var addqHistory = new Array();
	qHistory.push(addqHistory);
	if (qHistory.length > 10) {
		qHistory.shift();
	}
	// reset qHistory pointer to most recent (this) query
	qHistoryPtr = qHistory.length - 1;
	setqHistoryButtons();
	saveFields(qHistory[qHistoryPtr]);

	var data = 'driver=' + encodeURIComponent(document.getElementById('driver').value);
	data += '&host=' + encodeURIComponent(document.getElementById('host').value);
	data += '&user=' + encodeURIComponent(document.getElementById('user').value);
	data += '&password=' + encodeURIComponent(document.getElementById('password').value);
	data += '&database=' + encodeURIComponent(document.getElementById('database').value);
	data += '&timeout=' + encodeURIComponent(document.getElementById('timeout').value);
	data += '&memory=' + encodeURIComponent(document.getElementById('memory').value);
	data += '&sql=' + encodeURIComponent(document.getElementById('sql').value);
	
	xhr.open('POST', 'sqlExec.php', true);
	xhr.onreadystatechange = function() { returnResult(xhr) };
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(data);
	
	return false;
}

// the query result is returned as formatted html, ready to be displayed
function returnResult(result) {

	if (result.readyState == 3) {
		// the result data is downloading, and the cancel button will no longer respond
		setModalMessage('building result set', false);
	}
	else if (result.readyState == 4) {
		if ( (result.status >= 200 && result.status < 300) || (result.status == 304) ) {
			document.getElementById('result').innerHTML = result.responseText;
		}
		else {
			document.getElementById('result').innerHTML = '<div class="resultText error"> Server Error: ' + result.status + ', ' + result.statusText;
		}
		removeModalMessage();
	}
}

function setModalMessage(text, style) {
	if (!userFocusElement) {
		userFocusElement = document.activeElement;
		userFocusElement.blur();
	}

	document.getElementById('modalText').textContent = text;
	var cancelButton = document.getElementById('modalCancel');
	if (style == 'cancel') {
		cancelButton.style.display = 'inline-block';
	} 
	else {
		cancelButton.style.display = 'none';
	}
	var okButton = document.getElementById('modalOk');
	if (style == 'ok') {
		okButton.style.display = 'inline-block';
	} 
	else {
		okButton.style.display = 'none';
	}	
	document.getElementById('modalLightBox').style.display = 'block';
}

function removeModalMessage() {
	document.getElementById('modalLightBox').style.display = 'none';
	if (userFocusElement) {
		userFocusElement.focus();
		userFocusElement = null;
	}
}

function cancelQuery() {
	xhr.abort();
	document.getElementById('result').innerHTML = '<div class="resultText">Query canceled</div>';
	removeModalMessage();
}

function getqHistory() {
	// dir must be either 1 or -1
	dir = parseInt(this.value);

	// just in case the array index requested is out of range
	if ((qHistoryPtr + dir > -1) && (qHistoryPtr + dir < qHistory.length)) {
		// move the pointer
		qHistoryPtr += dir;
		setqHistoryButtons();
		// get fields from qHistory stack
		restoreFields(qHistory[qHistoryPtr])
	}
	return false;	
}

function saveFields(flds) {
		
	if (document.getElementById('mysql').selected == true) {
		flds['driver'] = 'mysql';
	}
	else  if (document.getElementById('mssql').selected == true) {
		flds['driver'] = 'mssql';
	}
	else {
		flds['driver'] = '';
	}

	flds['host'] = document.getElementById('host').value;
	flds['user'] = document.getElementById('user').value;
	flds['password'] = document.getElementById('password').value;
	flds['database'] = document.getElementById('database').value;
	flds['timeout'] = document.getElementById('timeout').value;
	flds['memory'] = document.getElementById('memory').value;
	flds['sql'] = document.getElementById('sql').value;
}


function restoreFields(flds) {
	mysql = document.getElementById('mysql');
	if (flds['driver'] == 'mysql') {
		mysql.setAttribute('selected', '');
	}
	else {
		mysql.removeAttribute('selected');
	}
	mssql = document.getElementById('mssql');
	if (flds['driver'] == 'mssql') {
		mssql.setAttribute('selected', '');
	}
	else {
		mssql.removeAttribute('selected');
	}	

	document.getElementById('host').value = flds['host'];
	document.getElementById('user').value = flds['user'];
	document.getElementById('password').value = flds['password'];
	document.getElementById('database').value = flds['database'];
	document.getElementById('timeout').value = flds['timeout'];
	document.getElementById('memory').value = flds['memory'];
	document.getElementById('sql').value = flds['sql'];
}

function setqHistoryButtons() {
	var back = document.getElementById('back');
	var forward = document.getElementById('forward');

	if (qHistoryPtr > 0) {
		back.removeAttribute('class');
		back.removeAttribute('disabled');
	}
	else {
		back.setAttribute('class', 'disabled');
		back.setAttribute('disabled', '');
	}

	if (qHistoryPtr < (qHistory.length -1 )) {
		forward.removeAttribute('class');
		forward.removeAttribute('disabled');
	}
	else {
		forward.setAttribute('class', 'disabled');
		forward.setAttribute('disabled', '');
	}	
}

function forceOpen() {
	// click event on hidden file input element to bring up dialog
	document.getElementById('file').click();
	return false;
}

function readFile(evt) {
	var file = evt.target.files[0];
	if (!file) {
		return;
	}

	if (file.size > 20971520) { // 20MB limit
		setModalMessage(file.name + ' is over the size limit of 20MB', 'ok');
		return;
	}
	setModalMessage('reading file ' + file.name, false);
	document.getElementById('sql').value ='';
	var reader = new FileReader();
	reader.onload = function(evt) {
		document.getElementById('sql').value = evt.target.result;
		removeModalMessage();
	}
	reader.readAsText(file);
}

function save() {
	// abort post for empty query
	if (!document.getElementById('sql').value) {
		return false;
	}
}

function catchKeys(evt) {

    var key = evt.which;

    var shift = evt.shiftKey;
    var ctrl = evt.ctrlKey;
    var alt = evt.altKey;
    
    if (key == 9 && !shift && !ctrl && !alt) { // simple TAB
    	// moves to the first control, conveniently, the execute button
    	document.getElementById('execute').focus();
    	evt.preventDefault();
        return false;
    }
    if (key == 13 && !shift && ctrl && !alt) { // CTRL-ENTER
    	// executes the query
    	document.getElementById('execute').click();
    	evt.preventDefault();
        return false;
    }
}

function init() {
	var modalCancel = document.getElementById('modalCancel');
	modalCancel.onclick = cancelQuery;

	var modalOk = document.getElementById('modalOk');
	modalOk.onclick = removeModalMessage;	

	var execute = document.getElementById('execute');
	execute.onclick = executeQuery;

	var back = document.getElementById('back');
	back.onclick = getqHistory;

	var forward = document.getElementById('forward');
	forward.onclick = getqHistory;	

	if (window.File && window.FileReader && window.FileList && window.Blob) {
		// the HTML5 file APIs are fully supported in this browser
		// show open and save buttons

		var open = document.getElementById('open');
		open.removeAttribute('class'); // remove class .hidden
		open.onclick = forceOpen;
		document.getElementById('file').addEventListener('change', readFile, false);

		var save = document.getElementById('save');
		save.removeAttribute('class'); // remove class .hidden
		save.onclick = save;		
	}

	document.getElementById('sql').addEventListener('keydown', catchKeys, false);
}

// yes Dorothy, these are global variables

var qHistory = [];
var qHistoryPtr = -1;

var xhr = new XMLHttpRequest();

var userFocusElement = null;

window.onload = init;