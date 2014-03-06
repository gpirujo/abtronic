<?php
    require 'ABTester.php';
    $ab = new ABTester();
    $ab->addTest('Pepe', array(
        'Olga' => array(array(0,49)),
        'Susana' => array(array(50,99)),
    ), 0);
    $ab->open();
?>
<html>
    <head>
        <title>Test</title>
    </head>
    <body>
    <?php $ab->outputIf('Pepe', 'Olga', function() { ?>
        olga
    <?php }); ?>
    </body>
</html>
<?php
    $ab->close();
?>
