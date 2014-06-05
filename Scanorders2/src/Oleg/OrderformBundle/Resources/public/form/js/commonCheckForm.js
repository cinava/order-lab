/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function cleanValidationAlert() {
    if( cicle == "new" || cicle == "amend" || cicle == "edit" ) {
        $('.validationerror-added').each(function() {
            $(this).remove();
        });
        //$('#validationerror').html('')
        dataquality_message1.length = 0;
        dataquality_message2.length = 0;
    }
}

function initAllElements() {

//    if( cicle == "new" || cicle == "amend" ) {
    if( cicle == "new" ) {
        var check_btns = $("[id=check_btn]");
        //console.log("check_btns.length="+check_btns.length);
        for (var i = 0; i < check_btns.length; i++) {
            var idArr = check_btns.eq(i).attr("id").split("_");
            if( idArr[2] != "slide" && check_btns.eq(i).attr('flag') != "done" ) {
                check_btns.eq(i).attr('flag', 'done');  //done required to see if the fields belonging to this button was already disabled when adding a new elements on multi forms
                disableInElementBlock(check_btns.eq(i), true, null, "notkey", null);
            }
        }
    }

}


function isKey(element, field) {

    if(
            element.hasClass('keyfield') ||
            element.hasClass('accessiontype-combobox') ||
            element.hasClass('mrntype-combobox')
    ) {
        return true;
    } else {
        return false;
    }

//    var idsArr = element.attr("id").split("_");
//    if( $.inArray(field, keys) == -1 ) {
//        return false;
//    } else {
//        if( field == "name" ) {
//            var holder = idsArr[idsArr.length-holderIndex];
//            console.log("is key: holder="+holder);
//            if( holder == "part" || holder == "block" ) {
//                return true
//            } else {
//                return false;
//            }
//        } else {
//            return true;
//        }
//    }
}


function trimWithCheck(val) {
    if( val && typeof val != 'undefined' && val != "" ) {
        val = val.toString();
        val = val.trim();
    }
    return val;
}

function invertButton(btn) {
    //console.log("invert Button: glyphicon class="+btn.find("i").attr("class"));
    if( btn.hasClass('checkbtn') ) {
        //console.log("check=>remove");
        btn.find("i").removeClass('glyphicon-check').addClass('glyphicon-remove');
        btn.removeClass('checkbtn').addClass('removebtn');
    } else {
        //console.log("remove=>check");
        btn.find("i").removeClass('glyphicon-remove').addClass('glyphicon-check');
        btn.removeClass('removebtn').addClass('checkbtn');
    }
    //console.log("finish invert Button: glyphicon class="+btn.attr("class"));
}

//button 'loading' and reset causes to change the class to the original button
function fixCheckRemoveButton(btn) {
    //printF(btn," fix button: ");
    if( btn.hasClass('checkbtn') ) {
        //console.log("fix check");
        btn.find("i").removeClass('glyphicon-remove').addClass('glyphicon-check');
    }
    if( btn.hasClass('removebtn') ) {
        //console.log("fix remove");
        btn.find("i").removeClass('glyphicon-check').addClass('glyphicon-remove');
    }
}

function createErrorWell(inputElement,name,errtext) {
    var errorStr = "";
    if( name == "patient" ) {
        errorStr = 'This is not a previously auto-generated MRN. Please correct the MRN or select "Auto-generated MRN" for a new one.';
    } else
    if( name == "accession" ) {
        errorStr = 'This is not a previously auto-generated accession number. Please correct the accession number or select "Auto-generated Accession Number" for a new one.';
    } else {
        errorStr = 'This is not a previously auto-generated number. Please correct this number or empty this field and click the check box to generate a new one.';
    }

    if( errtext ) {
        errorStr = errtext;
    }

    var errorHtml =
        '<div class="maskerror-added alert alert-danger">' + 
            errorStr +
        '</div>';

    inputElement.after(errorHtml);
    
    return errorHtml;
}

function deleteSuccess(btnObj,single) {
    var btnElement = btnObj.btn;
    //console.log("delete success: "+btnObj);
    //printF(btnElement,"Delete on Success:")
    if( !btnElement ) {
        return false;
    }
    cleanFieldsInElementBlock( btnElement, "all", single ); //single = true
    disableInElementBlock(btnElement, true, null, "notkey", null);
    invertButton(btnElement);
    setDefaultMask(btnObj);
}

function deleteError(btnObj,single) {

    fixCheckRemoveButton(btnObj.btn); //fix button, because btn.button('reset') revert back glyphicon to check button

    if( !single ) {
        //printF(btnElement,"btnElement:");
        //check if all children buttons are not checked == has class removebtn
        var errors = 0;
        var btnElement = btnObj.btn;
        var checkBtns = btnElement.closest('.panel').find('#check_btn');
        //console.log('checkBtns.length='+checkBtns.length);

        checkBtns.each( function() {
            //printF($(this),'check btn=');
            //printF(btnElement,'btnElement=');
            if( $(this).attr('class') != btnElement.attr('class') ) {
                if( $(this).hasClass('removebtn') ) {
                    errors++;
                }
            }
        });

        //console.log('errors='+errors);
        if( errors == 0 ) {
            deleteSuccess(btnElement,single);
            return;
        }

        var childStr = "Child";
        if( btnObj.name == "accession" ) {
            childStr = "Part";
        }
        if( btnObj.name == "part" ) {
            childStr = "Block";
        }
        alert("Can not delete this element. Make sure if " + childStr + " is deleted.");

    }
}

//check if parent has checked sublings with the same key value
function checkParent(element,keyValue,name,fieldName,extra) {
    var parentEl = element.parent().parent().parent().parent().parent().parent().parent().parent().parent();
    //console.log("checkParent parentEl.id=" + parentEl.attr('id') + ", class="+parentEl.attr('class'));

    //if this patient has already another checked accession, then check current accession is not possible
    //get patient accession buttons
    var retval = 1;

    //console.log("name+fieldName=" + name+fieldName);

    var sublingsKey = parentEl.find('.'+name+fieldName).each(function() {

        //printF($(this),"check sublings keys=");

        var keyField = $(this).find('.keyfield');

        //if( $(this).val() == "" ) {
        if( keyField.hasClass('select2') ) {
            var sublingsKeyValue = keyField.val();
        } else {
            var sublingsKeyValue = keyField.select2("val");
        }

        if( name == "accession" || name == "patient" ) {
            var keytype = $(this).find('.combobox ').not("*[id^='s2id_']").select2('val');
            var sublingsKeyValue = $(this).find('.keyfield ').val();
        }

        //console.log("checkParent sublingsKeyValue=" + sublingsKeyValue + ", keyValue="+keyValue + ", keytype="+keytype+", extra="+extra);

        if( $(this).find('#check_btn').hasClass('removebtn') && trimWithCheck(sublingsKeyValue) == trimWithCheck(keyValue) ) {
            alert("This keyfield is already in use and it is checked");
            retval = 0;
            return false;   //break each
        }
    });

    if( retval == 0 ) {
        return 0;
    }
    return 1;
}

//element: accession button
//set Patient by data from accession check
function setPatient( btn, keyvalue, extraid, single ) {

    var btnObj = new btnObject(btn,'full'); //'full' => get parent for accession too
    var parentBtnObj = new btnObject(btnObj.parentbtn);
    var parentKey = null;

    if( !parentBtnObj || !btnObj.parentbtn || keyvalue == '' ) {
        console.log("WARNING: Parent (here Patient) does not exists");
        return 0;
    }

    parentKey = trimWithCheck(parentBtnObj.key);

    //check if parent has the same key and type combination
    if( keyvalue == parentKey && extraid == parentBtnObj.type ) {
        return 1;
    }

    //parent has different key type combination and button is check
    if( !parentBtnObj.remove && parentKey && parentBtnObj.type && !(keyvalue == parentKey && extraid == parentBtnObj.type) ) {
        var r=confirm('Different MRN '+ parentKey +' is already set in this form. Are you sure that you want to change the patient?');
        if( r == true ) {
            //console.log("you decide to continue");
        } else {
            //console.log("you canceled");
            return 0;
        }
    }

    //Button is removed and key and type combination is different
    if( parentBtnObj.remove && parentKey && parentBtnObj.type && !(keyvalue == parentKey && extraid == parentBtnObj.type) ) {
        var r=confirm('Patient with MRN '+ parentKey +' is already set in this form. Are you sure that you want to change the patient?');
        if( r == true ) {
            //console.log("you decide to continue");
        } else {
            //console.log("you canceled");
            return 0;
        }
    }

    //if parent key field is already checked: clean it first
    if( parentBtnObj.remove ) {
        //console.log("parent key field is already checked: clean it first");
        //parentBtnObj.btn.trigger("click");

        checkForm( parentBtnObj.btn ).
            then(
            function(response) {
                //console.log("Success!", response);
                return setAndClickPatient();
            }
        ).
            then(
            function(response) {
                //console.log("Chaining with parent OK:", response);
            },
            function(error) {
                console.error("Set Patient by Accession Failed!", error);
            }
        );

    } else {
        return setAndClickPatient();
    }

    function setAndClickPatient() {
        var mrnArr = new Array();
        mrnArr['text'] = keyvalue;
        mrnArr['keytype'] = extraid;
        var keytypeElement = parentBtnObj.btn.closest('.row').find('.combobox').not("*[id^='s2id_']");
        setKeyGroup( keytypeElement, mrnArr );
        //console.log("trig ger parent key button");
        parentBtnObj.btn.trigger("click");

        return 1;
    }

}

