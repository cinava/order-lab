/**
 * Created by oli2002 on 8/22/14.
 */

//do not show the [X] (delete) button in the right upper corner of "Employment Period(s)"
// if it is the only one being displayed.
// When the user adds another one, then show an [X] next to each one.
function processEmploymentStatusRemoveButtons(btn) {

    if( cicle == "show_user" ) {
        return;
    }

    if( !btn && typeof btn != "undefined" ) {
        var btnEl = $(this);
        if( !btnEl.hasClass('btn-remove-minimumone-collection') && !btnEl.hasClass('btn-add-minimumone-collection') ) {
            return;
        }
    }

    var remBtns = $('.btn-remove-minimumone-collection');
    //console.log('remBtns.length='+remBtns.length);

    if( remBtns.length > 1 ) {
        //more than one element: show all remove buttons
        remBtns.show();

    } else {
        //0 or 1 element: hide remove buttons
        remBtns.hide();
    }

}

//on user load take care of hidden wells
function positionTypeListener() {
    $('.appointmenttitle-position-field').not("*[id^='s2id_']").each(function(e) {
        positionTypeAction(this);
    });

    //pgy update listener
    $('.pgylevel-field,.pgystart-field').on('change',function(e) {
        updateExpectedPgyListener( $(this) );
    });

    //pgy expected field init
    $('.pgylevelexpected-field').each(function(e) {
        //console.log('update expectedPgyLevel');
        updateExpectedPgyListener( $(this) );
    });

}

function updateExpectedPgyListener( element ) {
    var holder = element.closest('.user-collection-holder');
    var expectedPgyLevel = calculateExpectedPgy( element );
    //console.log('expectedPgyLevel='+expectedPgyLevel);
    holder.find('.pgylevelexpected-field').val(expectedPgyLevel);
}

//In the section "Academic Appointment Title(s)", if "Resident" is selected in the "Position Type" dropdown menu,
// unfold a second drop down under it with a field called "Residency Track:" and show three choices: "AP", "CP", and "AP/CP".
function positionTypeAction(element) {
    var fieldEl = $(element);
    //console.log(fieldEl);

    var holder = fieldEl.closest('.user-collection-holder');
    //console.log(holder);

    if( !holder.hasClass('user-appointmentTitles') ) {
        return;
    }

    //printF(fieldEl,'field el:');

    var value = fieldEl.select2('val');
    //console.log('value='+value);

    holder.find('.appointmenttitle-residencytrack-field').hide();
    holder.find('.appointmenttitle-fellowshiptype-field').hide();
    holder.find('.appointmenttitle-pgy-field').hide();

    if( value == 'Resident' ) {
        holder.find('.appointmenttitle-residencytrack-field').show();
        holder.find('.appointmenttitle-pgy-field').show();
    }

    if( value == 'Fellow' ) {
        holder.find('.appointmenttitle-fellowshiptype-field').show();
        holder.find('.appointmenttitle-pgy-field').show();
    }
}


function initUpdateExpectedPgy() {
    $('.update-pgy-btn').each( function() {

        var expectedPgyLevel = calculateExpectedPgy( $(this) );

        if( expectedPgyLevel != null ) {

            var holder = $(this).closest('.user-collection-holder');
            //console.log(holder);

            if( !holder.hasClass('user-appointmentTitles') ) {
                return;
            }

            //console.log( 'pgylevel='+pgylevel+', curYear='+curYear);
            holder.find('.pgylevelexpected-field').val(expectedPgyLevel);
        }
    });
}

