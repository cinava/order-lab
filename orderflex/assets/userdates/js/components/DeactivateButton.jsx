import React from 'react';
import axios from 'axios';
import { useRef } from 'react';
//import  { useNavigate } from 'react-router-dom'
import '../../css/index.css';

const DeactivateButton = ({deactivateRowRefs, modifiedRowRefs}) => {
    const buttonRef = useRef();
    const updateUrl = Routing.generate('employees_update_users_date');
    const redircetUrl = Routing.generate('employees_user_dates_show');

    function disableAccounts() {
        //alert("To be implemented");
        //var rows = $('.'+"table-row-"+data.id);
        //var rows = $(tableBodyRef).find();

        // var deactivateDataArr = [];
        //
        // for( let i = 0; i < deactivateRowRefs.length; i++ ) {
        //     console.log("deactivateRowRefs len="+deactivateRowRefs.length);
        //     console.log("row=",deactivateRowRefs[i]);
        //
        //     var row = deactivateRowRefs[i].current;
        //     var userId = row.id;
        //     userId = userId.replace('table-row-', '');
        //
        //     var startDate = $(row).find("#"+"datepicker-start-date-"+userId).val();
        //     var endDate = $(row).find("#"+"datepicker-end-date-"+userId).val();
        //
        //     var thisData = {'userId': userId, 'startDate': startDate, 'endDate': endDate};
        //     deactivateDataArr.push(thisData);
        // };
        //
        // var modifiedDataArr = [];
        // for( let i = 0; i < modifiedRowRefs.length; i++ ) {
        //     console.log("modifiedRowRefs len="+modifiedRowRefs.length);
        //     console.log("row=",modifiedRowRefs[i]);
        //
        //     var row = modifiedRowRefs[i].current;
        //     var userId = row.id;
        //     userId = userId.replace('table-row-', '');
        //
        //     var startDate = $(row).find("#"+"datepicker-start-date-"+userId).val();
        //     var endDate = $(row).find("#"+"datepicker-end-date-"+userId).val();
        //
        //     var thisData = {'userId': userId, 'startDate': startDate, 'endDate': endDate};
        //     modifiedDataArr.push(thisData);
        // };

        //console.log("modifiedRowRefs len="+modifiedRowRefs.length);
        //console.log("modifiedRowRefs"+modifiedRowRefs);

        var deactivateDataArr = processDeactivateRowRefData(deactivateRowRefs); //testing
        var modifiedDataArr = processModifiedRowRefData(modifiedRowRefs);

        if( deactivateDataArr.length > 0 || modifiedDataArr.length > 0 ) {
            //const navigate = useNavigate();
            var l = Ladda.create(buttonRef.current);
            l.start();
            //console.log("deactivateDataArr",deactivateDataArr);
            //console.log("modifiedDataArr",modifiedDataArr);
            //return;

            axios({
                method: 'post',
                url: updateUrl,
                data: {deactivateData: deactivateDataArr, modifiedData: modifiedDataArr}
            })
                .then((response) => {
                    //console.log("response.data=[" + response.data + "]");
                    l.stop();
                    if (response.data == "ok") {
                        //console.log("Active");
                        //navigate('/directory/employment-dates/view', { replace: true });
                        window.location.href = redircetUrl;
                    } else {
                        alert(response.data);
                    }
                }, (error) => {
                    //console.log(error);
                    var errorMsg = "Unexpected Error. " +
                        "Please make sure that your session is not timed out and you are still logged in. " + error;
                    //this.addErrorLine(errorMsg,'error');
                    alert(errorMsg);
                    l.stop();
                });
        } else {
            alert("No changes detected");
        }
    }

    function processDeactivateRowRefData( inputRefs ) {
        var outputDataArr = [];
        for( let i = 0; i < inputRefs.length; i++ ) {
            //console.log("inputRefs len="+inputRefs.length);

            var row = inputRefs[i].current;
            //console.log("row=",row);

            var userId = row.id;
            userId = userId.replace('table-row-', '');

            var startDate = $(row).find("#"+"datepicker-start-date-"+userId).val();
            var endDate = $(row).find("#"+"datepicker-end-date-"+userId).val();

            var thisData = {'userId': userId, 'startDate': startDate, 'endDate': endDate};
            outputDataArr.push(thisData);
        };
        return outputDataArr;
    }

    function processModifiedRowRefData( inputRefs ) {
        var outputDataArr = [];
        for( let i = 0; i < inputRefs.length; i++ ) {
            //console.log("inputRefs len="+inputRefs.length);

            var dateRef = inputRefs[i].current;
            //console.log("dateRef=",dateRef);

            var userId = dateRef.id;
            userId = userId.replace('datepicker-start-date-', '');
            userId = userId.replace('datepicker-end-date-', '');

            var startDate = $("#"+"datepicker-start-date-"+userId).val();
            var endDate = $("#"+"datepicker-end-date-"+userId).val();

            var thisData = {'userId': userId, 'startDate': startDate, 'endDate': endDate};
            outputDataArr.push(thisData);
        };
        return outputDataArr;
    }

    return (
        <p>
            <button ref={buttonRef} className="btn btn-warning" onClick={disableAccounts}
            >Deactivate selected accounts and save entered start and end dates</button>
        </p>
    );
};

export default DeactivateButton;

