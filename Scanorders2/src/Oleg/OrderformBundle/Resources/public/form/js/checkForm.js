/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var urlBase = $("#baseurl").val();
var urlCheck = "http://"+urlBase+"/check/";

var keys = new Array("mrn", "accession", "partname", "blockname");

//var arrayFieldShow = new Array("clinicalHistory","age","diffDisident"); //,"disident"); //display as array fields "sex"
var arrayFieldShow = new Array("diffDisident","specialStains");

//var selectStr = 'input[type=file],input.form-control,div.patientsex-field,div.diseaseType,div.select2-container,[class^="ajax-combobox-"],[class^="combobox"],textarea,select';  //div.select2-container, select.combobox, div.horizontal_type
//var selectStr = 'input[type=file],input.form-control,div.proceduresex-field,div.patientsex-field,div.diseaseType,div.select2-container,input.ajax-combobox,[class^="combobox"],textarea,select,input.ajax-combobox-staintype';
var selectStr = 'input[type=file],input.form-control,div.proceduresex-field,div.patientsex-field,div.diseaseType,div.select2-container,input.ajax-combobox,[class^="combobox"],textarea,select,input.ajax-combobox-staintype';

var orderformtype = $("#orderformtype").val();

var dataquality_message1 = new Array();
var dataquality_message2 = new Array();

var _external_user = null;

//var _autogenAcc = 8;
//var _autogenMrn = 13;

//add disident to a single form array field
$(document).ready(function() {

    if( orderformtype == "single") {
        arrayFieldShow.push("disident")
    }

    $("#save_order_onidletimeout_btn").click(function() {
        $(this).attr("clicked", "true");
    });

    //validation on form submit
    $("#scanorderform").on("submit", function () {
        return validateForm();
    });

    addKeyListener();

});

//  0         1              2           3   4  5  6   7
//oleg_orderformbundle_orderinfotype_patient_0_mrn_0_field
var fieldIndex = 3;     //get 'key'
var holderIndex = 5;    //get 'patient'
//console.log("urlCheck="+urlCheck);

//needed by a single slide form
var asseccionKeyGlobal = "";
var asseccionKeytypeGlobal = "";
var partKeyGlobal = "";
var blockKeyGlobal = "";
var mrnKeyGlobal = "";
var mrnKeytypeGlobal = "";

//remove errors from inputs
function addKeyListener() {
    //remove has-error class from mrn and accession inputs
    $('.accessionaccession').find('.keyfield').parent().keypress(function() {
        $(this).removeClass('has-error');
    });
    $('.patientmrn').find('.keyfield').parent().keypress(function() {
        //console.log("remove has-error on keypress");
        $(this).removeClass('has-error');
    });

    $('.ajax-combobox-partname').on("change", function(e) {
        //console.log("remove maskerror-added on change");
        $(this).siblings('.maskerror-added').remove();
    });
    $('.ajax-combobox-blockname').on("change", function(e) {
        //console.log("remove maskerror-added on change");
        $(this).siblings('.maskerror-added').remove();
    });
}

//object contains: input value, type, parent (btn element), name, fieldname
//parent = null - get parent for part and block only
//parent = 'full' - get parent if it exists even for accession
//parent = 'none' - don't get parent
function btnObject( btn, parent ) {

    this.btn = btn;
    this.element = null;    //input element
    this.key = "";
    this.type = null;
    this.typename = null;
    this.parentbtn = null;
    this.name = null;
    this.fieldname = null;
    this.remove = false;

    var gocontinue = true;

    if( !btn || typeof btn === 'undefined' || btn.length == 0 ) {
        //console.log('button is null => exit button object');
        gocontinue = false;
    }

    if( gocontinue ) {
        var parentEl = getParentElByBtn(btn);

        var inputEl = parentEl.find('input.keyfield');

        if( !inputEl || (inputEl.attr('class') == '') ) {
            gocontinue = false;
        }
    }

    if( gocontinue ) {

        this.element = inputEl;
        if( inputEl.hasClass("ajax-combobox") ) {
            if( inputEl.select2("val") ) {
                //console.log('select2 data OK');
                this.key = trimWithCheck( inputEl.select2('data').text );
            }
        } else {
            this.key = trimWithCheck( inputEl.val() );
        }

        //get type
        var typeObj = new typeByKeyInput(inputEl);
        this.type = typeObj.type;
        this.typename = typeObj.typename;
        this.typeelement = typeObj.typeelement;

        //get name
        var idsArr = inputEl.attr('id').split("_");
        this.name = idsArr[idsArr.length-holderIndex];       //i.e. "patient"
        this.fieldname = idsArr[idsArr.length-fieldIndex];   //i.e. "mrn"

        //get parent
        if( this.name == 'part' || this.name == 'block' || parent == 'full' ) {
            //console.log("get parent");
            this.parentbtn = getParentBtn( btn, this.name );
        }

        //if remove
        if( btn.hasClass('removebtn') ) {
            this.remove = true;
        }

    }

    //console.log(this);
    //console.log('finished btn object: this.name='+this.name+', this.key='+this.key+', this.type='+this.type);
}


