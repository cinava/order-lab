/**
 * Created by oli2002 on 9/17/14.
 */

////////////////////////////// TREE //////////////////////////////////

//attach parent combobox above current
function setParentComboboxree(targetid, entityName, rowElHtml) {

    var comboboxEl = $(targetid);
    //console.log('combobox len='+comboboxEl.length);

    var treeHolder = comboboxEl.closest('.composite-tree-holder');
    //console.log(treeHolder);

    var thisData = comboboxEl.select2('data');
    //console.log(thisData);

    if( !thisData ) {
        clearElementsIdName(treeHolder);
        return;
    }

    //console.log('thisData.pid='+thisData.pid);

    //exit if no parent
    if( thisData.pid == 0 ) {
        clearElementsIdName(treeHolder);
        return;
    }

    var treeArr = getChildrenByParent(entityName,comboboxEl,thisData.pid,null);

    var newElementsAppended = createNewTreenodeCombobox( entityName, treeHolder, comboboxEl, treeArr, rowElHtml, 'top' );
    if( newElementsAppended ) {
        newElementsAppended.select2('val',thisData.pid);
        setParentComboboxree(newElementsAppended, entityName, rowElHtml);
    }

//    if( treeArr == null ) {
//        return;
//    }
//
//    if( treeArr.length > 0 ) {
//
//        var label = "Top "+treeArr[0].leveltitle;
//        console.log('label='+label);
//
//        //readonly combobox
//        var readonly = "";
//        if( cycle.indexOf("show") != -1 ) {
//            readonly = "readonly";
//        }
//
//        //var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl);    //treeHolder.find('#node-userpositions-data');
//        var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl,rowElHtml);
//
//        //treeHolder.append(comboboxHtml);
//        var newElementsAppendedRaw = $(comboboxHtml).insertBefore(targetid.closest('.treenode'));
//
//        //change label
//        newElementsAppendedRaw.find('label').text(label+":");
//
//        //change institution for user position .userposition-institution
//        newElementsAppendedRaw.find('.userposition-institution').val(thisData.id);
//
//        var newElementsAppended = newElementsAppendedRaw.find('.ajax-combobox-institution');
//        populateSelectCombobox( newElementsAppended, treeArr, "Select an option");
//        newElementsAppended.select2('val',thisData.pid);
//
//        var newUserposition = newElementsAppendedRaw.find('select.userposition-positiontypes');    //find('.userposition-positiontypes');
//        newElementsAppendedRaw.find('div.userposition-positiontypes').remove();
//        specificRegularCombobox(newUserposition);
//
//        comboboxTreeListener( newElementsAppended, entityName, rowElHtml );
//
//        setParentComboboxree(newElementsAppended, entityName, rowElHtml);
//
//    } //if

}
function setTreeByClickingParent_OLD(targetid, entityName) {
    return; //testing

    var comboboxEl = $(targetid);
    //console.log('combobox len='+comboboxEl.length);


    var treeHolder = comboboxEl.closest('.composite-tree-holder');
    //console.log(treeHolder);

    var breadcrumbs = treeHolder.find('.tree-node-breadcrumbs').val();
    console.log('breadcrumbs='+breadcrumbs);

    if( !breadcrumbs ) {
        return;
    }

    var breadcrumbsArr = breadcrumbs.split(",");

    //var thisId = treeHolder.find('.tree-node-id').val();
    //var thisPid = treeHolder.find('.tree-node-parent').val();

    if( breadcrumbsArr.length > 0 ) {
        //var setid = breadcrumbsArr[0];
        //console.log('set id='+setid);
        //comboboxEl.select2('val',setid, true);

        var nextRowSiblings = comboboxEl.closest('.row');
        for( var i = 0; i < breadcrumbsArr.length; i++ ) {
            comboboxEl = nextRowSiblings.find('.ajax-combobox-institution');
            console.log('set id='+breadcrumbsArr[i]);
            comboboxEl.select2('val',breadcrumbsArr[i]);
            comboboxEl.trigger('change');
            var nextRowSiblings = nextRowSiblings.next();
        }
    }

}

