<script type="text/javascript">
    $("#btn_save.clickable").on( "click", function() {
        var errors = false;

        var val_instructions = $("#instructions").val();
        /*if(val_instructions==undefined || val_instructions==null || val_instructions=="" || val_instructions==0)
         {
         var msg = '<?php echo _l('You need to enter your question.',1)?>';
         parent.dhtmlx.message({text:msg,type:'error',expire:10000});
         errors = true;
         }*/

        var val_source = '-';
        if($("#source_shortdesc").is(':checked'))
            val_source = val_source+"shortdesc-";
        if($("#source_desc").is(':checked'))
            val_source = val_source+"desc-";
        if($("#source_img").is(':checked'))
            val_source = val_source+"img-";
        if($("#source_none").val()=="none")
            val_source = 'none';
        if(val_source=="" || val_source=="-")
        {
            var msg = '<?php echo _l('You need to enter source(s).',1)?>';
            parent.dhtmlx.message({text:msg,type:'error',expire:10000});
            errors = true;
        }

        var val_undefined = $("#undefined").val();
        var val_quality = $("#quality").val();
        if(val_quality==undefined || val_quality==null || val_quality=="" || val_quality==0)
            val_quality = 'good';

        <?php if(!empty($checkfields)) echo $checkfields; ?>

        if(!errors)
        {
            $.post('index.php?ajax=1&act=cat_win-foulefactory_project_form_update',
                {
                    'id_project': "<?php echo $id_project; ?>",
                    'instructions':val_instructions,
                    'source':val_source,
                    'undefined':val_undefined,
                    'quality':val_quality
                    <?php if(!empty($postfields)) echo $postfields; ?>
                },
                function(data){
                    var msg = '<?php echo _l('Data saved',1)?>';
                    parent.dhtmlx.message({text:msg,type:'success',expire:3000});
                    parent.displayFFProjects();
                });
        }
    });
</script>