/* 
 * This file contains set of tools to organize edit on field in table by double click on it
 */


/**
 * Event handler, shows edit field box and sends its value to server after edit
 * @param idSource object that has following methods:
 *     getRowCssId(clickedField)  -- returns css id of table row there editing cell is contained
 *     idToPost(clickedField) -- returns assoc. array with record ids to post 
 * @param clickedField field by which user did double click (this in event handler), usually td element
 * @param callUrl - url to which we send edited data
 * @param editFieldName field name of edited field in post variables that is sent to server 
 * @param editFieldTemplate template for edit field. Object with methods:
 *  getId() -- returns css id of field
 *  generate(value) value -- current field value; returns html representaion of field
 *  getVal() -- returns current field value that should be sent to server
 *  validate() -- optinal method, validates field, returns error message if field is invalid
 * @param successCallback -- function called on success, may be null. The function should update editing field back to "show info" state, removing editor widget.
 *  Success is then result.error field is empty in json server respose. 
 *  4 parameters are passed in callback: 
 *  fieldContainer -- editing field container (usualy td), cssRowId -- css id of tr containing field, newValue -- value after edit,
 *  result -- response from server
 * @param additionalParamsHandler callback function that should return assoc array with additional params that need to be sent to server,
 * may be null. cssRowId -- css id of tr containing field is passed into the handler
 */
function osEditTableField(idSource, clickedField, callUrl, editFieldName, editFieldTemplate, successCallback, additionalParamsHandler)
{
    if ($('#'+editFieldTemplate.getId()).length)
    {
        return;
    }
    var cssRowId = idSource.getRowCssId(clickedField);
    var fieldContainer = $(clickedField);
    var oldFieldContent = fieldContainer.html().trim();

    // replace on input field
    fieldContainer.html(editFieldTemplate.generate(oldFieldContent));
    $('#'+editFieldTemplate.getId()).focus().blur(function(){
                                             // then focus lost try to save
                                             // field
                                             var newValue = editFieldTemplate.getVal();
                                             if (oldFieldContent==newValue)
                                             {
                                                 fieldContainer.html(oldFieldContent);
                                                 return;
                                             }
                                             // validate field
                                             if ('validate' in editFieldTemplate)
                                             {    
                                                 var validateFieldError = editFieldTemplate.validate();
                                                 if (validateFieldError && validateFieldError.length)
                                                 {
                                                     alert(validateFieldError);
                                                     $('#'+editFieldTemplate.getId()).focus();
                                                     return;
                                                 }
                                             }
                                             
                                             
                                             var postParams = idSource.idToPost(clickedField);
                                             if (typeof additionalParamsHandler !=='undefined' && additionalParamsHandler)
                                             {
                                                postParams = jQuery.extend(postParams, additionalParamsHandler(cssRowId));
                                             }
                                             postParams[editFieldName] = newValue;
                                             
                                             $.post(callUrl, postParams,
                                                    function(result){
                                                        if (result.error)
                                                        {
                                                            // error occured
                                                            alert(result.error);
                                                            fieldContainer.html(oldFieldContent);
                                                        }
                                                        else
                                                        {
                                                            // remove field
                                                            $('#'+editFieldTemplate.getId()).remove();
                                                            
                                                            // update shown value
                                                            if (typeof successCallback !== 'undefined' && successCallback)
                                                            {
                                                                successCallback(fieldContainer, cssRowId, newValue, result);
                                                            }
                                                            else
                                                            {
                                                                fieldContainer.html(newValue);
                                                            }
                                                        }
                                                    },
                                                    'json');
                                         });
}

/**
 * Base abstract class
 * @returns {osEditFieldIdSource}
 */
function osEditFieldIdSource(){}
osEditFieldIdSource.prototype.getRowCssId = function (clickedField)
{
    return $(clickedField).parents('tr').attr('id');
}


function osEditFieldPsProductIdSource(){}
osEditFieldPsProductIdSource.prototype = Object.create(osEditFieldIdSource.prototype);
osEditFieldPsProductIdSource.prototype.idToPost = function (clickedField)
{
    var cssId = this.getRowCssId(clickedField);
    return {product_id: $('#'+cssId+' .ps_id').attr('productId'), combination_id: $('#'+cssId+' .ps_id').attr('combinationId')};
}

/**
 * Class purposed to extract all necessary ids from simple prefixed css id. for example <tr id="orderxx"> there xx is record id
 * @@param {string} prefix, in exmple above it is 'order'
 * @returns {undefined}
 */
