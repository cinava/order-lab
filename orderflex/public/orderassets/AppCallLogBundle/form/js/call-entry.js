/*
 * Copyright (c) 2017 Cornell University
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Created by App Ivanov on 7/25/2016.
 */


var _transTime = 500;
var _patients = [];
var _mrntype_original = null;

function initCallLogPage() {
    listnereAccordionMasterPatientParent();
    calllogInputListenerErrorWellRemove('patient-holder-1');
    calllogPressEnterOnKeyboardAction('patient-holder-1');

    calllogUpdatePatientAgeListener('patient-holder-1');

    //calllogEnableMessageCategoryService('patient-holder-1');
    //calllogMessageCategoryListener('patient-holder-1');
    calllogLocationNameListener('patient-holder-1');

    calllogEncounterReferringProviderListener('patient-holder-1');

    //formNodeCCICalculation_OLD();

    var formtype = $('#formtype').val();
    //console.log("init formtype="+formtype);
    if( formtype != "add-patient-to-list" ) {
        //console.log("init calllog Window CloseAlert for formtype="+formtype);
        calllogWindowCloseAlert();
    }

    // $('.summernote').summernote();
    //richTextInit();
}

// function richTextInit() {
//     $('.summernote').summernote();
// }

//prevent exit modified form
function calllogWindowCloseAlert() {

    //console.log("calllog Window CloseAlertcycle="+cycle);

    window.onbeforeunload = confirmModifiedFormExit;

    function confirmModifiedFormExit() {
        //console.log("modified msg");
        //http://stackoverflow.com/questions/37727870/window-confirm-message-before-reload
        //'Custom text support removed' in Chrome 51.0 and Firefox 44.0.
        return "Are you sure you would like to navigate away from this page? Text you may have entered has not been saved yet.";
    }
}

function calllogTriggerSearch(holderId,formtype) {
    if( holderId == null ) {
        holderId = 'patient-holder-1';
    }
    var triggerSearch = $('#triggerSearch').val();
    //console.log('triggerSearch='+triggerSearch);

    if( triggerSearch == 1 ) {
        var mrntype = $('#mrntype').val();
        var mrn = $('#mrn').val();
        //console.log('mrntype='+mrntype);
        findCalllogPatient(holderId, formtype, mrntype, mrn);
    }
}


function addnewCalllogPatient(holderId) {

    var holder = getHolder(holderId);

    var addBtn = holder.find("#addnew_patient_button").get(0);
    var lbtn = Ladda.create( addBtn );
    calllogStartBtn(lbtn);

    var mrntype = holder.find(".mrntype-combobox").select2('val');
    mrntype = trimWithCheck(mrntype);

    var mrn = holder.find(".patientmrn-mask").val();
    mrn = trimWithCheck(mrn);

    var dob = holder.find(".patient-dob-date").val();
    dob = trimWithCheck(dob);

    var lastname = holder.find(".encounter-lastName").val();
    lastname = trimWithCheck(lastname);

    var firstname = holder.find(".encounter-firstName").val();
    firstname = trimWithCheck(firstname);

    var middlename = holder.find(".encounter-middleName").val();
    middlename = trimWithCheck(middlename);

    var suffix = holder.find(".encounter-suffix").val();
    suffix = trimWithCheck(suffix);

    var sex = holder.find(".encountersex-field").select2('val');
    sex = trimWithCheck(sex);

    var sexStr = holder.find(".encountersex-field").select2('data').text;
    //sexStr = trimWithCheck(sexStr);

    var phone = holder.find(".patient-phone").val();
    phone = trimWithCheck(phone);

    var email = holder.find(".patient-email").val();
    email = trimWithCheck(email);

    var accessionnumber = holder.find(".accession-mask").val();
    accessionnumber = trimWithCheck(accessionnumber);

    var accessiontype = holder.find(".accessiontype-combobox").select2('val');
    accessiontype = trimWithCheck(accessiontype);
    
    if( email ) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if( re.test(String(email).toLowerCase()) ) {
            //email is valid
        } else {
            holder.find('#calllog-danger-box').html("Please enter a valid email address.");
            holder.find('#calllog-danger-box').show(_transTime);
            calllogStopBtn(lbtn);
            return false;
        }
    }

    //check if "Last Name" field + DOB field, or "MRN" fields are not empty
    //allow the creation of a patient record with the Last Name alone only
    //if( !mrn || !mrntype || !lastname || !dob ) {
    if( mrntype && mrn || lastname && dob || lastname ) {
        //if( mrntype && mrn || lastname ) {
        //ok
    } else {
        holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name and Date of Birth.");
        //holder.find('#calllog-danger-box').html("Please enter at least an MRN, Last Name, Date of Birth, Patient's Phone or Email.");
        //holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name.");
        holder.find('#calllog-danger-box').show(_transTime);

        calllogStopBtn(lbtn);
        return false;
    }


    //"Are You sure you would like to create a new patient registration record for
    //MRN: Last Name: First Name: Middle Name: Suffix: Sex: DOB: Alias(es):
    var confirmMsg = "Are you sure you would like to create a new patient registration record for";

    var creationStr = "";
    if( mrn )
        creationStr += " MRN: "+mrn+" ";
    if( lastname )
        creationStr += " Last Name: "+lastname+" ";
    if( firstname )
        creationStr += " First Name: "+firstname+" ";
    if( middlename )
        creationStr += " Middle Name: "+middlename+" ";
    if( suffix )
        creationStr += " Suffix: "+suffix+" ";
    if( sex )
        creationStr += " Gender: "+sexStr+" ";
    if( dob )
        creationStr += " DOB: "+dob+" ";
    if( phone )
        creationStr += " Phone: "+phone+" ";
    if( email )
        creationStr += " E-Mail: "+email+" ";
    if( accessiontype && accessionnumber ) {
        creationStr += " Accession Number: "+accessionnumber+" ("+holder.find(".accessiontype-combobox").select2('data').text+")";
    }

    confirmMsg = confirmMsg + creationStr;

    //TODO: lock all fields
    //console.log("lock all fields");
    disableAllFields(true, holderId);

    if( confirm(confirmMsg) == true ) {
        //x = "You pressed OK!";
    } else {
        //x = "You pressed Cancel!";
        //TODO: unlock all fields
        disableAllFields(false, holderId);

        calllogStopBtn(lbtn);
        return false;
    }

    var metaphone = calllogGetMetaphoneValue(holderId);

    //Clicking "Ok" in the Dialog confirmation box should use the variables
    // to create a create the new patient on the server via AJAX/Promise,
    // then lock the Patient Info fields, and change the title of the "Find Patient" button to "Re-enter Patient"
    //ajax
    var url = Routing.generate('calllog_create_patient');
    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true,
        data: {mrntype: mrntype, mrn: mrn, dob: dob, lastname: lastname, firstname: firstname, middlename: middlename, phone: phone, email: email, suffix: suffix, sex: sex, metaphone:metaphone, accessiontype:accessiontype, accessionnumber:accessionnumber  },
    }).success(function(data) {
        //console.log("output="+data);

        if( data.output == "OK" ) {

            //console.log("patien has been created: output OK");

            //testing!!!
            // var patient = getFirstPatient(data.patients); //testing!!!
            // var disableStr = "disabled";
            // var mrntype = holder.find('.mrntype-combobox');
            // var mrnid = holder.find('.patientmrn-mask');
            // calllogAddMrnType(patient);
            // mrntype.prop(disableStr, false);
            // mrnid.prop(disableStr, false);
            // //"readonly"
            // mrntype.prop("readonly", false);
            // mrnid.prop("readonly", false);
            // return; //testing!!!
            populatePatientsInfo(data.patients,creationStr,holderId,true,null);

            //console.log("Patient has been created");
            //hide find patient and add new patient
            holder.find('#search_patient_button').hide(_transTime);
            holder.find('#addnew_patient_button').hide(_transTime);
            //show Re-enter Patient
            holder.find('#reenter_patient_button').show(_transTime);
            //clean error message
            holder.find('#calllog-danger-box').html('');
            holder.find('#calllog-danger-box').hide(_transTime);

            //disable all fields
            disableAllFields(true, holderId);

            //show edit patient info button
            holder.find('#edit_patient_button').show(_transTime);

            holder.find('#add_patient_to_list_button').show(_transTime);

            //showCalllogCallentryForm(true);
            //hide "No single patient is referenced by this entry or I'll add the patient info later" link and all sections below
            //$('#callentry-nosinglepatient-link').hide(_transTime);
            //$('#callentry-form').hide(_transTime);
            //opens/shows the lower accordion that opens when you click "No single patient is referenced by this entry or I'll add the patient info later"
            //var nosinglepatientlink = $('#callentry-nosinglepatient-link');
            //if( nosinglepatientlink ) {
                //nosinglepatientlink.trigger("click");
                //nosinglepatientlink.hide();
            //}

        } else {
            //console.log("Patient has not been created not OK: data.output="+data.output);
            holder.find('#calllog-danger-box').html(data.output);
            holder.find('#calllog-danger-box').show(_transTime);
        }
    }).done(function() {
        //console.log("add new CalllogPatient done");
        calllogStopBtn(lbtn);
    });


}

function addCalllogPatientToList(holderId) {

    var holder = getHolder(holderId);

    var addBtn = holder.find("#add_patient_to_list_button").get(0);
    var lbtn = Ladda.create( addBtn );
    calllogStartBtn(lbtn);

    var patientListId = $('#patientListId').val();

    var patientId = holder.find('.patienttype-patient-id').val();
    patientId = trimWithCheck(patientId);

    //console.log("patientListId="+patientListId+"; patientId="+patientId);

    if(0) {
        var confirmMsg = "Are you sure you would like to add this patient to this patient list?";
        confirmMsg = confirmMsg;
        //console.log("lock all fields");
        disableAllFields(true, holderId);
        if (confirm(confirmMsg) == true) {
            //x = "You pressed OK!";
        } else {
            //x = "You pressed Cancel!";
            disableAllFields(false, holderId);
            calllogStopBtn(lbtn);
            return false;
        }
    }

    //ajax
    var url = Routing.generate('calllog_add_patient_to_list_ajax');

    url = url + "/" + patientListId + "/" + patientId;
    //console.log("url="+url);
    //return;

    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true,
        //data: {patientListId: patientListId, patientId: patientId},
    }).success(function(data) {
        //console.log("data="+data);

        if( data == "OK" ) {
            //Cancel onbeforeunload event handler
            window.onbeforeunload = null;

            //reload this page
            location.reload();
        } else {
            //console.log("Patient has not been created not OK: data="+data);
            holder.find('#calllog-danger-box').html(data);
            holder.find('#calllog-danger-box').show(_transTime);
        }
    }).done(function() {
        //console.log("add new CalllogPatient done");
        calllogStopBtn(lbtn);

    });


}