//function getSimpleFieldName( inputEl ) {
//    if( inputEl.hasClass("proceduredate-field") ) {
//        return "encounterDate";
//    }
//    if( inputEl.hasClass("procedurename-field") ) {
//        return "patname";
//    }
//    if( inputEl.hasClass("proceduresex-field") ) {
//        return "patsex";
//    }
//    if( inputEl.hasClass("procedureage-field") ) {
//        return "patage";
//    }
//    if( inputEl.hasClass("procedurehistory-field") ) {
//        return "pathistory";
//    }
//    return null;
//}

function calculateAgeByDob( btn ) {
    var accessionBtnObj = new btnObject(btn,'full');
    //console.log("accessionBtnObj.name="+accessionBtnObj.name);

    if( accessionBtnObj.name != 'accession' ) {
        return;
    }

    var patientBtnObj = accessionBtnObj.parentbtn;
    //console.log("par btn name="+patientBtnObj.name);

    var patientEl = getButtonElementParent(patientBtnObj);
    //console.log(patientEl);
    var dob = patientEl.find('.patientdob-mask');
    var dobValue = dob.val();
    //console.log("dobValue="+dobValue);

    var procedureEl = getButtonElementParent(btn);
    //console.log(procedureEl);
    var ageEl = procedureEl.find('.procedureage-field');

    //var dobDate = new Date(dobValue);
    var curAge = getAge(dobValue);
    //console.log("curAge=("+curAge+")");

    ageEl.val(curAge);
}

function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function getAjaxTimeoutMsg() {
    alert("Could not communicate with server: no answer after 15 seconds.");
    return false;
}

/////////////////////// validtion related functions /////////////////////////
function validateForm() {

    //console.log("validateForm enter");
    //return false;

    var saveClick = $("#save_order_onidletimeout_btn").attr('clicked');
    //console.log("saveClick="+ saveClick);

    var checkExisting = checkExistingKey("accession");
    //console.log( "accession checkExisting="+checkExisting);
    if( !checkExisting ) {
        if( orderformtype == "single") {
            if( saveClick == 'true' ) {
                //console.log( " single accession existing error => logout without saving");
                idlelogout();   //we have errors on the form, so logout without saving form
            }
            //console.log( "WARNING SINGLE RETURN: checkExisting="+checkExisting);
            return false;
        }
        //existingErrors++;
    }

    var checkExisting = checkExistingKey("patient");
    if( !checkExisting ) {
//        return false;
        //existingErrors++;
    }

    //check "Specify Another Specimen ID / Patient ID Issuer" is not set
    checkSpecifyAnotherIssuer("accession");
    checkSpecifyAnotherIssuer("patient");


    if( $('.maskerror-added').length > 0 ) {
        if( saveClick == 'true' ) {
            //console.log( " mrn existing error => logout without saving");
            idlelogout();   //we have errors on the form, so logout without saving form
        }
        //console.log( "WARNING RETURN 1: maskerror-added.length="+$('.maskerror-added').length);
        return false;
    }

    var checkMrnAcc = checkMrnAccessionConflict();
    //console.log( "checkMrnAcc="+checkMrnAcc);
    if( !checkMrnAcc ) {
        if( saveClick == 'true' ) {
            //console.log( " mrn-accession conflict => logout without saving");
            idlelogout();   //we have errors on the form, so logout without saving form
        }
        //console.log( "WARNING RETURN: checkMrnAcc="+checkMrnAcc);
        return false;
    }

    //console.log("validateForm: error length="+$('.maskerror-added').length);

    if( $('.maskerror-added').length > 0 ) {
        if( saveClick == 'true' ) {
            //console.log( $('.maskerror-added').length+ " error(s) => logout without saving");
            idlelogout();   //we have errors on the form, so logout without saving form
        }
        //console.log( "WARNING RETURN 2: maskerror-added.length="+$('.maskerror-added').length);
        return false;
    }

    //return false; //testing
    return true;
}

//accesion-MRN link validation when the user clicks "Submit" on multi-slide form
function checkMrnAccessionConflict() {

    var totalError = 0;

    if( validateMaskFields() > 0 ) {
        return false;
    }

    //check if conflict was handled by a choice, otherwise, do validation again.
    if( checkIfMrnAccConflictHandled() ) {
        return true;
    }

    if( orderformtype == "single") {
        var accessions = $('#accession-single').find('.keyfield');
        //console.log("singleform");
    } else {
        var accessions = $('.accessionaccession').find('.keyfield');
        //console.log("not singleform");
    }

    //console.log("accessions.length="+accessions.length + ", first id=" + accessions.first().attr('id') + ", class=" + accessions.first().attr('class') );
    //var prototype = $('#form-prototype-data').data('prototype-dataquality');
    //console.log("prototype="+prototype);
    var index = 0;

    //for all accession fields
    accessions.each(function() {

        var accInput = $(this);
        var accValue = accInput.val();

        //var acctypeField = accInput.closest('.row').find('.accessiontype-combobox').not("*[id^='s2id_']").first();
        var acctypeField = getKeyGroupParent(accInput).find('.accessiontype-combobox').not("*[id^='s2id_']").first();

        var acctypeValue = acctypeField.select2("val");
        var acctypeText = acctypeField.select2("data").text;

        if( orderformtype == "single") {
            var mrnHolder = $('#optional_param').find(".patientmrn");
        } else {
            var mrnHolder = accInput.closest('.panel-patient').find(".patientmrn");
        }

        var patientInput = mrnHolder.find('.keyfield').not("*[id^='s2id_']").first();
        var mrnValue = patientInput.val();
        //console.log("patientInput.first().id=" + patientInput.first().attr('id') + ", class=" + patientInput.first().attr('class'));

        var patientMrnInputs = mrnHolder.find('.mrntype-combobox').not("*[id^='s2id_']").first();
        //var mrntypeValue = patientMrnInputs.select2("val");
        var mrntypeValue = patientMrnInputs.select2("val");
        var mrntypeData = patientMrnInputs.select2("data");
        //console.log("sel id="+mrntypeData.id);
        var mrntypeText = mrntypeData.text;
        //console.log("patientInput.last().id=" + patientInput.last().attr('id') + ", class=" + patientInput.last().attr('class'));

        //console.log("accValue="+accValue + ", acctypeValue=" + acctypeValue + "; mrnValue="+mrnValue+", mrntypeValue="+mrntypeValue  );

        if(
            accValue && accValue !="" && acctypeValue && acctypeValue !="" &&
                mrnValue && mrnValue !="" && mrntypeValue && mrntypeValue !=""
        )
        {
            //console.log("validate accession-mrn-mrntype");

//            var mrn = "";
//            var mrntype = "";
//            var provider = "";
//            var date = "";

            accValue = trimWithCheck(accValue);
            acctypeValue = trimWithCheck(acctypeValue);

            $.ajax({
                url: urlCheck+"accession/check",
                type: 'GET',
                data: {key: accValue, extra: acctypeValue},
                contentType: 'application/json',
                dataType: 'json',
                timeout: _ajaxTimeout,
                async: false,
                success: function (data) {
                    //console.debug("get accession ajax ok");

//                    if( data == -1 ) {
//                        //object exists but no permission to see it (not an author or not pathology role)
//                        totalError++;
//                        return false;
//                    }

                    if( data == -2 ) {
                        //Existing Auto-generated object does not exist in DB
                        var errorHtml = createErrorWell(accInput,"accession");
                        $('#validationerror').append(errorHtml);
                        return false;
                    }

                    if( data.id ) {

                        var mrn = data['parent'];
                        var mrntype = data['extraid'];
                        var mrnstring = data['mrnstring'];
                        var orderinfo = data['orderinfo'];

                        mrn = trimWithCheck(mrn);
                        mrntype = trimWithCheck(mrntype);
                        mrnValue = trimWithCheck(mrnValue);
                        mrntypeValue = trimWithCheck(mrntypeValue);

                        //console.log('mrn='+mrn+', mrntype='+mrntype);

                        if( mrn == mrnValue && ( mrntype == mrntypeValue || 13 == mrntypeValue ) ) {    //13 - Auto-generated MRN. Need it for edit or amend form
                            //console.log("validated successfully !");
                        } else {
                            //console.log('mrn='+mrn+', mrntype='+mrntype+ " do not match to form's "+" mrnValue="+mrnValue+", mrntypeValue="+mrntypeValue);

                            var mrnObj = Array();
                            mrnObj["mrnValueForm"] = mrnValue;
                            mrnObj["mrnValueDB"] = mrn;
                            mrnObj["mrntypeIDForm"] = mrntypeValue;
                            mrnObj["mrntypeTextForm"] = mrntypeText;
                            mrnObj["mrnstring"] = mrnstring;
                            mrnObj["patientInput"] = patientInput;

                            var accObj = Array();
                            accObj["accValueForm"] = accValue;
                            accObj["accValueDB"] = null;
                            accObj["acctypeTextForm"] = acctypeText;
                            accObj["acctypeIDForm"] = acctypeValue;
                            accObj["accInput"] = accInput;

                            createDataquality( mrnObj, accObj, orderinfo, index );

                            index++;
                            totalError++;

                            //console.log('end of conflict process');
                        }//if

                    } else {
                        console.debug("validation: accession object not found");
                    }
                },
                error: function ( x, t, m ) {
                    console.debug("validation: get object ajax error accession");
                    if( t === "timeout" ) {
                        getAjaxTimeoutMsg();
                    }
                    return false;
                }
            });

        }

    });

    //console.log("totalError="+totalError);
    //return false; //testing

    if( totalError == 0 ) {
        return true;
    } else {
        return false;
    }

}

