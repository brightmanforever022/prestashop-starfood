<?php
if(_s("APP_TRENDS")=="1" && KAI9DF4!=1) {
    $last_update = SCI::getConfigurationValue("SC_TRENDS_UPDATED_AT");
    if(empty($last_update) || $last_update<date("Y-m-d"))
    {
        SCI::updateConfigurationValue("SC_TRENDS_UPDATED_AT", date("Y-m-d"));
?>
<script>
var trends_queue_calling = false;
var trends_queue_interval= null;

function makeTrendsCall()
{
    if(trends_queue_calling==false)
    {
        trends_queue_calling = true;

        $.post( "index.php?ajax=1&act=all_win-trends_loop_call", function(data) {
            trends_queue_calling=false;
            if(data.stop!=undefined && data.stop=="1")
                clearInterval(trends_queue_interval);
            else
                makeTrendsCall();
        }, "JSON");
    }
}
$( document ).ready(function() {
    makeTrendsCall();
    trends_queue_interval = setInterval(function(){ makeTrendsCall(); }, 10000);
});
</script>
<?php }
} ?>