//JS method: NOT USED
function submitPatientBtn(holderId) {

    var holder = getHolder(holderId);

    var addBtn = $("#submit_patient_button").get(0);
    var lbtn = Ladda.create( addBtn );
    calllogStartBtn(lbtn);

    //calllog-patient-id-patient-holder-1
    //console.log("id="+"#calllog-patient-id-"+holderId);
    var patientId = holder.find("#calllog-patient-id-"+holderId).val();
    //console.log(patientIdField);
    //var patientId = $("#calllog-patient-id-"+holderId).val();
    //console.log("patientId="+patientId);

    var mrntype = holder.find(".mrntype-combobox").select2('val');
    mrntype = trimWithCheck(mrntype);

    var mrn = holder.find(".patientmrn-mask").val();
    mrn = trimWithCheck(mrn);

    var dob = holder.find(".patient-dob-date").val();
    dob = trimWithCheck(dob);

    var lastname = holder.find(".encounter-lastName").val();
    lastname = trimWithCheck(lastname);

    var firstname = holder.find(".encounter-firstName").val();
    firstname = trimWithCheck(firstname);

    var middlename = holder.find(".encounter-middleName").val();
    middlename = trimWithCheck(middlename);

    var suffix = holder.find(".encounter-suffix").val();
    suffix = trimWithCheck(suffix);

    var sex = holder.find(".encountersex-field").select2('val');
    sex = trimWithCheck(sex);

    var phone = holder.find(".patient-phone").val();
    phone = trimWithCheck(phone);

    var email = holder.find(".patient-email").val();
    email = trimWithCheck(email);

    if( email ) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if( re.test(String(email).toLowerCase()) ) {
            //email is valid
        } else {
            holder.find('#calllog-danger-box').html("Please enter a valid email address.");
            holder.find('#calllog-danger-box').show(_transTime);
            calllogStopBtn(lbtn);
            return false;
        }
    }

    //check if "Last Name" field + DOB field, or "MRN" fields are not empty
    //if( !mrn || !mrntype || !lastname || !dob ) {
    if( mrntype && mrn || lastname && dob ) {
        //if( mrntype && mrn || lastname ) {
        //ok
    } else {
        holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name and Date of Birth.");
        //holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name.");
        holder.find('#calllog-danger-box').show(_transTime);

        calllogStopBtn(lbtn);
        return false;
    }

    //"Are You sure you would like to create a new patient registration record for
    //MRN: Last Name: First Name: Middle Name: Suffix: Sex: DOB: Alias(es):
    var confirmMsg = "Are You sure you would like to update the patient record for patient ID #"+patientId+". ";

    if( mrn )
        confirmMsg += " MRN:"+mrn;
    if( lastname )
        confirmMsg += " Last Name:"+lastname;
    if( firstname )
        confirmMsg += " First Name:"+firstname;
    if( middlename )
        confirmMsg += " Middle Name:"+middlename;
    if( suffix )
        confirmMsg += " Suffix:"+suffix;
    if( sex )
        confirmMsg += " Gender:"+sex;
    if( dob )
        confirmMsg += " DOB:"+dob;
    if( phone )
        confirmMsg += " Phone:"+phone;
    if( email )
        confirmMsg += " E-Mail:"+email;

    if( confirm(confirmMsg) == true ) {
        //x = "You pressed OK!";
    } else {
        //x = "You pressed Cancel!";
        calllogStopBtn(lbtn);
        return false;
    }

    var metaphone = calllogGetMetaphoneValue(holderId);

    //Clicking "Ok" in the Dialog confirmation box should use the variables
    // to create a create the new patient on the server via AJAX/Promise,
    // then lock the Patient Info fields, and change the title of the "Find Patient" button to "Re-enter Patient"
    //ajax
    var url = Routing.generate('calllog_edit_patient_record_ajax');
    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true,
        data: {patientId: patientId, mrntype: mrntype, mrn: mrn, dob: dob, lastname: lastname, firstname: firstname, middlename: middlename, phone: phone, email: email, suffix: suffix, sex: sex, metaphone:metaphone},
    }).success(function(data) {
        //console.log("output="+data);
        if( data == "OK" ) {
            //console.log("Patient has been created");
            //hide find patient and add new patient
            holder.find('#search_patient_button').hide(_transTime);
            holder.find('#addnew_patient_button').hide(_transTime);
            //show Re-enter Patient
            holder.find('#reenter_patient_button').show(_transTime);
            //clean error message
            holder.find('#calllog-danger-box').html('');
            holder.find('#calllog-danger-box').hide(_transTime);

            //disable all fields
            disableAllFields(true,holderId);

            //show edit patient info button
            holder.find('#edit_patient_button').show(_transTime);

        } else {
            //console.log("Patient has not been created");
            holder.find('#calllog-danger-box').html(data);
            holder.find('#calllog-danger-box').show(_transTime);
        }
    }).done(function() {
        calllogStopBtn(lbtn);
    });


}

//show call entry form and hide link
function showCalllogCallentryForm(show) {
    if( show == true ) {
        //console.log('show patient info');
        $('#callentry-nosinglepatient-link').hide(_transTime);
        $('#callentry-form').show(_transTime);

        //generate encounter ID. Use : encounterid
        //var encounterid = $('#encounterid').val();
        //$('.encounter-id').val(encounterid);

    } else {
        //console.log('hide patient info');
        $('#callentry-nosinglepatient-link').show(_transTime);
        $('#callentry-form').hide(_transTime);

        //delete encounter ID
    }
}

function clearCalllogPatient(holderId) {
    var holder = getHolder(holderId);

    //console.log("clear patient for Re-enter Patient");
    populatePatientInfo(null,false,true,holderId); //clear patient for Re-enter Patient

    //change the "Re-enter Patient" to "Find Patient"
    holder.find('#reenter_patient_button').hide(_transTime);
    holder.find('#search_patient_button').show(_transTime);

    //calllogHideAllAlias(true,true,holderId);

    //edit_patient_button
    holder.find('#edit_patient_button').hide(_transTime);

    holder.find('#add_patient_to_list_button').hide(_transTime);

    //change the accordion title back to "Patient Info"
    calllogSetPatientAccordionTitle(null,holderId);

    //hide call entry form
    showCalllogCallentryForm(false);

    //clear previous entries
    calllogShowHideListPreviousEntriesBtn(true);

    calllogRemovePreviousEncounters();
}

function findCalllogPatient(holderId,formtype,mrntype,mrn) {

    //just in case try to close again after calllog PressEnterOnKeyboardAction: close datepicker box
    //printF($(".datepicker-dropdown"),"datepicker-dropdown:");
    //$(".datepicker-dropdown").remove();

    var holder = getHolder(holderId);

    var searchBtn = holder.find("#search_patient_button").get(0);
    var lbtn = Ladda.create( searchBtn );
    calllogStartBtn(lbtn);

    //clear no matching box
    holder.find('#calllog-danger-box').hide(_transTime);
    holder.find('#calllog-danger-box').html("");

    //clear matching patient section
    holder.find('#calllog-matching-patients').hide(_transTime);
    holder.find('#calllog-matching-patients').html('');

    //addnew patient button
    holder.find('#addnew_patient_button').hide(_transTime);

    var searchedStr = "";

    if( mrntype ) {
        //
    } else {
        mrntype = holder.find(".mrntype-combobox").select2('val');
        mrntype = trimWithCheck(mrntype);
    }

    //set _mrntype_original
    if( _mrntype_original == null && _mrntype && _mrntype.length > 0 ) {
        _mrntype_original = _mrntype[0].id;
    }

    if( mrn ) {
        //
    } else {
        mrn = holder.find(".patientmrn-mask").val();
        mrn = trimWithCheck(mrn);
    }

    var dob = holder.find(".patient-dob-date").val();
    dob = trimWithCheck(dob);

    var lastname = holder.find(".encounter-lastName").val();
    lastname = trimWithCheck(lastname);

    var firstname = holder.find(".encounter-firstName").val();
    firstname = trimWithCheck(firstname);

    var phone = holder.find(".patient-phone").val();
    phone = trimWithCheck(phone);

    var email = holder.find(".patient-email").val();
    email = trimWithCheck(email);

    var accessionnumber = holder.find(".accession-mask").val();
    accessionnumber = trimWithCheck(accessionnumber);

    var accessiontype = holder.find(".accessiontype-combobox").select2('val');
    accessiontype = trimWithCheck(accessiontype);

    //console.log('mrntype='+mrntype+", mrn="+mrn+", dob="+dob+", lastname="+lastname+", firstname="+firstname);

    if( email ) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if( re.test(String(email).toLowerCase()) ) {
            //email is valid
        } else {
            holder.find('#calllog-danger-box').html("Please enter a valid email address.");
            holder.find('#calllog-danger-box').show(_transTime);
            calllogStopBtn(lbtn);
            return false;
        }
    }

    //Check if the entered MRN string has no digits AND the Last Name field is empty,
    // then set the MRN field value to empty, and set the Last Name field to the value entered in the MRN field,
    // then resume normal search algorithm.
    if( !lastname && mrn ) {
        //check if mrn has no digits
        if( !callloghasNumber(mrn) ) {
            lastname = mrn;
            mrn = "";
            holder.find(".encounter-lastName").val(lastname);
            holder.find(".patientmrn-mask").val(mrn);
        }
    }

    if( mrn && mrntype || accessionnumber && accessiontype || dob && lastname || dob && lastname && firstname || lastname  || phone || email ) {
        var andSearchStr = "";
        if( phone ) {
            andSearchStr = andSearchStr + " Phone: " + phone;
        }
        if( email ) {
            andSearchStr = andSearchStr + " E-mail: " + email;
        }
        //ok
        if( !searchedStr && mrn && mrntype ) {
            searchedStr = " (searched for MRN Type: "+holder.find(".mrntype-combobox").select2('val').text+"; MRN: "+mrn;
        }
        if( !searchedStr && accessionnumber && accessiontype ) {
            //searchedStr = " (searched for Accession Type: "+holder.find(".accessiontype-combobox").select2('val').text+"; Accession Number: "+accessionnumber;
            searchedStr = " (searched for Accession Number: "+accessionnumber+" ("+holder.find(".accessiontype-combobox").select2('val').text+")";
        }
        if( !searchedStr && dob && lastname ) {
            var firstnameStr = "";
            if( firstname ) {
                firstnameStr = "; First Name: "+firstname;
            }
            searchedStr = " (searched for DOB: "+dob+"; Last Name: "+lastname+firstnameStr;
        }
        if( !searchedStr && lastname ) {
            var firstnameStr = "";
            if( firstname ) {
                firstnameStr = "; First Name: "+firstname;
            }
            searchedStr = " (searched for Last Name: "+lastname+firstnameStr;
        }


        if( searchedStr ) {
            if( andSearchStr ) {
                searchedStr = searchedStr + ";" + andSearchStr;
            } else {
                //no additional search
            }
            searchedStr = searchedStr + ")";
        } else {
            if( andSearchStr ) {
                searchedStr = " (searched for" + andSearchStr + ")";
            } else {
                //no search params
            }
        }


    } else {
        //holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name and Date of Birth.");
        //holder.find('#calllog-danger-box').html("Please enter at least an MRN or Last Name.");
        holder.find('#calllog-danger-box').html("Please enter at least an MRN, Last Name, Date of Birth, Patient's Phone, Email or Accession.");
        holder.find('#calllog-danger-box').show(_transTime);
        calllogStopBtn(lbtn);
        return false;
    }

    var singleMatch = false;
    if( (mrn && mrntype) || (accessionnumber && accessiontype) || (dob && lastname) ) {
        singleMatch = true;
    }

    var metaphone = calllogGetMetaphoneValue(holderId);
    //console.log('metaphone='+metaphone);

    //var currentUrl = window.location.href;

    //ajax
    var url = Routing.generate('calllog_search_patient');
    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true,
        data: {mrntype: mrntype, mrn: mrn, accessionnumber: accessionnumber, accessiontype: accessiontype, dob: dob, lastname: lastname, firstname: firstname, phone: phone, email: email, formtype: formtype, metaphone: metaphone },
    }).success(function(resData) {
        var dataOk = false;
        var data = resData.patients;
        var searchedStr = resData.searchStr;
        var allowCreateNewPatient = resData.allowCreateNewPatient;

        if( data ) {
            var firstKey = Object.keys(data)[0];
            if( firstKey ) {
                var firstElement = data[firstKey];
                if( firstElement && firstElement.hasOwnProperty("id") ) {
                    //console.log("patient found: searchedStr="+searchedStr);
                    populatePatientsInfo(data, searchedStr, holderId, singleMatch, allowCreateNewPatient);
                    dataOk = true;
                }
            }
            if( data.length == 0 ) {
                //console.log("no patient found: searchedStr="+searchedStr);
                populatePatientsInfo(data, searchedStr, holderId, singleMatch, allowCreateNewPatient);
                dataOk = true;
            }
        }
        if( !dataOk ) {
            //console.log("Search is not performed");
            holder.find('#calllog-danger-box').html("Search is not performed. Please try to reload the page.");
            holder.find('#calllog-danger-box').show(_transTime);
        }
    }).done(function() {
        //console.log("search done");
        calllogStopBtn(lbtn);
        //close datepicker box
        //var datepickerDropdown = $(".datepicker-dropdown");
        //printF(datepickerDropdown,"datepicker-dropdown:");
        //datepickerDropdown.remove();
    });

}
function callloghasNumber(myString) {
    return (/\d/.test(myString));
}