function checkIfMrnAccConflictHandled() {

    //Initial check: get total number of checkboxes
    var totalcheckboxes = 0;

    var reruncount = 0;

    //console.log( "dataquality_message1[0]="+dataquality_message1[0] );
    //console.log( "dataquality_message2[0]="+dataquality_message2[0] );

    var countErrorBoxes = 0;

    var errorBoxes = $('#validationerror').find('.validationerror-added');
    //console.log("errorBoxes.length="+errorBoxes.length);

    for (var i = 0; i < errorBoxes.length; i++) {

        var errorBox = errorBoxes.eq(i);

        var checkedEl = errorBox.find("input:checked");
        //console.log("checkedEl="+checkedEl.val()+", id="+checkedEl.attr("id")+", class="+checkedEl.attr("class"));

        var checkedVal = checkedEl.val();
        //console.log("value="+checkedVal);

        if( checkedEl.is(":checked") ){
            //console.log("checked value="+checkedVal);
            if( checkedVal == "OPTION3" ) {
                reruncount++;
            }
            if( checkedVal == "OPTION1" ) {
                setDataquality( countErrorBoxes, dataquality_message1[countErrorBoxes] );
            }
            if( checkedVal == "OPTION2" ) {
                setDataquality( countErrorBoxes, dataquality_message2[countErrorBoxes] );
            }
        } else {
            //alert("Please select one of these options.");
        }
        totalcheckboxes++;

        countErrorBoxes++;

    }

    //clear array
    dataquality_message1.length = 0;
    dataquality_message2.length = 0;

    //console.log("totalcheckboxes="+totalcheckboxes+",reruncount="+reruncount);


    if( totalcheckboxes == 0 ) {
        //continue
        //console.log("totalcheckboxes is zero");
//    } else if( totalcheckboxes != 0 && totalcheckboxes == reruncount ) {    //all error boxes have third option checked
//        cleanValidationAlert();
    } else if( totalcheckboxes > 0 && reruncount > 0 ) { //submit was already pressed before and the third option is checked
        //console.log("conflict is not handled => clean validation alerts");
        cleanValidationAlert();
    } else {    //return true;
        //console.log("conflict handled => return true");
        //return false; //testing
        return true;
    }

    //validate form again
    //console.log("validate form again => return false");
    return false;
}

//create MRN-ACC conflict questions and highlight by red the error fields
function createDataquality( mrnObj, accObj, orderinfo, index ) {   //mrnValueForm, mrnValueDB, mrntypeTextForm, accValueForm, accValueDB, acctypeTextForm, mrnstring, orderinfo ) {

    var prototype = $('#form-prototype-data').data('prototype-dataquality');
    //console.log("prototype="+prototype);

    var nl = "\n";    //"&#13;&#10;";

    var mrnValueForm = mrnObj["mrnValueForm"];
    var mrnValueDB = mrnObj["mrnValueDB"];
    var mrntypeIDForm = mrnObj["mrntypeIDForm"];
    var mrntypeTextForm = mrnObj["mrntypeTextForm"];
    var mrnstring = mrnObj["mrnstring"];
    var patientInput = mrnObj["patientInput"];

    var accValueForm = accObj["accValueForm"];
    var accValueDB = accObj["accValueDB"];
    var acctypeTextForm = accObj["acctypeTextForm"];
    var acctypeIDForm = accObj["acctypeIDForm"];
    var accInput = accObj["accInput"];

    //console.log("create data quality: mrnValueForm="+mrnValueForm+", mrnValueDB="+mrnValueDB+", accValueForm="+accValueForm+", accValueDB="+accValueDB);

    var message_short = "MRN-ACCESSION CONFLICT:"+nl+"Entered Accession Number "+accValueForm+" ["+acctypeTextForm+"] belongs to Patient with "+mrnstring+", not Patient with MRN "
        +mrnValueForm+" ["+mrntypeTextForm+"] as you have entered.";
    var message = message_short + " Please correct either the MRN or the Accession Number above.";


    var message1 = "If you believe MRN "+mrnValueForm+" and MRN "+mrnValueDB + " belong to the same patient, please mark here:";
    var dataquality_message_1 = message_short+nl+"I believe "+mrnstring+" and MRN "+mrnValueForm+" ["+mrntypeTextForm+"] belong to the same patient";
    dataquality_message1.push(dataquality_message_1);

    var message2 = "If you believe Accession Number "+accValueForm+" belongs to patient MRN "+mrnValueForm+" and not patient MRN "+mrnValueDB+" (as stated by "+orderinfo+"), please mark here:";
    var dataquality_message_2 = message_short+nl+"I believe Accession Number "+accValueForm+" belongs to patient MRN "+mrnValueForm+" ["+mrntypeTextForm+"] and not patient "+mrnstring+" (as stated by "+orderinfo+")";
    dataquality_message2.push(dataquality_message_2);

    var message3 = "If you have changed the involved MRN "+mrnValueForm+" or the Accession Number "+accValueForm+" in the form above, please mark here:";

    if( !prototype ) {
        //console.log('WARNING: conflict prototype is not found!!!');
        return false;
    }

    var newForm = prototype.replace(/__dataquality__/g, index);

    newForm = newForm.replace("MRN-ACCESSION CONFLICT", message);

    newForm = newForm.replace("TEXT1", message1);
    newForm = newForm.replace("TEXT2", message2);
    newForm = newForm.replace("TEXT3", message3);

    //console.log("newForm="+newForm);

    var newElementsAppended = $('#validationerror').append(newForm);
    //var newElementsAppended = newForm.appendTo("#validationerror");

    //red
    if( accInput && patientInput ) {
        accInput.parent().addClass("has-error");
        patientInput.parent().addClass("has-error");
    }

    setDataqualityData( index, accValueForm, acctypeIDForm, mrnValueForm, mrntypeIDForm );
}

function setDataquality(index,message) {
    var partid = "#oleg_orderformbundle_orderinfotype_dataquality_"+index+"_";
    //console.log("message=" + message);
    $(partid+'description').val(message);
}


function setDataqualityData( index, accession, acctype, mrn, mrntype ) {
    var partid = "#oleg_orderformbundle_orderinfotype_dataquality_"+index+"_";
    //console.log("set Dataquality Data: "+accession + " " + acctype + " " + mrn + " " + mrntype);
    $(partid+'accession').val(accession);
    $(partid+'accessiontype').val(acctype);
    $(partid+'mrn').val(mrn);
    $(partid+'mrntype').val(mrntype);
}

