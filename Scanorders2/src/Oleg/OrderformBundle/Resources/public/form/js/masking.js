/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 1/6/14
 * Time: 4:17 PM
 * To change this template use File | Settings | File Templates.
 */

///////////////////// DEFAULT MASKS //////////////////////////
var _mrnplaceholder = "NOMRNPROVIDED-";
var _accplaceholder = "NOACCESSIONPROVIDED-";
var _maskErrorClass = "has-warning"; //"maskerror"
var _repeatBig = 25;
var _repeatSmall = 13;

function getMrnDefaultMask() {
    var mrns = [
        //{ "mask": "9[999999999999]" },
//        { "mask": "9" },
        //{ "mask": "m" },
//        {"mask": "*[m][m][m][m][m][m][m][m][m][m][m][*]"} //13 total: alfa-numeric leading and ending, plus 11 alfa-numeric and dash in the middle
        { "mask": getRepeatMask(_repeatSmall,"m") }
    ];

    var mask = {
        "mask": mrns
//        "repeat": _repeatSmall,
//        "greedy": false
    };

    return mask;
}

function getAgeDefaultMask() {
    return "f[9][9]";
}

function getAccessionDefaultMask() {
    //console.log('get default accession mask');
    var accessions = [
//        { "mask": "AA99-f[99999]" },
//        { "mask": "A99-f[99999]" }
        { "mask": "AA99-f[9][9][9][9][9]" },
        { "mask": "A99-f[9][9][9][9][9]" }
    ];
    return accessions;
}
///////////////////// END OF DEFAULT MASKS //////////////////////

function fieldInputMask() {

    $.extend($.inputmask.defaults.definitions, {
        'f': {  //masksymbol
            "validator": "[1-9]",
            "cardinality": 1,
            'prevalidator': null
        }
    });

    //any alfa-numeric without leading or ending '-'
    $.extend($.inputmask.defaults.definitions, {
        "m": {
            "validator": "[A-Za-z0-9-]",
            "cardinality": 1,
            'prevalidator': null
        }
    });

    $.extend($.inputmask.defaults, {
        "onincomplete": function(result){
            makeErrorField($(this),false);
        },
        "oncomplete": function(){ clearErrorField($(this)); },
        "oncleared": function(){ clearErrorField($(this)); },
        "onKeyValidation": function(result) {
            //console.log(result);
            makeErrorField($(this),false);
        },
        "onKeyDown": function(result) {
            //console.log(result);
            makeErrorField($(this),false);
        },
        placeholder: " ",
        clearMaskOnLostFocus: true
    });

    $(":input").inputmask();

    if( cicle == "new" || cicle == "create" ) {

        $(".accession-mask").inputmask( { "mask": getAccessionDefaultMask() } );
        $(".patientmrn-mask").inputmask( getMrnDefaultMask() );
        //$(".patientmrn-mask").inputmask( { "mask": "*", "repeat": 13, "greedy": false } );
        //$(".patientmrn-mask").inputmask( { "mask": "***" } );

    } else {
        //set mrn for amend
        var mrnkeytypeField = $('.mrntype-combobox').not("*[id^='s2id_']");
        mrnkeytypeField.each( function() {
            setMrntypeMask($(this),false);
        });

        //set accession for amend: do this in selectAjax.js when accession is loaded by Ajax
    }

    $(".patientage-mask").inputmask( { "mask": getAgeDefaultMask() });

    accessionTypeListener();
    mrnTypeListener();

}

//element is check button
function setDefaultMask( element ) {

    //printF(element,"Set default mask1:");
    var maskField = getKeyGroupParent(element).find("*[class$='-mask']");
    //printF(maskField,"Set default mask2:");

    clearErrorField(element);

    maskField.each(function() {
        if( $(this).hasClass('patientmrn-mask') ) {
            //console.log("Set default mask for MRN");
            $(this).inputmask( getMrnDefaultMask() );
        }

        if( $(this).hasClass('accession-mask') ) {
            //console.log("Set default mask for Accession");
            $(this).inputmask( { "mask": getAccessionDefaultMask() } );
        }
    });  

}