function populatePatientsInfo(patients,searchedStr,holderId,singleMatch,allowCreateNewPatient) {

    var holder = getHolder(holderId);

    //var patLen = patients.length;
    var patLen = getPatientsLength(patients);
    //console.log('patLen='+patLen);

    //clear matching patient section
    holder.find('#calllog-matching-patients').hide(_transTime);
    holder.find('#calllog-matching-patients').html('');

    //clear no matching box
    holder.find('#calllog-danger-box').hide(_transTime);
    holder.find('#calllog-danger-box').html("");

    //hide edit patient info button
    holder.find('#edit_patient_button').hide(_transTime);

    holder.find('#add_patient_to_list_button').hide(_transTime);

    //hide "No single patient is referenced by this entry or I'll add the patient info later" link
    showCalllogCallentryForm(false);

    _patients = patients;
    //console.log("_patients:");
    //console.log(_patients);

    var processed = false;

    if( patLen == 1 && singleMatch ) {

        //var patient = patients[0];
        var patient = getFirstPatient(patients);
        if (patient == null) {
            alert("No first patient found in the patient array");
        }
        //console.log('single found patient id=' + patient.id);

        var patMergedLen = getMergedPatientInfoLength(patient);
        //console.log('patMergedLen='+patMergedLen);

        if( patMergedLen == 0 && processed == false ) {
            //console.log('single patient populate');
            populatePatientInfo(patient, false, true, holderId); //single patient found
            disableAllFields(true, holderId);

            //show edit patient info button
            holder.find('#edit_patient_button').show(_transTime);

            holder.find('#add_patient_to_list_button').show(_transTime);

            //hide "No single patient is referenced by this entry or I'll add the patient info later" link

            //change the "Find or Add Patient" button title to "Re-enter Patient"
            holder.find('#reenter_patient_button').show(_transTime);
            holder.find('#search_patient_button').hide(_transTime);
            holder.find('#addnew_patient_button').hide(_transTime);

            //warning that no merge patients for set master record and un-merge
            var formtype = $('#formtype').val();
            //console.log('single patient populate: formtype='+formtype);

            if( formtype == "unmerge" || formtype == "set-master-record" ) {
                holder.find('#calllog-danger-box').html("This patient does not have any merged patient records");
                holder.find('#calllog-danger-box').show(_transTime);
            }
            //console.log("single patient populate: 1");

            if( formtype == "edit-patient" ) {
                //console.log("patient.id="+patient.id);
                var url = Routing.generate('calllog_patient_edit',{'id':patient.id});
                //alert("url="+url);
                window.location.href = url;
            }

            if( formtype == "call-entry" ) {
                //show
                //console.log('callentry-nosinglepatient-link show');
                showCalllogCallentryForm(true);
            }

            //if( formtype == "add-patient-to-list" ) {
            //    var listid = $('#patientListId').val();
            //    console.log("patient.id="+patient.id+"; listid="+listid);
            //    var url = Routing.generate('calllog_patient_edit',{'id':patient.id, 'listid':listid});
            //    alert("url="+url);
            //    window.location.href = url;
            //}

            processed = true;
            //console.log("single patient populate: finished");
        }
    }

    if( patLen == 0 && processed == false ) {

        //console.log("No matching patient records found.");
        //"No matching patient records found." and unlock fields
        holder.find('#calllog-danger-box').html("No matching patient records found. "+searchedStr+".");
        holder.find('#calllog-danger-box').show(_transTime);
        populatePatientInfo(null,true,false,holderId); //not found
        disableAllFields(false,holderId);

        //un-hide/show a button called "Add New Patient Registration"
        if( allowCreateNewPatient ) {
            //If Accession field does not exist use title: Add New Patient Record
            //If Accession field exists use title: Add New Patient Record and Accession Number
            if( calllogAccessionExists() ) {
                var addNewPatientBtnTitle = "Add New Patient Record and Accession Number";
            } else {
                var addNewPatientBtnTitle = "Add New Patient Record";
            }
            //console.log("addNewPatientBtnTitle="+addNewPatientBtnTitle);
            //holder.find('#addnew_patient_button').prop('title', addNewPatientBtnTitle);
            holder.find('#addnew_patient_button').html(addNewPatientBtnTitle);
            holder.find('#addnew_patient_button').show(_transTime);
        }
        processed = true;
    }

    if( processed == false && (patLen >= 1 || (!singleMatch && patLen == 1 )) ) {

        //console.log("show table with found patients");
        //show table with found patients
        populatePatientInfo(null,false,false,holderId); //multiple patients found
        disableAllFields(false,holderId);

        //un-hide/show a button called "Add New Patient Registration" because no unique patient has been found
        if( patLen > 1 ) {
            if( allowCreateNewPatient ) {
                //If Accession field does not exist use title: Add New Patient Record
                //If Accession field exists use title: Add New Patient Record and Accession Number
                if( calllogAccessionExists() ) {
                    var addNewPatientBtnTitle = "Add New Patient Record and Accession Number";
                } else {
                    var addNewPatientBtnTitle = "Add New Patient Record";
                }
                //console.log("addNewPatientBtnTitle="+addNewPatientBtnTitle);
                //holder.find('#addnew_patient_button').prop('title', addNewPatientBtnTitle);
                //$('#addnew_patient_button').attr('title', addNewPatientBtnTitle);
                holder.find('#addnew_patient_button').html(addNewPatientBtnTitle);
                holder.find('#addnew_patient_button').show(_transTime);
            }
        }

        createPatientsTableCalllog(patients,holderId);
        processed = true;
    }

    if( processed == false ){
        console.log("Logical error. Search patients not processed. patLen="+patLen);
    }
    //console.log("populate Patients Info: finished");
}

function createPatientsTableCalllog( patients, holderId ) {

    var holder = getHolder(holderId);
    var hasMaster = false;
    var matchingPatientsHtml = "";

    //for( var i = 0; i < patients.length; i++ ) {
    for( var i in patients ) {
        if (patients.hasOwnProperty(i)) {

            var patient = patients[i];
            //console.log('patient id='+patient.id);

            //var mergedPatientsInfoLength = getMergedPatientInfoLength(patient['mergedPatientsInfo']);
            //console.log('mergedPatientsInfoLength='+mergedPatientsInfoLength);
            //console.log('patient.mergedPatientsInfo:');
            //console.log(patient.mergedPatientsInfo);
            //var mergedPatientsInfoLength = (mergedPatientsInfoLength - 1);
            //var hasMergedPatients = "";
            //if( patient.mergedPatientsInfo && mergedPatientsInfoLength > 0 ) {
            //    hasMergedPatients = '<br><span class="label label-info">Has ' + mergedPatientsInfoLength + ' Merged Patients</span>';
            //}

            var masterId = patient['masterPatientId'];  //i+'-'+holderId
            //console.log('masterId='+masterId);

            var res = constractPatientInfoRow(patient, masterId, "master", holderId);
            matchingPatientsHtml += res['html'];

            if( res['hasMaster'] ) {
                //console.log("set hasMaster true");
                hasMaster = true;
            }

            matchingPatientsHtml = matchingPatientsHtml + constractMergedPatientInfoRow(patient, masterId, holderId);
        }
    }

    //Matching Patients
    var matchingPatientsHeaderHtml =
        '<div class="table-responsive">'+
        '<table id="calllog-matching-patients-table-'+holderId+'" class="table table-bordered">' +
        '<thead><tr>';

    if( hasMaster ) {
        //console.log("hasMaster true");
        matchingPatientsHeaderHtml += '<th>&nbsp;</th>';
    } else {
        //console.log("hasMaster false");
        matchingPatientsHeaderHtml += '<th></th>';
    }

    matchingPatientsHeaderHtml +=
        '<th>MRN</th>' +
        '<th>Last Name</th>' +
        '<th>First Name</th>' +
        '<th>Middle Name</th>' +
        '<th>Suffix</th>' +
        '<th>Gender</th>' +
        '<th>DOB</th>' +
        '<th>Contact Info</th>' +
        '<th>Action</th>' +
        '</tr></thead>' +
        '<tbody>';

    matchingPatientsHtml = matchingPatientsHeaderHtml + matchingPatientsHtml + "</tbody></table></div>";

    matchingPatientsHtml = matchingPatientsHtml +
        '<p data-toggle="tooltip" title="Please select the patient"><button type="button"'+
        //' id="matchingPatientBtn-'+holderId+'"'+
        ' class="btn btn-success btn-lg span4 matchingPatientBtn" align="center"'+
        ' disabled'+
        ' onclick="matchingPatientBtnClick(\''+holderId+'\')"'+
        '>Select Patient</button></p>';

    matchingPatientsHtml = matchingPatientsHtml +
            '<div id="calllog-select-patient-danger-box" class="alert alert-danger" style="display: none; margin: 5px;"></div>';

    holder.find('#calllog-matching-patients').html(matchingPatientsHtml);
    holder.find('#calllog-matching-patients').show(_transTime);


    holder.find('.matchingPatientBtn').parent().tooltip();

    holder.find('#calllog-matching-patients-table-'+holderId).on('click', '.clickable-row', function(event) {
        $(this).addClass('active').addClass('success').siblings().removeClass('active').removeClass('success');
        //enable button
        holder.find('.matchingPatientBtn').prop('disabled', false);
        holder.find('.matchingPatientBtn').parent().tooltip('destroy');
    });

}
function constractPatientInfoRow( patient, masterId, type, holderId ) {
    //to test use: http://www.bootply.com/4lsCo5q101
    var patientsHtml = "";
    var hasMaster = false;

    if( type == "master" ) {
        patientsHtml += '<tr id="'+patient.id+'" class="clickable-row">';
        if( patient['masterPatientId'] ) {
            patientsHtml += '<td>';
            patientsHtml += '<button type="button" class="btn btn-default btn-xs" onclick="clickMasterPatientBtn(this);" id="' + masterId + '">';
            patientsHtml += '<span class="glyphicon glyphicon-plus-sign"></span></button>';
            patientsHtml += '</td>';
            hasMaster = true;
        } else {
            patientsHtml += '<td></td>';
        }
    } else {
        //masterId
        patientsHtml += '<tr id="'+patient.id+'" class="clickable-row collapseme'+masterId+' collapse out" style="background: #A9A9A9;">';
        patientsHtml += '<td>';
        patientsHtml += '&nbsp;&nbsp;<span class="glyphicon glyphicon-link"></span>';
        patientsHtml += '</td>';
    }


    //action menu (only for call-entry form)
    var action = "";
    var formtype = $('#formtype').val();
    if( formtype == 'call-entry' ) {
        //var patMergedLen = getMergedPatientInfoLength(patient);
        //console.log('patMergedLen='+patMergedLen);
        var mergeUrl = Routing.generate('calllog_merge_patient_records') + "?mrntype=" + patient.mrntype + "&mrn=" + patient.mrn;
        var editUrl = Routing.generate('calllog_patient_edit_by_mrn') + "?mrntype=" + patient.mrntype + "&mrn=" + patient.mrn + '&show-tree-depth=2';
        var viewUrl = Routing.generate('calllog_patient_view_by_mrn') + "?mrntype=" + patient.mrntype + "&mrn=" + patient.mrn + '&show-tree-depth=2';

        var unmergeMenu = "";
        var setrecordMenu = "";
        //if( patMergedLen > 0 || ( patMergedLen == 0 && masterId && masterId != patient.id) ) {
        if (masterId) {
            var unmergeUrl = Routing.generate('calllog_unmerge_patient_records') + "?mrntype=" + patient.mrntype + "&mrn=" + patient.mrn;
            var setmasterUrl = Routing.generate('calllog_set_master_patient_record') + "?mrntype=" + patient.mrntype + "&mrn=" + patient.mrn;
            unmergeMenu = '<li><a href="' + unmergeUrl + '">Un-merge patient record</a></li>';
            setrecordMenu = '<li><a href="' + setmasterUrl + '">Set Master record</a></li>';
        }

        action =
            '<div class="btn-group">' +
            '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">' +
            'Action <span class="caret"></span></button>' +
            '<ul class="dropdown-menu dropdown-menu-right">' +
                //'<li><a href="javascript:void(0)" onclick="matchingPatientUnmergeBtnClick(\''+holderId+'\',\'unmerge\')">Un-merge patient record</a></li>'+
                //'<li><a href="javascript:void(0)" onclick="matchingPatientUnmergeBtnClick(\''+holderId+'\',\'set-master-record\')">Set Master record</a></li>'+
            '<li><a href="' + viewUrl + '" target="_blank">View patient record</a></li>' +
            '<li><a href="' + editUrl + '" target="_blank">Edit patient record</a></li>' +
            '<li><a href="' + mergeUrl + '" target="_blank">Merge patient record</a></li>' +
                //'<li><a href="' + unmergeUrl + '">Un-merge patient record</a></li>' +
                //'<li><a href="' + setmasterUrl + '">Set Master record</a></li>' +
            unmergeMenu +
            setrecordMenu +
            '</ul></div>';
    }

    patientsHtml +=
        '<td id="calllog-patientid-'+patient.id+'">'+
        patient.patientInfoStr +
        patient.mrn+' ('+patient.mrntypestr+')'+
        //hasMergedPatients +
        '</td>'+
        '<td>'+patient.lastname+'</td>'+
        '<td>'+patient.firstname+'</td>'+
        '<td>'+patient.middlename+'</td>'+
        '<td>'+patient.suffix+'</td>'+
        '<td>'+patient.sexstr+'</td>'+
        '<td>'+patient.dob+'</td>'+
        '<td>'+patient.contactinfo+'</td>'+
        '<td>'+action+'</td>'+
        '</tr>';

    var res = {'html':patientsHtml,'hasMaster':hasMaster};
    return res;
}
function constractMergedPatientInfoRow( patient, masterId, holderId ) {
    var mergedPatientsHtml = "";
    var mergedPatients = patient['mergedPatientsInfo'];
    for( var mergedId in mergedPatients ) {
        if( mergedPatients.hasOwnProperty(mergedId) ) {
            //alert("Key is " + mergedId + ", value is" + targetArr[mergedId]);
            //count = count + mergedPatients[mergedId]['patientInfo'].length;
            var patientsInfo = mergedPatients[mergedId]['patientInfo'];
            for( var index in patientsInfo ) {
                var patientInfo = patientsInfo[index];
                //console.log('merged Patient ID=' + patientInfo['id']);
                //console.log(patientInfo);
                //masterId = masterId + "-" + patientInfo['id'];
                var res = constractPatientInfoRow(patientInfo, masterId, "alert alert-info", holderId);
                mergedPatientsHtml = mergedPatientsHtml + res['html'];
            }
        }
    }
    return mergedPatientsHtml;
}

