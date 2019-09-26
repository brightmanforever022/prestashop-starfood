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
<h1>First notice of late payment due, Invoice {$invoiceNumber}, {$invoiceDate}.</h1>

<p>
Dear customer,
</p>
<p>
We would like to take this opportunity to thank you for your business with Christian Koehlert. This is a courtesy reminder to inform you that the below invoice is currently past due. 
</p>
<div style="padding-left: 2em; margin-left:0;">
<ul>
    <li>Invoice No. {$invoiceNumber}, Date: {$invoiceDate}, Amount: {displayPrice price=$invoiceSumToPay currency=$id_currency} </li>
</ul>
</div>
<p>
Per our agreement, kindly submit the amount of {displayPrice price=$invoiceSumToPay} to our bank account by {$today10}.
Bank account information can be found below.  
If your payment has already been made, please disregard this notice.
However, if you have not yet sent payment, kindly do so immediately. 
</p>
<p>
We would be grateful if you attended to this matter as soon as possible. If you should have any further questions or concerns, feel free to contact us. We look forward to hearing from you. </p>
<p>
</p>
<br/>
<p>
Sincerely,
<br/>
Customer Service Department
<br/>
Christian Koehlert 
</p>
</div>