function mrnTypeListener() {
    $('.mrntype-combobox').on("change", function(e) {
        //console.log("mrn type change listener!!!");
        setMrntypeMask($(this),true);
    });
}

function getMrnAutoGenMask() {
    var placeholderStr = getCleanMaskStr( _mrnplaceholder );
    var mask = {"mask": placeholderStr+"9999999999" };
    return mask;
}

//elem is a keytype element (select box)
function setMrntypeMask( elem, clean ) {
    //console.log("mrn type changed = " + elem.attr("id") + ", class=" + elem.attr("class") );

    var mrnField = getKeyGroupParent(elem).find('.patientmrn-mask');
    var value = elem.select2("val");
    var text = elem.select2("data").text;
    //console.log("text=" + text + ", value=" + value);

    //clear input field
    if( clean ) {
        mrnField.val('');
        clearErrorField(mrnField);
    }

    //mrnField.inputmask('remove');

    switch( text )
    {
        case "Auto-generated MRN":
            mrnField.inputmask( getMrnAutoGenMask() );
            var parent = elem.closest('.patientmrn');
            parent.find('#check_btn').trigger("click");
            //console.log('Auto-generated MRN !!!');
            break;
        case "Existing Auto-generated MRN":
            mrnField.inputmask( getMrnAutoGenMask() );
            break;
        case "New York Hospital MRN":
        case "Epic Ambulatory Enterprise ID Number":
        case "Weill Medical College IDX System MRN":
        case "Uptown Hospital ID":
        case "NYH Health Quest Corporate Person Index":
        case "New York Downtown Hospital":
            mrnField.inputmask( getMrnDefaultMask() );
            break;
        case "California Tumor Registry Patient ID":
        case "Specify Another Patient ID Issuer":
        case "De-Identified NYH Tissue Bank Research Patient ID":
            var repeatStr = getRepeatMask(_repeatBig,"m");
            mrnField.inputmask( { "mask": repeatStr } );
            break;
        case "De-Identified Personal Educational Slide Set Patient ID":
            var placeholderStr = user_name+"-EMRN-";
            var repeatmrn = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatmrn,"m");
            mrnField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified Personal Research Project Patient ID":
            var placeholderStr = user_name+"-RMRN-";
            var repeatmrn = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatmrn,"m");
            mrnField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "Enterprise Master Patient Index":
            mrnField.inputmask('remove');
        default:
            mrnField.inputmask('remove');
    }
}

//this function is called by getComboboxAccessionType() in selectAjax.js when accession type is initially populated by ajax
function setAccessionMask() {
    var acckeytypeField = $('.accessiontype-combobox').not("*[id^='s2id_']");
    acckeytypeField.each( function() {
        setAccessiontypeMask($(this),false);
    });
}

function accessionTypeListener() {
    $('.accessiontype-combobox').on("change", function(e) {
        console.log("accession type listener!!!");
        setAccessiontypeMask($(this),true);

        //enable optional_button for single form
        if( orderformtype == "single" ) {
            var accTypeText = $(this).select2('data').text;
            if( accTypeText == 'TMA Slide' ) {
                $("#optional_button").hide();
            } else {
                $("#optional_button").show();
            }
            
            if( accTypeText == 'Auto-generated Accession Number' ) {
                console.log("click on order info");
                checkFormSingle($('#optional_button'));
            }
        }

    });
}

function getAccessionAutoGenMask() {
    var placeholderStr = getCleanMaskStr( _accplaceholder );
    var mask = {"mask": placeholderStr+"9999999999" };
    return mask;
}