function checkExistingKey(name) {

    if( orderformtype == "single") {
        if( name == 'accession' ) {
            //var elements = $('#accession-single').find('.keyfield');
            var elements = $('.btn.btn-default.accessionbtn');
        }
        if( name == 'patient' ) {
            //var elements = $('.patientmrn').find('.keyfield');
            var elements = $('.btn.btn-default.patientmrnbtn');
        }
    } else {
        if( name == 'accession' ) {
            //var elements = $('.accessionaccession').find('.keyfield');
            var elements = $('.btn.btn-default.accessionaccessionbtn');
        }
        if( name == 'patient' ) {
            var elements = $('.btn.btn-default.patientmrnbtn');
        }
    }

    var len = elements.length;
    //console.log("len="+len);
    if( len == 0 ) {
        return false;
    }

    //for all accession or mrn buttons
    elements.each(function() {

        var elInput = $(this);
        //printF(elInput,"elInput element:");

        if( orderformtype == "single") {
            var single = true;
        } else {
            var single = false;
        }

        //var keyElement = findKeyElement(elInput, false);
        var keyElement = new btnObject(elInput);

        if( !keyElement || !keyElement.element ) {
            return false;
        }

        var elValue = keyElement.key;
        var eltypeValue = keyElement.type;
        var eltypeValueText = keyElement.typename;

        //printF(elInput,"input element:");
        //console.log("elValue="+elValue + ", eltypeValue=" + eltypeValue );

        //return false;

        if(
            elValue && elValue != "" && eltypeValueText && eltypeValueText.indexOf("Existing Auto-generated") != -1
        )
        {

            elValue = trimWithCheck(elValue);
            eltypeValue = trimWithCheck(eltypeValue);

            $.ajax({
                url: urlCheck+name+'/check',
                type: 'GET',
                data: {key: elValue, extra: eltypeValue},
                contentType: 'application/json',
                dataType: 'json',
                timeout: _ajaxTimeout,
                async: false,
                success: function (data) {
                    //console.debug("get element ajax ok");
                    if( data == -2 ) {
                        var errorHtml = createErrorWell(keyElement.element,name);
                        $('#validationerror').append(errorHtml);
                        return false;
                    }
                },
                error: function ( x, t, m ) {
                    console.debug("validation: get object ajax error "+name);
                    if( t === "timeout" ) {
                        getAjaxTimeoutMsg();
                    }
                    return false;
                }
            });

        }
    });

    //return false;
    return true;
}

function checkSpecifyAnotherIssuer( name ) {


    if( name == 'accession' ) {
        var elements = $('input.accessiontype-combobox');
        var errtext = "Please type in the name of the Specimen ID Issuer above";
    }
    if( name == 'patient' ) {
        var elements = $('input.mrntype-combobox');
        var errtext = "Please type in the name of the Patient ID Issuer above";
    }


    var len = elements.length;
    //console.log("len="+len);
    if( len == 0 ) {
        return false;
    }

    //for all accession or mrn buttons
    elements.each(function() {

        //var elInput = $(this);

        var keytypeText = $(this).select2('data').text;
        //console.log('keytypeText='+keytypeText);

        if( keytypeText == 'Specify Another Specimen ID Issuer' || keytypeText == 'Specify Another Patient ID Issuer' ) {

            //console.log('error: Specify Another is set!');
            var errorHtml = createErrorWell( $(this), name, errtext );
            $('#validationerror').append(errorHtml);

        }

    });


    //return true;
    return false;
}

////////////////////// end of validtion related functions //////////////////////





//TODO: functions to rewrite


//all: "all" => disable/enable all fields including key field
//flagKey: "notkey" => disable/enable all fields, but not key field (inverse key)
//flagArrayField: "notarrayfield" => disable/enable array fields
function disableInElementBlock( element, disabled, all, flagKey, flagArrayField ) {

    //console.log("disable element.id=" + element.attr('id') + ", class=" + element.attr("class") );

    var parentname = ""; //for multi form
    if( element.hasClass('accessionbtn') ) {
        parentname = "accession";
    }
    if( element.hasClass('partbtn') ) {
        parentname = "part";
    }
    if( element.hasClass('blockbtn') ) {
        parentname = "block";
    }
    if( element.hasClass('patientmrnbtn') ) {
        parentname = "patient";
    }

    var parent = getButtonElementParent( element );

    //console.log("parent.id=" + parent.attr('id') + ", parent.class=" + parent.attr('class'));

    var elements = parent.find(selectStr).not("*[id^='s2id_']");

    //console.log("elements.length=" + elements.length);

    for (var i = 0; i < elements.length; i++) {

        //console.log("\n\nDisable element.id=" + elements.eq(i).attr("id")+", class="+elements.eq(i).attr("class"));
        //  0         1              2           3   4  5
        //oleg_orderformbundle_orderinfotype_patient_0_mrn  //length=6
        var id = elements.eq(i).attr("id");
        var type = elements.eq(i).attr("type");

        //don't process elements not belonging to this button
        if( fieldBelongsToButton( element, elements.eq(i) ) === false ) {
            //console.log("this field does not belong to clicked button");
            continue;
        }

        //don't process slide fields
        if( id && id.indexOf("_slide_") != -1 ) {
            continue;
        }

        //don't process fields not containing patient (orderinfo fields)
        if( id && id.indexOf("_patient_") == -1 ) {
            continue;
        }

//        console.log("proceed before submitted by single form ...");
        //don't process patient fields if the form was submitted by single form: click on accession,part,block delete button
//        if( orderformtype == "single" && id && id.indexOf("_procedure_") == -1 ) {
//            continue;
//        }

        //don't process 0 disident field: part's Diagnosis :
        if( orderformtype == "single" && id && id.indexOf("disident_0_field") != -1 ) {
            continue;
        }

        //console.log("proceed before patient's name,sex,age ...");

        //don't process constatly locked fields: patient's name,sex,age
        if( elements.eq(i).hasClass('patientname-field') ) {
            continue;
        }
        if( elements.eq(i).hasClass('patientsex-field') ) {
            continue;
        }
        if( elements.eq(i).hasClass('patientage-field') ) {
            continue;
        }

        if( id && type != "hidden" ) {

            var thisfieldIndex = fieldIndex;
            if( type == "radio" ) {
                var thisfieldIndex = fieldIndex + 1;
            }

            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-thisfieldIndex];
            //console.log("disable field=(" + field + ")");

            if( all == "all" ) {
                disableElement(parentname, elements.eq(i),disabled);
            }

            if( flagKey == "notkey" ) {
                //check if the field is not key
                //printF(elements.eq(i),"check " + field+" if key: ");
                if( isKey(elements.eq(i), field) && flagKey == "notkey" ) {
                    //console.log("key!");
                    if( disabled ) {    //inverse disable flagKey for key field
                        //console.log("disable field=(" + field + ")");
                        disableElement(parentname,elements.eq(i),false);
                    } else {
                        //console.log("enable field=(" + field + ")");
                        disableElement(parentname,elements.eq(i),true);
                    }
                } else {
                    //console.log("not key!");
                    disableElement(parentname,elements.eq(i),disabled);
                }
            }

            if( flagArrayField == "notarrayfield" ) {
                if( $.inArray(field, arrayFieldShow) != -1 ) {
                    //console.log("Arrayfield: disable/enable array id="+elements.eq(i).attr("id"));
                    if( elements.eq(i).attr("id") && elements.eq(i).attr("id").indexOf(field+"_0") != -1 ) {
                        //console.log(field+"_0_field'");
                        if( disabled ) {    //inverse disable flag for key field
                            disableElement(parentname,elements.eq(i),false);
                        } else {
                            disableElement(parentname,elements.eq(i),true);
                        }
                    }
                }               
            }

        }

    }
}

//disable or enable element
function disableElement(parentname,element, flag) {

    var type = element.attr('type');
    var classs = element.attr('class');
    var tagName = element.prop('tagName');

    //console.log("disable classs="+classs+", tagName="+tagName+", type="+type+", id="+element.attr('id')+", flag="+flag);

    //return if this element does not belong to a pressed key element
    var idArr = element.attr('id').split("_");
    var fieldParentName = idArr[idArr.length-holderIndex];
    if( fieldParentName == "procedure" ) {
        fieldParentName = "accession";
    }

    //exception for simple fields; used for tooltip
//    if(
//        element.hasClass('procedurename-field') ||
//        element.hasClass('proceduresex-field') ||
//        element.hasClass('procedureage-field') ||
//        element.hasClass('proceduredate-field') ||
//        element.hasClass('procedurehistory-field')
//    ) {
//        fieldParentName = "accession";
//    }

    //console.log("fieldParentName="+fieldParentName+", parentname="+parentname);
    if( parentname == "" || parentname == fieldParentName ) {
        //console.log("continue");
    } else {
        return;
    }

    attachTooltip(element,flag,fieldParentName);

    if( tagName == "DIV" && classs.indexOf("select2") == -1 ) { //only for radio group
        //console.debug("radio disable classs="+classs+", id="+element.attr('id'));
        processGroup( element, "", flag );
        return;
    }

    if( tagName == "SELECT" || typeof classs !== "undefined" && classs.indexOf("select2") != -1 && ( tagName == "DIV" || tagName == "INPUT" ) ) { //only for select group
        //console.log("select disable classs="+classs+", id="+element.attr('id')+", flag="+flag);
        if( flag ) {    //disable
            //console.log("disable select2");
            element.select2("readonly", true);
        } else {    //enable
            //console.log("enable select2");
            element.select2("readonly", false);
            element.attr("readonly", false);
            element.removeAttr( "readonly" );
            //element.removeAttr( "disabled" );

        }
        return;
    }

    if( flag ) {

        if( type == "file" ) {
            //console.log("file disable field id="+element.attr("id"));
            element.attr('disabled', true);
        } else {
            //console.log("general disable field id="+element.attr("id"));
            element.attr('readonly', true);
        }

        if( classs && classs.indexOf("datepicker") != -1 ) {
            //console.log("disable datepicker classs="+classs);
            processDatepicker(element,"remove");
        }

        //disable children buttons
        element.parent().find("span[type=button],button[type=button]").attr("disabled", "disabled");

    } else {

        if( type == "file" ) {
            //console.log("file enable field id="+element.attr("id"));
            element.attr('disabled', false);
        } else {
            //console.log("general enable field id="+element.attr("id"));
            element.attr("readonly", false);
            element.removeAttr( "readonly" );
        }

        //enable children buttons
        element.parent().find("span[type=button],button[type=button]").removeAttr("disabled");

        if( classs && classs.indexOf("datepicker") != -1 ) {
            //console.log("enable datepicker classs="+classs);
            processDatepicker(element);
        }

    }
}

