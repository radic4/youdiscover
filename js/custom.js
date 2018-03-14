jQuery('.multiple-items').slick({
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 2,
    arrows: true,
    dots: true,
    responsive: [{
        breakpoint: 1199,
        settings: {
            slidesToShow: 3,
            slidesToScroll: 1
        }
    },
    {
        breakpoint: 767,
        settings: {
            slidesToShow: 1,
            dots: false,
            arrows: false,
            centerMode: true,
            centerPadding: '50px'
        }
    }]
});


/********************
SELECT IN TO DROPDOWN
********************/

function tamingselect() {
    if (!document.getElementById && !document.createTextNode) {
        return;
    }

    // Classes for the link and the visible dropdown
    var ts_selectclass = 'turnintodropdown'; // class to identify selects
    var ts_listclass = 'turnintoselect'; // class to identify ULs
    var ts_boxclass = 'dropcontainer'; // parent element
    var ts_triggeron = 'activetrigger'; // class for the active trigger link
    var ts_triggeroff = 'trigger'; // class for the inactive trigger link
    var ts_dropdownclosed = 'dropdownhidden'; // closed dropdown
    var ts_dropdownopen = 'dropdownvisible'; // open dropdown
    /*
        Turn all selects into DOM dropdowns
    */
    var count = 0;
    var toreplace = new Array();
    var sels = document.getElementsByTagName('select');
    for (var i = 0; i < sels.length; i++) {
        if (ts_check(sels[i], ts_selectclass)) {
            var hiddenfield = document.createElement('input');
            hiddenfield.name = sels[i].name;
            hiddenfield.type = 'hidden';
            hiddenfield.id = sels[i].id;
            hiddenfield.value = sels[i].options[sels[i].selectedIndex].value;
            sels[i].parentNode.insertBefore(hiddenfield, sels[i])
            var trigger = document.createElement('a');
            ts_addclass(trigger, ts_triggeroff);
            trigger.href = '#';
            trigger.onclick = function() {
                ts_swapclass(this, ts_triggeroff, ts_triggeron)
                ts_swapclass(this.parentNode.getElementsByTagName('ul')[0], ts_dropdownclosed, ts_dropdownopen);
                return false;
            }
            trigger.appendChild(document.createTextNode(sels[i].options[sels[i].selectedIndex].text));
            sels[i].parentNode.insertBefore(trigger, sels[i]);
            var replaceUL = document.createElement('ul');
            for (var j = 0; j < sels[i].getElementsByTagName('option').length; j++) {
                var newli = document.createElement('li');
                var newa = document.createElement('a');
                newli.v = sels[i].getElementsByTagName('option')[j].value;
                newli.elm = hiddenfield;
                newli.istrigger = trigger;
                newa.href = '#';
                newa.appendChild(document.createTextNode(
                    sels[i].getElementsByTagName('option')[j].text));
                newli.onclick = function() {
                    this.elm.value = this.v;
                    ts_swapclass(this.istrigger, ts_triggeron, ts_triggeroff);
                    ts_swapclass(this.parentNode, ts_dropdownopen, ts_dropdownclosed)
                    this.istrigger.firstChild.nodeValue = this.firstChild.firstChild.nodeValue;
                    return false;
                }
                newli.appendChild(newa);
                replaceUL.appendChild(newli);
            }
            ts_addclass(replaceUL, ts_dropdownclosed);
            var div = document.createElement('div');
            div.appendChild(replaceUL);
            ts_addclass(div, ts_boxclass);
            sels[i].parentNode.insertBefore(div, sels[i])
            toreplace[count] = sels[i];
            count++;
        }
    }

    /*Turn all ULs with the class defined above into dropdown navigations*/

    var uls = document.getElementsByTagName('ul');
    for (var i = 0; i < uls.length; i++) {
        if (ts_check(uls[i], ts_listclass)) {
            var newform = document.createElement('form');
            var newselect = document.createElement('select');
            for (j = 0; j < uls[i].getElementsByTagName('a').length; j++) {
                var newopt = document.createElement('option');
                newopt.value = uls[i].getElementsByTagName('a')[j].href;
                newopt.appendChild(document.createTextNode(uls[i].getElementsByTagName('a')[j].innerHTML));
                newselect.appendChild(newopt);
            }
            newselect.onchange = function() {
                window.location = this.options[this.selectedIndex].value;
            }
            newform.appendChild(newselect);
            uls[i].parentNode.insertBefore(newform, uls[i]);
            toreplace[count] = uls[i];
            count++;

        }
    }
    for (i = 0; i < count; i++) {
        toreplace[i].parentNode.removeChild(toreplace[i]);
    }

    function ts_check(o, c) {
        return new RegExp('\\b' + c + '\\b').test(o.className);
    }

    function ts_swapclass(o, c1, c2) {
        var cn = o.className

        if (cn == "trigger selectBtn" || cn == "selectBtn trigger") {
            jQuery('.selectBtn').removeClass('activetrigger');
            jQuery('.selectBtn').addClass('trigger');
        }

        if (cn == "dropdownhidden") {
            jQuery('.form-select ul').removeClass('dropdownvisible');
            jQuery('.form-select ul').addClass('dropdownhidden');
        }

        o.className = !ts_check(o, c1) ? cn.replace(c2, c1) : cn.replace(c1, c2);
    }

    function ts_addclass(o, c) {
        if (!ts_check(o, c)) { o.className += o.className == '' ? c : ' ' + c; }
    }
}