//keyEl is a input key field
//make sure to return the select2 fiels with s2id_... which is the first combobox. The second combobox is hidden input field.
function typeByKeyInput(keyEl) {

    this.type = null;
    this.typename = null;
    this.typeelement = null;

    if( orderformtype == "single" ) {
        if( keyEl.hasClass('accession-mask') ) {
            this.type = $('.accessiontype-combobox').first().select2('val');
            this.typename = $('.accessiontype-combobox').first().select2('data').text;
            this.typeelement = $('.accessiontype-combobox').first();
        }
        if( keyEl.hasClass('patientmrn-mask') ) {
            this.type = $('.mrntype-combobox').first().select2('val');
            this.typename = $('.mrntype-combobox').first().select2('data').text;
            this.typeelement = $('.mrntype-combobox').first();
        }
    } else {
        //var typeEl = keyEl.prev();
        var typeEl = keyEl.parent().find('.combobox').first();
        if( typeEl.hasClass('combobox') ) {    //type exists
            this.type = typeEl.select2('val');
            this.typename = typeEl.select2('data').text;
            this.typeelement = typeEl;
        }
    }

    //console.log(this);
}


//elem is a keytype (combobox)
function getParentElByBtn(btn) {

    if( !btn || typeof btn === 'undefined' || btn.length == 0 ) {
        //console.log('WARNING: button is not defined');
        return null;
    }

    //printF(btn,"get Parent By Btn: ");

    var parent = btn.closest('.row');

    if( orderformtype == "single") {
        if( btn.hasClass('patientmrnbtn') ) {
            var parent = $('#patient_0');
        }
        if( btn.hasClass('accessionbtn') ) {
            var parent = $('#accession-single');
        }
        if( btn.hasClass('partbtn') ) {
            var parent = $('#part-single');
        }
        if( btn.hasClass('blockbtn') ) {
            var parent = $('#block-single');
        }
    }

    return parent;
}

//get parent check button by using current button
function getParentBtn( btn, name ) {
    
    var parentBtn = null;
    
    if( orderformtype == "single" ) {
        if( name == 'accession' ) {
            parentBtn = $('.patientmrnbtn');
        }
        if( name == 'part' ) {
            parentBtn = $('.accessionbtn');
        } 
        if( name == 'block' ) {
            parentBtn = $('.partbtn');
        }    
    } else {
        var parentEl1 = btn.closest('.panel');      
        //console.log(parentEl1);
        var parentEl2 =  parentEl1.siblings().first();    //.find('#check_btn');
        //console.log(parentEl2);
        parentBtn = parentEl2.find('#check_btn');     
    }

    //console.log("parentBtn.length="+parentBtn.length);

    if( parentBtn && parentBtn.length == 0 ) {
        parentBtn = null;
    }
    
    return parentBtn;
}



/////////////// called by button click //////////////////////

