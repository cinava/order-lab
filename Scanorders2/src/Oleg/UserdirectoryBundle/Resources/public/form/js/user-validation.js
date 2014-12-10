/**
 * Created by oli2002 on 9/25/14.
 */

function validateUser(origuserid) {

    var actionFlag = 'new';

    if( typeof origuserid != "undefined" && origuserid != "" ) {
        var actionFlag = 'update';
    }
    //console.log("actionFlag="+actionFlag+", origuserid="+origuserid);

    removeAllErrorAlerts();

    var firstName = $('#oleg_userdirectorybundle_user_firstName').val();
    firstName = trimWithCheck(firstName);
    var lastName = $('#oleg_userdirectorybundle_user_lastName').val();
    lastName = trimWithCheck(lastName);

    var userType = $('.user-keytype-field').select2('val');
    userType = trimWithCheck(userType);
    var primaryPublicUserId = $('#oleg_userdirectorybundle_user_primaryPublicUserId').val();
    primaryPublicUserId = trimWithCheck(primaryPublicUserId);

    if( userType == "" ) {
        $('#userinfo').collapse('show');
        addErrorAlert("Primary Public User ID Type is empty");
        $('.user-keytype-field').parent().addClass("has-error");
        return false;
    }

    if( primaryPublicUserId == "" ) {
        $('#userinfo').collapse('show');
        addErrorAlert("Primary Public User ID is empty");
        $('#oleg_userdirectorybundle_user_primaryPublicUserId').parent().addClass("has-error");
        return false;
    }

    if( firstName == "" ) {
        $('#userinfo').collapse('show');
        addErrorAlert("First Name is empty");
        $('#oleg_userdirectorybundle_user_firstName').parent().addClass("has-error");
        return false;
    }

    if( lastName == "" ) {
        $('#userinfo').collapse('show');
        addErrorAlert("Last Name is empty");
        $('#oleg_userdirectorybundle_user_lastName').parent().addClass("has-error");
        return false;
    }

    //field with required attributes (location Name can not be empty)
    if( validateSimpleRequiredAttrFields() == false ) {
        return false;
    }

    //check usertype + userid combination
    var user = checkUsertypeUserid(userType,primaryPublicUserId);
    var userid = user.id;
    //it is not possible to edit usertype and userid, thereofore check this combination only for a new user
    if( userid && actionFlag == 'new' ) {

        $('#userinfo').collapse('show');
        $('#oleg_userdirectorybundle_user_primaryPublicUserId').parent().addClass("has-error");

        var userTypeText = $('.user-keytype-field').select2('data').text;

        var alert = 'An employee with the provided User ID Type "'+userTypeText+'" and User ID "'+primaryPublicUserId+'" already exists: ' +
            getUserUrl(userid,user.firstName+" "+user.lastName) +
            "Please correct the new employee's User ID Type and User ID or edit the existing employee's information.";
        addErrorAlert(alert);
        return false;
    }


    //check duplicate SSN
    var ssn = $('#oleg_userdirectorybundle_user_credentials_ssn').val();
    ssn = trimWithCheck(ssn);
    var user = checkDuplicateIdentifier(ssn,'ssn');
    var userid = user.id;
    if( userid && (actionFlag == 'new' || userid != origuserid && actionFlag == 'update') ) {

        $('#Credentials').collapse('show');
        $('#personalinfo').collapse('show');
        $('#oleg_userdirectorybundle_user_credentials_ssn').parent().addClass("has-error");

        var alert = "An employee with the provided Social Security Number (SSN) "+ssn+" already exists: " +
            getUserUrl(userid,user.firstName+" "+user.lastName) +
            "Please correct the new employee's Social Security Number (SSN) or edit the existing employee's information.";

        addErrorAlert(alert);

        return false;
    }

    //check existing MRN identifier
    var identifierKeytypemrn = $('.identifier-keytypemrn-field-holder').filter(':visible');
    identifierKeytypemrn.each( function(e){
        var keytypemrn = $(this).find('.identifier-keytypemrn-field').select2('val');
        console.log('keytypemrn='+keytypemrn);
    });

    return false; //testing
    $("form:first").submit();
}

function getUserUrl(userid,username) {
    var dataholder = document.querySelector('#form-prototype-data');
    var url = dataholder.dataset.userurllink;
    url = url.replace("user_replacement_id",userid);
    url = url.replace("user_replacement_username",username);
    return url;
}

function addErrorAlert($text) {
    var alert = '<div class="alert alert-danger user-error-alert" role="alert">'+
        $text +
        '</div>';
    $('#user-errors').append(alert);
}

function removeAllErrorAlerts() {
    $('.user-error-alert').remove();
    $('.has-error').removeClass('has-error');
}

function checkDuplicateIdentifier(number,name) {
    var user = new Array();
    var url = getCommonBaseUrl("util/"+name,"employees");
    $.ajax({
        url: url,
        type: 'GET',
        data: {number: number},
        timeout: _ajaxTimeout,
        async: false
    }).success(function(data) {
        if( data.length > 0 ) {
            user = data[0];
        } else {
            user['id'] = null;
        }
    });
    return user;
}

function checkUsertypeUserid(userType,userId) {
    var user = new Array();
    var url = getCommonBaseUrl("util/"+"usertype-userid","employees");
    $.ajax({
        url: url,
        type: 'GET',
        data: {userType: userType, userId: userId},
        timeout: _ajaxTimeout,
        async: false
    }).success(function(data) {
        if( data.length > 0 ) {
            user = data[0];
        } else {
            user['id'] = null;
        }
    });
    return user;
}


function validateSimpleRequiredAttrFields() {

    var errorCount = 0;

    $('input,textarea,select').filter('[required]').each( function() {
        var value = $(this).val();
        if( value == "" ) {
            $(this).parent().addClass("has-error");

            var msg = "Required Field is empty";

            if( $(this).hasClass('user-location-name-field') ) {
                $('#Locations').collapse('show');
                msg = "Location Name is empty";
            }

            addErrorAlert(msg);

            //attach on change listener
            $(this).change(function() {
                removeAllErrorAlerts();
            });

            errorCount++;
        }
    });

    if( errorCount == 0 ) {
        return true;
    } else {
        return false;
    }
}