//TODO: set hidden real institution field in the form every time the combobox is chnaged.
//Then, use this real institution field and parent field in controller to create a new instituition.
function comboboxTreeListener( target, entityName, rowElHtml ) {

    $(target).on('change', function(e){

        printF( $(this), "combobox on change:" );

        var comboboxEl = $(this);
        var thisData = comboboxEl.select2('data');

        var treeHolder = comboboxEl.closest('.composite-tree-holder');

        //console.log( thisData );

        /////////////////// create and set id if node is new ///////////////////
        setTreeNode( entityName, treeHolder, comboboxEl, thisData );
        var thisData = comboboxEl.select2('data');
        /////////////////// EOF create and set id if node is new ///////////////////

        //first remove all siblings after this combobox
        var allNextSiblings = comboboxEl.closest('.row').nextAll().remove();
        clearElementsIdName(treeHolder);

        //check if combobox cleared; if none => do nothing
        //console.log( thisData );
        if( !thisData ) {
            return;
        }

        var treeArr = getChildrenByParent(entityName,comboboxEl,null,thisData.id);
        //console.log( treeArr );
        //console.log( 'treeArr.length=' + treeArr.length );

        var newElementsAppended = createNewTreenodeCombobox( entityName, treeHolder, comboboxEl, treeArr, rowElHtml, 'bottom' );
        if( newElementsAppended ) {
            //remove id and name for all inputs preceding the input with selected node
            clearElementsIdName(treeHolder);
        }

//        //do nothing if new element was enetered
//        if( treeArr == null ) {
//            console.log('do nothing if new element was enetered');
//            return;
//        }
//
//        if( treeArr.length > 0 ) {
//
//            var label = treeArr[0].leveltitle;
//            //console.log( 'label='+ label );
//
//            //readonly combobox
//            var readonly = "";
//            if( cycle.indexOf("show") != -1 ) {
//                readonly = "readonly";
//            }
//
//            //var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl);    //treeHolder.find('#node-userpositions-data');
//            var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl,rowElHtml);
//
//            //var comboboxHtml = '<input id="new-tree" class="ajax-combobox-institution" type="text"/>';
//
//            //var treeHolder = comboboxEl.closest('.composite-tree-holder');
//            //console.log( treeHolder );
//
//            //treeHolder.append(comboboxHtml);
//            var newElementsAppendedRaw = $(comboboxHtml).appendTo(treeHolder);
//
//            //change label
//            newElementsAppendedRaw.find('label').text(label+":");
//
//            //change institution for user position .userposition-institution
//            newElementsAppendedRaw.find('.userposition-institution').val(thisData.id);
//
//            //console.log( 'newid='+newid );
//            //var newElementsAppended = treeHolder.find('#institution-'+newid);
//            var newElementsAppended = newElementsAppendedRaw.find('.ajax-combobox-institution');
//            //console.log( 'newElementsAppended.id='+newElementsAppended.attr('id') );
//            populateSelectCombobox( newElementsAppended, treeArr, "Select an option");
//
//            //var newUserposition = treeHolder.find('#userposition-'+newid);
//            var newUserposition = newElementsAppendedRaw.find('select.userposition-positiontypes');    //find('.userposition-positiontypes');
//            //console.log( 'newUserposition.id='+newUserposition.attr('id') );
//            newElementsAppendedRaw.find('div.userposition-positiontypes').remove();
//            specificRegularCombobox(newUserposition);
//
//            //remove id and name for all inputs preceding the input with selected node
//            clearElementsIdName(treeHolder);
//
//            //add listener to this element
//            comboboxTreeListener( newElementsAppended, entityName, rowElHtml );
//
//        } //if

    });

}

