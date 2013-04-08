function cnt(w,x){
var y=w.value;
var r = 0;
a=y.replace(/\s/g,' ');
a=a.split(',');
for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
x.value=r;
}


function limitText(limitField, limitCount, limitNum) {
if (limitField.value.length > limitNum) {
limitField.value = limitField.value.substring(0, limitNum);
} else {
limitCount.value = limitNum - limitField.value.length;
}
}


function checkMaxSelected (select, maxSelected, displ_error_nummaxcat) {
    if (!select.storeSelections) {
        select.storeSelections = new Array(select.options.length);
        select.optionsSelected = 0;
    }

    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].selected && !select.storeSelections[i]) {
            if (select.optionsSelected < maxSelected) {
                select.storeSelections[i] = true;
                select.optionsSelected++;
            }
            else {
                alert(displ_error_nummaxcat + maxSelected);
                select.options[i].selected = false;
            }
        }
        else if (!select.options[i].selected && select.storeSelections[i]) {
            select.storeSelections[i] = false;
            select.optionsSelected--;
        }
    }
}


///////////////////////
// DISPLAY LOADER

	function getScrollTop() {
		if ( document.documentElement.scrollTop )
			return document.documentElement.scrollTop;
		return document.body.scrollTop;
	}

	function showNotify( str ) {
		var elem = document.getElementById('status_notification');
		elem.style.display = 'block';
		elem.style.visibility = 'visible';

		if ( elem.currentStyle && elem.currentStyle.position == 'absolute' ) {
			elem.style.top = getScrollTop();
		}

		elem.innerHTML = str;
	}

	function hideNotify() {
		var elem = document.getElementById('status_notification');
		elem.style.display = 'none';
		elem.style.visibility = 'hidden';
	}

	window.onscroll = function () {
		var elem = document.getElementById('status_notification');
		if ( !elem.currentStyle || elem.currentStyle.position != 'absolute' ) {
			window.onscroll = null;
		} else {
			window.onscroll = function () { elem.style.top = getScrollTop(); };
			document.getElementById('status_notification').style.top = getScrollTop();
		}
	}

	

// END - DISPLAY LOADER
///////////////////////