//set Element. Element is a block of fields
//element: check_btn element
//cleanall: clean all fields
//key: set only key field
function setElementBlock( element, data, cleanall, key ) {

//    //console.debug( "element.id=" + element.attr('id') + ", class=" + element.attr('class') );
//    var parent = element.parent().parent().parent().parent().parent().parent();
//    //console.log("set parent.id=" + parent.attr('id') + ", class=" + parent.attr('class') + ", key="+key);
//
//    //var single = false;
//    if( orderformtype == "single" ) {
//    //if( !parent.attr('id') ) {
//        //var single = true;
//        var parent = element.parent().parent().parent().parent().parent().parent().parent();
//        console.log("Single set! parent.id=" + parent.attr('id') + ", class=" + parent.attr('class') + ", key="+key);
//    }

    var parent = getButtonElementParent( element );

    //console.log(parent);
    //console.log("key="+key+", single="+single);
    //printF(parent,"Set Element Parent: ");

    if( key == "key" && orderformtype == "single" && !element.hasClass("patientmrnbtn") ) {
        var inputField = element.parent().find('.keyfield').not("*[id^='s2id_']");
        //console.log("inputField.id=" + inputField.attr('id') + ", class=" + inputField.attr('class'));
        //console.log(inputField);
        var idsArrTemp = inputField.attr("id").split("_");
        var field = idsArrTemp[idsArrTemp.length-fieldIndex];    //default
        //console.log("Single Key field=" + field);
        if( field == "partname" ) {
            var elements = $('#part-single').find('.keyfield').not("*[id^='s2id_']");
        } else if( field == "blockname" ) {
            var elements = $('#block-single').find('.keyfield').not("*[id^='s2id_']");
        } else if( field == "accession" ) {
            //var elements = $('#accession-single').find('.keyfield').not("*[id^='s2id_']");
            var elements = $('.singleorderinfo').find('.accessiontype-combobox').not("*[id^='s2id_']");    //treat accession as a group
        } else if( field == "mrn" ) {
            var elements = $('.singleorderinfo').find('.mrntype-combobox').not("*[id^='s2id_']");    //treat mrn as a group
        } else {
            //console.debug('WARNING: logical error! No key for single order form is found: field='+field);
        }
    } else {
        //console.log("regular set element block");
        var elements = parent.find(selectStr).not("*[id^='s2id_']");
    }

    //console.log("elements.length=" + elements.length);

    for( var i = 0; i < elements.length; i++ ) {

        //console.log('\n\n'+"Set Element.id=" + elements.eq(i).attr("id")+", class="+elements.eq(i).attr("class"));

        /////////////// exception for simple fields /////////////////////////
//        var simpleField = getSimpleFieldName( elements.eq(i) );
//        if( simpleField && (simpleField in data) ) {
//            var simpleValue = data[simpleField];
//            //console.log("simple field value="+simpleField+", simpleValue="+simpleValue);
//            if( simpleField == 'patsex' ) {
//                var dataArr = {text: simpleValue};
//                processGroup( elements.eq(i), dataArr, "ignoreDisable" );
//            } else {
//                elements.eq(i).val(simpleValue);
//            }
//            continue;
//        }
        /////////////// EOF exception for simple fields /////////////////////////

        //  0         1              2           3   4  5
        //oleg_orderformbundle_orderinfotype_patient_0_mrn  //length=6
        var id = elements.eq(i).attr("id");
        var type = elements.eq(i).attr("type");
        var classs = elements.eq(i).attr("class");
        var value = elements.eq(i).attr("value");
        //console.log("id=" + id + ", type=" + type + ", class=" + classs + ", value=" + value );

        //don't process elements not belonging to this button
        if( fieldBelongsToButton( element, elements.eq(i) ) === false ) {
            continue;
        }

        //exception
        if( id && id.indexOf("primaryOrgan") != -1 ) {
            //console.log("skip id="+id);
            continue;
        }

        //don't process ajax-combobox-staintype. It will be populated by block's field field
        if( elements.eq(i).hasClass('ajax-combobox-staintype') ) {
            continue;
        }

        if( id ) {

            var idsArr = elements.eq(i).attr("id").split("_");
            var field = idsArr[idsArr.length-fieldIndex];    //default
            //console.log("######## field = " + field);// + ", data text=" + data[field]['text']);

            if( key == "key" ) {

                if( $.inArray(field, keys) != -1 ) {
                    //console.log("set key field = " + data[field][0]['text'] );
                    setArrayField( elements.eq(i), data[field], parent );
                    //elements.eq(i).val(data[field]);
                    break;
                }
            }

//            if( type == "radio" ) {
//                field = idsArr[idsArr.length-(fieldIndex + 1)];
//            }

            if( type == "hidden" ) {
                field = idsArr[idsArr.length-(fieldIndex + 1)];
            }

            if( data == null  ) {   //clean fields
                //console.log("data is null");
                if( $.inArray(field, keys) == -1 || cleanall) {
                    elements.eq(i).val(null);   //clean non key fields
                } else {
                    //console.log("In array. Additional check for field=("+field+")");
                    if( field == "partname" ) {
                        var holder = idsArr[idsArr.length-holderIndex];
                        //console.log("holder="+holder);
                        if( holder != "part" && holder != "block" ) {
                            //console.log("disable!!!!");
                            elements.eq(i).val(null);   //clean non key fields with filed "name"
                        }
                    }
                }
            } else {

                //get field name for select fields i.e. procedure
                if( classs && classs.indexOf("select2") != -1 ) {

                    holder = idsArr[idsArr.length-holderIndex];
                    //console.log("select2 holder="+holder);
                    if( holder != "part" && holder != "block" && holder != "patient" ) {
                        field = holder;
                        //console.log("new field="+field);
                    }
                }

                //console.log("2 field = " + field);
                if( data[field] && data[field] != undefined && data[field] != "" ) {
                    //console.log("data is not null: set text for field " + field);
                    setArrayField( elements.eq(i), data[field], parent );
                } else {
                    //console.log("data is empty: don't set text field");
                }

                //console.log("diseaseTypeRender");
                //diseaseTypeRender();

            }

        }

    } //for

}

