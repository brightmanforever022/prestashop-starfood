<div style="margin-left: 1cm">
{literal}
    <style>
        * {font-size: 11pt; }
        td {font-size: 9pt; }
        .bottomInfo td {font-size: 8pt; }
        .signature{font-family: Mathilde; font: 26pt bold;}
        h1 {font-size: 11pt}
    </style>
{/literal}
<h1>Final notice of late payment due, Invoice {$invoiceNumber}, {$invoiceDate}.</h1>

<p>
Dear customer,
</p>
<p>
As a final professional courtesy and in follow-up to the previous correspondence regarding same, we are contacting you again to remind you that the below invoice remains unpaid and past due. 
</p>
<div style="padding-left: 2em; margin-left:0;">
<ul>
    <li>Invoice No. {$invoiceNumber}, Date: {$invoiceDate}, Amount: {displayPrice price=$invoiceSumToPay currency=$id_currency} </li>
</ul>
</div>
<p>
This matter requires your immediate attention. Per our agreement, kindly submit the amount of {displayPrice price=$invoiceSumToPay} to our bank account by {$today10}.
Bank account information can be found below.  
If your payment has already been made, please disregard this notice.
However, if you have not yet sent payment, kindly do so directly. 
</p>
<p>
We would be grateful if you attended to this matter immediately; otherwise, we will have no other recourse but to begin legal proceedings in an attempt to collect the money owed, 
as well as any interest incurred to date. We will also attempt to recover any legal fees associated with this case, including court costs and attorney fees, 
to the extent permitted by law, if the above-referenced invoice is not paid to our bank by {$today10}.
</p>
<p>
We sincerely hope you make a best effort to pay this invoice in order to avoid the course of action outlined here. 
Please contact us regarding this matter. We kindly await your reply. 
</p>
<br/>
Sincerely,
<br/>
Customer Service Department
<br/>
Christian Koehlert 
</div>
<br/>