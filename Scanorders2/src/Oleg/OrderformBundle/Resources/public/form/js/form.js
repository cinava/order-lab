/**
 * User: oli2002
 * Date: 8/14/13
 * Time: 12:36 PM
 */

//add all element to listeners again, the same as in ready
function initAdd() {

    expandTextarea();

//    $(".combobox").combobox();
    regularCombobox();

    initDatepicker();

    //clean validation elements
    //console.log("clean initAdd");
    cleanValidationAlert();

    fieldInputMask();

    setResearch();

    setEducational();

}

//confirm delete
function deleteItem(id) {
    //check if this is not the last element in the parent tree
    var thisId = "formpanel_"+id;
    //console.log("replace thisId="+thisId);
    var thisParent = $("#"+thisId).parent();
    //console.log("replace thisParent="+thisParent.attr('id'));
    var elements = thisParent.children( ".panel" );
    //console.log("replace elements.length="+elements.length);

    if( confirm("This auction will affect this element and all its children. Are you sure?") ) {

        if( elements.length > 1 ) {

            $('#formpanel_'+id).remove();

            //check if it is only one element left
            if( (elements.length-1) == 1 ) {
                //change "delete" to "clear"
                var element = thisParent.children( ".panel" );
                //console.log("rename element="+element.attr('id'));
                var delBtnToReplace = element.children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
                //console.log("rename delBtnToReplace="+delBtnToReplace.attr('id'));
                delBtnToReplace.html('Clear');
            }

        } else {
            //clear the form and all children
            var ids = id.split("_");
            //alert("You can't delete only one left " + ids[0]);

            //console.log("id="+id);
            //console.log("rename elements.length="+elements.length);
            addSameForm(ids[0], ids[1], ids[2], ids[3], ids[4], ids[5], ids[6], ids[7], ids[7], ids[8]);

            $('#formpanel_'+id).remove();

            //make sure to rename delete button to "Clear" if it is only one element left
            if( elements.length == 1 ) {
                //change "delete" to "clear"
                var element = thisParent.children( ".panel" );
                var delBtnToReplace = element.children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
                delBtnToReplace.html('Clear');
            }
        }
    }

    //clean validation elements
    cleanValidationAlert();

    return false;
}