function listnereAccordionMasterPatientParent() {
    //testing
}
function clickMasterPatientBtn(btn) {
    var id = $(btn).attr('id');
    //console.log('id='+id);

    if( $(".collapseme"+id).hasClass("out") ) {
        //console.log('show');
        $(".collapseme"+id).show(_transTime);
        $(".collapseme"+id).removeClass('out').addClass('in');
        $(btn).parent().find("span.glyphicon").removeClass("glyphicon-plus-sign").addClass("glyphicon-minus-sign");
    } else {
        //console.log('hide');
        $(".collapseme"+id).hide(_transTime);
        $(".collapseme"+id).removeClass('in').addClass('out');
        $(btn).parent().find("span.glyphicon").removeClass("glyphicon-minus-sign").addClass("glyphicon-plus-sign");
    }
}

function getMergedPatientInfoLength( patient ) {
    if( patient['mergedPatientsInfo'] ) {
        var mergedPatientsInfo = patient['mergedPatientsInfo'][patient.id]['patientInfo'];
        return getPatientsLength(mergedPatientsInfo);
    } else {
        return 0;
    }
}
function getPatientsLength( patients ) {
    var count = 0;
    for( var k in patients ) {
        if( patients.hasOwnProperty(k) ) {
            //console.log("Key is " + k + ", value id is " + patients[k].id);
            count++;
        }
    }
    return count;
}
function getFirstPatient(patients) {
    for( var k in patients ){
        if( patients.hasOwnProperty(k) ) {
            //console.log("Key is " + k + ", value id is " + patients[k].id);
            return patients[k];
        }
    }
    return null;
}

//"Select Patient" button clicked.
var matchingPatientBtnClick = function(holderId) {
    //console.log('holderId='+holderId);
    var holder = getHolder(holderId);

    var patientToPopulate = getCalllogPatientToPopulate(holderId,true); //keepOriginalPatient = true
    //console.log('patientToPopulate='+patientToPopulate.id+"; fullName="+patientToPopulate.fullName);

    populatePatientInfo(    //matching btn click:
        patientToPopulate,  //patient
        false,              //showinfo
        true,               //modify
        holderId            //holderId
    );
    disableAllFields(true,holderId);

    //show edit patient info button
    holder.find('#edit_patient_button').show(_transTime);

    holder.find('#add_patient_to_list_button').show(_transTime);

    //change the "Find or Add Patient" button title to "Re-enter Patient"
    holder.find('#reenter_patient_button').show(_transTime);
    holder.find('#search_patient_button').hide(_transTime);

    //remove and hide matching patients table
    holder.find('#calllog-matching-patients-table-'+holderId).remove();
    holder.find('#calllog-matching-patients').html('');
    holder.find('#calllog-matching-patients').hide(_transTime);

    var formtype = $('#formtype').val();
    //console.log('formtype='+formtype);
    if( formtype == "call-entry" ) {
        //console.log('callentry-nosinglepatient-link show');
        //show
        showCalllogCallentryForm(true);
    }

    if( formtype == "add-patient-to-list" ) {
        return;
    }

    calllogScrollToTop();
}
//
var getCalllogPatientToPopulate = function(holderId,keepOriginalPatient) {
    //console.log("original replace Calllog PatientToPopulate");
    var holder = getHolder(holderId);
    var index = holder.find('#calllog-matching-patients-table-'+holderId).find('.active').attr('id');
    //console.log('patient id to populate='+index);
    //remove holderId from index
    //index = index.replace("-"+holderId, "");
    //console.log('index='+index);

    if( typeof keepOriginalPatient === 'undefined' ){
        keepOriginalPatient = false;
    }
    //console.log("keepOriginalPatient=" + keepOriginalPatient);

    //find patient with id from _patients array
    var patientToPopulate = getPatientByIdFromPatients(index,_patients,keepOriginalPatient);

    //for call_entry return master record instead of the actual clicked patient record
    if( keepOriginalPatient == false ) {
        var masterPatientId = patientToPopulate['masterPatientId'];
        //console.log("Replace by masterPatientId?=" + masterPatientId);
        if (masterPatientId) {
            //console.log("masterPatientId=" + masterPatientId);
            patientToPopulate = getPatientByIdFromPatients(masterPatientId, _patients, keepOriginalPatient);
        }
    }

    return patientToPopulate;
}
function getPatientByIdFromPatients(index,patients,keepOriginalPatient) {
    //console.log("Start: get patients by index="+index);
    if( typeof keepOriginalPatient === 'undefined' ){
        keepOriginalPatient = false;
    }

    for( var k in patients ) {
        if( patients.hasOwnProperty(k) ) {
            var patient = patients[k];
            var masterPatientId = patient['masterPatientId'];
            //console.log("Key is " + k + ", value id is " + patient.id);
            //console.log("masterPatientId=" + masterPatientId);

            //patient is a master patient or the patient without merged records
            if( k == index ) {
                return patients[k];
            }

            //if( patient['mergedPatientsInfo'] && patient['mergedPatientsInfo'].length > 0 ) {
            if( masterPatientId ) {
                var mergedPatients = patient['mergedPatientsInfo'][masterPatientId]['patientInfo'];
                //console.log("check merged patient");
                for( var mergedIndex in mergedPatients ) {
                    //console.log("mergedIndex="+mergedIndex);
                    if( mergedPatients.hasOwnProperty(mergedIndex) ) {
                        if( mergedIndex == index ) {
                            return mergedPatients[mergedIndex];
                        }
                    }
                }
            }
            //else {
            //    if( k == index ) {
            //        return patients[k];
            //    }
            //}
        }
    }
    return null;
}

function disableAllFields(disable,holderId) {
    //console.log("disableAllFields: disable="+disable);
    var holder = getHolder(holderId);

    disableField(holder.find(".mrntype-combobox"),disable);

    disableField(holder.find(".patientmrn-mask"),disable);

    disableField(holder.find(".patient-dob-date"),disable);

    //disableField(holder.find(".patient-dob-date"),disable);

    disableField(holder.find(".encounter-lastName"),disable);

    disableField(holder.find(".encounter-firstName"),disable);

    disableField(holder.find(".encounter-middleName"),disable);

    disableField(holder.find(".encounter-suffix"),disable);

    disableSelectFieldCalllog(holder.find(".encountersex-field"),disable);
    //disableField(holder.find(".encountersex-field"),disable);

    disableField(holder.find(".patient-phone"),disable);

    disableField(holder.find(".patient-email"),disable);

    disableField(holder.find(".accession-mask"),disable);
    disableSelectFieldCalllog(holder.find(".accessiontype-combobox"),disable);

    //console.log("disableAllFields: finished");
}
function disableField(fieldEl,disable) {
    var disableStr = "readonly"; //disabled
    if( disable ) {
        //lock field
        fieldEl.prop(disableStr, true);
        fieldEl.closest('.input-group').find('input').prop(disableStr, true);
        if( fieldEl.hasClass('datepicker') ) {
            var elementDatepicker = fieldEl.closest('.input-group.date');
            elementDatepicker.datepicker("remove");
        }
        //if( fieldEl.hasClass("combobox") ) {
            //console.log('combobox lock');
            //fieldEl.select2("readonly", true);
            //fieldEl.select2("enable", false);
        //}
    } else {
        //unlock field
        fieldEl.prop(disableStr, false);
        fieldEl.closest('.input-group').find('input').prop(disableStr, false);
        if( fieldEl.hasClass('datepicker') ) {
            var elementDatepicker = fieldEl.closest('.input-group.date');
            initSingleDatepicker(elementDatepicker);
        }
        //if( fieldEl.hasClass("combobox") ) {
            //console.log('combobox unlock');
            //fieldEl.select2("readonly", false);
            //fieldEl.select2("enable", true);
        //}
    }

    if( fieldEl.hasClass('combobox') ) {
        disableSelectFieldCalllog(fieldEl,disable);
    }
}
function disableSelectFieldCalllog(fieldEl,disable) {
    if( disable ) {
        fieldEl.prop('disabled', true);
    } else {
        fieldEl.prop('disabled', false);
    }
}

