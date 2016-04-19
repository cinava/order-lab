/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/13/13
 * Time: 5:50 PM
 * To change this template use File | Settings | File Templates.
 */

var _treenodedata = [];

var _commenttype = [];
var _identifiers = [];
var _fellowshiptype = [];
var _researchlabs = [];
var _locations = [];
var _buildings = [];

var _rooms = [];
var _suites = [];
var _floors = [];
var _mailboxes = [];
var _efforts = [];
var _addmintitles = [];
var _apptitles = [];
var _medicaltitles = [];

//trainings 6 from 8
var _residencySpecialtys = [];
var _fellowshipSubspecialtys = [];
var _trainingmajors = [];
var _trainingminors = [];
var _traininghonors = [];
var _fellowshipTitles = [];
var _traininginstitution = [];
var _locationusers = [];
var _jobTitles = [];

//grants
var _sourceorganization = [];
var _grants = [];

var _cities = [];
var _organizations = [];

function initAllComboboxGeneric(newForm) {

    getComboboxGeneric(newForm,'identifierkeytype',_identifiers,false);
    getComboboxGeneric(newForm,'fellowshiptype',_fellowshiptype,false);
    getComboboxGeneric(newForm,'researchlab',_researchlabs,false);
    getComboboxGeneric(newForm,'location',_locations,false,'');
    getComboboxGeneric(newForm,'building',_buildings,false,'');

    getComboboxGeneric(newForm,'room',_rooms,false);
    getComboboxGeneric(newForm,'suite',_suites,false);
    getComboboxGeneric(newForm,'floor',_floors,false);
    getComboboxGeneric(newForm,'mailbox',_mailboxes,false);
    getComboboxGeneric(newForm,'effort',_efforts,false);
    getComboboxGeneric(newForm,'administrativetitletype',_addmintitles,false);
    getComboboxGeneric(newForm,'appointmenttitletype',_apptitles,false);
    getComboboxGeneric(newForm,'medicaltitletype',_medicaltitles,false);

    //trainings
    getComboboxGeneric(newForm,'trainingmajors',_trainingmajors,true);
    getComboboxGeneric(newForm,'trainingminors',_trainingminors,true);
    getComboboxGeneric(newForm,'traininghonors',_traininghonors,true);
    getComboboxGeneric(newForm,'trainingfellowshiptitle',_fellowshipTitles,false);
    getComboboxGeneric(newForm,'traininginstitution',_traininginstitution,false,'');
    getComboboxGeneric(newForm,'locationusers',_locationusers,false,'');
    getComboboxGeneric(newForm,'jobtitle',_jobTitles,false);
    //getComboboxGeneric(newForm,'fellowshipsubspecialty',_fellowshipSubspecialtys,false);

    //grants
    getComboboxGeneric(newForm,'sourceorganization',_sourceorganization,false);
    getComboboxGeneric(newForm,'grant',_grants,false);

    getComboboxGeneric(newForm,'city',_cities,false);
    getComboboxGeneric(newForm,'organization',_organizations,false);

    //getComboboxGeneric(newForm,'userpositions',_userpositions,true);

    setBuidlingListener(newForm);

}


function setElementToId( target, dataarr, setId ) {
    if( typeof dataarr === "undefined" || dataarr == undefined || dataarr.length == 0 ) {
        $(target).select2('data', null);
        return;
    }

    if( typeof setId === "undefined" ) {
        var firstObj = dataarr[0];
        var setId = firstObj.id;
    }

    //console.log("setId="+setId+", target="+target);
    $(target).select2('val', setId);

    return setId;
}

function getDataIdByText(arr,text) {
    var id = null;
    for(var k in arr) {
        if( arr[k].text == text ) {
            id = arr[k].id;
            break;
        }
    }
    return id;
}


//#############  compositetree  ##############//

