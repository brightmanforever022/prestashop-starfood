
$(document).ready(function() {
    $('#print_label').click(function() {
        PrintLabels();
    });


    $('#print_label_pdf').on('click', function() {

        var count = $('input[name="productId[]"]:checked').length;

        if(count < 1) {
            alert('No products are selected');
            throw new Error('No products are selected');
        }

        var products = [];



        $('input[name="productId[]"]:checked').each(function(key, item) {

            var row_product = $(this).parents('tr');
            // var dbk_name = $(row_product).find('.dbk_name');
            // dbk_name=$.trim(dbk_name.text());

            // var sup_refference = $(row_product).find('.ps_sku');
            // sup_refference=$.trim(sup_refference.text());

            // var price = $(row_product).find('.dbk_price');
            // price=$.trim(price.text());

            var name = $(this).attr('data-ref');
            name=$.trim(name);

            var v = name.split('_');
            var sup_refference = v[0] ? v[0] : '';
            var size = v[2] ? v[2] : '';
            var dbk_name = v[1] ? v[1] : '';
            var price = $(row_product).find('.product_price_show').text();

            var arr = {
                'dbkname':dbk_name,
                'suprefference':sup_refference,
                'price':price,
                'size':size
            };

            products.push(arr);

        });

        var ids = {
            'products' : products
        };

        console.log(ids);

        $('#dbkPDFForm_json').val(JSON.stringify(products));
        $('#dbkPDFForm').submit();

       /* $.ajax({
            type: 'POST',
            url: 'https://www.vipdress.de/admin123/index2.php/ajax/dbk_products/generatePDFlabels',
            data: 'products='+JSON.stringify(products),
            success: function(msg) {
                $(document).html(msg);
            }
        });*/
    });

    function startupCode() {

    }
    function frameworkInitShim() {
        dymo.label.framework.init(startupCode);
    }
    window.onload = frameworkInitShim;
})
    // called when the document completly loaded
    function PrintLabels()
    {

        var count = $('input[name="productId[]"]:checked').length;

        if(count < 1) {
            alert('No products are selected');
            throw new Error('No products are selected');
        }
        // select printer to print on
        // for simplicity sake just use the first LabelWriter printer
        var printers = dymo.label.framework.getPrinters();
        if (printers.length == 0) {
            alert("No DYMO printers are installed. Install DYMO printers.");
            throw "No DYMO printers are installed. Install DYMO printers.";
        }


        var printerName = "";
        for (var i = 0; i < printers.length; ++i)
        {
            var printer = printers[i];
            if (printer.printerType == "LabelWriterPrinter")
            {
                printerName = printer.name;
                break;
            }
        }

        if (printerName == "") {
            alert("No LabelWriter printers found. Install LabelWriter printer");
            throw "No LabelWriter printers found. Install LabelWriter printer";
        }

        // finally print the label
        var success = false;
        $('input[name="productId[]"]:checked').each(function(key, item) {
            console.log($(this).val());

            var row_product = $(this).parents('tr');
            // var dbk_name = $(row_product).find('.dbk_name');
            // dbk_name=$.trim(dbk_name.text());

            // var sup_refference = $(row_product).find('.ps_sku');
            // sup_refference=$.trim(sup_refference.text());

            // var price = $(row_product).find('.dbk_price');
            // price=$.trim(price.text());

            var name = $(this).attr('data-ref');
            name=$.trim(name);

            var v = name.split('_');
            var sup_refference = v[0] ? v[0] : '';
            var size = v[2] ? v[2] : '';
            var dbk_name = v[1] ? v[1] : '';
            var price = $(row_product).find('.product_price_show').text();



        // prints the label


            try
            {
                // open label
                var labelXml = '<?xml version="1.0" encoding="utf-8"?>\
                    <DieCutLabel Version="8.0" Units="twips">\
                <PaperOrientation>Portrait</PaperOrientation>\
                <Id>WhiteNameBadge11356</Id>\
                <IsOutlined>false</IsOutlined>\
                <PaperName>11356 White Name Badge - virtual</PaperName>\
            <DrawCommands>\
            <RoundRectangle X="0" Y="0" Width="2340" Height="5040" Rx="270" Ry="270" />\
                </DrawCommands>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT_3</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Bottom</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">Artikelnr. / Style:</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="True" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="88.1514892578125" Y="331.200012207031" Width="2196.64846038818" Height="284.571441650391" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <BarcodeObject>\
                <Name>-BARCODE</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <Text>425078184891</Text>\
                <Type>Ean13</Type>\
                <Size>Small</Size>\
                <TextPosition>Bottom</TextPosition>\
                <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <TextEmbedding>Full</TextEmbedding>\
                <ECLevel>0</ECLevel>\
                <HorizontalAlignment>Center</HorizontalAlignment>\
                <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />\
                </BarcodeObject>\
                <Bounds X="425.062683105469" Y="4143.60009765625" Width="1491.87463378906" Height="795" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT__1</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Bottom</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">Größe / Size:</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="True" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="81.5999984741211" Y="1060.6884765625" Width="1981.39416503906" Height="257.142852783203" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT___1</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Bottom</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">Farbe / Color:</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="True" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="88.1514892578125" Y="1813.18872070313" Width="1701.1083984375" Height="303.257141113281" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Top</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">'+sup_refference+'</String>\
                <Attributes>\
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="105.00244140625" Y="639.382080078125" Width="1576.9951171875" Height="234.857147216797" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT_1</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Top</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">'+size+'</String>\
                <Attributes>\
                <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="90.00244140625" Y="1343.31323242188" Width="1171.9951171875" Height="339.857147216797" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT___2</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Top</VerticalAlignment>\
                <TextFitMode>None</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">'+dbk_name+'</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="81.5999984741211" Y="2100.30883789063" Width="2083.38061523437" Height="992.710815429688" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT_2</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Top</VerticalAlignment>\
                <TextFitMode>ShrinkToFit</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">Preis / Price:</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="True" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="141.599998474121" Y="3285" Width="1498.19995117188" Height="285" />\
                </ObjectInfo>\
                <ObjectInfo>\
                <TextObject>\
                <Name>TEXT_4</Name>\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
                <BackColor Alpha="0" Red="255" Green="255" Blue="255" />\
                <LinkedObjectName />\
                <Rotation>Rotation0</Rotation>\
                <IsMirrored>False</IsMirrored>\
                <IsVariable>False</IsVariable>\
                <GroupID>-1</GroupID>\
                <IsOutlined>False</IsOutlined>\
                <HorizontalAlignment>Left</HorizontalAlignment>\
                <VerticalAlignment>Top</VerticalAlignment>\
                <TextFitMode>ShrinkToFit</TextFitMode>\
                <UseFullFontHeight>True</UseFullFontHeight>\
                <Verticalized>False</Verticalized>\
                <StyledText>\
                <Element>\
                <String xml:space="preserve">'+price+'</String>\
            <Attributes>\
            <Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
                <ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />\
                </Attributes>\
                </Element>\
                </StyledText>\
                </TextObject>\
                <Bounds X="126.599998474121" Y="3630" Width="1003.19995117187" Height="405" />\
                </ObjectInfo>\
                </DieCutLabel>';



                var label = dymo.label.framework.openLabelXml(labelXml);
               // var label = dymo.label.framework.openLabelFile(labelUri).getLabelXml();


                // create label set to print data
                var labelSetBuilder = new dymo.label.framework.LabelSetBuilder();

                // first label
                var record = labelSetBuilder.addRecord();
                record.setText("Text", sup_refference);


             /*  var image = document.createElement('img');
               // var labelXml1 = dymo.label.framework.openLabelXml(labelXml);
                var pngData = dymo.label.framework.renderLabel(label, "", printerName);
                image.src = "data:image/png;base64," + pngData;
                document.getElementById("top_container").appendChild(image);*/

                // create label set to print data
                //var labelSetBuilder = new dymo.label.framework.LabelSetBuilder();


                var labelk = dbk_name + '<br>' + sup_refference + '<br>' + size + '<br>' + price;

                // first label
               // var record = labelSetBuilder.addRecord();
                //record.setText("Text", labelk);



               // label.print(printerName, "", labelSetBuilder);
                label.print(printerName);
                 success = true;
            }
            catch(e)
            {
                alert(e.message || e);
            }
        });

        if(success) {
            alert("Label(s) are printed");
        }
    };

   /*function initTests()
	{
		if(dymo.label.framework.init)
		{
			//dymo.label.framework.trace = true;
			dymo.label.framework.init(onload);
		} else {
			onload();
		}
	}

	// register onload event
	if (window.addEventListener)
		window.addEventListener("load", initTests, false);
	else if (window.attachEvent)
		window.attachEvent("onload", initTests);
	else
		window.onload = initTests;
*/
