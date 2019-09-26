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
<h1>Zahlungserinnerung zur Rechnung {$invoiceNumber} vom {$invoiceDate}.</h1>

<p>
Sehr geehrte Damen und Herren,
</p>
<p>
wir wissen, dass in der Hektik des Alltags vieles vergessen werden kann.
Deshalb möchten wir Sie mit diesem Schreiben freundlich an eine noch ausstehende Rechnung erinnern:
</p>
<div style="padding-left: 2em; margin-left:0;">
<ul>
    <li>Rechnung Nr. {$invoiceNumber} vom {$invoiceDate}, Betrag: {displayPrice price=$invoiceSumToPay currency=$id_currency} </li>
</ul>
</div>
<p>
Bitte überweisen Sie den Betrag in Höhe von {displayPrice price=$invoiceSumToPay} bis zum {$today10} auf das unten genannte Konto. 
Sollte es offene Fragen oder eventuelle Beanstandungen geben, bitten wir Sie sich mit uns in Verbindung zu setzen. 
</p>
<p>
Sollten Sie den Betrag zwischenzeitlich beglichen haben, betrachten Sie diese Zahlungserinnerung bitte als gegenstandslos.
</p>
<p>
Wir stehen Ihnen für Fragen jederzeit zur Verfügung.
</p>
<br/><br/>
Mit freundlichen Grüßen
<p>
Christian Koehlert Kundenservice
</p>
</div>