//this function is used for form compositetree hierarchy using select2, not jstree
function getComboboxCompositetree(holder) {

    if( typeof cycle === 'undefined' ) {
        var cycle = 'edit';
    }
    //console.log('inst cycle='+cycle);

    var targetid = ".ajax-combobox-compositetree";
    if( $(targetid).length == 0 ) {
        return;
    }
    if( typeof holder !== 'undefined' && holder.length > 0 ) {
        targetid = holder.find(targetid);
        if( targetid.length == 0 ) {
            return;
        }
    }

    $(targetid).each( function(e){

        var entityName =$(this).attr("data-compositetree-classname");
        //console.log('entityName='+entityName);

        var bundleName =$(this).attr("data-compositetree-bundlename");
        //console.log('bundleName='+bundleName);

        //var entityName = 'Institution';
        _treenodedata[bundleName+entityName] = [];

        getComboboxSingleCompositetree($(this),bundleName,entityName);
    });
}


function getComboboxSingleCompositetree(comboboxEl,bundleName,entityName) {

    //console.log('getComboboxSingleCompositetree:');
    //console.log(comboboxEl);

    var thisid = comboboxEl.val();
    //console.log('thisid='+thisid);

    if( !thisid ) {
        thisid = 0;
    }


    if( _treenodedata[bundleName+entityName].hasOwnProperty(thisid) ) {
        populateComboboxCompositetreeData(bundleName,entityName,comboboxEl,_treenodedata[bundleName+entityName][thisid]);
        return;
    }


    getChildrenByParent(bundleName,entityName,comboboxEl,thisid,null).
    then(function (optionData) {
        populateComboboxCompositetreeData(bundleName,entityName,comboboxEl,optionData);
    });

}

function populateComboboxCompositetreeData(bundleName,entityName,comboboxEl,optionData) {
    //console.log('populate combobox data:');
    //console.log(optionData);

    var rowElHtml = comboboxEl.closest('.row')[0].outerHTML;

    //console.log('populate combobox');
    //target, data, placeholder, multipleFlag, filter
    populateSelectCombobox( comboboxEl, optionData, "Select an option" );

    if( !comboboxEl.hasClass('show-as-single-node') ) {
        comboboxTreeListener( comboboxEl, bundleName, entityName, rowElHtml );
        comboboxEl.trigger('change');
        setParentComboboxree(comboboxEl, bundleName, entityName, rowElHtml);
    }
}

function getChildrenByParent( bundleName, entityName, thiselement, thisid, parentid, opt ) {

    return Q.promise(function(resolve, reject, treedata) {

        //console.log('entityName='+entityName+', thisid='+thisid+", parentid="+parentid);

        //do nothing if new element was enetered. In this case pid will be a string with a new element name.
        if( !isInt(thisid) && !isInt(parentid) ) {
            //console.log('thisid and pid not int');
            reject('id and pid null');
            return;
        }

        //console.log('_treenodedata:');
        //console.log(_treenodedata);
        if( thisid != null && _treenodedata[bundleName+entityName].hasOwnProperty(thisid) ) {
            //console.log('_treenodedata exists for thisid='+thisid);
            resolve(_treenodedata[bundleName+entityName][thisid]);
            return;
        }

        //console.log('get treenode data by ajax');

        var opt = 'combobox';
        //var treeHolder = thiselement.closest('.composite-tree-holder');
    //    if( treeHolder.hasClass('compositetree-with-userpositions') ) {
    //        opt = opt + ',userpositions';
    //    }

        //current userid
        var userid = null;
        var dataElement = document.getElementById("form-prototype-data");
        //console.log(dataElement);
        if( dataElement ) {
            userid = dataElement.getAttribute('data-userid');
            //console.log('userid='+userid);
        }

        //employees_get_compositetree
        var treeUrl = Routing.generate('employees_get_composition_tree');
        //console.log('treeUrl='+treeUrl);
        treeUrl = treeUrl + '?thisid=' + thisid + '&id=' + parentid + '&bundlename=' + bundleName + '&classname=' + entityName + '&opt=' + opt + '&userid=' + userid + '&type=default';
        //console.log('user-selectAjax.js: final treeUrl='+treeUrl);

        if( typeof cycle === 'undefined' ) {
            cycle = 'new';
        }
        treeUrl = treeUrl + "&cycle="+cycle;

        if( !_ajaxTimeout ) {
            var _ajaxTimeout = 300000; //300 000 => 300sec
        }

//        console.log('continue:');
//        console.log('ajaxTimeout='+_ajaxTimeout);
//        console.log('asyncflag='+asyncflag);

        try {
            $.ajax({
                url: treeUrl,
                timeout: _ajaxTimeout,
                async: asyncflag
            }).success(function(data) {
                //console.log(data);
                _treenodedata[bundleName+entityName][thisid] = data;
                resolve(data);
            }).error(function() {
                //console.log('error getting nodes');
                reject('error getting nodes');
            });
        }
        catch(err) {
            console.log("ajax tree retrival failed err="+err);
            throw new Error("ajax tree retrival failed err="+err);
        }

    });
}


