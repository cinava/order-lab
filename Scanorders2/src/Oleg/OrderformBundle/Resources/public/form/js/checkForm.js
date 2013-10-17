/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/18/13
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

function checkForm( elem ) {

    var element = $(elem);

    console.log( "element.id=" + element.attr('id') );

//    var elementParent = element.parent();
//    console.log("elementParent.class="+elementParent.attr('class')); //formcheck_patient_0_0_0_0_0_0_0_0

//    var elementInput = element.closest("div.input-group").find("input[name='oleg_orderformbundle_orderinfotype[patient][0][mrn]']");
    //var elementInput = element.closest("div.input-group").find("input[type=text]");

    var elementInput = element.parent().parent().find("input");  //find("input[type=text]");
    console.log("elementInput.class="+elementInput.attr('class'));

//    var elementInput2 = element.find('input[type=text],textarea,select').filter(':visible:first');
//    console.log("elementInput2.class=" + elementInput2.attr('class'));

    //  0         1              2           3   4  5
    //oleg_orderformbundle_orderinfotype_patient_0_mrn
    //oleg_orderformbundle_orderinfotype_patient_0_specimen_0_accession_0_accession
    var inputId = elementInput.attr('id');
    console.log("inputId="+inputId);

    var idsArr = inputId.split("_");

    var name = idsArr[idsArr.length-3];
    var patient = idsArr[4];
    var key = idsArr[4];
//    var procedure = idsArr[3];
//    var accession = idsArr[4];
//    var part = idsArr[5];
//    var block = idsArr[6];
//    var slide = idsArr[7];
//    var scan = idsArr[8];
//    var stain = idsArr[9];

    if( element.find("i").attr("class") != "glyphicon glyphicon-check" ) {
        setElement(element, name, null, true);
        disableAllInsideElements(element, false);
        invertButton(element);
        return;
    }

    //get mrn field for this patient: oleg_orderformbundle_orderinfotype_patient_0_mrn
    var id = "oleg_orderformbundle_orderinfotype_"+name+"_"+patient+"_mrn";

    var mrn = $("#"+inputId).val();
    console.log("mrn="+mrn+", name="+name);

//    if( mrn == "" || mrn == undefined) {
    if( !mrn ) {
        //console.log("mrn undefinded!");
        $('#'+inputId).popover( {content:"Please fill out MRN field"} );
        $('#'+inputId).popover('show');
        return;
    } else {
        //console.log("mrn definded="+mrn);
    }

    var urlCheck = "http://collage.med.cornell.edu/order/scanorder/Scanorders2/web/app_dev.php/check/";
    console.log("urlCheck="+urlCheck);

    //var ids = new Array(0,0,0,0,0,0,0,0);
    //var formbody = getFormBody( "patient", ids[0], ids[1], ids[2], ids[3], ids[4], ids[5], ids[6], ids[7] );
    //$('#check_div').html(formbody);
    //$('#check_div').load('http://collage.med.cornell.edu/order/scanorder/Scanorders2/web/app_dev.php/check/patient');

    $.ajax({
        url: urlCheck+name,
        type: 'GET',
        data: {mrn: mrn},
        contentType: 'application/json',
        dataType: 'json',
        success: function (data) {
            if( data.id ) {
                console.debug("inmrn="+ data.inmrn);
                console.debug("data.id="+ data.id);
                console.debug("data.name="+ data.name);
                //first: set elements
                setElement(element, name, data);
                //second: disable or enable element. Make sure this function runs after setElement
                disableAllInsideElements(element, true);
            } else {
                console.debug("not found: inmrn="+ data.inmrn);
                disableAllInsideElements(element, false);
            }
            invertButton(element);
        },
        error: function () {
            console.debug("ajax error");
            setElement(element, name, null);
            disableAllInsideElements(element, false);
            invertButton(element);
        }
    });

}