function createNewTreenodeCombobox( entityName, treeHolder, comboboxEl, treeArr, rowElHtml, attachflag ) {
    //do nothing if new element was enetered
    if( treeArr == null ) {
        console.log('do nothing if new element was enetered');
        return false;
    }

    if( treeArr.length > 0 ) {

        var label = treeArr[0].leveltitle;
        //console.log( 'label='+ label );

        //readonly combobox
        var readonly = "";
        if( cycle.indexOf("show") != -1 ) {
            readonly = "readonly";
        }

        //var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl);    //treeHolder.find('#node-userpositions-data');
        //var comboboxHtml = getNewTreeNode(treeHolder,comboboxEl,rowElHtml,treeArr);
        var comboboxHtml = rowElHtml;   //changePoistionTypeByNodeid(treeHolder,comboboxEl,rowElHtml,treeArr);

        //var comboboxHtml = '<input id="new-tree" class="ajax-combobox-institution" type="text"/>';

        //var treeHolder = comboboxEl.closest('.composite-tree-holder');
        //console.log( treeHolder );

        //treeHolder.append(comboboxHtml);
        if( attachflag == 'bottom' ) {
            var newElementsAppendedRaw = $(comboboxHtml).appendTo(treeHolder);
        }

        if( attachflag == 'top' ) {
            var newElementsAppendedRaw = $(comboboxHtml).insertBefore(comboboxEl.closest('.treenode'));
            //label = "Top "+label;
        }

        //change label
        newElementsAppendedRaw.find('label').text(label+":");

        ///////////// change institution for user position .userposition-institution
        var thisData = comboboxEl.select2('data');
        //console.log('change user position inst.id='+thisData.pid);
        var userPositionInstitution = newElementsAppendedRaw.find('.userposition-institution');
        //console.log(userPositionInstitution);
        userPositionInstitution.val(thisData.pid);

        ///////////// initialize the node
        var newElementsAppended = newElementsAppendedRaw.find('.ajax-combobox-institution');
        //console.log( 'newElementsAppended.id='+newElementsAppended.attr('id') );
        populateSelectCombobox( newElementsAppended, treeArr, "Select an option");

        ///////////// prepare the positiontypes
        var newUserposition = newElementsAppendedRaw.find('select.userposition-positiontypes');    //find('.userposition-positiontypes');
        //console.log( 'newUserposition.id='+newUserposition.attr('id') );
        //remove unneeded div
        newElementsAppendedRaw.find('div.userposition-positiontypes').remove();
        //init the positiontypes
        specificRegularCombobox(newUserposition);
        //set the positiontypes
        var positiontypes = treeArr[0].positiontypes;
        //console.log('positiontypes='+positiontypes);
        if( positiontypes ) {
            newUserposition.select2('val', positiontypes);
        } else {
            //console.log('clear position types');
            //console.log( newUserposition );
            newUserposition.select2('data', null);
        }
        //replace id and name by current node id
        changePoistionTypeByNodeid(newUserposition,thisData.id);

        //add listener to this element
        comboboxTreeListener( newElementsAppended, entityName, rowElHtml );

        return newElementsAppended;
    } //if

    return false;
}

function changePoistionTypeByNodeid(element,nodeid) {
    var elId = element.attr('id');
    var elName = element.attr('name');
    //console.log('modify element: prefix='+prefix+', id='+elId+', elName='+elName);

    if( !elId || !elName ) {
        return;
    }

    //oleg_userdirectorybundle_user_administrativeTitles_0_institution_userposition151_positionTypes
    //replace userposition151 by 'userposition'+nodeid
    var userPosArr = elId.split("_userposition");

    //151_positionTypes
    var secondStr = userPosArr[1];

    var secondStrArr = secondStr.split('_');

    var originalId = secondStrArr[0];
    //console.log( "originalId=" + originalId );

    var positionOld = 'userposition'+originalId;
    var positionNew = 'userposition'+nodeid;
    //console.log( "positionOld=" + positionOld + ", positionNew=" + positionNew );

    //replace
    elId = elId.replace(positionOld, positionNew);
    elName = elName.replace(positionOld, positionNew);

    element.attr('id',elId);
    element.attr('name',elName);
}

function getNewTreeNode(treeHolder,comboboxEl,rowElHtml,treeArr) {

    //console.log( "rowElHtml=" + rowElHtml );

    //rowElHtml = '<div class="trenode">'+rowElHtml+'</div>';

//    var comboboxId = comboboxEl.attr('id');
//    console.log( "comboboxId=" + comboboxId );
//
//    var thisid = treeArr[0].id;
//
//    //oleg_userdirectorybundle_user_administrativeTitles_0_institution_userposition151_positionTypes
//    //replace _userposition151_ by '_userposition'+thisid+'_'
//    var userPosArr = comboboxId.split("_userposition");
//
//    //151_positionTypes
//    var secondStr = userPosArr[1];
//
//    var secondStrArr = secondStr.split('_');
//
//    var originalId = secondStrArr[0];
//    console.log( "originalId=" + originalId );
//
//    var positionOld = '_userposition'+originalId+'_';
//    var positionNew = '_userposition'+thisid+'_';
//
//    var re = new RegExp(positionOld, 'g');
//    rowElHtml = rowElHtml.replace(re, positionNew);


    var index = 0;
    //console.log( "index=" + index );

    index = '_userPositions_'+index;

    //rowElHtml = rowElHtml.replace(/__userpositions__/g, index);
    rowElHtml = rowElHtml.replace(/_userPositions_0/g, index);

    //console.log( "rowElHtml=" + rowElHtml );

    return rowElHtml;
}

