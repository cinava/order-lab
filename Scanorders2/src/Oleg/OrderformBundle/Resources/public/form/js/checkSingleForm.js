/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/18/13
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

var _max_tab_height = 0;

//inputField - input field element which is tested for value is being set
function waitWhenReady( fieldsArr, count, limit ) {

    var inputId = fieldsArr[count];

    var inputField = $(inputId).find('.keyfield').not("*[id^='s2id_']");

    if( inputField.hasClass('combobox')  ) {
        var testValue = inputField.select2('data').text;
    } else {
        var testValue = inputField.val();
    }

    var checkButton = $(inputId).find('#check_btn');
    //var isCheckBtn = checkButton.find("i").hasClass('checkbtn');

    if( limit == 0 ) {
        //printF(checkButton,"click button:");
        //console.log("check single form: count="+count);
        //checkButton.trigger("click");   //click only once
        var checkres = checkForm( checkButton, true );
    }

    //console.log("error length="+$('.maskerror-added').length);
    if( $('.maskerror-added').length > 0 || !checkres ) {
        //fieldsArr = null;
        return false;
    }

    return true;
}

function finalStepCheck() {
    $('#part-single').css( "width", "20%" );
    $('#block-single').css( "width", "20%" );
    $('#maincinglebtn').show();
    //console.log("asseccionKeyGlobal="+asseccionKeyGlobal+", asseccionKeytypeGlobal="+asseccionKeytypeGlobal+", partKeyGlobal="+partKeyGlobal+", blockKeyGlobal="+blockKeyGlobal);
}

//Check form single
function checkFormSingle( elem ) {

    if( validateMaskFields() > 0 ) {
        //console.log("errors > 0 => return");
        return false;
    }      

    if( $('#maincinglebtn').is(":visible") ) {
        //console.log("maincinglebtn is visible => return");
        return false;
    }

//    console.log("cancheck="+cancheck);
//    if( !cancheck ) {         
//        return;
//    }

    //$('#optional_param').collapse('toggle');    //open. Need to open to populate patient (if existed) linked to accession

    //console.log("start ajax");
    $("#loading-modal").modal('show');

    var fieldsArr = new Array();
    fieldsArr[0] = '#accession-single';
    fieldsArr[1] = '#part-single';
    fieldsArr[2] = '#block-single';

    var ajaxOK = true;

    if( !waitWhenReady( fieldsArr, 0, 0 ) ) {
        ajaxOK = false;
    }

    if( !waitWhenReady( fieldsArr, 1, 0 ) ) {
        ajaxOK = false;
    }

    if( !waitWhenReady( fieldsArr, 2, 0 ) ) {
        ajaxOK = false;
    }

    //console.log("stop ajax");
    $("#loading-modal").modal('hide');

    if( !ajaxOK ) {
        return false;
    }

    if( $('.maskerror-added').length == 0 ) {
        collapseElementFix($('#optional_param'));
        finalStepCheck();
    } else {
        return false;
    }

    initOptionalParam();

    //console.log("ready!!!");

    return true;
}


//Remove form single
function removeFormSingle( elem ) {

    //console.log("asseccionKeyGlobal="+asseccionKeyGlobal+", asseccionKeytypeGlobal="+asseccionKeytypeGlobal+", partKeyGlobal="+partKeyGlobal+", blockKeyGlobal="+blockKeyGlobal);    

    $("#loading-modal").modal('show');

    //console.log("trigger blockbtn: class="+$('.blockbtn').attr("class"));
    $('.blockbtn').trigger("click");

    $('.partbtn').trigger("click");

    $('.accessionbtn').trigger("click");
    
    if( $('.patientmrn').find('i').hasClass('removebtn') ) {
        $('.patientmrn').trigger("click");
    }  

    $('#part-single').css( "width", "25%" );
    $('#block-single').css( "width", "25%" );
    $('#maincinglebtn').hide();
    collapseElementFix($('#optional_param'));   //close optional info

    $("#loading-modal").modal('hide');

}

function checkSingleFormOnNext( elem ) {
    //data-target="#orderinfo_param"
    if( validateMaskFields() > 0 ) {
        return false;
    } else {
        //console.log("no masking errors");
        $("#next_button").hide();

        var accTypeText = $('.accessiontype-combobox').first().select2('data').text;
        if( accTypeText != 'TMA Slide' ) {
            $("#optional_button").show();
        }

        collapseElementFix($('#orderinfo_param'));
    }
    return true;
}

function collapseElementFix(elem) {
    $(document).ready(function() {
        elem.collapse('toggle');
    });
}


function getAccessionInfoDebug(text) {
    var accTypeText = $('.accessiontype-combobox').select2('data').text;
    var accTypeVal = $('.accessiontype-combobox').select2('val');
    var accNum = $('.accession-mask').val();
    console.log("############ "+text+": Accession #="+accNum+", type text="+accTypeText+", type id="+accTypeVal);
}

//open a tab with max height
function initOptionalParam() {

    $('#optional_param_tab a').click(function (e) {

        e.preventDefault();

        var elem = $(this);

        elem.tab('show');

    });

}




