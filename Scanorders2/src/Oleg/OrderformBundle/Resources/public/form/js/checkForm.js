/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/18/13
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

var keys = new Array("mrn", "accession", "name");
var arrayFieldShow = new Array("clinicalHistory","age"); //display as array fields
var urlCheck = "http://collage.med.cornell.edu/order/scanorder/Scanorders2/web/app_dev.php/check/";

//  0         1              2           3   4  5  6   7
//oleg_orderformbundle_orderinfotype_patient_0_mrn_0_field
var fieldIndex = 3;     //get 'mrn'
var holderIndex = 5;    //get 'patient'
console.log("urlCheck="+urlCheck);

function checkForm( elem ) {

    var element = $(elem);

    //console.log( "element.id=" + element.attr('id') );

    var elementInput = element.parent().parent().find("input");  //find("input[type=text]");
    //console.log("elementInput.class="+elementInput.attr('class'));

    //  0         1              2           3   4  5  6   7
    //oleg_orderformbundle_orderinfotype_patient_0_mrn_0_field
    var inputId = elementInput.attr('id');
    console.log("inputId="+inputId);

    var idsArr = inputId.split("_");

    var name = idsArr[idsArr.length-holderIndex];
    var patient = idsArr[4];
    var key = idsArr[4];

    if( element.find("i").attr("class") == "glyphicon glyphicon-remove" ) { //Remove Button Cliked

        console.log("Remove Button Cliked");
        //setElementBlock(element, null, true);
        cleanFieldsInElementBlock( element, "all" );
        disableInElementBlock(element, true, null, "notkey", null);
        invertButton(element);
        return;

    } else {    //Check Button Cliked

        console.log("Check Button Cliked");

        //get mrn field for this patient: oleg_orderformbundle_orderinfotype_patient_0_mrn
        var id = "oleg_orderformbundle_orderinfotype_"+name+"_"+patient+"_mrn";

        var mrn = $("#"+inputId).val();
        console.log("mrn="+mrn+", name="+name);

        if( !mrn ) {
//            //console.log("mrn undefinded!");
//            $('#'+inputId).popover( {content:"Please fill out MRN field"} );
//            $('#'+inputId).popover('show');
            setKeyValue(element);
//            disableInElementBlock(element, false, null, "notkey", null);
//            invertButton(element);
            return;
        }

        $.ajax({
            url: urlCheck+name,
            type: 'GET',    //TODO: use POST
            data: {mrn: mrn},
            contentType: 'application/json',
            dataType: 'json',
            success: function (data) {
                console.debug("get object ajax ok "+name);
                if( data.id ) {
                    //console.debug("inmrn="+ data.inmrn);
                    //console.debug("data.id="+ data.id);
                    //console.debug("data.name="+ data.name);
                    //first: set elements
                    setElementBlock(element, data);
                    //second: disable or enable element. Make sure this function runs after setElementBlock
                    disableInElementBlock(element, true, "all", null, "notarrayfield");
                } else {
                    console.debug("not found");
                    cleanFieldsInElementBlock( element );
                    disableInElementBlock(element, false, null, "notkey", null);
                }
                invertButton(element);
            },
            error: function () {
                console.debug("get object ajax error "+name);
                //setElementBlock(element, null);
                cleanFieldsInElementBlock( element );
                disableInElementBlock(element, false, "all", null, null);
                invertButton(element);
            }
        });
    }

    return;
}