//use this one: this function is automatically detect the parent and run chaining according if this button has parent.
//if parent exists, than parent button clicked first, then this button is processed.
//parent = 'none' : don't use parent (no chaining)
function checkForm( btnel, parent ) {

    return new Q.promise(function(resolve, reject) {

        var btn = $(btnel);
        var hasParent = false;
        var parentBtnObj = null;

        var btnObj = new btnObject(btn);
        //console.log('check form: name='+btnObj.name+', input='+btnObj.key+', type='+btnObj.type);

        //if delete button?
        if( btnObj && btnObj.remove ) {
            //console.log('execute click this');
            executeClick( btnObj );
            resolve("Delete => no children");
            return;
        }

        parentBtnObj = new btnObject(btnObj.parentbtn);
        if( parentBtnObj && parentBtnObj.btn ) {
            hasParent = true;
        }

        if( parent == 'none' ) {
            hasParent = false;
        }

        //never click parent if current button is remove button
        if( btnObj.remove ) {
            hasParent = false;
        }

        //never click parent if parent value is not empty
        if( parentBtnObj && parentBtnObj.key && parentBtnObj.key != '' ) {
            hasParent = false;
        }

        if( hasParent ) {
            //console.log('execute click parent then this');
            //alert('execute click parent then this, name='+btnObj.name);

            checkForm( parentBtnObj.btn ).
            then(
                function(response) {
                    //console.log("Success!", response);
                    return executeClick( btnObj );
                }
            ).
            then(
                function(response) {
                    //console.log("Chaining with parent OK:", response);
                    resolve("Chaining with parent OK: "+response);
                },
                function(error) {
                    console.error("Failed!", error);
                    reject(Error("Failed to execute click with parent, error="+error));
                }
            );


        } else {
            //console.log('execute click this');
            //alert('execute click this, name='+btnObj.name);
            executeClick( btnObj ).
            then(function(response) {
                    //console.log("Check click this OK:", response);
                    resolve("Check click this OK: "+response);
            },function(error) {
                    //console.error("Failed!", error);
                    reject(Error("Failed to execute click with no parent, error="+error));
                }
            );
        }

    });
}
/////////////// end of button click //////////////////////

