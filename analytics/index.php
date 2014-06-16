<!DOCTYPE html>
<html>
<head>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-51391553-1', 'abtronic.gpirujo.com');
        //ga('set', 'dimension1', 'VarB');
        ga('send', 'pageview');
    </script>
<?php
$paginas = array(
    'A' => array('B'),
    'B' => array('C'),
    'C' => array('D'),
    'D' => array('A'),
);
$pagina = $_GET['pagina'] ? $_GET['pagina'] : 'A';
?>
    <title><?= $pagina ?></title>
</head>
<body>
    <ul>
<?php
foreach ($paginas[$pagina] as $destino) {
    ?><li><a id="<?= $destino ?>" href="?pagina=<?= $destino ?>"><?= $destino ?></a></li><?php
}
?>
    </ul>
</body>
</html>