//main input form from html button: add parent form (always type='multi')
//TODO: remove add parent and use only addChildForms in loop including adding parent
function addSameForm( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    var uid = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid+"_"+slideid+"_"+scanid+"_"+stainid;  //+"_"+diffdiag+"_"+specstain+"_"+image;

    //console.log("addSameForm="+name+"_"+uid);

    //prepare form ids and pass it as array
    //increment by 1 current object id
    var btnids = getIds(name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid);
    var id = btnids['id'];
    var idsorig = btnids['orig'];
    var ids = btnids['ids'];
    var idsm = btnids['idsm'];
    var idsp = btnids['idsp'];

    //place the form in the html page after the last similar element: use parent
    //    var holder = "#formpanel_"+name+"_"+uid;
    //formpanel_slide_0_0_0_0_0_0_0_0
    var partialId = "formpanel_"+name+"_"+btnids['partialId'];
    var elements = $('[id^='+partialId+']');
    var holder = elements.eq(elements.length-1);
    //console.log( "id="+holder.attr('id') );

    //console.log("holder="+holder);

    //attach form
    $(holder).after( getForm( name, id, idsorig, ids, idsm ) );

    if( name == "slide" ) {
        //addCollFieldFirstTime( "relevantScans", ids );
        //addDiffdiagFieldFirstTime( name, ids );
        //addCollFieldFirstTime( "specialStains", ids );
    }

    //bind listener to the toggle button
    bindToggleBtn( name + '_' + ids.join("_") );
    bindDeleteBtn( name + '_' + ids.join("_") );
    //originOptionMulti(ids);
    diseaseTypeListener();
    initComboboxJs(ids);
    initAdd();
    addKeyListener();

    //create children nested forms
    //var nameArray = ['patient', 'procedure', 'accession', 'part', 'block', 'slide', 'stain_scan' ];
    var nameArray = ['patient', 'procedure', 'part', 'block', 'slide' ];
    var length = nameArray.length
    var index = nameArray.indexOf(name);
    //console.log("index="+index+" len="+length);
    var parentName = name;
    for (var i = index+1; i < length; i++) {
        //console.log("=> name="+nameArray[i]);

        if( nameArray[i] == 'stain_scan' ) {
            addChildForms( parentName, 'stain', nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
            addChildForms( parentName, 'scan', nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
        } else {
            addChildForms( parentName, nameArray[i], nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
        }

    }

    //replace all "add" buttons of this branch with "add" buttons for the next element. use parent and children
    var thisId = "formpanel_"+name+"_"+ids.join("_");
    //console.log("thisId="+thisId);
    var thisParent = $("#"+thisId).parent();
    var childrens = thisParent.children( ".panel" );

    //var addbtn = '<button id="form_add_btn_' + name + '_' + ids.join("_") + '" type="button" class="testjs add_form_btn btn btn-xs btn_margin" onclick="addSameForm(\'' + name + '\''+ ',' + ids.join(",") + ')">Add</button>';
    var addbtn =  getHeaderAddBtn( name, ids );

    for (var i = 0; i < childrens.length; i++) {
        var addBtnToReplace = childrens.eq(i).children(".panel-heading").children(".form-btn-options").children(".add_form_btn");
        addBtnToReplace.replaceWith( addbtn );

        //rename "clear" to "Delete"
        if( childrens.length > 1 ) {
            //console.log("childrens.length="+childrens.length);
            var delBtnToRename = childrens.eq(i).children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
            delBtnToRename.html('Delete');
        }
    }

    initAllElements();
}

//add children forms triggered by parent form
function addChildForms( parentName, name, prevName, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    //idsu: +1 for the parent object (parentName)
    //attach to previous object (prevName)
    var btnids = getIds( parentName, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
    var idsorig = btnids['orig'];
    var ids = btnids['ids'];
    var idsm = btnids['idsm'];
    var id = btnids['id'];
    id = id - 1;
    var idsu = ids.join("_");

    var uid = prevName+"_"+idsu;
    var holder = "#form_body_"+uid;
    //console.debug(name+": ADD CHILDS to="+holder+" uid="+idsu);

    //attach children form
    $(holder).append( getForm( name, id, idsorig, ids, idsm  ) );

    if( name == "slide" ) {
        //addCollFieldFirstTime( "relevantScans", ids );
        //addDiffdiagFieldFirstTime( name, ids );
        //addCollFieldFirstTime( "specialStains", ids );
    }
    
    bindToggleBtn( name + '_' + ids.join("_") );
    bindDeleteBtn( name + '_' + ids.join("_") );
    //originOptionMulti(ids);
    diseaseTypeListener();
    initComboboxJs(ids);
    initAdd();
    addKeyListener();
}

//input: current form ids
function getForm( name, id, idsorig, ids, idsm ) {

    var deleteStr = "Clear";
    var idsu = ids.join("_");
    var idsc = ids.join(",");
    //increment by 1 current object id
    var formbody = getFormBody( name, ids[0], ids[1], ids[2], ids[3], ids[4], ids[5], ids[6], ids[7] );

    //console.log("getForm: "+name+"_"+", id="+id+", ids="+ids+', idsm='+idsm);

    if( name == "scan" || name == "stain" ) {
        var addbtn = "";
        var deletebtn = "";
        //var itemCount = (id+2);
    } else {
        var addbtn = getHeaderAddBtn( name, ids );
        var deletebtn = getHeaderDeleteBtn( name, ids, deleteStr );
    }

    //get itemCount from partialId
    var itemCount = getIdByName(name,ids) + 1;
    //console.log('itemCount='+itemCount);

    var title = name;
    if( name == "procedure" ) {
        title = "accession";
    }

    var formhtml =
        '<div id="formpanel_' +name + '_' + idsu + '" class="panel panel-'+name+' panel-multi-form">' +
            '<div class="panel-heading">' +

                '<button id="form_body_toggle_'+ name + '_' + idsu +'" type="button"' +
                    'class="btn btn-default btn-xs form_body_toggle_btn glyphicon glyphicon-folder-open pull-left"' +
                    'data-toggle="collapse" data-target="#form_body_'+name+'_'+idsu+'">'+
                '</button>'+

//            '<button id="form_body_toggle_'+ name + '_' + idsu +'" type="button" class="btn btn-default btn-xs form_body_toggle_btn glyphicon glyphicon-folder-open black" data-toggle="collapse" data-target="#form_body_'+name+'_'+idsu+'"></button>' +
//            '&nbsp;' +
//            '<div class="element-title">' + capitaliseFirstLetter(title) + ' ' + itemCount + '</div>' +
                '<h4 class="panel-title element-title">' + capitaliseFirstLetter(title) + ' ' + itemCount + '</h4>' +
                '<div class="form-btn-options">' +
                    addbtn +
                    deletebtn +
                '</div>' +
                '<div class="clearfix"></div>' +
            '</div>' +  //panel-heading
            '<div id="form_body_' + name + '_' + idsu + '" class="panel-body collapse in">' +
                formbody +
            '</div>' +
            '</div>';

    return formhtml;
}

function getFormBody( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    //console.log("name="+name+",patient="+patientid+ ",procedure="+procedureid+",accession="+accessionid+",part="+partid+",block="+blockid+",slide="+slideid);

    var collectionHolder =  $('#form-prototype-data');

    // Get the data-prototype explained earlier
    var prototype = collectionHolder.data('prototype-'+name);
    //console.log("prototype="+prototype);

    //console.log("before replace patient...");
    var newForm = prototype.replace(/__patient__/g, patientid);
    //console.log("before replace procedure... NewForm="+newForm);
    newForm = newForm.replace(/__procedure__/g, procedureid);
    newForm = newForm.replace(/__accession__/g, accessionid);
    newForm = newForm.replace(/__part__/g, partid);
    newForm = newForm.replace(/__block__/g, blockid);
    newForm = newForm.replace(/__slide__/g, slideid);
    newForm = newForm.replace(/__scan__/g, scanid);
    newForm = newForm.replace(/__stain__/g, stainid);

    //newForm = newForm.replace(/__clinicalHistory__/g, 0);   //add only one clinical history to the new form
    newForm = newForm.replace(/__paper__/g, 0);   //add only one clinical history to the new form

    //replace origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag with correct ids
    //origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag
    var newOriginId = "origin_option_multi_patient_"+patientid+"_procedure_"+procedureid+"_accession_"+accessionid+"_part_"+partid+"_origintag";
    newForm = newForm.replace(/origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag/g, newOriginId);

    newForm = newForm.replace(/__[a-zA-Z0-9]+__/g, 0); //replace everything what is left __*__ by 0 => replace all array fields by 0

    //remove required
    newForm = newForm.replace(/required="required"/g, "");

    //console.log("newForm="+newForm);

    return newForm;
}

function getHeaderAddBtn( name, ids ) {
    var addbtn = '<button id="form_add_btn_' + name + '_' + ids.join("_") + '" type="button"'+
                    'class="btn btn-default btn-xs add_form_btn pull-right"' +
                    'onclick="addSameForm(\'' + name + '\''+ ',' + ids.join(",") + ')">Add'+
                '</button>';
    return addbtn;
}

function getHeaderDeleteBtn( name, ids, deleteStr ) {
    var deletebtn = '<button id="delete_form_btn_' + name + '_' + ids.join("_") + '" type="button"'+ ' style="margin-right:1%;" ' +
                        'class="btn btn-danger btn-xs delete_form_btn pull-right">' + deleteStr +
                    '</button>';
    return deletebtn;
}

//Helpers
function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getIds( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {
    var id = 0;
    var nextName = "";

    var patientidm = patientid;
    var procedureidm = procedureid;
    var accessionidm = accessionid;
    var partidm = partid;
    var blockidm = blockid;
    var slideidm = slideid;
    var scaniddm = scanid;
    var stainidm = stainid;

    var patientidp = patientid;
    var procedureidp = procedureid;
    var accessionidp = accessionid;
    var partidp = partid;
    var blockidp = blockid;
    var slideidp = slideid;
    var scaniddp = scanid;
    var stainidp = stainid;

    var partialId = "";

    var orig = [patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid];

    switch(name)
    {
        case "patient":
            patientidm = patientid-1;
            patientid++;
            patientidp = patientid+1;
            id = patientid;
            nextName = "procedure";
            partialId = "";
            break;
        case "procedure":
            procedureidm = procedureid-1;
            procedureid++;
            procedureidp = procedureid+1;
            id = procedureid;
            nextName = "accession";
            partialId = patientid;
            break;
        case "accession":
            accessionidm = accessionid-1;
            accessionid++;
            accessionidp = accessionid+1;
            id = accessionid;
            nextName = "part";
            partialId = patientid+"_"+procedureid;
            break;
        case "part":
            partidm = partid-1;
            partid++;
            partidp = partid+1;
            id = partid;
            nextName = "block";
            partialId = patientid+"_"+procedureid+"_"+accessionid;
            break;
        case "block":
            blockidm = blockid-1;
            blockid++;
            blockidp = blockid+1;
            id = blockid;
            nextName = "slide";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid;
            break;
        case "slide":
            slideidm = slideid-1;
            slideid++;
            slideidp = slideid+1;
            id = slideid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        case "scan":
            scaniddm = scanid-1;
            scanid++;
            scaniddp = scanid+1;
            id = scanid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        case "stain":
            stainidm = stainid-1;
            stainid++;
            stainidp = stainid+1;
            id = stainid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        default:
            id = 0;
    }

    var idsArray = [patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid];
    var idsArrayM = [patientidm, procedureidm, accessionidm, partidm, blockidm, slideidm, scaniddm, stainidm];
    var idsArrayP = [patientidp, procedureidp, accessionidp, partidp, blockidp, slideidp, scaniddp, stainidp];

    var res_array = {
        'id' : id,
        'orig' : orig,
        'ids' : idsArray,
        'idsm' : idsArrayM,
        'idsp' : idsArrayP,
        'nextName' : nextName,
        'partialId' : partialId
    };

    return res_array;
}

function getIdByName( name, ids ) {
    var id = -1;

    switch(name)
    {
        case "patient":
            id = ids[0];
            break;
        case "procedure":
            id = ids[1];
            break;
        case "accession":
            id = ids[2];
            break;
        case "part":
            id = ids[3];
            break;
        case "block":
            id = ids[4];
            break;
        case "slide":
            id = ids[5];
            break;
        default:
            id = 0;
    }

    return id;
}

//bind listener to the toggle button
function bindToggleBtn( uid ) {
    //console.log("toggle uid="+uid);
    $('#form_body_toggle_'+ uid).on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        //e.preventDefault();
        var className = $(this).attr("class");
        var id = this.id;
        //alert(className);

//        if( className == 'form_body_toggle_btn glyphicon glyphicon-folder-open') {
        if( $(this).hasClass('glyphicon-folder-open') ) {
            $("#"+id).removeClass('glyphicon-folder-open');
            $("#"+id).addClass('glyphicon-folder-close');
        } else {
            $("#"+id).removeClass('glyphicon-folder-close');
            $("#"+id).addClass('glyphicon-folder-open');
        }
    });
}
//multy form delete
function bindDeleteBtn( uid ) {
    //console.log('delete uid='+uid);
    $('#delete_form_btn_'+uid).on('click', function(e) {
        var id = uid;
        //alert("clicked delete!");
        deleteItem(id);
    });
}

function setNavBar() {

    $('ul.li').removeClass('active');

    var full = window.location.pathname;

    var id = 'scanorderhome';

    if( full.indexOf("scan-order/multi-slide-clinical") !== -1 ) {
        id = 'placescanorder';
    }

    if ( full.indexOf("scan-order/multi-slide-educational") !== -1 ) {
        id = 'placescanorder';
    }

    if ( full.indexOf("scan-order/multi-slide-research") !== -1 ) {
        id = 'placescanorder';
    }

    if( full.indexOf("scan-order/one-slide") !== -1 ) {
        id = 'placescanorder';
    }

    if( full.indexOf("scan-order/multi-slide-table-view") !== -1 ) {
        id = 'placescanorder';
    }

    if( full.indexOf("my-scan-orders") !== -1 ) {
        id = 'myscanorders';
    }

    //Admin
    if( full.indexOf("/user/listusers") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/admin/") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/incoming-scan-orders") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/access-requests") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/account-requests") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/listusers") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/users/") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/event-log") !== -1 ) {
        id = 'admin';
    }
    if( full.indexOf("/settings") !== -1 ) {
        id = 'admin';
    }
    
    if( full.indexOf("/users/") !== -1 ) {
        if( $('#nav-bar-admin').length > 0 ) {
           id = 'admin';
        } else {
           id = 'user';
        }
    }

    //console.log("id="+id);
    //console.info("full="+window.location.pathname+", id="+id + " ?="+full.indexOf("multi/clinical"));

    $('#nav-bar-'+id).addClass('active');
}