//modify all id and name by attaching a prefix "newelement_" to all ajax-combobox-institution element prior to the last not empty combobox
function clearElementsIdName(treeHolder) {
    //console.log('treeHolder=');
    //console.log(treeHolder);

    var lastNode = treeHolder.find('.treenode').last();
    //printF(lastNode,'clear el by lastNode:');
    //console.log('lastNode:');
    //console.log(lastNode);

    clearRecursivelyIdName(lastNode);
}

function clearRecursivelyIdName(treenode) {
    //console.log('process treenode:');
    //console.log(treenode);

    if( treenode.length == 0 ) {
        //console.log('treenode is null');
        return;
    }

    var lastComboboxEl = treenode.find('div.ajax-combobox-institution');
    var lastInputEl = treenode.find('input.ajax-combobox-institution');
    if( lastComboboxEl.length == 0 || lastInputEl.length == 0 ) {
        //console.log('lastComboboxEl or lastInputEl is null');
        return;
    }

    if( !lastComboboxEl.attr('id') ) {
        //console.log('lastComboboxEl id is null');
        return;
    }

    var comboboxData = lastComboboxEl.select2('data');
    //console.log('comboboxData:');
    //console.log(comboboxData);

    if( comboboxData == null ) {
        //console.log('comboboxData is null');
        //unmap element
        mapTreeNode(lastInputEl,false);
        clearRecursivelyIdName(treenode.prev());
        return;
    }

    //console.log('lastComboboxEl:');
    //console.log(lastComboboxEl);

    //console.log('comboboxData.id='+comboboxData.id+', text='+comboboxData.text);

    if( comboboxData && comboboxData.id ) {
        //unmap all previous siblings
        treenode.prevAll().each( function(){
            var inputEl = $(this).find('input.ajax-combobox-institution');
            mapTreeNode(inputEl,false);
        });
        //map element
        mapTreeNode(lastInputEl,true);
        return;
    }

    return;
}

function mapTreeNode( element, mapped ) {
    //console.log('modify element:');
    //console.log(element.prevObject);
    //printF(element,'modify id and name:');
    var prefix = 'newnode_';
    var elId = element.attr('id');
    var elName = element.attr('name');
    //console.log('modify element: prefix='+prefix+', id='+elId+', elName='+elName);

    if( !elId || !elName ) {
        return;
    }

    if( mapped ) {
        //remove prefix
        elId = elId.replace(prefix, "");
        elName = elName.replace(prefix, "");
    } else {
        //add prefix
        if( elId.indexOf(prefix) == -1 ) {
            elId = prefix+elId;
        }
        if( elName.indexOf(prefix) == -1 ) {
            elName = prefix+elName;
        }
    }

    element.attr('id',elId);
    element.attr('name',elName);
}

function getNewTreeNode_ORIG(treeHolder) {
    var datael = treeHolder.find('#node-userpositions-data');

    if( !datael ) {
        return;
    }

    var prototype = datael.data('prototype-'+'user-userpositions');

    //var index = 0;
    var index = getNextElementCount(treeHolder,'ajax-combobox-institution');

    //prototype = prototype.replace("__documentContainers__", "0");
    prototype = prototype.replace(/__userpositions__/g, index);

    //console.log( "prototype=" + prototype );

    return prototype;
}

