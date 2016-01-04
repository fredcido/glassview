<html>
    <head>
        <title>Testes Dojo Custom Module</title>

        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" >
        <link rel="stylesheet" type="text/css" href="dijit/themes/claro/claro.css" />

        <script type="text/javascript" charset="ISO-8859-1">
        //<!--
			//var djConfig = { parseOnLoad: true, locale: 'pt-br',debugAtAllCosts: true};
			var djConfig = { parseOnLoad: true, locale: 'pt-br'};
        //-->
        </script>

        <script type="text/javascript"src="dojo/dojo.js"></script>

        <style type="text/css">
            body, html {
                font-family:helvetica,arial,sans-serif;
                font-size:90%;
                background: #C5DCF7;
                width: 100%;
                height: 100%;
                margin: 0;
            }

        </style>
    </head>

    <body class="claro" id="boss">
	
	Testes Dojo Custom Module
		<pre>
dojo.require('modulo.default.test');

var ObjModDefTest = new modulo.default.test();

ObjModDefTest.teste();
		</pre>
	
	</body>
</html>