jQuery(document).ready(function() {
    tamingselect();
    jQuery('.trigger').addClass('selectBtn');
});


// Close select on click outside

jQuery(document).mouseup(function(e) {
    var selectForm = jQuery(".form-select");

    if (!selectForm.is(e.target) && selectForm.has(e.target).length === 0) {
        jQuery('.form-select ul').removeClass('dropdownvisible');
        jQuery('.form-select ul').addClass('dropdownhidden');
        jQuery('.selectBtn').removeClass('activetrigger');
        jQuery('.selectBtn').addClass('trigger');
    }

});

/********************
END SELECT IN TO DROPDOWN
********************/


if (jQuery("#slg").length) {

    var count = 1;
    var group = document.getElementById('slg');
    var list_group = group.querySelector('li ul');
    var list_array = group.querySelectorAll('li ul li');
    var search = group.getElementsByTagName('input')[0];

    search.addEventListener('input', function() {
        for (var i = 0; i < list_array.length; i++) {
            matching(list_array[i])
        }
        show_list(list_group);
        key_up_down();
    });

    search.addEventListener('click', function() {
        init_list();
        show_list(list_group);
        key_up_down();
    });

    search.addEventListener('keypress', function() {
        hide_list(list_group)
        init_list();
    });

    function matching(item) {
        var str = new RegExp(search.value, 'gi');
        if (item.innerHTML.match(str)) {
            item.className = 'true'
        } else {
            item.className = 'false';
            count = 0
        }
    }

    function init_list() {
        count = 0;
        for (var i = 0; i < list_array.length; i++) {
            init_item(list_array[i]);
            list_array[i].addEventListener('click', copy_paste);
        }
    }

    function init_item(item) {
        item.className = 'true';
    }

    function copy_paste() {
        search.value = this.innerHTML;
        // todo : check match of list text and input value for .current
        init_list();
        hide_list(list_group);
    }

    function hide_list(eleHide) {
        eleHide.className = 'false';
    }

    function show_list(eleShow) {
        eleShow.className = 'true';
    }

    function key_up_down() {
        var items = group.querySelectorAll('li[class="true"]');
    }

    jQuery(document).mouseup(function(outside) {
        if (outside.target != list_group && outside.target.parentNode != list_group) {
            list_group.className = 'false';
        }
        else {
            list_group.className = 'true';
        }
    });
}

