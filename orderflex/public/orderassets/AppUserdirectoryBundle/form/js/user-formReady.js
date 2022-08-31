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
 * user-formReady.js
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 2/27/14
 * Time: 12:55 PM
 * To change this template use File | Settings | File Templates.
 */

//import { setCicleShow } from '/public/orderassets/AppUserdirectoryBundle/form/js/user-common.js';
//import { setNavBar } from '/public/orderassets/AppUserdirectoryBundle/form/js/user-navbar.js';
//import { fieldInputMask } from '/public/orderassets/AppUserdirectoryBundle/form/js/user-masking.js';

//require('/public/orderassets/AppUserdirectoryBundle/form/js/user-common');

//Window.prototype.fieldInputMask = fieldInputMask;
//Window.prototype.setCicleShow = setCicleShow;
//Window.prototype.getSitename  = getSitename;

//import {UserCommon} from '/public/orderassets/AppUserdirectoryBundle/form/js/user-common-object.js';
//require('/public/orderassets/AppUserdirectoryBundle/form/js/user-common-object.js');

$(document).ready(function() {

    //console.log('user form ready');
    
    //checkBrowserComptability();

    //let userCommon = new UserCommon();
    //userCommon.setCicleShow();

    // var myCar = new Car("Ford", 2014);
    // console.log(myCar.name + " " + myCar.year);
    // console.log("cycle====="+myCar.setCicleShow());

    setCicleShow();

    //$(this).scrollTop(0);

    setNavBar();

    fieldInputMask();

    //tooltip
    //$(".element-with-tooltip").tooltip();
    initTooltips();

    initConvertEnterToTab();

    initDatepicker();

    expandTextarea();

    $('.panel-collapse').collapse({'toggle': false});

    regularCombobox();

    initTreeSelect();

    //composite tree as combobox select2 view
    getComboboxCompositetree();

    //jstree in admin page for Institution tree
    //move to hierarchy-index.html.twig
    // getJstree('UserdirectoryBundle','Institution');
    // getJstree('UserdirectoryBundle','CommentTypeList');
    // getJstree('UserdirectoryBundle','FormNode');
    // getJstree('OrderformBundle','MessageCategory');
    // getJstree('DashboardBundle','ChartTypeList');
    // getJstree('DashboardBundle','TopicList');

    //home page institution with user leafs
    //displayInstitutionUserTree();
    //getJstree('UserdirectoryBundle','Institution_User','nomenu','nosearch','closeall');

    getComboboxResidencyspecialty();

    //getComboboxCommentType();

    //init generic comboboxes
    initAllComboboxGeneric();

    processEmploymentStatusRemoveButtons();

    positionTypeListener();

    initUpdateExpectedPgy();

    initFileUpload();

    windowCloseAlert();

    confirmDeleteWithExpired();

    initDatetimepicker();

    userCloneListener();
    
    userTypeListener();

    userPreferencesHideListener();

    //initPasswordBox();

    identifierTypeListener();

    researchLabListener();

    grantListener();

    initTypeaheadUserSiteSearch();

    degreeListener();

    generalConfirmAction();

    userPnotifyDisplay();
});


