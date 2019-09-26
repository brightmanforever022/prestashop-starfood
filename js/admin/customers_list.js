$(function(){
    var baseUrl = currentIndex+'&ajax=1&token='+token+'&action=';
    
    // set handlers
    $('.firstName').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=firstname', 'firstname', 
            new editFieldTextField('firstname'));
    });
    
    $('.lastName').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=lastname', 'lastname', 
            new editFieldTextField('lastname'));
    });
    
    $('.email').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=email', 'email', 
            new editFieldTextField('email'));
    });
    
    $('.company').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=company', 'company', 
            new editFieldTextField('company'));
    });
    
    $('.city').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=city', 'city', 
            new editFieldTextField('city'));
    });
    
    $('.address1').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=address1', 'address1', 
            new editFieldTextField('address1'));
    });
    
    $('.address2').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=address2', 'address2', 
            new editFieldTextField('address2'));
    });
    
    $('.postcode').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=postcode', 'postcode', 
            new editFieldTextField('postcode'));
    });
    
    $('.note').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=note', 'note', 
            new editFieldTextareaField('note', 15));
    });
    
    $('.country').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=id_country', 'id_country', 
            new editFieldSelectField('id_country', countriesList), 
            function(fieldContainer, cssRowId, newValue){
                // search for text for customer
                for(var i=0; i<countriesList.length; i++)
                {
                    if (countriesList[i].id==newValue)
                    {
                        $(fieldContainer).html(countriesList[i].name);
                        break;
                    }
                }
            });
    });
    
    $('.gender').dblclick(function(){
        new osEditTableField(new osEditFieldSimplePrefixIdSource('tr_'), this, baseUrl+'EditSimpleTxtField&field=id_gender', 'id_gender', 
            new editFieldSelectField('id_gender', gendersList), 
            function(fieldContainer, cssRowId, newValue){
                // search for text for customer
                for(var i=0; i<gendersList.length; i++)
                {
                    if (gendersList[i].id==newValue)
                    {
                        $(fieldContainer).html(gendersList[i].name);
                        break;
                    }
                }
            });
    });
});

