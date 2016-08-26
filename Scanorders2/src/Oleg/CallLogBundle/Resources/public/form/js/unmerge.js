/**
 * Created by ch3 on 8/26/2016.
 */

//overwrite function
var matchingPatientUnmergeBtnClick = function(holderId,formtype) {
    console.log('un-merged holderId='+holderId);

    var holder = getHolder(holderId);

    //clear no matching box
    holder.find('#calllog-select-patient-danger-box').hide();
    holder.find('#calllog-select-patient-danger-box').html("");

    var index = holder.find('#calllog-matching-patients-table-'+holderId).find('.active').attr('id');
    //remove holderId from index
    index = index.replace("-"+holderId, "");
    console.log('index='+index);

    //find patient with id from _patients array
    var selectedPatient = getPatientByIdFromPatients(index,_patients);
    console.log('selectedPatient:');
    console.log(selectedPatient);

    //for call_entry return master record instead of the actual clicked patient record
    //var masterPatient = null;
    var mergedPatientsInfo = null;
    var mergedPatientsInfoLength = null;
    var masterPatientId = selectedPatient['masterPatientId'];
    console.log("masterPatientId=" + masterPatientId);
    if( masterPatientId ) {
        //console.log("masterPatientId=" + masterPatientId);
        selectedPatient = getPatientByIdFromPatients(masterPatientId,_patients);
        mergedPatientsInfo = selectedPatient['mergedPatientsInfo'][masterPatientId]['patientInfo'];
        mergedPatientsInfoLength = getPatientsLength(mergedPatientsInfo);
    }

    //This patient record has not been merged with any other patient records.
    //var mergedPatientsInfo = selectedPatient['mergedPatientsInfo'][masterPatientId];
    //var mergedPatientsInfoLength = getMergedPatientsInfoLength(mergedPatientsInfo);
    //console.log('mergedPatientsInfo='+mergedPatientsInfo);
    //console.log('mergedPatientsInfoLength='+mergedPatientsInfoLength);
    if( mergedPatientsInfo && mergedPatientsInfoLength > 0 ) {
        //ok
        console.log('mergedPatientsInfo ok');
    } else {
        //console.log('mergedPatientsInfo not ok');
        //alert("This patient record has not been merged with any other patient records.");
        holder.find('#calllog-select-patient-danger-box').html("This patient record has not been merged with any other patient records.");
        holder.find('#calllog-select-patient-danger-box').show();
        return;
    }

    //populatePatientInfo(_patients[index],null,true,holderId);
    //disableAllFields(true,holderId);

    populateInputFieldCalllog(holder.find(".calllog-patient-id"),selectedPatient,'id',true);
    holder.find(".calllog-patient-id").trigger('change');
    holder.find(".calllog-patient-id").change();

    //show edit patient info button
    //holder.find('#edit_patient_button').show();
    //hide "No single patient is referenced by this entry or I'll add the patient info later" link
    //holder.find('#callentry-nosinglepatient-link').hide();

    //change the "Find or Add Patient" button title to "Re-enter Patient"
    //holder.find('#reenter_patient_button').show();
    //holder.find('#search_patient_button').hide();

    //remove and hide matching patients table
    holder.find('#calllog-matching-patients-table-'+holderId).remove();
    holder.find('#calllog-matching-patients').html('');
    holder.find('#calllog-matching-patients').hide();

    //remove old and create a new list of the "Patient Info"
    $('.calllog-patient-holder').remove();
    createPatientInfos( selectedPatient, formtype );

}

function createPatientInfos( patient, formtype ) {

    console.log('createPatientInfos; id='+patient.id);

    var patientInfo = patient['mergedPatientsInfo'][patient.id]['patientInfo'];
    //var mergeInfo = patient['mergedPatientsInfo'][patient.id]['mergeInfo'];

    //var infosHtml = "patient id " + patient.id + "<br>";
    var infosHtml = patientInfoHtml(patient,true,formtype);


    for( var patientId in patientInfo ) {
        if( patientInfo.hasOwnProperty(patientId) ) {
            //infosHtml = infosHtml + '<p><div class="alert alert-info">'+mergedId+'</div></p>';

            infosHtml = infosHtml + patientInfoHtml(patientInfo[patientId],false,formtype);

        }
    }

    //infosHtml = "<p>" + infosHtml + "</p>";

    //rewrite formtype
    if( formtype == null ) {
        formtype = $('#formtype').val();
    } else {
        $('#formtype').val(formtype);
    }
    if( formtype == "unmerge" ) {
        var btnTitle = "Unmerge";
        $('.calllog-title').html('Un-merge Patient Records');
    } else {
        var btnTitle = "Set Master Record";
        $('.calllog-title').html('Set Master Patient Record');
    }

    infosHtml +=
        '<button id="unmerge_patient_button" type="button" class="btn btn-lg btn-success" align="center"'+
        'onclick="unmergePatientBtn()"'+
        '>' + btnTitle + '</button>';

    $("#calllog-patient-list").html("");
    $("#calllog-patient-list").append(infosHtml);
}