//set Element. Element is a block of fields
//element: check_btn element
//cleanall: clean all fields
//key: set only key field
function setElementBlock( element, data, cleanall, key ) {

    //console.debug( "name=" + name + ", data.id=" + data.id + ", sex=" + data.sex );
    var parent = element.parent().parent().parent().parent().parent().parent();
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
            var field = idsArr[idsArr.length-fieldIndex];    //default
            console.log("field = " + field);// + ", data text=" + data[field]['text']);

            if( key == "key" ) {
                if( $.inArray(field, keys) != -1 ) {
                    console.log("set key field = " + data[field][0]['text'] );
                    setArrayField( elements.eq(i), data[field], parent );
                    //elements.eq(i).val(data[field]);
                    break;
                }
            }

            if( type == "radio" ) {
                var field = idsArr[idsArr.length-(fieldIndex + 1)];
                console.log("radio field: " + field );
                setArrayField( elements.eq(i), data[field], parent );
//                if( data != null && data[field] ) {
//                    console.log("check radio (data): " + value + "?=" + data[field] );
//                    if( value == data[field] ) {
//                        elements.eq(i).prop('checked',true);
//                    }
//                } else {
//                    console.log("check radio (data null): " + value + "?=" + data[field] );
//                    elements.eq(i).prop('checked',false);
//                }
            }

            //fields text and all others including textarea (i.e. clinicalHistory textarea field does not have type="textarea", so it has type undefined)
//            if( type == "text" || !type ) {
            if( 1 ) {
                //var field = idsArr[idsArr.length-fieldIndex];
                if( data == null  ) {   //clean fields

                    if( $.inArray(field, keys) == -1 || cleanall) {
                        elements.eq(i).val(null);   //clean non key fields
                    } else {
                        //console.log("In array. Additional check for field=("+field+")");
                        if( field == "name" ) {
                            var holder = idsArr[idsArr.length-holderIndex];
                            //console.log("holder="+holder);
                            if( holder != "part" && holder != "block" ) {
                                //console.log("disable!!!!");
                                elements.eq(i).val(null);   //clean non key fields with filed "name"
                            }
                        }
                    }
                } else {
                    //console.log("set text field = " + data[field][0]['text']);
                    //if( $.isArray(data[field]) ) {
                        setArrayField( elements.eq(i), data[field], parent );
//                    } else {
//                        console.log("It is not an array");
//                        elements.eq(i).val(data[field]);
//                    }
                }
            }

            if( classs && classs.indexOf("datepicker") != -1 ) {
                //var field = idsArr[idsArr.length-fieldIndex];
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

//set array field such as ClinicalHistory array fields
//element is an input element jquery object
function setArrayField(element, dataArr, parent) {

    for (var i = 0; i < dataArr.length; i++) {

        //var dataArr = data[field];
        var id = dataArr[i]["id"];
        var text = dataArr[i]["text"];
        var provider = dataArr[i]["provider"];
        var date = dataArr[i]["date"];

        console.log( "set array field text=" + text + ", provider="+provider+", date="+date + ", id=" + element.attr("id") );

        var idsArr = parent.attr("id").split("_");
        var elementIdArr = element.attr("id").split("_");
        // 0        1               2           3    4      5          6        7
        //oleg_orderformbundle_orderinfotype_patient_0_clinicalHistory_0_clinicalHistory
        // 0        1               2           3    4      5   6     7     8   9  10      11      12 13
        //oleg_orderformbundle_orderinfotype_patient_0_specimen_0_accession_0_part_0_diffDiagnoses_0_name

        var thisFieldIndex = fieldIndex;
        var thisHolderIndex = holderIndex;

        var type = element.attr("type");
        var classs = element.attr("class");
        var tagName = element.prop("tagName");

        console.debug("set array field parent.id=" + parent.attr("id") + ", tagName=" + tagName + ", type=" + type + ", classs=" + classs + ", text=" + text );

        if( type == "radio" ) {
            //id=oleg_orderformbundle_orderinfotype_patient_0_sex_0_field_0
            thisFieldIndex = fieldIndex + 1;
            thisHolderIndex = holderIndex + 1;
        }

        var fieldName = elementIdArr[elementIdArr.length-thisFieldIndex];
        var holderame = elementIdArr[elementIdArr.length-thisHolderIndex];
        var ident = holderame+fieldName;

        //var attachElement = element.parent().parent().parent().parent().parent();
        var attachElement = parent.find("."+ident.toLowerCase());

        if( $.inArray(fieldName, arrayFieldShow) != -1 ) { //show all fields

            //patient_0_0_0_0_0_0_0_0

            //var name = idsArr[0];
            var patient = idsArr[1];
            var procedure = idsArr[2];
            var accession = idsArr[3];
            var part = idsArr[4];
            var block = idsArr[5];
            var slide = idsArr[6];
            var coll = i+1;
            console.log("SHOW set array ident=" + ident );

            //fieldName = "clinicalHistory";

            var newForm = getCollField( ident, patient, procedure, accession, part, block, slide, coll );
            //console.log("newForm="+newForm);

//        var tagReplace = "</textarea>";
//        var textStr = text+"</textarea>";

//        if( type == "text" ) {
//            tagReplace = '"readonly="readonly">';
//            //textStr = '"readonly="readonly" value="'+text+'">';
//            textStr = tagReplace;
//        }

            //newForm = newForm.replace(tagReplace, textStr);

            var labelStr = " entered on " + date + " by "+provider + "</label>";
            newForm = newForm.replace("</label>", labelStr);

//        var idStr = 'readonly="readonly" value="'+id+'" ';
//        newForm = newForm.replace('readonly="readonly"', idStr);

            var idStr = 'type="hidden" value="'+id+'" ';
            newForm = newForm.replace('type="hidden"', idStr);

            //console.log("newForm="+newForm);

            console.log("attachElement class="+attachElement.attr("class")+",id="+attachElement.attr("id"));

//        attachElement.before(newForm);
            attachElement.prepend(newForm);

        } else {    //show the valid field (with validity=1)
            console.log("NO SHOW");
        }

        if( tagName == "INPUT" ) {

            if( type == "text" ) {
                //find the last attached element to attachElement
//            var firstAttachedElement = attachElement.find('*[type="'+type+'"]').first();
                var firstAttachedElement = attachElement.find('input').first();
                firstAttachedElement.val(text);
            }

            if( classs && classs.indexOf("datepicker") != -1 ) {
                var firstAttachedElement = attachElement.find('input').first();
                if( text && text != "" ) {
                    firstAttachedElement.datepicker( 'setDate', new Date(text) );
                    firstAttachedElement.datepicker( 'update');
                } else {
                    firstAttachedElement.datepicker({autoclose: true});
                    //firstAttachedElement.val( 'setDate', new Date() );
                    //firstAttachedElement.datepicker( 'update');
                }
            }

        } else if ( tagName == "TEXTAREA" ) {
            var firstAttachedElement = attachElement.find('textarea').first();
            console.log("textarea firstAttachedElement class="+firstAttachedElement.attr("class")+",id="+firstAttachedElement.attr("id"));
            firstAttachedElement.val(text);
        } else {
            console.log("logical error: undefined tagName");
        }

    }

}

function cleanArrayField( element, field ) {
    //console.log( "clean array field id=" + element.attr("id") );
    //delete if id != 0
    if( element.attr("id") && element.attr("id").indexOf(field+"_0_field") != -1 ) {
        element.val(null);
    } else {
        element.parent().parent().remove();
    }
}

//clean fields in Element Block
//all: if set to "all" => clean all fields, including key field
function cleanFieldsInElementBlock( element, all ) {
    //console.debug( "name=" + name + ", data.id=" + data.id + ", sex=" + data.sex );
    var parent = element.parent().parent().parent().parent().parent().parent();
    //console.log("clean parent.id=" + parent.attr('id'));
    var elements = parent.find('input,textarea,select');

    for (var i = 0; i < elements.length; i++) {

        var id = elements.eq(i).attr("id");
        var type = elements.eq(i).attr("type");

        if( id ) {

            if( type == "text" || !type ) {
                var clean = false;
                var idsArr = id.split("_");
                var field = idsArr[idsArr.length-fieldIndex];
                if( all == "all" ) {
                    //elements.eq(i).val(null);
                    clean = true;
                } else {
                    //check if the field is not key
                    if( !isKey(elements.eq(i), field) ) {
                        //elements.eq(i).val(null);
                        clean = true;
                    }
                }
                if( clean ) {
                    //console.log("in array field=" + field );
                    if( $.inArray(field, arrayFieldShow) == -1 ) {
                        //console.log("clean not array");
                        elements.eq(i).val(null);
                    } else {
                        //console.log("clean as an array");
                        cleanArrayField( elements.eq(i), field );
                    }
                }
            }

            if( type == "radio" ) {
                elements.eq(i).prop('checked',false);
            }

        }

    }
}

function isKey(element, field) {
    var idsArr = element.attr("id").split("_");
    if( $.inArray(field, keys) == -1 ) {
        return false;
    } else {
        if( field == "name" ) {
            var holder = idsArr[idsArr.length-holderIndex];
            //console.log("holder="+holder);
            if( holder == "part" || holder == "block" ) {
                return true
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

function initAllElements() {
    if( type ) {
        if( type == 'single' ) {    //single form

        } else {    //multi form
            initAllMulti();
        }
    }
}

function initAllMulti() {
    var check_btns = $("[id=check_btn]");
    console.log("check_btns.length="+check_btns.length);
    for (var i = 0; i < check_btns.length; i++) {
        var idArr = check_btns.eq(i).attr("id").split("_");
        if( idArr[2] != "slide" && check_btns.eq(i).attr('flag') != "done" ) {
            check_btns.eq(i).attr('flag', 'done');
            disableInElementBlock(check_btns.eq(i), true, null, "notkey", null);
        }
    }
}

//all: "all" => disable/enable all fields including key field
//flagKey: "notkey" => disable/enable all fields, but not key field (inverse key)
//flagArrayField: "notarrayfield" => disable/enable array fields
function disableInElementBlock( element, disabled, all, flagKey, flagArrayField ) {

    //console.log("disable element.id=" + element.attr('id'));

    var parent = element.parent().parent().parent().parent().parent().parent();

    //console.log("parent.id=" + parent.attr('id') + ", parent.class=" + parent.attr('class'));

    var elements = parent.find('input,textarea,select');

    //console.log("elements.length=" + elements.length);

    for (var i = 0; i < elements.length; i++) {

        //console.log("element.id=" + elements.eq(i).attr("id"));
        //  0         1              2           3   4  5
        //oleg_orderformbundle_orderinfotype_patient_0_mrn  //length=6
        var id = elements.eq(i).attr("id");
        if( id ) {
            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-fieldIndex];
            //console.log("field=(" + field + ")");

            if( all == "all" ) {
                disableElement(elements.eq(i),disabled);
            }

            if( flagKey == "notkey" ) {
                //check if the field is not key
                if( isKey(elements.eq(i), field) && flagKey == "notkey" ) {
                    if( disabled ) {    //inverse disable flagKey for key field
                        disableElement(elements.eq(i),false);
                    } else {
                        disableElement(elements.eq(i),true);
                    }
                } else {
                    disableElement(elements.eq(i),disabled);
                }
            }

            if( flagArrayField == "notarrayfield" ) {
                if( $.inArray(field, arrayFieldShow) != -1 ) {
                    if( elements.eq(i).attr("id") && elements.eq(i).attr("id").indexOf(field+"_0_"+field) != -1 ) {
                        if( disabled ) {    //inverse disable flag for key field
                            disableElement(elements.eq(i),false);
                        } else {
                            disableElement(elements.eq(i),true);
                        }
                    }
                }
            }

        }

    }
}

function disableElement(element, flag) {

    if( !element ) return;

    var type = element.attr('type');
    var classs = element.attr('class');
    if( flag ) {
        //console.log("disable field id="+element.attr("id"));
        //element.prop("disabled", true);
        //console.log("disable classs="+classs);
        if( type == "radio" ) {
            var type = element.attr('checked');
            if( element.is(":checked") ){
                element.attr("disabled", false);
            } else {
                element.attr("disabled", true);
            }
        } else {
            //console.log("disable classs="+classs);
            element.attr('readonly', true);
            //element.off("click");
            //element.bind('click', false);
            if( classs && classs.indexOf("datepicker") != -1 ) {
                //console.log("disable datepicker classs="+classs);
                //element.datepicker("remove");
                //element.off();
                initDatepicker(element,"remove");
            }
        }

    } else {
        //console.log("enable field id="+element.attr("id"));
        //element.prop("disabled", false);
        element.attr("readonly", false);
        element.removeAttr( "readonly" );
        //element.removeAttr( "disabled" );
        if( type == "radio" ) {
            element.prop("disabled", false);
        }
        if( classs && classs.indexOf("datepicker") != -1 ) {
            //console.log("enable datepicker classs="+classs);
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

function setKeyValue(element) {
    var name = "";
    var keyElement = null;
    var parent = element.parent().parent().parent().parent().parent().parent();
    //console.log("set key value: parent.id=" + parent.attr('id') + ", parent.class=" + parent.attr('class'));

    var elements = parent.find('input,select');
    //console.log("set key value: elements.length=" + elements.length);

    for (var i = 0; i < elements.length; i++) {
        var id = elements.eq(i).attr("id");
        if( id ) {
            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-fieldIndex];
            //console.log("set key value: field=(" + field + ")");

            if( $.inArray(field, keys) != -1 ) {
                console.log("set key value: found key=(" + field + ")");
                name = field;
                keyElement = elements.eq(i);
                break;
            }
        }
    }

    if( name == "name" ) return;

//    //console.debug("mrn="+ data.mrn);
//    if( name != "accession" ) {
//        //data = new Array();
//        //data[name] = "Automatic Generated";
//        //setElementBlock(element, data, null, "key");
//        keyElement.val("Automatic Generated");
//    }

    $.ajax({
        url: urlCheck+name,
        type: 'GET',
        contentType: 'application/json',
        dataType: 'json',
        success: function (data) {
            if( data[name] ) {
                //console.debug(name+"="+ data[name]);
                setElementBlock(element, data, null, "key");

                disableInElementBlock(element, false, null, "notkey", null);
                invertButton(element);
            }
        },
        error: function () {
            console.debug("set key ajax error");
        }
    });

    return keyElement;
}


//TODO: add listener for key fields. If change, disable all element