function setTreeNode( entityName, treeHolder, node, data ) {
    //3 case: data has id and text (both equal to a node name), but does not have pid - new node => pid is previous select box
    //generate new node in DB
    if( data && data.id && !data.hasOwnProperty("pid") ) {
        //console.log("3 case: new node");

        var prevNodeData = node.closest('.row').prev().find('.ajax-combobox-institution').select2('data');
        //console.log(prevNodeData);

        var conf = "Are you sure you want to create " + "'" + data.id + "?";
        if( prevNodeData && data.hasOwnProperty("leveltitle") ) {
            conf = "Are you sure you want to create " + "'" + data.id + "' under " + prevNodeData.leveltitle + "?";
        }
        if( !window.confirm(conf) ) {
            //treeHolder.find('.tree-node-id').val(0);
            node.select2('data', null);
            return;
        }

        var thisPid = 0;
        if( prevNodeData ) {
            thisPid = prevNodeData.id;
        }

        var newnodeid = jstree_action_node(entityName, 'create_node', null, data.id, thisPid, null, null, null, null, 'combobox');
        if( newnodeid ) {
            node.select2("data", {id: newnodeid, text: data.id});
            //node.trigger('change');
        } else {
            //newnodeid = 0;
            node.select2('data', null);
        }
        //console.log(node.select2('data'));
    }
}
function setNodeIdPid_OLD( entityName, treeHolder, node, data ) {

    //console.log( data );

    //treeHolder.find('.tree-node-id').val(data.id);
    //treeHolder.find('.tree-node-parent').val(data.pid);

    //1 case: data has id and pid - existing node (pid might be 0)
    if( data && data.id && data.hasOwnProperty("pid") ) {
        //console.log("1 case: data has id and pid - existing node");
        treeHolder.find('.tree-node-id').val(data.id);
        treeHolder.find('.tree-node-parent').val(data.pid);
    }

    //2 case: data is null - clear node => set as previous select box
    if( !data ) {
        //console.log("2 case: data is null - clear node");
        var prevNodeData = node.closest('.row').prev().find('.ajax-combobox-institution').select2('data');
        //console.log(prevNodeData);
        var thisId = 0;
        var thisPid = 0;
        if( prevNodeData ) {
            thisId = prevNodeData.id;
            thisPid = prevNodeData.pid;
        }
        //console.log( 'id='+thisId+", pid="+thisPid );
        treeHolder.find('.tree-node-id').val(thisId);
        treeHolder.find('.tree-node-parent').val(thisPid);
    }

    //3 case: data has id and text (both equal to a node name), but does not have pid - new node => pid is previous select box
    //generate new node in DB
    if( data && data.id && !data.hasOwnProperty("pid") ) {
        //console.log("3 case: new node");

        var prevNodeData = node.closest('.row').prev().find('.ajax-combobox-institution').select2('data');
        //console.log(prevNodeData);

        var conf = "Are you sure you want to create " + "'" + data.id + "?";
        if( prevNodeData && data.hasOwnProperty("leveltitle") ) {
            conf = "Are you sure you want to create " + "'" + data.id + "' under " + prevNodeData.leveltitle + "?";
        }
        if( !window.confirm(conf) ) {
            //treeHolder.find('.tree-node-id').val(0);
            node.select2('data', null);
            return;
        }

        var thisPid = 0;
        if( prevNodeData ) {
            thisPid = prevNodeData.id;
        }

        var newnodeid = jstree_action_node(entityName, 'create_node', null, data.id, thisPid, null, null, null, null, 'combobox');

        if( newnodeid ) {
            treeHolder.find('.tree-node-id').val(newnodeid);
            treeHolder.find('.tree-node-parent').val(prevNodeData.id);
        } else {
            treeHolder.find('.tree-node-id').val(0);
            treeHolder.find('.tree-node-parent').val(prevNodeData.id);
        }

    }

    //set id and pid only
//    if( data && data.id && !data.hasOwnProperty("pid") ) {
//        console.log("3 case: new node");
//        var prevNodeData = node.closest('.row').prev().find('.ajax-combobox-institution').select2('data');
//        console.log(prevNodeData);
//        var thisId = data.id;
//        var thisPid = 0;
//        if( prevNodeData ) {
//            //thisId = prevNodeData.id
//            thisPid = prevNodeData.id;
//        }
//        console.log( 'id='+thisId+", pid="+thisPid );
//        treeHolder.find('.tree-node-id').val(thisId);
//        treeHolder.find('.tree-node-parent').val(thisPid);
//    }

    //console.log( 'id='+treeHolder.find('.tree-node-id').val()+", pid="+treeHolder.find('.tree-node-parent').val() );
}

////////////////////////////// EOF TREE //////////////////////////////////













////////////////////////////// OLD TREE: TO DELETE //////////////////////////////////