//patient - patient ifno
//showinfo - force to show encounter info
//modify - modify fields in the patient info
function populatePatientInfo( patient, showinfo, modify, holderId, singleMatch ) {

    var holder = getHolder(holderId);

    populateInputFieldCalllog(holder.find(".calllog-patient-id-radio"),patient,'id',modify);
    disableField(holder.find(".calllog-patient-id-radio"),false);

    //calllog-patient-id
    populateInputFieldCalllog(holder.find(".calllog-patient-id"),patient,'id',modify);
    holder.find(".calllog-patient-id").trigger('change');
    holder.find(".calllog-patient-id").change();

    //patienttype-patient-id
    populateInputFieldCalllog(holder.find(".patienttype-patient-id"),patient,'id',modify);
    holder.find(".patienttype-patient-id").trigger('change');
    holder.find(".patienttype-patient-id").change();

    //testing!!!
    // if( patient ) {
    //     var disableStr = "disabled";
    //     var mrntype = holder.find('.mrntype-combobox');
    //     var mrnid = holder.find('.patientmrn-mask');
    //     calllogAddMrnType(patient);
    //     mrntype.prop(disableStr, false);
    //     mrnid.prop(disableStr, false);
    //     //"readonly"
    //     mrntype.prop("readonly", false);
    //     mrnid.prop("readonly", false);
    //     return; //testing
    // }

    processMrnFieldsCalllog(patient,modify,holderId);

    populateInputFieldCalllog(holder.find(".patient-dob-date"),patient,'dob',modify);

    populateInputFieldCalllog(holder.find(".encounter-lastName"),patient,'lastname',modify);

    populateInputFieldCalllog(holder.find(".encounter-firstName"),patient,'firstname',modify);

    populateInputFieldCalllog(holder.find(".encounter-middleName"),patient,'middlename');

    populateInputFieldCalllog(holder.find(".encounter-suffix"),patient,'suffix');

    populateSelectFieldCalllog(holder.find(".encountersex-field"),patient,'sex');

    populateInputFieldCalllog(holder.find(".patient-phone"),patient,'phone',modify);

    populateInputFieldCalllog(holder.find(".patient-email"),patient,'email',modify);

    //console.log('middlename='+middlename+'; suffix='+suffix+'; sex='+sex);
    //console.log('showinfo='+showinfo);
    if( patient && patient.id || showinfo ) {
        //console.log('show encounter info');
        holder.find('#encounter-info').show(_transTime);  //collapse("show");
        holder.find('#addnew_patient_button').hide(_transTime);
    } else {
        //console.log('hide  encounter info');
        holder.find('#encounter-info').hide(_transTime);  //collapse("hide");
    }

//        //change the "Find or Add Patient" button title to "Re-enter Patient"
//        if( patient && patient.id && patient.lastname && patient.firstname && patient.dob ) {
//            holder.find('#search_patient_button').html('Re-enter Patient');
//        } else {
//            holder.find('#search_patient_button').html('Find Patient');
//        }

    //when the patient is selected change the title of the accordion from "Patient Info" to:
    // "LastName, FirstName MiddleName Suffix | MM-DD-YYYYY | M | MRN Type: MRN"
    if( patient ) {
        calllogSetPatientAccordionTitle(patient, holderId);
    }

    calllogShowHideListPreviousEntriesBtn(patient);
    if( patient ) {
        //click btn
        $('#calllog-list-previous-entries-btn').click();
        $('#calllog-list-previous-tasks-btn').click();
    }

    //TODO: add previous encounters to the ".combobox-previous-encounters"
    if( patient ) {
        calllogAddPreviousEncounters(patient);
    }

    //console.log('populate PatientInfo: finished');
}

function populateInputFieldCalllog( fieldEl, data, index, modify ) {
    var value = null;
    if( data ) { //&& data[index]
        value = data[index];
        //lock field
//            fieldEl.prop('disabled', true);
//            fieldEl.closest('.input-group').find('input').prop('disabled', true);
//            if( fieldEl.hasClass('datepicker') ) {
//                var elementDatepicker = fieldEl.closest('.input-group.date');
//                elementDatepicker.datepicker("remove");
//            }
        disableField(fieldEl,true);
    } else {
        //unlock field
//            fieldEl.prop('disabled', false);
//            fieldEl.closest('.input-group').find('input').prop('disabled', false);
//            if( fieldEl.hasClass('datepicker') ) {
//                var elementDatepicker = fieldEl.closest('.input-group.date');
//                initSingleDatepicker(elementDatepicker);
//            }
        disableField(fieldEl,false);
    }
    //console.log(index+': value='+value);

    if( typeof modify === 'undefined' ){
        modify = true;
    }

    if( modify ) {
        fieldEl.val(value);
    }

    //attache alias
    //if( index == "lastname" || index == "firstname" || index == "middlename" || index == "suffix" ) {
    var statusIndex = index+"Status";
    if( data && statusIndex in data && data[statusIndex] == 'alias' ) {

//                var aliasHtml =
//                    '<span class="input-group-addon">'+
//                        '<input'+
//                            ' type="checkbox" id="oleg_calllogbundle_patienttype_encounter_0_patfirstname_0_alias"'+
//                            ' name="oleg_calllogbundle_patienttype[encounter][0][patfirstname][0][alias]"'+
//                            ' value="1"'+
//                        '>'+
//                        '<label style="margin:0;" for="oleg_calllogbundle_patienttype_encounter_0_patfirstname_0_alias">Alias</label>'+
//                '</span>';

        //show alias with checked checkbox
        var parentEl = fieldEl.parent();
        parentEl.find('.input-group-addon').show(_transTime);
        parentEl.removeClass('input-group-hidden').addClass('input-group');
        parentEl.find('input[type=checkbox]').prop('checked', true);
    }

    //}

    return value;
}

function populateSelectFieldCalllog( fieldEl, data, index ) {
    //var disableStr = "readonly"; //disabled
    var disableStr = "disabled";
    var value = null;
    if( data ) { //&& data[index]
        value = data[index];
        //lock field
        fieldEl.prop(disableStr, true);
    } else {
        //unlock field
        fieldEl.prop(disableStr, false);
    }
    //console.log('populate Select Field Calllog: value='+value);
    //console.log(fieldEl);
    fieldEl.select2('val', value);
    //if( value ) {
    //    //console.log("set value");
    //    fieldEl.select2('val', value);
    //    //console.log("after set value");
    //} else {
    //    //console.log("set data");
    //    fieldEl.select2('data', null);
    //    //console.log("after set data");
    //}
    //console.log('after populate Select Field Calllog !!!: value='+value);
    return value;
}

function processMrnFieldsCalllog( patient, modify, holderId ) {
    //console.log("process Mrn FieldsCalllog patient:");
    //console.log(patient);

    //var disableStr = "readonly"; //disabled
    var disableStr = "disabled";

    var holder = getHolder(holderId);

    if( typeof modify === 'undefined' ){
        modify = true;
    }

    var mrntype = holder.find('.mrntype-combobox');
    var mrnid = holder.find('.patientmrn-mask');

    if( patient && patient.mrntype && patient.mrn ) {

        calllogAddMrnType(patient);

        mrntype.select2('val',patient.mrntype);
        setMrntypeMask(mrntype,false);

        mrnid.val(patient.mrn);

        mrntype.prop(disableStr, true);
        mrnid.prop(disableStr, true);
        //"readonly"
        mrntype.prop("readonly", true);
        mrnid.prop("readonly", true);

    } else {

        mrntype.prop(disableStr, false);
        mrnid.prop(disableStr, false);
        //"readonly"
        mrntype.prop("readonly", false);
        mrnid.prop("readonly", false);

        if( modify ) {
            mrntype.select2('val', _mrntype_original);
            setMrntypeMask(mrntype,false);
        }

        if( modify ) {
            mrnid.val(null);
        }

    }
}
function calllogAddMrnType(patient) {

    //patient.mrntype = 30;
    //patient.mrntypestr = "mrn30";

    var newEl = {id:patient.mrntype, text:patient.mrntypestr};
    _mrntype.push(newEl);
   //console.log(_mrntype);
    
    var targetid = ".mrntype-combobox";
    targetid = getElementTargetByHolder(null,targetid);

    populateSelectCombobox( targetid, _mrntype, null );

    $(targetid).select2('val', patient.mrntype);

    return;
}


function editPatientBtn(holderId) {
    //disableAllFields(false,holderId);
    //calllogHideAllAlias(false,false,holderId);

    //var r = confirm("Are you sure you would like to navigate away from this page? Text you may have entered has not been saved yet.");
    //if (r == true) {
    //    //x = "You pressed OK!";
    //} else {
    //    //x = "You pressed Cancel!";
    //    return;
    //}

    var holder = getHolder(holderId);
    //calllog-patient-id-patient-holder-1
    //calllog-patient-id-patient-holder-1
    //console.log("id="+"#calllog-patient-id-"+holderId);
    var patientId = holder.find("#calllog-patient-id-"+holderId).val();
    //console.log("patientId="+patientId);
    var url = Routing.generate('calllog_patient_edit',{'id':patientId});
    //alert("url="+url);
    window.location.href = url;
}

//function calllogHideAllAlias(hide,clear,holderId) {
//    var holder = getHolder(holderId);
//    if( hide ) {
//        //hide all alias
//        holder.find('.alias-group').find('.input-group-addon').hide();
//        holder.find('.alias-group').find('.input-group').removeClass('input-group').addClass('input-group-hidden');
//    } else {
//        //show all alias
//        holder.find('.alias-group').find('.input-group-addon').show();
//        holder.find('.alias-group').find('.input-group-hidden').removeClass('input-group-hidden').addClass('input-group');
//    }
//    if( clear ) {
//        holder.find('.alias-group').find('input[type=checkbox]').prop('checked', false);
//    }
//}

function getHolder(holderId) {
    if( holderId ) {
        return $('#'+holderId);
    }
    return $('.calllog-patient-holder');
}

//Any subsequent click or tap on any element (button, field, etc) should hide this red well.
function calllogInputListenerErrorWellRemove( holderId ) {
    var holder = getHolder(holderId);
    holder.find('input').on('focus', function(event) {
        //console.log("calllogInputListenerErrorWellRemove click id="+$(this).attr("id"));
        holder.find('#calllog-danger-box').hide(_transTime);
        holder.find('#calllog-danger-box').html("");
    });
}

//when the patient is selected change the title of the accordion from "Patient Info" to:
// "LastName, FirstName MiddleName Suffix | MM-DD-YYYYY | M | MRN Type: MRN"
function calllogSetPatientAccordionTitle( patient, holderId ) {
    //console.log("calllog SetPatientAccordionTitle");
    //if( !patient ) {
    //    return;
    //}
    var formtype = $('#formtype').val();
    //console.log('formtype='+formtype);
    var holder = getHolder(holderId);
    var panelEl = holder.find(".calllog-patient-information-panel");
    if( patient ) {
        var patientInfoArr = [];
        if( patient.fullName )
            patientInfoArr.push(patient.fullName); //"LastName, FirstName MiddleName Suffix
        if( patient.dob )
            patientInfoArr.push(patient.dob); //MM-DD-YYYYY
        if( patient.sexstr )
            patientInfoArr.push(patient.sexstr); //M
        if( patient.age )
            patientInfoArr.push(patient.age); //5 y.o.

        // if( patient.locationInfo ) {
        //     patientInfoArr.push(patient.locationInfo);
        // }

        //console.log("push mrn="+patient.mrntypestr + ": "+patient.mrn);

        patientInfoArr.push(patient.mrntypestr + ": "+patient.mrn); //MRN Type: MRN
        var patientInfo = patientInfoArr.join(" | ");
        //console.log("patientInfo="+patientInfo);
        if( patientInfo ) {
            holder.find('.calllog-patient-panel-title').html(patientInfo);
            $('#user-headroom-header').html(patientInfo);
            if( formtype == "call-entry" ) {
                panelEl.collapse('hide');
            }
        }
    } else {
        holder.find('.calllog-patient-panel-title').html("Patient Info");
        $('#user-headroom-header').html("Patient Info");
        //panelEl.show(_transTime);
        if( formtype == "call-entry" ) {
            panelEl.collapse('show');
        }
    }
    //console.log("calllog SetPatientAccordionTitle: finished");
}