//elem is a keytype element (select box)
function setAccessiontypeMask(elem,clean) {
    //console.log("accession type changed = " + elem.attr("id") + ", class=" + elem.attr("class") );

    var accField = getKeyGroupParent(elem).find('.accession-mask');
    //printF(accField,"Set Accession Mask:")

    var value = elem.select2("val");
    var text = elem.select2("data").text;
    //console.log("text=" + text + ", value=" + value);

    //clear input field
    if( clean ) {
        accField.val('');
        clearErrorField(accField);
    }

    switch( text )
    {
        case "Auto-generated Accession Number":
            accField.inputmask( getAccessionAutoGenMask() );
            var btn = elem.closest('.accessionaccession').find('#check_btn');
            btn.trigger("click");
            //console.log('Auto-generated Accession !!!');
            //printF(btn,"btn to click:");
            break;
        case "Existing Auto-generated Accession Number":
            accField.inputmask( getAccessionAutoGenMask() );
            break;
        case "NYH CoPath Anatomic Pathology Accession Number":
            accField.inputmask( {"mask": getAccessionDefaultMask() } );
            break;
        case "De-Identified Personal Educational Slide Set Specimen ID":
            var placeholderStr = user_name+"-E-";
            var repeatnum = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatnum,"m");
            accField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified Personal Research Project Specimen ID":
            var placeholderStr = user_name+"-R-";
            var repeatnum = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatnum,"m");
            accField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified NYH Tissue Bank Research Specimen ID":
        case "California Tumor Registry Specimen ID":
        case "Specify Another Specimen ID Issuer":
        case "TMA Slide":
            var repeatStr = getRepeatMask(_repeatBig,"m");
            accField.inputmask( { "mask": repeatStr } );
            break;
        default:
            accField.inputmask('remove');
    }
}

function noMaskError( element ) {
    //console.log( "complete="+ element.inputmask("isComplete")+", !allZeros="+!allZeros(element) );

    var keytypeText = element.closest(".row").find('.accessiontype-combobox').select2('data').text;

    if( ( keytypeText == "NYH CoPath Anatomic Pathology Accession Number" && element.hasClass('accession-mask') ) || element.hasClass('patientage-mask')) {   //regular mask
        if( !allZeros(element) && element.inputmask("isComplete") ) {
            return true;
        } else {
            return false;
        }
    } else {    //non zero mask
        if( !allZeros(element) ) {
            return true;
        } else {
            return false;
        }
    }
}

function makeErrorField(element, appendWell) {
    //console.log("make red field id="+element.attr("id")+", class="+element.attr("class"));

    if( noMaskError(element) ) {
        clearErrorField(element);
        return;
    }

    var value =  element.val().trim();
    //console.log("error: value="+value);
    if( value != "" ) {
        element.parent().addClass(_maskErrorClass);
        createErrorMessage( element, null, appendWell );
    }

}

function clearErrorField( element ) {

    //check if not all zeros
    if( allZeros(element) ) {
        //console.log("all zeros!");
        return;
    }

    //console.log("make ok field id="+element.attr("id")+", class="+element.attr("class"));
    element.parent().removeClass(_maskErrorClass);
    $('.maskerror-added').remove();
}

function allZeros(element) {

    if( !element.inputmask("hasMaskedValue") ) {
        return false;
    }

    //console.log("element.val()="+element.val());
    //printF(element,"all zeros? :")
    var res = element.val().trim().match(/^[0]+$/);
    //console.log("res="+res);
    if( res ) {
        //console.log("all zeros!");
        return true;
    }
    return false;
}