function priorityOption() {

    if( $('#priority_option').is(':visible') ) {
        $('#priority_option').collapse('hide');
    }

    $('#oleg_orderformbundle_orderinfotype_priority').change(function(e) {
        e.preventDefault();
        $('#priority_option').collapse('toggle');
    });

    var checked = $('#oleg_orderformbundle_orderinfotype_priority').find('input[type=radio]:checked').val();
    //console.log("checked="+checked);
    if( checked == 'Stat' ) {
        $('#priority_option').collapse('show');
    }
}

//function onCwid(){
//    window.open("http://weill.cornell.edu/its/identity-security/identity/cwid/")
//}

function expandTextarea() {
//    var $element = $('.textarea').get(0);
    var elements = document.getElementsByClassName('textarea');
    for (var i = 0; i < elements.length; ++i) {
        var element = elements[i];
        element.addEventListener('keyup', function() {
            this.style.overflow = 'hidden';
            this.style.height = 0;
            var newH = this.scrollHeight + 10;
            //console.log("cur h="+this.style.height+", newH="+newH);
            this.style.height = newH + 'px';
        }, false);
    }

}


function initDatepicker() {

    var datepickers = $('.input-group.date');

    if( cicle != "show" ) {
        initSingleDatepicker( datepickers );
    
        //make sure the masking is clear when input is cleared by datepicker
        datepickers.datepicker().on("clearDate", function(e){
                var inputField = $(this).find('input');
                //printF(inputField,"Clear input:");
                clearErrorField( inputField );
        });
    }

}

