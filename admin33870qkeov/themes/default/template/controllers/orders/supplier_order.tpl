<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <style type="text/css" media="all">
            {literal}
            * {font-size: 16px;}
            table {border-collapse: collapse; border:1px solid black}
            table th {background-color: #ccc;} 
            table td {border: 1px solid black; padding:10px; border-spacing:0px}
            /*
            table.products {border: none;}
            table.products td {border: none; padding:3px;}
            .bold { font-weight: bold;}
            .boldRed { font-weight: bold; color:red }
            .redBg {background-color: red;}
            .supplierReference{font-size: 18px;}
            .summaryReference{color: red}*/
            {/literal}
        </style>
    </head>
    <body>
        <h2>{l s='Ordered products'} </h2>
        <table>
            <thead>
                <tr>
                    <th>{l s='Photo'}</th>
                    <th>{l s='Sup. reference'}</th>
                    <th>{l s='Size'}</th>
                    <th>{l s='Quantity'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$products item=product}
                <tr>
                    <td><img src="{$product.imageLink}" /></td>
                    <td>{$product.product_supplier_reference}</td>
                    <td>{$product.size}</td>
                    <td>{$product.quantity}</td>
                </tr>
                {foreachelse}
                    <tr>
                        <td colspan="4"><em>{l s='No orders selected or selected orders contain no items'}</em></td>
                    </tr>    
                {/foreach}
            </tbody>
        </table>
    </body>
</html>