//function patientInfoHtml( patient, mergeInfo, mergedId ) {
function patientInfoHtml( patient, masterPatient, formtype ) {

    //var formtype = $('#formtype').val();
    //rewrite formtype
    if( formtype == null ) {
        formtype = $('#formtype').val();
    } else {
        $('#formtype').val(formtype);
    }

    //console.log('formtype='+formtype);
    var unMergeBox = "";
    var mergeInfoStr = "";

    if( formtype == 'unmerge' ) {
        //var mergeInfoStrArr = [];
        //var mergeInfoArr = patient.mergeInfo;
        //console.log("mergeInfoArr length="+mergeInfoArr.length);

        unMergeBox += '<input type="checkbox" name="un-merge-patients" value="' + patient.id + '" style="margin-right: 5px;">';
        unMergeBox += '<label style="margin-right: 15px;">Un-Merge</label>';

        mergeInfoStr = "<p>"+patient.mergeInfo+"</p>";

//                for( var index in mergeInfoArr ) {
//                    if( mergeInfoArr.hasOwnProperty(index) ) {
//                        var mergeInfo = mergeInfoArr[index];
//                        console.log("MID=" + mergeInfo.mergeId + "; details=" + mergeInfo.mergeDetails);
//                        //console.log(mergeInfo);
//                        //var boxId = patient.id+"-mergeid-"+mergeInfo.mergeId;
//                        //var details = '<input type="checkbox" name="un-merge-patients" value="' + boxId + '" style="margin-right: 5px;">';
//                        mergeInfoStrArr.push(mergeInfo.mergeDetails);
//                    }
//                }
        //});
        //unMergeBox = '<label style="margin-right: 5px;">Un-Merge</label>' +
        //'<input type="checkbox" name="un-merge-patients" value="' + patient.id + '" style="margin-right: 15px;">';
        //mergeInfoStr = "<p>"+mergeInfoStrArr.join("<br>")+"</p>";
    }//if

    var mergedId = null;

    var selectedMaser = "";
    if( masterPatient ) {
        selectedMaser = 'checked';
    }

    var radioBox = '<input type="radio" name="calllog-patient-master-record" '+
        'value="'+patient.id+'" class="calllog-patient-id-radio" '+selectedMaser+'>';

    var patientInfoHtml =
        '<div class="calllog-patient-holder">'+
        '<input type="hidden" id="calllog-patient-id-'+patient.id+'" class="calllog-patient-id" value="'+patient.id+'" />'+
        '<input type="hidden" id="calllog-patient-mergeid-'+patient.id+'" class="calllog-patient-mergeid" value="'+mergedId+'" />'+
        '<div class="panel panel-primary">'+
        '<div class="panel-heading">'+
        '<h4 class="panel-title">'+
        unMergeBox+
        '<a data-toggle="collapse" href="#calllog-PatientInformation-'+patient.id+'">'+
            //'Patient Info'+' ID# '+patient.id+' Merge ID# '+mergedId+' '+mergeInfo+
            //'Patient Info'+' Merge ID# '+mergedId+' '+mergeInfo+
        'Patient '+' ID# '+ patient.id +
            //": "+patient.mergeInfo+
        '</a>'+
        mergeInfoStr+
        '</h4>'+
        '</div>'+
        '<div id="calllog-PatientInformation-'+patient.id+'" class="panel-collapse collapse in">'+
        '<div class="panel-body">'+
        simpleRadioField("Master Merge Record:",radioBox)+
        simpleField("MRN Type:",patient.mrntypestr)+
        simpleField("MRN:",patient.mrn)+
        simpleField("DOB:",patient.dob)+
        simpleField("Patient's Last Name (at the time of encounter):",patient.lastname)+
        simpleField("Patient's First Name (at the time of encounter):",patient.firstname)+
        simpleField("Patient's Middle Name (at the time of encounter):",patient.middlename)+
        simpleField("Patient's Suffix (at the time of encounter):",patient.suffix)+
        simpleField("Patient's Sex (at the time of encounter):",patient.sexstr)+
        '</div>'+
        '</div>'+
        '</div>'+
        '</div>';

    return patientInfoHtml;
}
function simpleField(label,value) {
    var field =
        '<p>'+
        '<div class="row">'+
        '<div class="col-xs-6" align="right">'+
        '<label>'+label+'</label>'+
        '</div>'+
        '<div class="col-xs-6" align="left">'+
            //'<input type="text" class="form-control" value="'+value+'">'+
        '<div class="form-control" style="min-height:34px; height:auto;" disabled>'+
        value+
        '</div>'+
        '</div>'+
        '</div>'+
        '</p>';
    return field;
}
function simpleRadioField(label,value) {
    var field =
        '<p>'+
        '<div class="row">'+
        '<div class="col-xs-6" align="right">'+
        '<label>'+label+'</label>'+
        '</div>'+
        '<div class="col-xs-6" align="left">'+
        value+
        '</div>'+
        '</div>'+
        '</p>';
    return field;
}