function osEditFieldSimplePrefixIdSource(prefix)
{
    this.prefix = prefix;
}
osEditFieldSimplePrefixIdSource.prototype = Object.create(osEditFieldIdSource.prototype);
osEditFieldSimplePrefixIdSource.prototype.constructor = osEditFieldSimplePrefixIdSource;
osEditFieldSimplePrefixIdSource.prototype.idToPost = function (clickedField)
{
    var result = {};
    result[this.prefix+'_id'] = this.getRowCssId(clickedField).replace(this.prefix, '');
    return result;
}


/**
 * Class presents simple text field for editPsProductField function
 * @param {type} fieldName
 * @param size -- size of field
 * @returns {editFielfTextField}
 */
function editFieldTextField(fieldName, size)
{
    this.fieldName = fieldName;
    if (typeof size!== 'undefined' && size)
    {
        this.size = size;
    }
    else
    {
        this.size = 0;
    }
    
    /**
     * 
     * @returns css fieldId id without #
     */
    this.getId = function()
    {
        return this.fieldName+'EditField';
    }
    
    this.generate = function(value)
    {
        return '<input type="text" name="'+this.fieldName+'"'+(this.size?' size="'+this.size+'"':'')+' id="'+this.getId()+'" value="'+value+'" />'
    }
    
    this.getVal = function()
    {
        return $('#'+this.getId()).val();
    }
}


/**
 * Class presents text field, there int value should be entered
 * @param {type} fieldName
 * @param {type} size
 * @returns {undefined}
 */
function editFieldIntField(fieldName, size)
{
    editFieldTextField.apply(this, arguments);
}
editFieldIntField.prototype = Object.create(editFieldTextField.prototype);
editFieldIntField.prototype.validate = function ()
{
    if(!isInteger(this.getVal()))
    {
        return errIntegerNumber;
    }
}


/**
 * Class presents simple text field for editPsProductField function
 * @param {type} fieldName
 * @param selectList array of objects {id: name:}
 * @param idName optional name of id attribute in selectList, by default name "id" is used
 * @param optionName optional name of option(text for customer) attribute in selectList, by default name "name" is used
 * @returns {editFielfTextField}
 */
function editFieldSelectField(fieldName, selectList, idName, optionName)
{
    this.fieldName = fieldName;
    this.selectList = selectList;
    if (typeof idName!== 'undefined' && idName)
    {
        this.idName = idName;
    }
    else
    {
        this.idName = 'id';
    }
    
    if (typeof optionName!== 'undefined' && optionName)
    {
        this.optionName = optionName;
    }
    else
    {
        this.optionName = 'name';
    }
    
    /**
     * 
     * @returns css fieldId id without #
     */
    this.getId = function()
    {
        return this.fieldName+'EditField';
    }
    
    this.generate = function(value)
    {
        var selectHtml = '<select name="'+this.fieldName+'" id="'+this.getId()+'">';
        for (var i=0; i<this.selectList.length; i++)
        {
            // value of fielf is text visible to customer, so we compare by it
            selectHtml += '<option value="'+this.selectList[i][this.idName]+'" '+(this.selectList[i][this.optionName]==value?'selected="selected"':'')+'>'+this.selectList[i][this.optionName]+'</option>';
        }
        return selectHtml+'</select>';
    }
    
    this.getVal = function()
    {
        return $('#'+this.getId()).val();
    }
}

/**
 * Class presents simple text field for editPsProductField function
 * @param {type} fieldName
 * @param cols -- size of field
 * @param rows -- size of field
 * @returns {editFielfTextField}
 */
function editFieldTextareaField(fieldName, cols, rows)
{
    this.fieldName = fieldName;
    if (typeof cols!== 'undefined' && cols)
    {
        this.cols = cols;
    }
    else
    {
        this.cols = 0;
    }
    
    if (typeof rows!== 'undefined' && cols)
    {
        this.rows = rows;
    }
    else
    {
        this.rows = 0;
    }
    
    /**
     * 
     * @returns css fieldId id without #
     */
    this.getId = function()
    {
        return this.fieldName+'EditField';
    }
    
    this.generate = function(value)
    {
        var style = '';
        if (this.cols)
        {
            style += ' width: '+this.cols+'em;';
        }
        if (this.rows)
        {
            style += ' height: '+this.rows+'ex;';
        }
        
        return '<textarea name="quantity"'+(this.cols?' cols="'+this.cols+'"':'')+(this.rows?' rows="'+this.rows+'"':'')+' id="'+this.getId()+'" style="'+
            style+'" >'+value.replace(/<br\/?>/g, "\n")+'</textarea>';
    }
    
    this.getVal = function()
    {
        return $('#'+this.getId()).val();
    }
}


/**
 * Standard succee handler for update after edit in textarea.
 * @param {type} cssRowId
 * @param {type} newValue
 * @param {type} result
 */
function editFieldUpdateAfterTextareaEdit(fieldContainer, cssRowId, newValue, result)
{
    fieldContainer.html(newValue.replace(/\n/g, '<br/>'));
}