//Pressing "Enter" on the keyboard while the cursor is in the MRN, DOB, Last Name, or First Name field should press the "Find Patient" button.
function calllogPressEnterOnKeyboardAction( holderId ) {
    //console.log("calllog Press EnterOnKeyboardAction");
    var formtype = $('#formtype').val();
    //console.log("formtype=" + formtype);
    if( formtype == 'call-entry' || formtype == 'add-patient-to-list' ) {
        var holder = getHolder(holderId);

        holder.find('.patientmrn-mask, .patient-dob-date, .encounter-lastName, .encounter-firstName, .patient-phone, .patient-email').on('keydown', function (event) {
        //holder.find('.patientmrn-mask').on('keydown', function (event) {
            //console.log("calllog PressEnterOnKeyboardAction val=" + $(this).val()+", event="+event.which);

            if( event.which == 13 ) {
                event.preventDefault();

                //alert('You pressed enter!');
                if( $(this).val() ) {

                    holder.find('#search_patient_button').click();

                    setTimeout(function () {
                        //close datepicker box
                        var datepickerDropdown = $(".datepicker-dropdown");
                        //printF(datepickerDropdown, "datepicker-dropdown:");
                        datepickerDropdown.remove();
                        $("#patient-holder-1").trigger("click");
                    }, 100);

                }
            }
        });

    }
}

function calllogScrollToTop() {
    //$(window).scrollTop(0);
    $("html, body").animate({ scrollTop: 0 }, "slow");
}

function calllogPresetMrnMrntype(holderId) {
    var holder = getHolder(holderId);
    var mrn = $('.patientmrn-mask').val();
    var mrntype = $('.mrntype-combobox').select2('val');
    //console.log("1 preset mrntype="+mrntype);

    //var mrn = $('#url-mrn').val();
    //var mrntype = $('#url-mrntype').val();
    //
    //if( mrntype ) {
    //    var mrntypeField = holder.find('.mrntype-combobox');
    //    mrntypeField.select2('val',mrntype);
    //    setMrntypeMask(mrntypeField,false);
    //    mrntypeField.prop('disabled', false);
    //}
    //
    //if( mrn ) {
    //    var mrnField = holder.find('.patientmrn-mask');
    //    mrnField.val(mrn);
    //    mrnField.prop('disabled', false);
    //}

    //trigger patient search
    if( mrntype && mrn ) {
        //setTimeout(function(){
        //    holder.find('#search_patient_button').click();
        //}, 300);

        var formtype = $('#formtype').val();
        var mrntype = $('#mrntype').val();
        //console.log("2 preset mrntype="+mrntype);
        findCalllogPatient(holderId, formtype, mrntype);
    }

}

//prefill location name if it has been opened
function calllogToggleSingleEncounterPanel(btn,target) {
    var formcycle = $('#formcycle').val();
    if( formcycle == 'show' ) {
        return;
    }
    //preset .user-location-name-field to the 'Encounter's Location'
    var locationNameField = $(btn).closest('.panel').find('.user-location-name-field');
    locationNameField.val("Encounter's Location");

    $(target).toggle();
    //toggleSinglePanel(btn,target);
}
//function calllogToggleSinglePanel( el, target ) {
//    $(target).toggle();
//
//    //console.log("btnTarget="+btnTarget);
//    //var btnEl = $(el).closest('.panel-heading').find('button');
//    //btnEl.trigger("click");
//    //btnEl.click();
//}

//overwrite calllog SetPatientAccordionTitle according to a new encounter date
function calllogUpdatePatientAgeListener(holderId) {
    $('input.encounter-date').on("input change", function (e) {
        calllogUpdatePatientAge($(this),holderId);
    });
}
function calllogUpdatePatientAge(fieldEl,holderId) {
    var holder = getHolder(holderId);

    var dateField = fieldEl.val();
    //console.log('dateField='+dateField);

    var patientId = holder.find('.patienttype-patient-id').val();
    if( !patientId ) {
        return;
    }

    var url = Routing.generate('calllog_get_patient_title');
    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true,
        data: {patientId: patientId, nowStr:dateField },
    }).success(function(data) {
        //console.log("output="+data);
        if( data != "ERROR" ) {
            holder.find('.calllog-patient-panel-title').html(data);
            $('#user-headroom-header').html(data);
        } else {
            holder.find('.calllog-patient-panel-title').html("Patient Info");
            $('#user-headroom-header').html("Patient Info");
        }
    }).done(function() {
        //console.log("update patient title done");
    });
}

function calllogEnableMessageCategoryService(holderId) {
    var holder = getHolder(holderId);

    //enable the last of '.composite-tree-holder'
    //var lastCategory = holder.find('.composite-tree-holder').find('.treenode').last();
    var lastCategory = holder.find('input.ajax-combobox-compositetree').last();
    //printF(lastCategory,"lastCategory:");
    //console.log(lastCategory);
    lastCategory.prop('disabled', false);
}



//remove disabled formnode-holders
function calllogSubmitForm(btn,messageStatus) {

    var lbtn = Ladda.create( btn );
    calllogStartBtn(lbtn);

    $('#calllog-msg-danger-box').html("");
    $('#calllog-msg-danger-box').hide();

    //checks
    var holder = $('.calllog-patient-holder');

    /////// If the user enters patient info, does NOT press the "Find Patient" button (or presses it, but does not select a patient) ////////
    var mrn = holder.find(".patientmrn-mask").val();
    var dob = holder.find(".patient-dob-date").val();
    var lastname = holder.find(".encounter-lastName").val();
    var firstname = holder.find(".encounter-firstName").val();
    var phone = holder.find(".patient-phone").val();
    var email = holder.find(".patient-email").val();
    var patientId = holder.find(".patienttype-patient-id").val();
    if( !patientId && (mrn || dob || lastname || firstname || phone || email) ) {
        var confMsg = 'You have entered patient information, but patient has not been found.'+
            ' The patient info will be discarded and call entry will not be attached to the patient.' +
            ' Otherwise, press "Cancel" and then press "Find Patient" button.'+
            ' Are you sure you want to proceed without patient?';
        if( confirm(confMsg) == true ) {
            //x = "You pressed OK!";
        } else {
            //x = "You pressed Cancel!";
            calllogStopBtn(lbtn);
            return false;
        }
    }
    /////// EOF If the user enters patient info, does NOT press the "Find Patient" button (or presses it, but does not select a patient) ////////

    ///////////// if issue is not selected => "Please select the appropriate issue to save your entry" ///////////////
    var messageCategoryError = null;
    var messageHolder = $('.ajax-combobox-messageCategory').closest('.composite-tree-holder');
    var messageCategories = messageHolder.find('.treenode');
    if( messageCategories ) {

        var firstMessageCategory = messageCategories.first().find('input.ajax-combobox-messageCategory');
        //console.log(firstMessageCategory);

        if( firstMessageCategory.hasClass('combobox-compositetree-postfix-level') ) {

            var postfixMinLevel = firstMessageCategory.data("label-postfix-level");
            postfixMinLevel = parseInt(postfixMinLevel);
            postfixMinLevel = postfixMinLevel + 1;
            //console.log("postfixMinLevel=" + postfixMinLevel);
            //var treeNodes = $('.composite-tree-holder').find('.treenode');

            var messageCategoriesLength = messageCategories.length;
            //console.log("messageCategoriesLength=" + messageCategoriesLength);

            //console.log(parseInt(messageCategoriesLength) + " < " + parseInt(postfixMinLevel));
            if( parseInt(messageCategoriesLength) < parseInt(postfixMinLevel) ) {
                messageCategoryError = "Please select the appropriate service and issue to save your entry.";// + " [Service is not selected]";
            } else {
                //console.log(messageCategories.last().find('.ajax-combobox-messageCategory'));
                var messageCategoryData = messageCategories.last().find('.ajax-combobox-messageCategory').select2('data'); //'data'
                if( messageCategoryData ) {
                    //console.log("messageCategory text=" + messageCategoryData.text);
                    if( !messageCategoryData.text ) {
                        messageCategoryError = "Please select the appropriate issue to save your entry.";// + " [Issue is not selected]";
                        //console.log("messageCategoryData.text=" + messageCategoryData.text);
                    }
                } else {
                    //console.log("messageCategoryData is null");
                    messageCategoryError = "Please select the appropriate issue to save your entry.";
                }
            }

            if( messageCategoryError ) {
                $('#calllog-msg-danger-box').html(messageCategoryError);
                $('#calllog-msg-danger-box').show();
                calllogStopBtn(lbtn);
                return false;
            }
        }
    }
    //console.log("exit");
    //calllogStopBtn(lbtn);
    //return false;
    ///////////// EOF if issue is not selected => "Please select the appropriate issue to save your entry" ///////////////

    ///////////// Edit/Amend: Please provide the amendment reason; Check message and encounter version if outdated ///////////////
    var formcycle = $('#formcycle').val();
    if( formcycle == 'edit' || formcycle == 'amend' ) {

        //Please provide the amendment reason
        var amendmentReason = $(".ajax-combobox-amendmentReason");
        //console.log(amendmentReason);
        if( amendmentReason.length > 0 ) {
            //console.log("process amendmentReason="+amendmentReason);
            var amendmentReasonData = amendmentReason.select2('data');
            if (amendmentReasonData && amendmentReasonData.id) {
                //ok
            } else {
                $('#calllog-msg-danger-box').html("Please provide the amendment reason.");
                $('#calllog-msg-danger-box').show();
                calllogStopBtn(lbtn);
                return false;
            }
        }

        //Check message and encounter version if outdated
        if( $('#entityId') ) {
            var entityId = $('#entityId').val();
            var latestNextMessageVersion = $('#currentMessageVersion').val();
            var latestNextEncounterVersion = $('#currentEncounterVersion').val();
            var versionValid = calllogIsMessageVersionValid(entityId,latestNextMessageVersion,latestNextEncounterVersion);
            //console.log("versionValid="+versionValid);
            if( versionValid === true ) {
                //ok
            } else {
                var newEntryUrl = $('#latestEntryUrl').val();
                var newEntryUrl = '<a href="'+newEntryUrl+'" target="_blank">HERE</a>';
                var versionErrorMsg = "The entry you are editing has been already updated with a new information. " +
                    "Please click "+newEntryUrl+" to see the latest updated entry on a new page.";
                $('#calllog-msg-danger-box').html(versionErrorMsg);
                $('#calllog-msg-danger-box').show();
                calllogStopBtn(lbtn);
                return false;
            }
        }
    }
    ///////////// EOF Please provide the amendment reason. ///////////////



    //B- Uniqueness of the Encounter Location Name. If the entered location name already exists in the database
    // (but any associated entered (non-empty) field values such as phone number do not equal associated values in the DB),
    // show a red well (dialog box? notification?) with:
    //Encounter location named "XXX" already exits. Please select this encounter location or
    // enter a different unique location name to create a new encounter location record.
    //Location's fields are locked, so it is not possible to modify fields
    if( 0 ) {
        var locationPhone = $('.user-location-phone-field').val();
        var locationRoom = $('.ajax-combobox-room').select2('val');
        var locationSuite = $('.ajax-combobox-suite').select2('val');
        if (locationPhone || locationRoom || locationSuite) {
            var locationUrl = Routing.generate('calllog_check_encounter_location');
            $.ajax({
                url: locationUrl,
                timeout: _ajaxTimeout,
                type: "GET",
                async: asyncflag,
                data: {phone: locationPhone, room: locationRoom, suite: locationSuite},
            }).success(function (data) {
                //console.log("data="+data);
                if (data == "Not Exists") {
                    //ok
                } else {

                }
            }).fail(function () {
                //alert(error);
            }).done(function () {
                //calllogStopBtn(lbtn);
                //console.log("token ok");
            });
        }
    }

    //C- Uniqueness of the Healthcare Provider Name. Fields are locked and not possible to modify them.
    //return; //testing

    ///////// if the other fields in that accordion remain empty and only "Lab Result Date" has a value, do not write it to the DB. /////////
    removeDefaultDateTimeIfEmptyOtherFieldsInSection($('#calllog-new-entry-form'));
    //calllogStopBtn(lbtn);//testing
    //return; //testing
    ///////// EOF if the other fields in that accordion remain empty and only "Lab Result Date" has a value, do not write it to the DB. /////////

    if( messageStatus == "Draft" ) {
        $('.formnode-holder-disabled').remove();
        $('#messageStatusJs').val(messageStatus);
        $('#calllog-new-entry-form').submit();
    }

    if( messageStatus == "Signed" ) {
        //check password by ajax. error="User name / password combination not accepted. Please try again."
        var token = $('#calllog-user-password').val();
        var error = "User name / password combination not accepted. Please try again.";

        var url = Routing.generate('employees_authenticate_user');
        $.ajax({
            url: url,
            timeout: _ajaxTimeout,
            type: "POST",
            async: asyncflag,
            data: {token: token},
        }).success(function(data) {
            //console.log("data="+data);
            if( data == "OK" ) {
                $('.formnode-holder-disabled').remove();
                $('#messageStatusJs').val(messageStatus);
                $('#calllog-new-entry-form').submit();
            } else {
                $('#calllog-msg-danger-box').html(error);
                $('#calllog-msg-danger-box').show();
                calllogStopBtn(lbtn);
            }
        }).fail(function() {
            //alert(error);
            $('#calllog-msg-danger-box').html(error);
            $('#calllog-msg-danger-box').show();
        }).done(function() {
            //calllogStopBtn(lbtn);
            //console.log("token ok");
        });
    }
}