function updatePgy(btn) {

    var btnEl = $(btn);

    var holder = btnEl.closest('.user-collection-holder');
    //console.log(holder);

    if( !holder.hasClass('user-appointmentTitles') ) {
        return;
    }

    var pgystart = holder.find('.pgystart-field').val();
    var pgylevel = holder.find('.pgylevel-field').val();
    var pgylevelexpected = holder.find('.pgylevelexpected-field').val();

    //console.log( 'pgystart='+pgystart+', pgylevel='+pgylevel+', pgylevelexpected='+pgylevelexpected);

    //A- If both field have no value - the button does nothing
    if( pgystart == "" && pgylevel == "" ) {
        return;
    }

    //C- If only the PGY level has value, the button does nothing
    if( pgystart == "" && pgylevel != "" ) {
        return;
    }

    var today = new Date();
    var curYear = today.getFullYear();

    //B- If only the date has value - the button updates the year of the date to current (does not change month of date)
    if( pgystart != "" && pgylevel == "" ) {
        var pgyDate = new Date(pgystart);
        pgyDate.setFullYear(curYear);
        //console.log( 'pgyDate='+pgyDate);

        holder.find('.pgystart-field').datepicker( 'setDate', pgyDate );
        holder.find('.pgystart-field').datepicker( 'update');
    }


    //During academic year that started on: [July 1st 2011]
    //The Post Graduate Year (PGY) level was: [1]
    //Expected Current Post Graduate Year (PGY) level: [4] (not a true fleld in the database, not editble)
    //
    //D- If both the date and the PGY have value and the academic year is not current
    // (meaning the current date is later than listed date +1 year (in the example above, if current date is later than July 1st 2012) ,
    // the function takes the current year (for example 2014), subtracts the year in the date field (let's say 2011), and add the result to the current PGY level value
    // (let's say 1, replacing it with 4), then updates the year of the field with current (2011->2014).
    if( pgystart != "" && pgylevel != "" ) {

        var pgyDate = new Date(pgystart);

        var expectedPgyLevel = calculateExpectedPgy(btnEl);

        if( expectedPgyLevel != null ) {

            //console.log( 'pgylevel='+pgylevel+', curYear='+curYear);
            holder.find('.pgylevel-field').val(expectedPgyLevel);
            holder.find('.pgylevelexpected-field').val(expectedPgyLevel);

            //updates the year of the field with current (2011->2014)
            pgyDate.setFullYear(curYear);
            holder.find('.pgystart-field').datepicker( 'setDate', pgyDate );
            holder.find('.pgystart-field').datepicker( 'update');
        }

    }

}

//element is any element of the pgy well holder
function calculateExpectedPgy(element) {

    var newPgyLevel = null;

    var holder = element.closest('.user-collection-holder');
    //console.log(holder);

    if( holder.length == 0 || !holder.hasClass('user-appointmentTitles') ) {
        //console.log('holder is null => return newPgyLevel null');
        return newPgyLevel;
    }

    var pgystart = holder.find('.pgystart-field').val();
    var pgylevel = holder.find('.pgylevel-field').val();

    if( pgylevel != "" ) {
        newPgyLevel = pgylevel;
    }

    //During academic year that started on: [July 1st 2011]
    //The Post Graduate Year (PGY) level was: [1]
    //Expected Current Post Graduate Year (PGY) level: [4] (not a true fleld in the database, not editble)
    //
    //D- If both the date and the PGY have value and the academic year is not current
    // (meaning the current date is later than listed date +1 year (in the example above, if current date is later than July 1st 2012) ,
    // the function takes the current year (for example 2014), subtracts the year in the date field (let's say 2011), and add the result to the current PGY level value
    // (let's say 1, replacing it with 4), then updates the year of the field with current (2011->2014).
    if( pgystart != "" && pgylevel != "" ) {

        var today = new Date();
        var curYear = today.getFullYear();

        var pgyDate = new Date(pgystart);
        var pgyYear = pgyDate.getFullYear();

        var diffYear = getYearByDiff(null,pgystart);

        //console.log( 'diffYear='+diffYear);

        if( diffYear >= 1 ) {

            //add the result to the current PGY level value
            newPgyLevel = parseInt(pgylevel) + ( parseInt(curYear)-parseInt(pgyYear) );
        }

    }

    //console.log( 'res: newPgyLevel='+newPgyLevel);

    return newPgyLevel;
}


//for positive year date1 > date2
function getYearByDiff(date1,date2) {

    if( date1 == null ) {
        var date1Date = new Date(); //today date
    } else {
        var date1Date = new Date(date1);
    }

    if( date2 == null ) {
        var date2Date = new Date(); //today date
    } else {
        var date2Date = new Date(date2);
    }

    var years = date1Date.getFullYear() - date2Date.getFullYear();
    var m = date1Date.getMonth() - date2Date.getMonth();
    if (m < 0 || (m === 0 && date1Date.getDate() < date2Date.getDate())) {
        years--;
    }
    return years;
}


