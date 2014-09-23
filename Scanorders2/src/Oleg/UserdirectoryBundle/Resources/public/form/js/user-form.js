/**
 * Created by oli2002 on 8/22/14.
 */

function validateUpdateUser(origuserid) {

    removeAllErrorAlerts();

    var firstName = $('#oleg_userdirectorybundle_user_firstName').val();
    firstName = trimWithCheck(firstName);
    var lastName = $('#oleg_userdirectorybundle_user_lastName').val();
    lastName = trimWithCheck(lastName);

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

    //check duplicate Username (CWID)
    var cwid = $('#oleg_userdirectorybundle_user_username').val();
    cwid = trimWithCheck(cwid);
    if( cwid == "" ) {
        cwid = "AUTOGENERATED" + "_" + "TEMPORARY" + "_" + lastName + "_" + firstName;
    }
    var user = checkDuplicateIdentifier(cwid,'cwid');
    var userid = user.id;
    if( userid != origuserid ) {
        $('#userinfo').collapse('show');
        $('#oleg_userdirectorybundle_user_username').parent().addClass("has-error");
        var alert = "You can not change the employee's CWID because an employee with the provided CWID "+cwid+" already exists: " +
            getUserUrl(userid,user.firstName+" "+user.lastName) +
            "Please correct this employee's CWID.";
        addErrorAlert(alert);
        return false;
    }

    $("form:first").submit();
}

function validateNewUser() {

    removeAllErrorAlerts();

    var firstName = $('#oleg_userdirectorybundle_user_firstName').val();
    firstName = trimWithCheck(firstName);
    var lastName = $('#oleg_userdirectorybundle_user_lastName').val();
    lastName = trimWithCheck(lastName);

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

    //check duplicate Username (CWID)
    var autogen = "";
    var cwid = $('#oleg_userdirectorybundle_user_username').val();
    cwid = trimWithCheck(cwid);
    if( cwid == "" ) {
        autogen = "User Name (CWID) field can not be empty. ";
        cwid = "AUTOGENERATED" + "_" + "TEMPORARY" + "_" + lastName + "_" + firstName;
        $('#oleg_userdirectorybundle_user_username').val(cwid);
    }
    var user = checkDuplicateIdentifier(cwid,'cwid');
    var userid = user.id;
    if( userid ) {
        $('#userinfo').collapse('show');
        $('#oleg_userdirectorybundle_user_username').parent().addClass("has-error");
        var alert = autogen + "An employee with the provided CWID "+cwid+" already exists: " +
                    getUserUrl(userid,user.firstName+" "+user.lastName) +
                    "Please correct the new employee's CWID or edit the existing employee's information.";
        addErrorAlert(alert);
        return false;
    }


    //check duplicate EIN
    var ein = $('#oleg_userdirectorybundle_user_credentials_employeeId').val();
    ein = trimWithCheck(ein);
    var user = checkDuplicateIdentifier(ein,'ein');
    var userid = user.id;
    if( userid ) {
        $('#Credentials').collapse('show');
        $('#identifiers').collapse('show');

        $('#oleg_userdirectorybundle_user_credentials_employeeId').parent().addClass("has-error");

        var alert = "An employee with the provided Employee Identification Number (EIN) "+ein+" already exists: " +
            getUserUrl(userid,user.firstName+" "+user.lastName) +
            "Please correct the new employee's Employe Identification Number (EIN) or edit the existing employee's information.";

        addErrorAlert(alert);

        return false;
    }

    //check duplicate SSN
    var ssn = $('#oleg_userdirectorybundle_user_credentials_ssn').val();
    ssn = trimWithCheck(ssn);
    var user = checkDuplicateIdentifier(ssn,'ssn');
    var userid = user.id;
    if( userid ) {
        $('#Credentials').collapse('show');
        $('#personalinfo').collapse('show');

        $('#oleg_userdirectorybundle_user_credentials_ssn').parent().addClass("has-error");

        var alert = "An employee with the provided Social Security Number (SSN) "+ssn+" already exists: " +
            getUserUrl(userid,user.firstName+" "+user.lastName) +
            "Please correct the new employee's Social Security Number (SSN) or edit the existing employee's information.";

        addErrorAlert(alert);

        return false;
    }

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
        if( data != "" ) {
            user = data[0];
        } else {
            user['id'] = null;
        }
    });
    return user;
}