//set array field such as ClinicalHistory array fields
//element is an input element jquery object
function setArrayField(element, dataArr, parent) {

    //console.log(dataArr);

    if( !dataArr ) {
        return false;
    }

    var type = element.attr("type");
    var classs = element.attr("class");
    var tagName = element.prop("tagName");
    var value = element.attr("value");
    //console.log("Set array: type=" + type + ", id=" + element.attr("id")+", classs="+classs + ", len="+dataArr.length + ", value="+value+", tagName="+tagName);

    for (var i = 0; i < dataArr.length; i++) {

        //var dataArr = data[field];
        var id = dataArr[i]["id"];
        var text = dataArr[i]["text"];
        var provider = dataArr[i]["provider"];
        var date = dataArr[i]["date"];
        var validity = dataArr[i]["validity"];
        var coll = i+1;

        //console.log( "set array field i="+i+", id="+id+", text=" + text + ", provider="+provider+", date="+date + ", validity="+validity );

        //if(
            //(validity == 'invalid' && dataArr.length > 1)
                //&&
            //!(validity == 'invalid' && dataArr.length == 1 && provider == user_name )
        //) {
        if( validity == 'invalid' && dataArr.length > 1 ) {
            continue;
        }

        //console.log("parent id=" + parent.attr("id"));
        var idsArr = parent.attr("id").split("_");
        var elementIdArr = element.attr("id").split("_");
        //console.log("in loop parent.id=" + parent.attr("id") + ", tagName=" + tagName + ", type=" + type + ", classs=" + classs + ", text=" + text );

        var fieldName = elementIdArr[elementIdArr.length-fieldIndex];
        var holderame = elementIdArr[elementIdArr.length-holderIndex];
        var ident = holderame+fieldName;
        //console.log("ident=" + ident + ", coll="+coll );

        //var attachElement = element.parent().parent().parent().parent().parent();

        var attachElement = parent.find("."+ident.toLowerCase());   //patientsex

        //console.log("attachElement class="+attachElement.attr("class")+",id="+attachElement.attr("id"));

        if( $.inArray(fieldName, arrayFieldShow) != -1 ) { //show all fields from DB

            //var name = idsArr[0];
            var patient = idsArr[1];
            var procedure = idsArr[2];
            var accession = idsArr[3];
            var part = idsArr[4];
            var block = idsArr[5];
            var slide = idsArr[6];

            //console.log("Create array empty field, fieldName=" + fieldName + ", patient="+patient+", part="+part );

            var newForm = getCollField( ident, patient, procedure, accession, part, block, slide, coll );
            //console.log("newForm="+newForm);

            var origId = id;
            if( fieldName == "specialStains" ) {
                //special stain has id of the staintipe select box
                id = dataArr[i]["staintype"];
            }

            var labelStr = " entered on " + date + " by "+provider + "</label>";
            newForm = newForm.replace("</label>", labelStr);

            var idStr = 'type="hidden" value="'+id+'" ';
            newForm = newForm.replace('type="hidden"', idStr);

            //console.log("newForm="+newForm);

            if( fieldName == "disident" && orderformtype == "single" ) {
                //attachElement
                attachElement = $('.partdiffdisident');
                //console.log("attachElement class="+attachElement.attr("class")+",id="+attachElement.attr("id"));
                $('#partdisident_marker').append(newForm);
            } else {
                //console.log("attachElement class="+attachElement.attr("class")+",id="+attachElement.attr("id"));
                attachElement.prepend(newForm);
            }

            if( fieldName == "specialStains" ) {
                //pre-populate select2 with stains
                getComboboxSpecialStain(urlCommon,new Array(patient,procedure,accession,part,block,coll),true,id);
            }

        } else {    //show the valid field (with validity=1)
            //console.log("NO array Fiel dShow");
        }

        //set data
        if( tagName == "INPUT" ) {
            //console.log("input tagName: fieldName="+fieldName);

            if( type == "file" ) {

                element.hide();
                //var paperLink = '<a href="../../../../web/uploads/documents/'+dataArr[i]["path"]+'" target="_blank">'+dataArr[i]["name"]+'</a>';
                var paperLink = text;
                //console.log("paperLink="+paperLink);
                element.parent().append(paperLink);

            } else if( type == "text" ) {
                //console.log("type text, text="+text);

                if( fieldName == "accession" || fieldName == "mrn" ) {
                    setKeyGroup(element,dataArr[i]);
                    continue;
                }

                //save keys for single form, because all keys will be removed by the first clean functions
                if( orderformtype == "single") {
                    if( fieldName == "partname" ) {
                        partKeyGlobal = text;
                    }
                    if( fieldName == "blockname" ) {
                        blockKeyGlobal = text;
                    }
                }

                //find the last attached element to attachElement

                var firstAttachedElement = attachElement.find('input,textarea').first();
                
                //printF(firstAttachedElement,"firstAttachedElement: ");

                if( fieldName == "partname" || fieldName == "blockname" ) {
                    if( orderformtype == "single" ) {
                        var firstAttachedElement = element;
                    } else {
                        var firstAttachedElement = attachElement.find('.keyfield ').first();
                    }
                    //printF(firstAttachedElement,"firstAttachedElement=");
                    firstAttachedElement.select2('data', {id: text, text: text});
                } else {
                    if( classs.indexOf("select2") != -1 ) {
                        var firstAttachedElement = element;
                        //printF(firstAttachedElement,"firstAttachedElement=");
                        //console.log("!!!!!!!!!!!! Set Value as select="+text+", id="+id);
                        firstAttachedElement.select2('data', {id: text, text: text});
                        //firstAttachedElement.select2('val', id);
                    } else {
                        //console.log("!!!!!!!!!!!! Set Value text="+text);
                        firstAttachedElement.val(text);
                    }
                }


            } else if( classs && classs.indexOf("datepicker") != -1 ) {
                //console.log("datepicker");
                var firstAttachedElement = attachElement.find('input').first();
                if( text && text != "" ) {
                    firstAttachedElement.datepicker( 'setDate', new Date(text) );
                    firstAttachedElement.datepicker( 'update');
                } else {
                    //firstAttachedElement.datepicker({autoclose: true});
                    initSingleDatepicker(firstAttachedElement);
                    //firstAttachedElement.val( 'setDate', new Date() );
                    //firstAttachedElement.datepicker( 'update');
                }
            }

        } else if ( tagName == "TEXTAREA" ) {

            if( fieldName == "disident" && orderformtype == "single" ) {
                var firstAttachedElement = $('#partdisident_marker').find('.row').find('textarea'); //the last diffDiagnosis field is part's disident field
                //console.log("disident: " + firstAttachedElement.attr("class")+",id="+firstAttachedElement.attr("id") + ", text="+text);
            } else {
                var firstAttachedElement = attachElement.find('textarea').first();
            }

            //console.log("textarea firstAttachedElement class="+firstAttachedElement.attr("class")+",id="+firstAttachedElement.attr("id") + ", text="+text);
            firstAttachedElement.val(text);

        } else if ( (tagName == "DIV" && classs.indexOf("select2") != -1) || tagName == "SELECT" ) {

            //console.log("### DIV select2:  select field, id="+id+",text="+text);
            //console.log("id="+element.attr("id"));

            //set mrntype
            if( fieldName == "mrn" || fieldName == "accession" ) {
                //mrnKeyGlobal = text;
                //mrnKeytypeGlobal = dataArr[i]["keytype"];
                setKeyGroup(element,dataArr[i]);
            } else {
                element.select2('data', {id: text, text: text}); //TODO: set by id .select2.('val':id);
            }

        } else if ( tagName == "DIV" ) {
            //console.log("### set array field as DIV, id="+element.attr("id")+", text="+text );
            //get the first (the most recent added) group
            var firstAttachedElement = attachElement.find('.horizontal_type').first();
            processGroup( firstAttachedElement, dataArr[i], "ignoreDisable" );
        } else {
            //console.log("logical error: undefined tagName="+tagName);
        }

        //set hidden id of the element
        var directParent = element.parent().parent().parent();
        //console.log("hidden directParent="+directParent.attr("id") + ", class="+directParent.attr("class") );
        if( $.inArray(fieldName, arrayFieldShow) == -1 ) {
            var hiddenElement = directParent.find('input[type=hidden]');
            hiddenElement.val(id);
            //console.log("set hidden "+fieldName+", set id="+id + " hiddenId="+hiddenElement.attr("id") + " hiddenClass="+hiddenElement.attr("class") );
        }

    } //for loop

}