//function getComboboxCommentType(holder) {
//
//    setCommentTypeTreeChildren(holder);
//
//    var targetid = ".ajax-combobox-commenttype";
//
//    if( $(targetid).length == 0 ) {
//        return;
//    }
//
//    if( typeof holder !== 'undefined' && holder.length > 0 ) {
//        targetid = holder.find(targetid);
//
//        if( targetid.length == 0 )
//            return;
//    }
//
//    var url = getCommonBaseUrl("util/common/"+"commenttype","employees"); //always use "employees" to get commenttype
//
//    if( _commenttype.length == 0 ) {
//        $.ajax({
//            url: url,
//            timeout: _ajaxTimeout,
//            async: asyncflag
//        }).success(function(data) {
//            _commenttype = data;
//            populateParentChildTree( targetid, _commenttype, "Select an option or type in a new value", false, 'ajax-combobox-commentsubtype' );
//        });
//    } else {
//        populateParentChildTree( targetid, _commenttype, "Select an option or type in a new value", false, 'ajax-combobox-commentsubtype' );
//    }
//
//}

function getComboboxResidencyspecialty(holder) {

    //hide residency Specialty on view if fellowship Subspecialty is not empty => get all residencyspecialty and fellowshipsubspecialty
    if( cycle == "show_user" ) {
        getComboboxGeneric(holder,'residencyspecialty',_residencySpecialtys,false);
        getComboboxGeneric(holder,'fellowshipsubspecialty',_fellowshipSubspecialtys,false);
        return;
    }

    setResidencyspecialtyTreeChildren(holder);

    var targetid = ".ajax-combobox-residencyspecialty";

    var residencySpecialtyFound = false;

    if( $(targetid).length == 0 ) {
        return;
    }

    if( typeof holder !== 'undefined' && holder.length > 0 ) {
        targetid = holder.find(targetid);

        if( targetid.length == 0 ) {
            return;
        }
    }

    if( typeof cycle === 'undefined' ) {
        cycle = 'new';
    }
    var cycleStr = "?cycle="+cycle;

    var url = getCommonBaseUrl("util/common/generic/"+"residencyspecialty"+cycleStr,"employees");

    //console.log('cycle='+cycle);

    if( _residencySpecialtys.length == 0 ) {
        $.ajax({
            url: url,
            timeout: _ajaxTimeout,
            async: asyncflag
        }).success(function(data) {
            _residencySpecialtys = data;
            populateParentChildTree( targetid, _residencySpecialtys, "Select an option or type in a new value", false, 'ajax-combobox-fellowshipsubspecialty' );
        });
    } else {
        populateParentChildTree( targetid, _residencySpecialtys, "Select an option or type in a new value", false, 'ajax-combobox-fellowshipsubspecialty' );
    }

}


function setBuidlingListener(holder) {
    //add listener for: Pull in the address of the building into the address fields once the building is selected
    $('.ajax-combobox-building').on("change", function(e) {
        var holder = $(this).closest('.user-collection-holder');
        //console.log(holder);
        setGeoLocation( holder, $(this).select2('data') );
    });
}