//use "eternicode/bootstrap-datepicker": "dev-master"
//process Datepicker: add or remove click event to the field and its siblings calendar button
//element: null or jquery object. If null, all element with class datepicker will be assign to calendar click event
//remove null or "remove"
function processDatepicker( element, remove ) {

    if( cicle != "show" ) {

        //replace element (input field) by a parent with class .input-group .date
        if( !element ) {
            element = $('.input-group.date');
        } else {
            element = element.closest('.input-group.date');
        }

        //var btn = element.parent().find('.input-group-addon');
        //printF(btn,"Datepicker Btn:");

        if( remove == "remove" ) {
            //printF(element,"Remove datepicker:");
            element.datepicker("remove");

            //make sure the masking is clear when input is cleared by datepicker
            var inputField = element.find('input');
            clearErrorField( inputField );

            //btn.attr( "disabled", true );
        } else {
            initSingleDatepicker(element);
            //btn.attr( "disabled", false );
        }

    }
}

function initSingleDatepicker( datepickerElement ) {
    datepickerElement.datepicker({
        autoclose: true,
        clearBtn: true,
        //todayBtn: "linked",
        todayHighlight: true
    });
}

function setResearch() {
    //get value of project title field on change
    $('.combobox-research-projectTitle').on("change", function(e) {
        //console.log('listener: project Title changed');
        getSetTitle();
        getOptionalUserResearch();
    });
}

function setEducational() {
    //get value of project title field on change
    $('.combobox-educational-courseTitle').on("change", function(e) {
        //console.log('listener: course Title changed');
        getLessonTitle();
        getOptionalUserEducational();
    });
}

function inArrayCheck( arr, needle ) {
    //console.log('len='+arr.length+", needle: "+needle+"?="+parseInt(needle));

    if( needle == '' ) {
        return -1;
    }

    if( needle == parseInt(needle) ) {
        return needle;
    }

    for( var i = 0; i < arr.length; i++ ) {
        //console.log(arr[i]['text']+'?='+needle);
        if( arr[i]['text'] === needle ) {
            return arr[i]['id'];
        }
    }
    return -1;
}


function printF(element,text) {
    var str = "id="+element.attr("id") + ", class=" + element.attr("class")
    if( text ) {
        str = text + " : " + str;
    }
    console.log(str);
}