function initFileUpload() {

    if( $('.dropzone').length == 0 ) {
        return;
    }

    var dataElement = document.getElementById("form-prototype-data");

//    if( dataElement.length == 0 || typeof dataElement.dataset === 'undefined' ) {
//        return;
//    }

    //var url = dataElement.dataset.uploadurl;
    var url = dataElement.getAttribute('data-uploadurl');
    console.log('url='+url);

    //var userid = dataElement.dataset.userid;
    var userid = dataElement.getAttribute('data-userid');
    console.log('userid='+userid);

    Dropzone.autoDiscover = false;

    var clickable = true;
    var addRemoveLinks = true;
    if( cicle == "show_user" ) {
        clickable = false;
        addRemoveLinks = false;
    }
    console.log('clickable='+clickable);

    var previewHtml =
        '<div class="dz-preview dz-file-preview" style="width:30%; margin:0;">'+
            '<div class="dz-details">'+
                '<div class="dz-filename"><span data-dz-name></span></div>'+
                '<div class="dz-size" data-dz-size></div>'+
                '<img data-dz-thumbnail />'+
            '</div>'+
            '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>'+
            '<div class="dz-success-mark"><span>✔</span></div>'+
            '<div class="dz-error-mark"><span>✘</span></div>'+
            '<div class="dz-error-message"><span data-dz-errormessage></span></div>'+
//            '<button type="button" class="btn btn-danger" data-dz-remove>Delete</button>'+
        '</div>';

    $(".file-upload-dropzone").dropzone({
        url: url,
        clickable: clickable,
        addRemoveLinks: addRemoveLinks,
        maxFiles: 10,
        previewTemplate: previewHtml,
        dictDefaultMessage: 'Drag and drop files here to upload or click to select a file',
        sending: function(file, xhr, formData){
            formData.append('userid', userid);
            var filename = file.name;
            //console.log('filename='+filename);
            formData.append('filename', filename);
        },
        success: function(file, responseText){
            //console.log('responseText='+responseText);
            //console.log(responseText);
            //console.log(file);

            //pupulate document id input field
            var documentid = responseText.documentid;
            console.log('documentid='+documentid);
            var holder = $(this.element).closest('.files-upload-holder');
            var fileIdField = holder.find('.file-upload-id');
            fileIdField.val(documentid);
            //file.previewTemplate.appendChild(document.createTextNode(responseText));
        },
        maxfilesexceeded: function(file) {
            this.removeFile(file);
        },
        removedfile: function(file) {
            console.log('remove file name='+file.name);
            //this.removeFile(file);
            var holder = $(this.element).closest('.files-upload-holder');
            var documentid = holder.find('.file-upload-id').val();
            var url = getCommonBaseUrl("file-delete","employees");
            //use comment id and documentid
            $.post(url, { documentid: documentid, commentid: null, commenttype: null } );
        }
//        init: function() {
//            return;
//            thisDropzone = this;
//
//            //console.log(thisDropzone);
//            var holder = $(thisDropzone.element).closest('.files-upload-holder');
//
//            var existedfiles = holder.find('.file-holder');
//            console.log('existedfiles len='+existedfiles.length);
//
//            var data = new Array();
//
//            existedfiles.each( function() {
//                console.log('filename='+$(this).find('.file-upload-uniquename').val())
//                var fileArr = new Array();
//                fileArr['name'] = $(this).find('.file-upload-uniquename').val();
//                fileArr['size'] = $(this).find('.file-upload-size').val();
//                fileArr['dir'] = $(this).find('.file-upload-uploaddirectory').val();
//                data.push(fileArr);
//            });
//
//            console.log('data len='+data.length);
//
//            for( var i = 0; i < data.length; i++ ) {
//
//                var value = data[i];
//
//                console.log('name='+value.name);
//
//                var mockFile = { name: value.name, size: value.size };
//
//                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
//
//                var filepath = "http://collage.med.cornell.edu/order/Uploaded/pathology-employees/Documents/"+value.name;
//                console.log('path='+filepath);
//
//                thisDropzone.options.thumbnail.call(thisDropzone, mockFile, filepath);
//            }
//            //See more at: http://www.startutorial.com/articles/view/dropzonejs-php-how-to-display-existing-files-on-server#sthash.sqF6KDsk.dpuf
//        }
//        confirm: function(question, accepted, rejected) {
//            console.log();
//            // Do your thing, ask the user for confirmation or rejection, and call
//            // accepted() if the user accepts, or rejected() otherwise. Make
//            // sure that rejected is actually defined!
//        }

    });


//    $('#jquery-fileupload').fileupload({});

}