function getComboboxTreeByPid( parentElement, fieldClass, parentId, clearFlag ) {

    //console.log( "onchange=" + fieldClass );

    var holder = parentElement.closest('.user-collection-holder');
    if( typeof holder === "undefined" || holder.length == 0 ) {
        //console.log( "holder is not found! class="+fieldClass );
        return;
    }
    //console.log( holder );

    var targetEl = holder.find("."+fieldClass).not("*[id^='s2id_']");
    if( typeof targetEl === "undefined" || targetEl.length == 0 ) {
        //console.log( "target is not found!" );
        return;
    }

    var targetId = '#' + targetEl.attr('id');
    //console.log( "targetId="+targetId );

    if( typeof parentId === "undefined" || parentId == null ) {
        parentId = parentElement.select2('val');
    }
    //console.log( "parentId="+parentId );

    if( typeof clearFlag === "undefined" ) {
        clearFlag = true;
    }

//    if( clearFlag ) {
//        //clear combobox
//        //oleg_userdirectorybundle_user_administrativeTitles_0_service
//        //oleg_userdirectorybundle_user_administrativeTitles_0_service
//        console.log( "clear combobox, targetId="+targetId);
//        populateSelectCombobox( targetId, null, "Select an option or type in a new value" );
//        setElementToId( targetId );
//        $(targetId).select2("readonly", true);
//        clearChildren(holder,fieldClass);
//    }

    if( parentId ) {

        var fieldName = fieldClass.replace("ajax-combobox-", "");
        //console.log( "fieldName="+fieldName+", parentid="+parentId );
        var url = getCommonBaseUrl("util/common/"+fieldName,"employees"); //always use "employees" to get children

        //url = url + "?pid="+parentId;

        var curid = null;
        //use curid to add current object. However, it causes the problems by showing not correct children list
        //var curid = targetEl.select2('val');
        //console.log("curid="+curid);
        //if( isInt(curid) ) {
        //    url = url + "&id="+curid;
        //}

        $.ajax({
            url: url,
            //type: 'POST',
            data: {id: curid, pid: parentId},
            timeout: _ajaxTimeout,
            async: asyncflag
        }).success(function(data) {
            //console.log('success: data:');
            //console.log(data);

            if( !data || data.length == 0 ) {
                //console.log('data is null');
                clearTreeToDown(targetId,holder,fieldClass,parentId);
                $(targetId).select2("readonly", false);
            } else {
                //console.log('data ok');
                populateSelectCombobox( targetId, data, "Select an option or type in a new value" );
                //$(targetId).select2("readonly", false);

                //get parent id
                var thisParentId = null;
                var thisData = $(targetId).select2('data');
                if( thisData ) {
                    //console.log( "thisData is ok" );
                    var thisParentId = thisData.parentid;
                } else {
                    //console.log( "thisData is null" );
                }
                //console.log( "thisParentId="+thisParentId );
                if( thisParentId != parentId ) {
                    //console.log( "clear and populate this thisParentId="+thisParentId );
                    //clear tree
                    clearTreeToDown(targetId,holder,fieldClass,parentId);
                    //re-populate this select box
                    populateSelectCombobox( targetId, data, "Select an option or type in a new value" );
                } {
                    //console.log( "load children this thisParentId="+thisParentId );
                    loadChildren($(targetId),holder,fieldClass);
                }
                $(targetId).select2("readonly", false);
            }

//            //test value
//            console.log(fieldClass+': after value='+$(targetId).select2('val'));
//            if( $(targetId).select2('data') ) {
//                console.log('after text='+$(targetId).select2('data').text);
//            }
        });

    }
    else {

        if( clearFlag ) {
            //console.log( "clear combobox, targetId="+targetId);

            //clear combobox
            //populateSelectCombobox( targetId, null, "Select an option or type in a new value" );
            //setElementToId( targetId );
            //$(targetId).select2("readonly", true);

            //clearChildren(holder,fieldClass);
            clearTreeToDown(targetId,holder,fieldClass,parentId);
        }
    }

}

function populateParentChildTree(target, data, placeholder, multipleFlag, childClass) {

    var targetElements = $(target);

    targetElements.each( function() {

        var selectId = '#'+$(this).attr('id');

        populateSelectCombobox( selectId, data, placeholder, multipleFlag );

        //children
        //console.log('################################# populate Parent Child Tree childClass='+childClass);
        getComboboxTreeByPid($(this),childClass,null,true);

    });

}

//If default value will be set "Weill Cornell Medical College", then saving the user data will save default value but user might not be aware of that.
function setDeafultData(target,data,text) {
    //set default to "Weill Cornell Medical College"
    var value = $(target).select2('val');
    //console.log('value='+value);
    if( !value ) {
        var setId = getDataIdByText(data,text);
        setElementToId( target, data, setId );
    }
}


function loadChildren(parentElement,holder,fieldClass) {

    var childrenTargetClass = getChildrenTargetClass(fieldClass);

    var parentId = parentElement.select2('val');

    if( childrenTargetClass && parentId ) {
        //console.log( "################################# load Children="+childrenTargetClass );
        getComboboxTreeByPid(parentElement,childrenTargetClass,null,true);
    }

}