//set key type field. Used by set and clean functions
//element - is key type element (combobox): id=oleg_orderformbundle_orderinfotype_patient_0_procedure_0_accession_0_accession_0_keytype
function setKeyGroup( element, data ) {
    //console.log("########### set key group: element id="+element.attr("id") + ", class="+element.attr("class")+", keytype="+data['keytype']+", text="+data['text']);

    if( element.attr('class').indexOf("combobox") == -1 ) {
        //console.log("key group: not a a keytype combobox => return");
        return;
    }

    var holder = element.closest('.row');
    //printF(holder,"Holder of key group:");

    //var keytypeEl = holder.find('select.combobox');
    var keytypeEl = holder.find('.combobox').first();
    //var keytypeEl = element;
    //var keytypeEl = new typeByKeyInput(element).typeelement;
    //var typeObj = new typeByKeyInput(element);
    //this.type = typeObj.type;
    //this.typename = typeObj.typename;
    //var keytypeEl = typeObj.typeelement;

    //printF(keytypeEl,"Set Key Group: keytype Element:");

    //do not change type only if current type is "existing.." and returned keytypename is "auto-generated"
    var currentKeytypeText = keytypeEl.select2("data").text;
    var currentKeytypeId = keytypeEl.select2("data").id;
    var currentKeytypeVal = keytypeEl.select2("val");

    var tosetKeytypeText = data['keytypename'];

    //console.log('Keytype: tosetKeytypeText='+tosetKeytypeText +', currentKeytypeText='+currentKeytypeText+", currentKeytypeId="+currentKeytypeId+", currentKeytypeVal="+currentKeytypeVal);

    if( tosetKeytypeText && tosetKeytypeText.indexOf("Auto-generated") != -1 && currentKeytypeText.indexOf("Existing Auto-generated") != -1 ) {
        //don't change type
        //console.log('dont change keytype: tosetKeytypeText='+tosetKeytypeText);
    } else {
        //console.log('change keytype: tosetKeytypeText='+tosetKeytypeText);
        keytypeEl.select2('val', data['keytype']);
    }

    //element.select2( 'data', { text: data['keytypename'] } );

    //TODO: what to do when amend with check boxes
    if( element.hasClass('mrntype-combobox') ) {
        setMrntypeMask(element,false); //true
    }
    if( element.hasClass('accessiontype-combobox') ) {
        setAccessiontypeMask(element,false); //true
    }
    //console.log("Set Key Group: asseccionKeyGlobal="+asseccionKeyGlobal+", asseccionKeytypeGlobal="+asseccionKeytypeGlobal+", partKeyGlobal="+partKeyGlobal+", blockKeyGlobal="+blockKeyGlobal+", mrnKeyGlobal="+mrnKeyGlobal+", mrnKeytypeGlobal="+mrnKeytypeGlobal);

    var inputholder = getButtonParent(element);
    var keyEl = inputholder.find('input.keyfield');
    //console.log("set keytype group: keyEl id="+keyEl.attr("id") + ", class="+keyEl.attr("class")+", keyEl.length="+keyEl.length);
    keyEl.val(data['text']);
}

//process groups such as radio button group
function processGroup( element, data, disableFlag ) {

    //printF(element,"process group:");

    if( typeof element.attr("id") == 'undefined' || element.attr("id") == "" ) {
        return;
    }

    var elementIdArr = element.attr("id").split("_");
    var fieldName = elementIdArr[elementIdArr.length-(fieldIndex+1)];

    //var element = elementInside.parent().parent().parent();
    //var radios = element.find("input:radio");

    //console.log("process group id="+element.attr("id")+ ", class="+element.attr("class") + ", fieldName="+fieldName );

    var partId = 'input[id*="'+fieldName+'_"]:radio';
    var members = element.find(partId);

    for( var i = 0; i < members.length; i++ ) {
        var localElement = members.eq(i);
        var value = localElement.attr("value");
        //console.log("radio id: " + localElement.attr("id") + ", value=" + value );

        if( disableFlag == "ignoreDisable" ) {  //use to set radio box

            if( data && data != "" ) {  //set fields with data
                //console.log("data ok, check radio (data): " + value + "?=" + data['text'] );
                if( value == data['text'] ) {
                    //console.log("Match!" );
                    //console.log("show and set children: disableFlag="+disableFlag+", origin="+data['origin']+", primaryorgan="+data['primaryorgan']);
                    localElement.prop('checked',true);
                    diseaseTypeRenderCheckForm(element,data['origin'],data['primaryorgan']);    //set diseaseType group
                }
            } else {
                //console.log("no data radio: value=" + value);
                //console.log("hide children: disableFlag="+disableFlag);
                localElement.prop('checked',false);
                hideDiseaseTypeChildren( element ); //unset and hide diseaseType group

            }

        } else  {
            if( disableFlag ) {
                //console.log("disable radio: value=" + value);
                if( localElement.is(":checked") ){
                    localElement.attr("disabled", false);
                } else {
                    localElement.attr("disabled", true);
                }
            } else {
                //console.log("enable radio: value=" + value);
                localElement.prop("disabled", false);
            }
        }

    }

}

//check for single form if the field belongs to the button
function fieldBelongsToButton(btn,fieldEl) {

    if( orderformtype != "single") {
        return true;
    }

    var id = fieldEl.attr('id');

    if( !id || typeof id === "undefined" || id == "" ) {
        return false;
    }

    var idsArr = id.split("_");
    //var fieldName = idsArr[idsArr.length-fieldIndex];
    var holdername = idsArr[idsArr.length-holderIndex];

    var btnObj = new btnObject(btn);

    //compare button name with holdername: 'patient' ?= 'accession'
    //console.log("compare:"+btnObj.name+"?="+holdername);
    if( btnObj.name == holdername ) {
        return true;
    }

    //excemption: procedure does not have its own button; it is triggered by accession
    if( btnObj.name == 'accession' && holdername == 'procedure' ) {
        return true;
    }

    return false;
}

function cleanArrayFieldSimple( element, field, single ) {
    //console.log( "clean simple array field id=" + element.attr("id") );

    //delete if id != 0
    if( element.attr("id") && element.attr("id").indexOf(field+"_0_field") != -1 ) {
        element.val(null);
    } else {
        element.parent().parent().remove();
    }
}

function cleanBlockSpecialStains( element, field, single ) {

    //printF(element,'clean block element:');

    //don't process special staintype. It will be processed by special stain field.
    if( element.hasClass('ajax-combobox-staintype') ) {
        return;
    }

    //don't process not 0 id. They will be delete by 0 id field
    //if( element.attr('id').indexOf("specialStains_0_field") == -1 ) {
    //    return;
    //}

    //console.log( "\nClean Block Special Stains elements id=" + element.attr("id") + ", field=" + field );

    var fieldHolder = element.closest('.blockspecialstains');
    var fieldInputColls = fieldHolder.find('.fieldInputColl');
    //console.log( "fieldInputColls.length=" + fieldInputColls.length );

    if( fieldInputColls.length == 0 ) {
        return false;
    }

    var stainfieldEl = fieldInputColls.first().find('.input-group-oleg').find('textarea');
    var idsArr = stainfieldEl.attr("id").split("_");

    fieldInputColls.each( function() {
        $(this).closest('.row').remove();
    });

    //construct new 0 special stain group
    var patient = idsArr[1];
    var procedure = idsArr[2];
    var accession = idsArr[3];
    var part = idsArr[4];
    var block = idsArr[5];
    var slide = null;
    var ident = "block"+"specialStains";
    var newForm = getCollField( ident, patient, procedure, accession, part, block, slide, 0 );
    fieldHolder.prepend(newForm);
    getComboboxSpecialStain(urlCommon,new Array(patient,procedure,accession,part,block,0),true);

//    //set to the first item
//    if( field == "specialStains" ) {
//        setElementToId( element, _stain );
//    }
}

function cleanPartDiffDisident( element, field, single ) {

    //console.log( "\nClean Part Diff Disident elements id=" + element.attr("id") + ", field=" + field );

    var fieldHolder = element.closest('.partdiffdisident');
    var fieldInputColls = fieldHolder.find('.form-control');
    //console.log( "fieldInputColls.length=" + fieldInputColls.length );

    if( fieldInputColls.length == 0 ) {
        return false;
    }

    var stainfieldEl = fieldInputColls.first();
    var idsArr = stainfieldEl.attr("id").split("_");

    fieldInputColls.each( function() {
        $(this).closest('.row').remove();
    });

    //construct new 0 special stain group
    var patient = idsArr[1];
    var procedure = idsArr[2];
    var accession = idsArr[3];
    var part = idsArr[4];
    var block = null;
    var slide = null;
    var ident = "part"+"diffDisident";
    var newForm = getCollField( ident, patient, procedure, accession, part, block, slide, 0 );
    fieldHolder.prepend(newForm);
}

function cleanPartDisident( element, field, single ) {

    //console.log( "\nClean Part Disident elements id=" + element.attr("id") + ", field=" + field );

    //just clean tex from 0 field
    if( element.attr('id').indexOf("disident_0_field") != -1 ) {
        element.val(null);
        return;
    }

    var fieldHolder = element.closest('#partdisident_marker');
    var fieldInputColls = fieldHolder.find('textarea');
    //console.log( "fieldInputColls.length=" + fieldInputColls.length );

    if( fieldInputColls.length == 0 ) {
        return;
    }

    fieldInputColls.each( function() {
        $(this).closest('.row').remove();
    });
}