function calllogStartBtn(lbtn) {
    $('button').prop('disabled',true);
    lbtn.start();
}
function calllogStopBtn(lbtn) {
    lbtn.stop();
    $('button').prop('disabled',false);
}

function removeDefaultDateTimeIfEmptyOtherFieldsInSection( formElement ) {
    var selectStr = 'input,textarea,select';
    formElement.find('.with-default-datetime').each( function() {
        var section = $(this).closest('.form-nodes-holder');
        var fields = section.find(selectStr).not("*[id^='s2id_']").not(".with-default-datetime");
        removeDefaultDateTimeIfOtherEmpty($(this),fields);
    });
}
function removeDefaultDateTimeIfOtherEmpty( thisField, allFields ) {
    //printF(thisField,"thisField=");
    //console.log("thisField="+thisField.value);
    var allFieldsLen = allFields.length;
    //console.log("allFieldsLen="+allFieldsLen);
    var empty = 0;
    allFields.each( function() {
        //console.log("field="+$.trim(this.value));
        if ($.trim(this.value) == "") empty++;
    });

    //console.log("empty="+empty);
    if( empty == allFieldsLen ) {
        //console.log("set datetime empty; original="+thisField.value+"; thisField.val()="+thisField.val());
        thisField.val(null);
        //if( thisField.hasClass('datepicker') ) {
        //    thisField.val(null);
        //} else {
        //    thisField[0].selectedIndex = -1;
        //}
    } else {
        //console.log("Don't change default datetime; original="+thisField.value);
    }
}

function calllogLocationNameListener(holderId) {
    //var holder = getHolder(holderId);

    var target = ".ajax-combobox-locationName";

    $(target).on("change", function (e) {
        //console.log("calllogLocationNameListener: change", e);

        //populate location fields by name
        //var value = $("[name='nameofobject']");

        var locationNameEl = $(this);

        var selectData = locationNameEl.select2('data');

        //clean id
        var holder = locationNameEl.closest('.panel-body');
        var idEl = holder.find('.user-object-id-field');
        idEl.val(null);

        if( !selectData ) {
            //console.log('no selectData');
            //TODO: clean all fields?
            locationNamePopulateLocationFields( holder, null);
            return;
        }

        var locationId = selectData.id;
        if( !locationId ) {
            //console.log('no locationId');
            return;
        }

        var url = Routing.generate('employees_get_location_by_name');
        $.ajax({
            url: url,
            timeout: _ajaxTimeout,
            //type: "GET",
            async: asyncflag,
            data: {locationId: locationId},
        }).success(function(data) {
            //console.log("data length="+data.length);
            //console.log(data);

            if( data ) {
                //populate location fields
                //var holder = locationNameEl.closest('.panel-body');
                //console.log("holder:");
                //console.log(holder);
                locationNamePopulateLocationFields( holder, data);
            }

        }).fail(function() {
            alert("Error getting location by location ID "+locationId);
        }).done(function() {
            //console.log("update patient title done");
        });

    });

    return;
}

var _encounterLocationTypeIds = null;
function locationNamePopulateLocationFields( holder, data ) {

    //set the default location type
    if( !_encounterLocationTypeIds ) {
        //holder.find('.user-location-locationTypes').select2('val',_encounterLocationTypeId);
        _encounterLocationTypeIds = holder.find('.user-location-locationTypes').select2('val');
    }

    var fieldNames = ['locationTypes','phone','room','suite','floor','floorSide','building','comment','street1','street2','city','state','zip','country','county','institution'];

    for( var i = 0; i < fieldNames.length; i++ ) {
        //text += fieldNames[i] + "<br>";
        var fieldName = fieldNames[i]; //phone
        //console.log("fieldName="+fieldName);

        var partialIdStr = "["+fieldName+"]";
        //console.log("partialIdStr="+partialIdStr);
        var fieldEl = holder.find('[name*="'+partialIdStr+'"]');
        //console.log("found=" + fieldEl.attr('id'));
        printF(fieldEl, "found=");

        var locationId = null;
        if( data && ('id' in data) ) {
            if( data['id'] ) {
                locationId = data['id'];
            }
        }

        if( fieldEl ) {

            var fieldVal = null;

            //if( (fieldName in data) && data[fieldName] ) {
            if( locationId && data && (fieldName in data) ) {
                //var partialIdStr = partialId+"_"+fieldName;
                //[currentLocation][room]
                //var partialIdStr = "["+partialId+"]["+fieldName+"]";
                //var partialIdStr = "["+fieldName+"]";
                //console.log("partialIdStr="+partialIdStr);
                //var fieldEl = holder.find('[name*="'+partialIdStr+'"]');
                //if( fieldEl ) {
                //console.log("found=" + fieldEl.attr('id'));
                //printF(fieldEl, "found=");
                //if (fieldEl.hasClass('combobox')) {
                //    fieldEl.select2('val', data[fieldName]);
                //} else {
                //    fieldEl.val(data[fieldName]);
                //}
                //}
                fieldVal = data[fieldName];
            }

            if( fieldEl.hasClass('combobox') ) {
                console.log("select2 set fieldVal=" + fieldVal);
                fieldEl.select2('val', fieldVal);
            } else if( fieldEl.hasClass('ajax-combobox-compositetree') ) {
                console.log("select2 set compositetree fieldVal=" + fieldVal);
                fieldEl.select2('val', fieldVal);
            } else {
                fieldEl.val(fieldVal);
            }

            //lock/unlock the field
            if( data && locationId ) {
                //lock
                disableField(fieldEl,true)
            } else {
                //unlock
                disableField(fieldEl,false)
            }

        }
    }//for

    //set id
    var idEl = holder.find('.user-object-id-field');
    idEl.val(locationId);

    //set location name
    var locationNameData = holder.find('.ajax-combobox-locationName').select2('data');
    if( locationNameData ) {
        holder.find('.user-location-name-field').val(locationNameData.text);
    } else {
        holder.find('.user-location-name-field').val(null);
    }

    //set location type to default
    if( !locationId ) {
        holder.find('.user-location-locationTypes').select2('val',_encounterLocationTypeIds);
    }

}

//ajax-combobox-encounterReferringProvider
function calllogEncounterReferringProviderListener(holderId) {
    var target = ".ajax-combobox-encounterReferringProvider";

    $(target).on("change", function (e) {
        //console.log("calllogEncounterReferringProviderListener: change", e);

        //populate location fields by name
        //var value = $("[name='nameofobject']");

        var providerNameEl = $(this);

        var selectData = providerNameEl.select2('data');

        //clean id
        var holder = providerNameEl.closest('.panel-body');

        if( !selectData ) {
            //console.log('no selectData');
            //TODO: clean all fields?
            calllogEncounterReferringProviderPopulate(holder,null);
            return;
        }

        var providerId = selectData.id;
        if( !providerId ) {
            //console.log('no providerId');
            return;
        }

        var url = Routing.generate('scan_get_encounterreferringprovider');
        $.ajax({
            url: url,
            timeout: _ajaxTimeout,
            //type: "GET",
            async: asyncflag,
            data: {providerId: providerId},
        }).success(function(data) {
            //console.log("data length="+data.length);
            //console.log(data);

            if( data ) {
                //populate location fields
                //var holder = locationNameEl.closest('.panel-body');
                //console.log("holder:");
                //console.log(holder);
                calllogEncounterReferringProviderPopulate(holder,data);
            }

        }).fail(function() {
            alert("Error getting provider by provider ID "+providerId);
        }).done(function() {
            //console.log("update patient title done");
        });

    });

    return;
}
function calllogEncounterReferringProviderPopulate( holder, data ) {
    var fieldNames = ['referringProviderSpecialty','referringProviderPhone','referringProviderEmail'];

    for( var i = 0; i < fieldNames.length; i++ ) {
        //text += fieldNames[i] + "<br>";
        var fieldName = fieldNames[i]; //phone
        //console.log("fieldName="+fieldName);

        var partialIdStr = "["+fieldName+"]";
        //console.log("partialIdStr="+partialIdStr);
        var fieldEl = holder.find('[name*="'+partialIdStr+'"]');
        //console.log("found=" + fieldEl.attr('id'));
        printF(fieldEl, "found=");

        var userId = null;
        if( data && ('id' in data) ) {
            if( data['id'] ) {
                userId = data['id'];
            }
        }

        if( fieldEl ) {

            var fieldVal = null;

            //if( (fieldName in data) && data[fieldName] ) {
            if( userId && data && (fieldName in data) ) {
                fieldVal = data[fieldName];
            }

            if( fieldEl.hasClass('combobox') ) {
                fieldEl.select2('val', fieldVal);
            } else {
                fieldEl.val(fieldVal);
            }

            //lock/unlock the field
            if( data && userId ) {
                //lock
                disableField(fieldEl,true)
            } else {
                //unlock
                disableField(fieldEl,false)
            }

        }
    }//for
}

function calllogShowHideListPreviousEntriesBtn(patient) {
    if( patient ) {
        //$('#calllog-list-previous-entries-btn').show();
        $('#calllog-list-previous-entries').html("");
    } else {
        $('#calllog-list-previous-entries-btn').hide();
    }
}
function calllogListPreviousEntriesForPatient( holderId, messageCategoryId ) {

    //patientId = patient['id'];
    var holder = getHolder(holderId);
    var patientId = holder.find("#calllog-patient-id-"+holderId).val();
    if( !patientId ) {
        return;
    }

    var messageIdStr = "";
    var messageId = $("#calllog-current-message-id").val();
    if( messageId ) {
        messageIdStr = "&messageid="+messageId;
    }

    //reset: show button and clear entries list
    //$('#calllog-list-previous-entries').html("");
    calllogShowHideListPreviousEntriesBtn(true);

    if( typeof messageCategoryId === 'undefined' ) {
        messageCategoryId = null;
    }
    //console.log("messageCategoryId="+messageCategoryId);

    var btn = document.getElementById("calllog-list-previous-entries-btn");
    var lbtn = Ladda.create(btn);
    calllogStartBtn(lbtn);

    var url = Routing.generate('calllog-list-previous-entries');
    url = url + "?patientid="+patientId+"&type="+messageCategoryId+messageIdStr;

    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        type: "GET",
        //type: "POST",
        //data: {id: userid },
        dataType: 'json',
        async: asyncflag
    }).success(function(response) {
        //console.log(response);
        var template = response;
        $('#calllog-list-previous-entries').html(template); //Change the html of the div with the id = "your_div"
        calllogShowHideListPreviousEntriesBtn(null); //hide btn

        var filterSelectBox = $('.filter-message-category');
        //printF(filterSelectBox,"filterSelectBox:");
        //console.log(filterSelectBox);
        specificRegularCombobox(filterSelectBox);
        if( messageCategoryId ) {
            filterSelectBox.select2('val', messageCategoryId);
        }

    }).done(function() {
        calllogStopBtn(lbtn);
    }).error(function(jqXHR, textStatus, errorThrown) {
        console.log('Error : ' + errorThrown);
    });
}