function clearTreeToDown(targetId,holder,fieldClass,parentId) {

    //console.log( "clear tree to down: targetId="+targetId+", fieldClass="+fieldClass+", parentId="+parentId+", cleanThis="+cleanThis );

    if( $(targetId).length == 0 ) {
        //console.log( "clear tree to down: element with targetId does not exists" );
        return;
    }

    var thisParentId = null;
    var thisData = $(targetId).select2('data');
    if( thisData ) {
        //console.log( "thisData is ok" );
        var thisParentId = thisData.parentid;
    } else {
        //console.log( "thisData is null" );
    }
    //console.log( "thisParentId="+thisParentId );

    if( thisParentId == null || parentId == null || thisParentId != parentId ) {
        $(targetId).val('');
        populateSelectCombobox( targetId, null, "Select an option or type in a new value" );
        $(targetId).select2("readonly", true);
    }

    //var holder = parentElement.closest('.user-collection-holder');
    var childrenTargetClass = getChildrenTargetClass(fieldClass);

    if( childrenTargetClass ) {

        //console.log( "clear Children="+childrenTargetClass );
        var childrenTargetId = '#' + holder.find("."+childrenTargetClass).not("*[id^='s2id_']").attr('id');

        if( $(childrenTargetId).select2('data') && $(childrenTargetId).select2('data').parentid != thisParentId ) {
            $(childrenTargetId).val('');
            populateSelectCombobox( childrenTargetId, null, "Select an option or type in a new value" );
            $(childrenTargetId).select2("readonly", true);
        }

        clearTreeToDown(childrenTargetId,holder,childrenTargetClass,thisParentId);

    } else {
        //console.log( "don't clear="+fieldClass );
    }

}

//This function executes twice (?)
var _initInstitutionManuallyCount = 0;
function initInstitutionManually() {
    if( _initInstitutionManuallyCount > 0 ) {
        return;
    }
    _initInstitutionManuallyCount = 1;

    $('.ajax-combobox-institution-preset').each(function(e){
        //console.log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! init inst manually" );
        var clearFlag = true; //clear children and default service
        getComboboxTreeByPid($(this),'ajax-combobox-department',null,clearFlag);
    });
}



////////////////// mixed functions ////////////////////
function initTreeSelect(clearFlag) {

    //console.log( "init Tree Select" );

    if( typeof clearFlag === "undefined" ) {
        clearFlag == true;
    }

    $('.ajax-combobox-institution,.ajax-combobox-institution-preset').on('change', function(e){
        //console.log( "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! institution on change" );
        getComboboxTreeByPid($(this),'ajax-combobox-department',null,clearFlag);
    });

    $('.ajax-combobox-department').on('change', function(e){
        //console.log( "department on change" );
        getComboboxTreeByPid($(this),'ajax-combobox-division',null,clearFlag);
    });

    $('.ajax-combobox-division').on('change', function(e){
        //console.log( "division on change" );
        getComboboxTreeByPid($(this),'ajax-combobox-service',null,clearFlag);
    });

    //comments type and subtypes
    $('.ajax-combobox-commenttype').on('change', function(e){
        getComboboxTreeByPid($(this),'ajax-combobox-commentsubtype',null,clearFlag);
    });

    //residencyspecialty and fellowshipsubspecialty
    $('.ajax-combobox-residencyspecialty').on('change', function(e){
        getComboboxTreeByPid($(this),'ajax-combobox-fellowshipsubspecialty',null,clearFlag);
    });
}

function getChildrenTargetClass(fieldClass) {

    //console.log( "get children target class: fieldClass="+fieldClass );

    var childrenTargetClass = null;

    if( fieldClass == "ajax-combobox-institution" ) {
        childrenTargetClass = "ajax-combobox-department";
    }
    if( fieldClass == "ajax-combobox-department" ) {
        childrenTargetClass = "ajax-combobox-division";
    }
    if( fieldClass == "ajax-combobox-division" ) {
        childrenTargetClass = "ajax-combobox-service";
    }

    //comments type and subtypes
    if( fieldClass == "ajax-combobox-commenttype" ) {
        childrenTargetClass = "ajax-combobox-commentsubtype";
    }

    return childrenTargetClass;
}
////////////////// EOF mixed functions ////////////////////




///////////////// Institution Tree ///////////////////
function setInstitutionTreeChildren(holder) {

    //console.log( "set Institution Tree Children" );

    if( typeof holder == 'undefined' ) {
        holder = $('body');
    }

    //department
    populateSelectCombobox( holder.find(".ajax-combobox-department"), null, "Select an option or type in a new value", false );

    //division
    populateSelectCombobox( holder.find(".ajax-combobox-division"), null, "Select an option or type in a new value", false );

    //service
    populateSelectCombobox( holder.find(".ajax-combobox-service"), null, "Select an option or type in a new value", false );

}
///////////////// EOF Institution Tree ///////////////////



