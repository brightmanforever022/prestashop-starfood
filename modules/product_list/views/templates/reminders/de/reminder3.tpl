<div style="margin-left: 1cm">
{literal}
    <style>
        * {font-size: 11pt; }
        td {font-size: 9pt; }
        .bottomInfo td {font-size: 8pt; }
        .signature{font: 26pt bold;}
        h1 {font-size: 11pt}
    </style>
{/literal}
<h1>Letzte Mahnung zur Rechnung {$invoiceNumber} vom {$invoiceDate}.</h1>
<p>
Sehr geehrte Damen und Herren,
</p>
<p>
auf unsere vorherigen Zahlungserinnerungen haben wir leider noch keinen Zahlungseingang erhalten.
</p>
<p>
Deshalb bitten wir Sie mit diesem Schreiben letztmalig die folgende ausstehende Rechnung zu begleichen:
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
Sollte der Betrag zu dem genannten Datum nicht bei uns eingehen, sind wir leider gezwungen, rechtliche Schritte einzuleiten. Wir hoffen jedoch, dass es dazu nicht kommen muss und wir Ihnen und uns diese Unannehmlichkeiten ersparen können.
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