function calllogShowHideListPreviousTasksBtn(patient) {
    if( patient ) {
        $('#calllog-list-previous-tasks').html("");
    } else {
        $('#calllog-list-previous-tasks-btn').hide();
    }
}
function calllogListPreviousTasksForPatient( holderId, cycle, messageCategoryId ) {

    //patientId = patient['id'];
    var holder = getHolder(holderId);
    var patientId = holder.find("#calllog-patient-id-"+holderId).val();
    if( !patientId ) {
        return;
    }

    var messageIdStr = "";
    var messageId = $("#calllog-current-message-id").val();
    if( messageId ) {
        messageIdStr = "&messageid="+messageId;
    }

    //reset: show button and clear tasks list
    calllogShowHideListPreviousTasksBtn(true);

    if( typeof messageCategoryId === 'undefined' ) {
        messageCategoryId = null;
    }
    console.log("messageCategoryId="+messageCategoryId);

    if( typeof cycle === 'undefined' ) {
        cycle = "show";
    }

    var btn = document.getElementById("calllog-list-previous-tasks-btn");
    var lbtn = Ladda.create(btn);
    calllogStartBtn(lbtn);

    var url = Routing.generate('calllog-list-previous-tasks');
    url = url + "?patientid="+patientId+"&cycle="+cycle+"&type="+messageCategoryId+messageIdStr;

    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        type: "GET",
        //type: "POST",
        //data: {id: userid },
        dataType: 'json',
        async: asyncflag
    }).success(function(response) {
        //console.log(response);
        var template = response;
        $('#calllog-list-previous-tasks').html(template); //Change the html of the div with the id = "your_div"
        calllogShowHideListPreviousTasksBtn(null); //hide btn

        var filterSelectBox = $('.filter-message-category');
        //printF(filterSelectBox,"filterSelectBox:");
        //console.log(filterSelectBox);
        specificRegularCombobox(filterSelectBox);
        if( messageCategoryId ) {
            filterSelectBox.select2('val', messageCategoryId);
        }

    }).done(function() {
        calllogStopBtn(lbtn);
    }).error(function(jqXHR, textStatus, errorThrown) {
        console.log('Error : ' + errorThrown);
    });
}

function calllogGetMetaphoneValue(holderId) {
    var holder = getHolder(holderId);
    var metaphoneRes = null;
    var metaphone = holder.find('#search_metaphone:checked').val();
    //console.log('metaphone='+metaphone);
    if( metaphone ) {
        metaphoneRes = true;
    }
    //console.log('metaphoneRes='+metaphoneRes);
    return metaphoneRes;
}

function calllogIsMessageVersionValid( messageId, latestNextMessageVersion, latestNextEncounterVersion ) {

    if( !messageId || !latestNextMessageVersion || !latestNextEncounterVersion ) {
        return false;
    }

    var result = false;
    var url = Routing.generate('calllog-check-message-version');

    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        type: "GET",
        //type: "POST",
        data: {messageId:messageId, latestNextMessageVersion:latestNextMessageVersion, latestNextEncounterVersion:latestNextEncounterVersion },
        dataType: 'json',
        async: false //use synchronous => wait for response.
    }).success(function(response) {
        //console.log('response='+response);
        if( response == 'OK' ) {
            //console.log('response OK!');
            result = true;
        } else {
            //console.log('response not OK');
            result = false;
        }
    }).done(function() {
        //
    }).error(function(jqXHR, textStatus, errorThrown) {
        console.log('Error : ' + errorThrown);
    });

    return result;
}

function calllogAddPreviousEncounters(patient) {
    var url = Routing.generate('calllog-get-previous-encounters');
    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        type: "GET",
        //type: "POST",
        data: {patientId:patient.id},
        dataType: 'json',
        async: true //use synchronous => wait for response.
    }).done(function(response) {
        console.log('response:');
        console.log(response);
        //TODO: add encounters to .combobox-previous-encounters select2 (implement as in updateUserComboboxes)

        response.forEach(function(item){
            var thisEncounterId = item['id'];
            var thisEncounterText = item['text'];
            console.log('thisEncounterText='+thisEncounterText+", thisEncounterId="+thisEncounterId);
            //text += thisEncounterText;
            if( thisEncounterText ) {
                var newOption = new Option(thisEncounterText, thisEncounterId, false, false);
                //var newOption = new Option(thisEncounterId, thisEncounterText, false, false);
                $("select.combobox-previous-encounters").append(newOption).trigger('change');
                //$("select.combobox-previous-encounters").append(newOption);
            }
        });


        // for(var thisEncounterId in responseReverse) {
        //     var thisEncounterText = responseReverse[thisEncounterId];
        //     console.log('thisEncounterText='+thisEncounterText+", thisEncounterId="+thisEncounterId);
        //     if( thisEncounterText ) {
        //         var newOption = new Option(thisEncounterText, thisEncounterId, false, false);
        //         //var newOption = new Option(thisEncounterId, thisEncounterText, false, false);
        //         $("select.combobox-previous-encounters").append(newOption).trigger('change');
        //         //$("select.combobox-previous-encounters").append(newOption);
        //     }
        // }
        //$("select.combobox-previous-encounters").trigger('change');
        //console.log("text="+text);

        calllogEncounterListener();

    }).always(function() {
        //
    }).error(function(jqXHR, textStatus, errorThrown) {
        console.log('Error : ' + errorThrown);
    });
}
function calllogRemovePreviousEncounters() {
    //remove all encounters except (Auto-generated Encounter Number) with value=""
    //$('select.combobox-previous-encounters').select2('data', null);
    var existingOptions = $("select.combobox-previous-encounters").find("option");
    //console.log(existingOptions);
    existingOptions.each( function() {
        //console.log("value="+this.value);
        if( this.value ) {
            //console.log("remove this value="+this.value+"; text="+this.text);
            this.remove();
        }
    });
    $("select.combobox-previous-encounters").trigger('change');

    $(".message-previousEncounterId").val(null);
}

function calllogEncounterListener() {
    //update encounter fields
    $("select.combobox-previous-encounters").on("change", function(event) {
        var encounterId = $(this).select2('val');
        console.log("encounter changed: change encounterId="+encounterId);

        //set previous EncounterId - it will be used by a controller to attach this previous encounter to the message
        $(".message-previousEncounterId").val(encounterId);

        //hide current encounter data and show snapshot of the selected encounter?

        //load the previous encounter values for the selected encounter id into the existing fields of that accordion and lock them
        var url = Routing.generate('calllog-get-encounter-by-id');
        $.ajax({
            url: url,
            timeout: _ajaxTimeout,
            type: "GET",
            //type: "POST",
            data: {encounterId:encounterId},
            dataType: 'json',
            async: true //use synchronous => wait for response.
        }).done(function(response) {
            //console.log('response='+response);

            calllogPopulatePreviousEncounterInfo(response);

        }).always(function() {
            //
        }).error(function(jqXHR, textStatus, errorThrown) {
            console.log('Error : ' + errorThrown);
        });

    });
}

//Hide autogenerated sections and show/replace by selected encounter
function calllogPopulatePreviousEncounterInfo(encounterHtml) {
    //console.log('encounterHtml:');
    //console.log(encounterHtml);

    if( encounterHtml ) {
        $("#calllog-message-encounter-new").hide();
        $("#calllog-message-encounter-previous").html(encounterHtml);
    } else {
        $("#calllog-message-encounter-new").show();
        $("#calllog-message-encounter-previous").html(null);
    }
}


// function taskStatusBtnListener() {
//     return false;
//
//     $('.task-status-checkbox').on('change',function() {
//         console.log("on task-status-checkbox changed!");
//     });
//
//     $('.task-status-checkbox').change(function(){
//         console.log("change task-status-checkbox changed!");
//         if($(this).is(':checked')) {
//             // Checkbox is checked..
//         } else {
//             // Checkbox is not checked..
//         }
//     });
// }
function calllogTaskStatusCheckboxClick(btn) {
    //console.log(btn);
    var holderCheckbox = $(btn).closest('.calllog-checkbox-checkbox');
    var updateBtn = holderCheckbox.find('.btn-update-task');

    var originalTaskStatus = $(btn).data("taskstatus");



    if ($(btn).is(':checked')) {
        //console.log("task-status-checkbox checked");
        if( originalTaskStatus == "checked" ) {
            updateBtn.hide();
        } else {
            updateBtn.show();
        }
    }
    else {
        //console.log("task-status-checkbox !checked");
        if( originalTaskStatus == "checked" ) {
            updateBtn.show();
        } else {
            updateBtn.hide();
        }
    }
}
function calllogUpdateTaskBtnClicked(btn,cycle) {

    var checkboxBtn = $(btn).closest('.calllog-checkbox-checkbox').find('.task-status-checkbox');

    var status = null;
    if( checkboxBtn.is(':checked') ) {
        //console.log("task-status-checkbox is checked");
        status = 'completed';
    }
    else {
        //console.log("task-status-checkbox is unchecked");
        status = 'pending';
    }

    var r = confirm("Are you sure you want to change this task's status to "+status+"?");
    if( r == true ) {
        //OK
    } else {
        return false;
    }

    if( typeof cycle === 'undefined' ) {
        cycle = "show";
    }

    //$(btn).hide();
    var lbtn = Ladda.create(btn);
    $(btn).prop('disabled', true);
    $(btn).attr("disabled", true);
    lbtn.start();

    var errorDiv = $(btn).closest('.calllog-checkbox-checkbox').find('.calllog-danger-box');
    errorDiv.html(null);
    errorDiv.hide();

    var taskId = checkboxBtn.attr('id');
    //console.log("update task id="+taskId+"; status="+status);

    var url = Routing.generate('calllog_update_task');

    url = url + "/" + taskId + "/" + status;
    //console.log("url="+url);
    //return;

    $.ajax({
        url: url,
        timeout: _ajaxTimeout,
        async: true
    }).success(function(data) {
        //console.log("data="+data);

        var error = data['error'];
        var msg = data['msg'];

        if( !error ) {
            if ( cycle == "list" )
            {
                lbtn.stop();
                $(btn).prop('disabled', false);
                $(btn).attr("disabled", false);
                $(btn).hide(_transTime);
                //remove record from new page
                var taskTr = $(btn).closest('.calllog-task-tr');
                if( status == 'completed' ) {
                    taskTr.find('.calllog-task-td').removeClass('bg-danger').addClass('bg-success');
                    taskTr.find('.task-status-checkbox').data('taskstatus', "checked");
                }
                if( status == 'pending' ) {
                    taskTr.find('.calllog-task-td').removeClass('bg-success').addClass('bg-danger');
                    taskTr.find('.task-status-checkbox').data('taskstatus', null);
                }
                //taskTr.hide('slow');
            }
            else if( cycle == "new" || cycle == "show" )
            {
                lbtn.stop();
                $(btn).prop('disabled', false);
                $(btn).attr("disabled", false);
                //remove record from new page
                var taskTr = $(btn).closest('.calllog-task-tr');
                taskTr.hide('slow', function(){ taskTr.remove(); });
                //taskTr.hide('slow');
            }  else {
                location.reload(); //reload this page
            }
        } else {
            errorDiv.html(msg);
            errorDiv.show(_transTime);
            lbtn.stop();
            $(btn).prop('disabled', false);
            $(btn).attr("disabled", false);
        }

    }).done(function() {
        //lbtn.stop();
        //$(btn).prop('disabled', false);
        //$(btn).attr("disabled", false);
    });
}


function calllogAccessionExists() {
    var accessionnumber = $(".accession-mask").val();
    var accessiontype = $(".accessiontype-combobox").select2('val');
    if( accessiontype && accessionnumber ) {
        return true;
    }
    return false;
}