//cleanall: clean all fields
function setElement( element, name, data, cleanall ) {

    //console.debug( "name=" + name + ", data.id=" + data.id + ", sex=" + data.sex );
    var parent = element.parent().parent().parent().parent().parent();
    console.log("set parent.id=" + parent.attr('id'));
    var elements = parent.find('input,textarea,select');

    for (var i = 0; i < elements.length; i++) {

        //console.log("element.id=" + elements.eq(i).attr("id"));
        //  0         1              2           3   4  5
        //oleg_orderformbundle_orderinfotype_patient_0_mrn  //length=6
        var id = elements.eq(i).attr("id");
        var type = elements.eq(i).attr("type");
        var classs = elements.eq(i).attr("class");
        var value = elements.eq(i).attr("value");
        console.log("id=" + id + ", type=" + type + ", class=" + classs + ", value=" + value );

        if( id ) {

            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-1];    //default

            if( type == "radio" ) {
                var field = idsArr[idsArr.length-2];
                if( data != null && data[field] ) {
                    console.log("check radio:7 " + value + "?=" + data[field] );
                    if( value == data[field] ) {
                        elements.eq(i).prop('checked',true);
                    }
                } else {
                    elements.eq(i).prop('checked',false);
                }
            }

            //fields text and all others including textarea (i.e. clinicalHistory textarea field does not have type="textarea", so it has type undefined)
            if( type == "text" || !type ) {
                //var field = idsArr[idsArr.length-1];
                if( data == null  ) {   //clean fields
                    if( $.inArray(field, keys) == -1 || cleanall) {
                        elements.eq(i).val(null);   //clean non key fields
                    } else {
                        //console.log("In array. Additional check for field=("+field+")");
                        if( field == "name" ) {
                            var holder = idsArr[idsArr.length-3];
                            //console.log("holder="+holder);
                            if( holder != "part" && holder != "block" ) {
                                //console.log("disable!!!!");
                                elements.eq(i).val(null);   //clean non key fields with filed "name"
                            }
                        }
                    }
                } else {
                    console.log("set text field = " + data[field] );
                    elements.eq(i).val(data[field]);
                }
            }

            if( classs && classs.indexOf("datepicker") != -1 ) {
                //var field = idsArr[idsArr.length-1];
                if( data == null ) {
                    elements.eq(i).val(null);
                } else {
                    if( data[field] ) {
                        elements.eq(i).datepicker( 'setDate', new Date(data[field].date) );
                        elements.eq(i).datepicker( 'update');
                    }
                }
            }

            console.log("field=" + field + ", value=" + value );

        }

    }

}

function disableAllElements() {
    if( type ) {
        if( type == 'single' ) {    //single form

        } else {    //multi form
            disableAllMulti();
        }
    }
}

function disableAllMulti() {
    var check_btns = $("[id=check_btn]");
    console.log("check_btns.length="+check_btns.length);
    for (var i = 0; i < check_btns.length; i++) {
        var idArr = check_btns.eq(i).attr("id").split("_");
        if( idArr[2] != "slide" && check_btns.eq(i).attr('flag') != "done" ) {
            check_btns.eq(i).attr('flag', 'done');
            disableAllInsideElements( check_btns.eq(i), true );
        }
    }
}

var keys = new Array("mrn","accession","name", "clinicalHistory");
function disableAllInsideElements( element, disabled ) {

    console.log("disable element.id=" + element.attr('id'));

    var parent = element.parent().parent().parent().parent().parent();

    console.log("parent.id=" + parent.attr('id') + ", parent.class=" + parent.attr('class'));

    var elements = parent.find('input,textarea,select');

    //console.log("elements.length=" + elements.length);

    for (var i = 0; i < elements.length; i++) {

        //console.log("element.id=" + elements.eq(i).attr("id"));
        //  0         1              2           3   4  5
        //oleg_orderformbundle_orderinfotype_patient_0_mrn  //length=6
        var id = elements.eq(i).attr("id");
        if( id ) {
            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-1];
            console.log("field=(" + field + ")");

            if( $.inArray(field, keys) == -1 ) {
                //console.log("disable!!!!");
                disableElement(elements.eq(i),disabled);
            } else {
                //console.log("In array. Additional check for field=("+field+")");
                if( field == "name" ) {
                    var holder = idsArr[idsArr.length-3];
                    console.log("holder="+holder);
                    if( holder != "part" && holder != "block" ) {
                        //console.log("disable!!!!");
                        disableElement(elements.eq(i),disabled);
                    }
                }
            }
        }

    }
}


function disableElement(element, flag) {
    var type = element.attr('type');
    var classs = element.attr('class');
    if( flag ) {
        //element.prop("disabled", true);
        if( type == "radio" ) {
            var type = element.attr('checked');
            if( element.is(":checked") ){
                //do nothing for checked button
                element.attr("disabled", false);
            } else {
                element.attr("disabled", true);
            }
        } else {
            element.attr('readonly', true);
            //element.off("click");
            //element.bind('click', false);
            if( classs && classs.indexOf("datepicker") != -1 ) {
                console.log("disable datepicker classs="+classs);
                //element.datepicker("remove");
                //element.off();
                initDatepicker(element,"remove");
            }
        }

    } else {
        //element.prop("disabled", false);
        element.attr("readonly", false);
        element.removeAttr( "readonly" );
        //element.removeAttr( "disabled" );
        if( type == "radio" ) {
            element.prop("disabled", false);
        }
        if( classs && classs.indexOf("datepicker") != -1 ) {
            console.log("enable datepicker classs="+classs);
            //$(".datepicker").datepicker({autoclose: true});
            //element.datepicker({autoclose: true});
            initDatepicker(element);
        }

    }
}

function invertButton(btn) {
    //class="glyphicon glyphicon-check"
    if( btn.find("i").attr("class") == "glyphicon glyphicon-check" ) {
        //btn.removeClass("glyphicon glyphicon-check");
        //btn.addClass("glyphicon glyphicon-remove");
        btn.find("i").removeClass('glyphicon-check').addClass('glyphicon-remove');
    } else {
        //btn.removeClass("glyphicon glyphicon-remove");
        //btn.addClass("glyphicon glyphicon-check");
        btn.find("i").removeClass('glyphicon-remove').addClass('glyphicon-check');
    }

}

//TODO: add listener for key fields. If change, disable all element