function validateMaskFields( element, fieldName ) {

    var errors = 0;
    $('.maskerror-added').remove();

    //console.log("validate mask fields: fieldName="+fieldName);

    if( element ) {

        //console.log("validate mask fields: element id=" + element.attr("id") + ", class=" + element.attr("class") );

        var parent = getKeyGroupParent(element);
        var errorFields = parent.find("."+_maskErrorClass);

        if( fieldName == "partname" ) { //if element is provided, then validate only element's input field. Check parent => accession

            var parent = element.closest('.panel-procedure').find('.accessionaccession');
            //console.log("parent id=" + parent.attr("id") + ", class=" + parent.attr("class") );
            var errorFields = parent.find("."+_maskErrorClass);
            //console.log("count errorFields=" + errorFields.length );

            if( errorFields.length > 0 ) {
                var partname = getKeyGroupParent(element).find("*[class$='-mask']");   //find("input").not("*[id^='s2id_']");
                createErrorMessage( partname, "Accession Number above", true );   //create warning well under partname
            }
        }

    } else {
        var errorFields = $("."+_maskErrorClass);
    }

    errorFields.each(function() {
        var elem = $(this).find("*[class$='-mask']");
        //console.log("error id=" + elem.attr("id") + ", class=" + elem.attr("class") );

        //Please correct the invalid accession number
        var errorHtml = createErrorMessage( elem, null, true );

        $('#validationerror').append(errorHtml);

        errors++;
    });


    //console.log("number of errors =" + errors );
    return errors;
}

function createErrorMessage( element, fieldName, appendWell ) {

//    if( !allZeros(element) ) {
//        //clearErrorField(element);
//        return;
//    }

    if( noMaskError(element) ) {
        return;
    }

    if( !fieldName ) {
        var fieldName = "field marked in red above";
        if( element.hasClass("accession-mask") ) {
            fieldName = "Accession Number";
        }
        if( element.hasClass("patientmrn-mask") ) {
            fieldName = "MRN";
        }
    }

    var errorHtml =
        '<div class="maskerror-added alert alert-danger">' +
            'Please correct the invalid ' + fieldName + '.' +
            '</div>';

    //console.log("element id="+element.attr("id")+", class="+element.attr("class"));

    if( appendWell ) {
        element.after(errorHtml);
    }

    return errorHtml;
}

function changeMaskToNoProvided( combobox, fieldName ) {
    if( fieldName == "mrn" ) {
        var mrnField = getKeyGroupParent(combobox).find('.patientmrn-mask');
        mrnField.inputmask( getMrnAutoGenMask() );
    }
    if( fieldName == "accession" ) {
        var accField = getKeyGroupParent(combobox).find('.accession-mask');
        //printF(accField,"change to noprovided: ");
        accField.inputmask( getAccessionAutoGenMask() );
    }
}

function getCleanMaskStr( str) {
    //console.log("str="+str);

    var defarr = $.inputmask.defaults.definitions;

    for( var index in defarr ) {
        index = index.trim();
        if( index != "*" ) {
            //console.log( "index="+index);
            var replaceValue = "\\\\"+index;
            var regex = new RegExp( index, 'g' );
            str = str.replace(regex, replaceValue);
        }

    }

    //console.log( "str="+str);
    return str;
}

function getRepeatNum( placeholderStr, rnum ) {
    var origLength = placeholderStr.length;
    //console.log("origLength=" + origLength);
    var res = rnum - origLength;
    //console.log("res=" + res);
    return res;
}

//allsame - if true: use * as the first and last masking characters (no leading and ending dashes)
function getRepeatMask( repeat, char, allsame ) {
    if( allsame ) {
        var repeatStr = char;
    } else {
        var repeatStr = "*";
        repeat = repeat - 1;
    }

    for (var i=1; i<repeat; i++ ) {
        repeatStr = repeatStr + char;
    }

    if( allsame ) {
        //
    } else {
        repeatStr = repeatStr + "*";
    }

    return repeatStr;
}

//elem: button, combobox (keytype) or input field
function getKeyGroupParent(elem) {
    //printF(elem, "@@@@@@@@@@@@@ Get parent for element:");
    if( orderformtype == "single" && elem.attr('class').indexOf("mrn") == -1) {
        var parent = $('.singleorderinfo');

    } else {
        var parent = elem.closest('.row');
    }
    return parent;
}

//elem is a keytype (combobox)
function getButtonParent(elem) {
    if( orderformtype == "single") {
        if( elem.hasClass('mrntype-combobox') ) {
            var parent = $('#patient');
        }
        if( elem.hasClass('accessiontype-combobox') ) {
            var parent = $('#accession-single');
        }
    } else {
        var parent = elem.closest('.row');
    }
    return parent;
}
