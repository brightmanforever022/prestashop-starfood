<div style="margin-left: 1cm">
{$customerFirstName} {$customerLastName}<br />
{if !empty($customerCompany)}{$customerCompany}<br />{/if}
{$customerAddr1}<br />
{if !empty($customerAddr2)}{$customerAddr2}<br />{/if}
{$customerPostcode} {$customerCity}<br />
{$customerCountry}<br />
</div>
<div style="width:100%; text-align: right">Hamburg, {$currentDate}</div>
<br /><br /><br /><br />