///////////////// Comments Types Tree - initialize the children to null ///////////////////
function setCommentTypeTreeChildren(holder) {

    if( typeof holder == 'undefined' ) {
        holder = $('body');
    }

    var targetId = holder.find(".ajax-combobox-commentsubtype");

    //subTypes
    populateSelectCombobox( targetId, null, "Select an option or type in a new value", false );
}
///////////////// EOF Comments Types ///////////////////


///////////////// Residency Specialty Tree - initialize the children to null ///////////////////
function setResidencyspecialtyTreeChildren(holder) {

    if( typeof holder == 'undefined' ) {
        holder = $('body');
    }

    var targetId = holder.find(".ajax-combobox-fellowshipsubspecialty");

    //subTypes
    populateSelectCombobox( targetId, null, "Select an option or type in a new value", false );
}
///////////////// EOF Comments Types ///////////////////


///////////////// Tree managemenet ///////////////////
//redirect to correct controller with node id and parent
function editTreeNode(btn) {
    var holder = $(btn).closest('.tree-node-holder');
    //console.log(holder);

    //get node id
    var inputEl = holder.find('input.combobox:text').not("*[id^='s2id_']");
    //console.log(inputEl);
    var nodeid = inputEl.select2('val');
    var res = getInstitutionNodeInfo(inputEl);
    var nodename = res['name'];
    //console.log('nodeid='+nodeid+', nodename='+nodename);

    if( nodename == null ) {
        return;
    }
    //redirect to edit page
    var url = getCommonBaseUrl("admin/list/"+nodename+"s/"+nodeid,"employees");
    //console.log("url="+url);

    window.open(url);
    //window.location.href = url;
}

//redirect to correct controller with node id and parent
function addTreeNode(btn) {
    var holder = $(btn).closest('.tree-node-holder');
    //console.log(holder);

    //get node id
    var inputEl = holder.find('input.combobox:text').not("*[id^='s2id_']");
    //console.log(inputEl);
    var nodeid = inputEl.select2('val');

    //get parent id
    var res = getInstitutionNodeInfo(inputEl);
    var parentClass = res['parentClass'];
    var nodename = res['name'];

    if( nodename == null ) {
        return;
    }

    if( parentClass ) {
        //console.log('parentClass='+parentClass);
        var treeHolder = $(btn).closest('.user-collection-holder');
        var parentEl = treeHolder.find('.'+parentClass);
        //console.log(parentEl);
        var parentid = parentEl.select2('val');
        if( !parentid || parentid == "" ) {
            alert("Parent is not specified");
            return;
        }
        var url = getCommonBaseUrl("admin/list/"+nodename+"/new/parent/"+parentid,"employees");
    } else {
        var url = getCommonBaseUrl("admin/list/institutions/new","employees");
    }
    //redirect to add page
    window.open(url);
    //window.location.href = url;
}

//function getNodeParentClass(nodeInputElement) {
//
//    var parentClass = null;
//
//    if( nodeInputElement.hasClass("ajax-combobox-department") ) {
//        parentClass = "ajax-combobox-institution";
//    }
//    if( nodeInputElement.hasClass("ajax-combobox-division") ) {
//        parentClass = "ajax-combobox-department";
//    }
//    if( nodeInputElement.hasClass("ajax-combobox-service") ) {
//        parentClass = "ajax-combobox-division";
//    }
//
//    return parentClass;
//}

function getInstitutionNodeInfo(nodeInputElement) {

    var name = null;
    var parentClass = null;

    if( nodeInputElement.hasClass("ajax-combobox-institution") ) {
        name = "institution";
    }
    if( nodeInputElement.hasClass("ajax-combobox-department") ) {
        name = "department";
        parentClass = "ajax-combobox-institution";
    }
    if( nodeInputElement.hasClass("ajax-combobox-division") ) {
        name = "division";
        parentClass = "ajax-combobox-department";
    }
    if( nodeInputElement.hasClass("ajax-combobox-service") ) {
        name = "service";
        parentClass = "ajax-combobox-division";
    }

    var res = new Array();
    res['name'] = name;
    res['parentClass'] = parentClass;

    return res;
}
///////////////// EOF Tree managemenet ///////////////////