//element - input field element
function cleanArrayField( element, field, single ) {

    //console.log( "Clean field=" + field );

    if( field == "specialStains" ) {
        cleanBlockSpecialStains(element, field, single);
        return;
    }

    if( field == "diffDisident" ) {
        cleanPartDiffDisident(element, field, single);
        return;
    }

    if( field == "disident" && orderformtype == "single" ) {
        cleanPartDisident(element, field, single);
        return;
    }

    if( $.inArray(field, arrayFieldShow) == -1 ) {
        cleanArrayFieldSimple(element,field,single);
        return;
    }


    //clean array field id=oleg_orderformbundle_orderinfotype_patient_0_procedure_0_accession_0_part_0_diffDisident_2_field
    //console.log( "\nclean array element id=" + element.attr("id") + ", field=" + field );
    //delete if id != 0 or its not the last element

    //get row element - fieldHolder
    if( element.is('[readonly]') ) {    //get row for gray out fields without buttons
        //console.log( "readonly" );
        var fieldHolder = element.parent().parent();
    } else {
        //console.log( "not readonly" );
        var fieldHolder = element.parent().parent().parent().parent().parent();
    }

    //console.log( "fieldHolder id=" + fieldHolder.attr("id") + ", class=" + fieldHolder.attr("class") );

    var rows = fieldHolder.parent().find('.row');

    //console.log( "rows.length=" + rows.length );

    if( rows.length == 0 ) {
        return false;
    }

    //if( element.attr("id") && element.attr("id").indexOf(field+"_0_field") != -1 || rows.length == 1 ) {
    if( rows.length == 1 ) {

        element.val(null);

        //change - button (if exists) by + button
        var delBtn = element.parent().find('.delbtnCollField');
        //console.log("work on delBtn id="+delBtn.attr("id")+",class="+delBtn.attr("class"));
        if( delBtn.length != 0 ) {

            //console.log("delBtn exists !");
            //add + btn if not exists
            var addBtn = element.parent().find('.addbtnCollField');
            //console.log("work on addBtn id="+addBtn.attr("id")+",class="+addBtn.attr("class"));
            if( addBtn.length == 0 ) {
                delBtn.after( getAddBtn() );
            }

            delBtn.remove();
        } else {
            //console.log("no delBtn");
        }

        //Optional: change id of all element in row to '0'. This will bring the form to the initial state.
        changeIdtoIndex(element,field,0);

    } else {
        //delete hole row
        //console.log( "prepare to delete: fieldHolder id=" + fieldHolder.attr("id") + ", class=" + fieldHolder.attr("class") );
        //console.log(fieldHolder);
        //disident "Diagnosis" field has fieldHolder "Slide Info" for a single form.
        //It is array field for single form ( arrayFieldShow.push("disident") ), however it is excemption field because it's single
        //if( fieldHolder.attr("id") != "single-scan-order-slide-info" && fieldHolder.hasClass("panel") ) {
        if( !fieldHolder.hasClass("panel") ) {
            //console.log( "delete: fieldHolder id=" + fieldHolder.attr("id") + ", class=" + fieldHolder.attr("class") );
            fieldHolder.remove();
        }
    }
}

function changeIdtoIndex( element, field, index ) {

    //get row element - fieldHolder
    if( element.is('[readonly]') ) {    //get row for gray out fields without buttons
        var fieldHolder = element.parent().parent();
    } else {                            //get row for enabled fields with buttons
        //var fieldHolder = element.parent().parent().parent().parent().parent();
        var fieldHolder = element.parent().parent().parent().parent().parent();
    }

    //change id of the field to 0
    var fieldId = element.attr("id");
    var fieldIdOrig = fieldId;
    var fieldName = element.attr("name");
    //console.log("fieldId="+fieldId+", fieldName="+fieldName);

    var idArr = fieldId.split("_"+field+"_");
    var idValue = idArr[1].split("_")[0];
    //console.log("idValue="+idValue);

    //var regexId = new RegExp( field + '_' + idValue, 'g' );
    fieldId = fieldId.replace( field + '_' + idValue, field + '_' + index);

    //change name of the field to 0
    var nameArr = fieldName.split("["+field+"]");
    var nameValueStr = nameArr[1];
    var nameValueArr = nameValueStr.split("[");
    var nameValue = nameValueArr[1].split("]")[0];
    //console.log("nameValue="+nameValue);

    var strTofind = '[' + field + ']' + '[' + nameValue + ']';
    //console.log("strTofind="+strTofind);    //strTofind=[diffDisident][6]
    var strReplace = '[' + field + ']' + '['+index+']';
    //console.log("strReplace="+strReplace);

    fieldName = fieldName.replace(strTofind, strReplace);   //[diffDisident][0]

    //console.log("fieldId="+fieldId+", fieldName="+fieldName);

    element.attr('id',fieldId);
    element.attr('name',fieldName);

    //replace id of label
    var rows = fieldHolder.parent().find('.row').first();
    //console.log( "rows id=" + rows.attr("id") + ", class=" + rows.attr("class") );

    var rowLabel = rows.first().find($('label[for='+fieldIdOrig+']'));
    //console.log( "rowLabel id=" + rowLabel.attr("id") + ", class=" + rowLabel.attr("class") );

    //var textLabel = rows.first().find($('label[for='+fieldIdOrig+']')).text();
    //console.log( "textLabel=" + textLabel );

    rowLabel.attr('id',fieldId);
    rowLabel.attr('for',fieldId);

    return;
}

//clean fields in Element Block, except key field
//all: if set to "all" => clean all fields, including key field
function cleanFieldsInElementBlock( element, all, single ) {

    var parent = getButtonElementParent( element );

    //console.log("clean single=" + single);

    //console.log("clean parent.id=" + parent.attr('id'));
    //printF(parent,"clean => parent");

    var elements = parent.find(selectStr).not("*[id^='s2id_']");

    for (var i = 0; i < elements.length; i++) {

        var id = elements.eq(i).attr("id");
        var type = elements.eq(i).attr("type");
        var tagName = elements.eq(i).prop('tagName');
        var classs = elements.eq(i).attr('class');

        //console.log("\n\nClean Element id="+id+", classs="+classs+", type="+type+", tagName="+tagName);

        //don't process elements not belonging to this button
        if( fieldBelongsToButton( element, elements.eq(i) ) === false ) {
            //console.log("this field does not belong to clicked button");
            continue;
        }

        //don't process simple fields, these fileds don't have id because they are not part of form
        if( typeof id === 'undefined' ) {
            continue;
        }

        //don't process slide fields
        if( id && id.indexOf("_slide_") != -1 ) {
            continue;
        }
        //don't process fields not containing patient (orderinfo fields)
        if( id && id.indexOf("_patient_") == -1 ) {
            continue;
        }
        //don't process patient fields if the form was submitted by single form: click on accession,part,block delete button
        if( single && id && id.indexOf("_procedure_") == -1 ) {
            //console.log("don't process patient fields if the form was submitted by single form");
            //continue;
        }

        //console.log("clean id="+id+", type="+type+", tagName="+tagName);

        //don't clean key fields belonging to other block button
        if( elements.eq(i).hasClass('keyfield') || elements.eq(i).hasClass('accessiontype-combobox') || elements.eq(i).hasClass('mrntype-combobox') ) {
            var btnObj = new btnObject( element );

            //check type
            if( btnObj.typeelement && btnObj.typeelement.attr('id').replace("s2id_","") == elements.eq(i).attr('id') ) {
                //console.log( "type length="+btnObj.typeelement.length );
                //printF(btnObj.typeelement," Clean type: ");
                //btnObj.typeelement.select2("val", 1 );
                var dataArr = new Array();
                dataArr['text'] = "";
                dataArr['keytype'] = 1;
                setKeyGroup( btnObj.typeelement, dataArr );
            }

            //check field
            //console.log("btn field id="+btnObj.element.attr('id'));
            //console.log("element field id="+elements.eq(i).attr('id'));
            if( btnObj.element && btnObj.element.attr('id') != elements.eq(i).attr('id') ) {
                //console.log("don't clean this field!");
                continue;
            }
        }

        if( type == "file" ) {

            elements.eq(i).parent().find('a').remove();
            elements.eq(i).show();

        } else if( type == "text" || !type ) {

            //console.log("clean as text");
            var clean = false;
            var idsArr = id.split("_");
            var field = idsArr[idsArr.length-fieldIndex];
            if( all == "all" ) {
                clean = true;
            } else {
                //check if the field is not key
                if( !isKey(elements.eq(i), field) ) {
                    clean = true;
                }
            }
            if( clean ) {
                //console.log("in array field=" + field );
                if( $.inArray(field, arrayFieldShow) == -1 ) {
                   //console.log("clean not as arrayFieldShow");

                    if( tagName == "DIV" && classs.indexOf("select2") == -1 ) {
                        //console.log("clean as radio");
                        processGroup( elements.eq(i), "", "ignoreDisable" );
                    } else if( classs.indexOf("select2") != -1 ) {
                        //console.log("clean as regular select (not keyfield types), field="+field);
                        elements.eq(i).select2('data', null);
                    } else {
                        //console.log("clean as regular");
                        elements.eq(i).val(null);
                    }

                } else {
                    //console.log("clean as an arrayFieldShow");
                    cleanArrayField( elements.eq(i), field, single );
                }
            }

        }

    }
}