function executeClick( btnObjInit ) {

    return Q.promise(function(resolve, reject) {

        var gocontinue = true;

        if( !btnObjInit || typeof btnObjInit === 'undefined' || btnObjInit.length == 0 ) {
            gocontinue = false;
            reject(Error("parent key is empty"));
        }

        if( gocontinue ) {

            var btnObj = new btnObject(btnObjInit.btn);
            var casetype = 'check';
            var btn = btnObj.btn;
            var urlcasename = null;
            var ajaxType = 'GET';
            var key = btnObj.key;
            var type = btnObj.type;
            var parentKey = null;
            var parentType = null;
            var grandparentKey = null;
            var grandparentType = null;
            var single = false; //temp

            //console.log('executeClick: name='+btnObj.name+', key='+key+', parentKey='+parentKey+', parentType='+parentType);

            if( btnObj && btnObj.key == '' && !btnObj.remove ) {
                //console.log('Case 1: key not exists => generate');
                casetype = 'generate';
            } else if( btnObj && btnObj.key != '' && !btnObj.remove ) {
                //console.log('Case 2: key exists => check');
                casetype = 'check';
            } else if( btnObj && btnObj.remove ) {
                //console.log('Case 3: key exists and button delete => delete');
                casetype = 'delete';
            } else {
                //console.log('Logical error: invalid key');
            }

            //console.log('executeClick: casetype='+casetype);

            urlcasename = btnObj.name+'/'+casetype;

            if( casetype == 'delete' ) {

                if( !key || key == "" ) {
                    reject(Error("Delete with no key"));
                    return;
                }

                ajaxType = 'DELETE';

                var extraStr = "";
                if( type ) {
                    extraStr = "?extra="+type;
                }
                key = trimWithCheck(key);
                urlcasename = urlcasename + '/' + key + extraStr;
            }

            //get parent
            var parentBtnObj = new btnObject(btnObj.parentbtn);
            if( parentBtnObj ) {
                parentKey = parentBtnObj.key;
                parentType = parentBtnObj.type;
            }

            //get grand parent
            var grandparentBtnObj = new btnObject(parentBtnObj.parentbtn);
            if( grandparentBtnObj ) {
                grandparentKey = grandparentBtnObj.key;
                grandparentType = grandparentBtnObj.type;
            }

            //trim values
            key = trimWithCheck(key);
            type = trimWithCheck(type);
            parentKey = trimWithCheck(parentKey);
            parentType = trimWithCheck(parentType);
            grandparentKey = trimWithCheck(grandparentKey);
            grandparentType = trimWithCheck(grandparentType);

            //temp
            if( orderformtype == "single" ) {
                single = true;
            }

            btn.button('loading');
            //var lbtn = Ladda.create(btn[0]);
            //lbtn.start();


            $.ajax({
                url: urlCheck+urlcasename,
                type: ajaxType,
                contentType: 'application/json',
                dataType: 'json',
                timeout: _ajaxTimeout,
                async: true,    //use synchronous call
                data: {key: key, extra: type, parentkey: parentKey, parentextra: parentType, grandparentkey: grandparentKey, grandparentextra: grandparentType },
                success: function (data) {

                    btn.button('reset');
                    //lbtn.stop();

                    //console.debug("ajax casetype="+casetype);

                    //////////////// generate ////////////////
                    if( casetype == 'generate' ) {
                        if( data ) {
                            //console.debug("ajax generated data is found");
                            invertButton(btn);
                            setElementBlock(btn, data, null, "key");
                            disableInElementBlock(btn, false, null, "notkey", null);
                            setObjectInfo(btnObj,0);
                            resolve("Object was generated successfully");
                        } else {
                            //console.debug("Object was not generated");
                            reject(Error("Object was not generated"));
                        }
                    }
                    //////////////// end of generate ////////////////

                    //////////////// delete ////////////////
                    if( casetype == 'delete' ) {
                        if( data >= 0 || data == -1 ) {
                            console.debug("Delete Success, data="+data);
                            deleteSuccess(btnObj,single);
                            resolve("Object was deleted, data="+data);
                        } else {
                            console.debug("Delete with data Error: data="+data);
                            deleteError(btnObj,single);
                            //invertButton(btn);
                            reject(Error("Delete ok with Error"));
                        }
                        removeInfoFromElement(btnObj);
                    }
                    //////////////// end of delete ////////////////

                    //////////////// check ////////////////
                    if( casetype == 'check' ) {
                        if( data == -2 ) {

                            //Existing Auto-generated object does not exist in DB
                            createErrorWell(btnObj.element,btnObj.name);
                            reject(Error("Existing Auto-generated object does not exist in DB"));

                        } else
                        if( data.id && data.id != '' ) {    //test this condition for external user

                            var gonext = 1;

                            if( !single ) {
                                gonext = checkParent(btn,key,btnObj.name,btnObj.fieldname,btnObj.type); //check if this key is not used yet, when a new key field is checked in the added entity
                                //console.debug("0 gonext="+gonext);
                            }

                            //console.log("gonext="+gonext);
                            if( gonext == 1 ) {

                                //set this element
                                //console.debug("continue gonext="+gonext);
                                //first: set elements
                                setElementBlock(btn, data);
                                //second: disable or enable element. Make sure this function runs after set Element Block
                                disableInElementBlock(btn, true, "all", null, "notarrayfield");
                                invertButton(btn);
                                setObjectInfo(btnObj,1);

                                //set patient (in accession case)
                                if( btnObj.name == "accession" && gonext == 1) {
                                    var parentkeyvalue = data['parent'];
                                    var extraid = data['extraid'];
                                    //console.log("key parent="+parentkeyvalue+", extraid="+extraid);
                                    gonext = setPatient(btn,parentkeyvalue,extraid,single);
                                }

                            }

                            resolve("ajax key value data is found");

                        } else {
                            //console.debug("not found");
                            disableInElementBlock(btn, false, null, "notkey", null);
                            invertButton(btn);
                            calculateAgeByDob(btn);
                            setObjectInfo(btnObj,0);
                            resolve("data is null");
                        }

                    } //check
                    //////////////// end of check ////////////////

                },
                error: function ( x, t, m ) {
                    btn.button('reset');

                    if( t === "timeout" ) {
                        getAjaxTimeoutMsg();
                    }

                    if( casetype == 'check' ) {
                        cleanFieldsInElementBlock( btn, null, single );
                        disableInElementBlock(btn, false, "all", null, null);
                        invertButton(btn);
                    }

                    if( casetype == 'delete' ) {
                        deleteError(btnObj,single);
                    }

                    //console.debug(btnObj.name+": ajax error for casetype="+casetype);
                    reject(Error(btnObj.name+": ajax error for casetype="+casetype));
                }
            }); //ajax

        } //if gocontinue
        
    }); //promise
}

