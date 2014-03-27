<?php
require 'ABTronic.php';

ABTronic::run(array(
    'HomePageMap' => array(
        'affects' => 10,
        'options' => array(
            'A' => 25,
            'B' => 25,
        ),
    ),
    'PostingButton' => array(
        'affects' => 50,
        'options' => array(
            'A' => 40,
            'B' => 40,
        ),
    ),
), function($abt) {
    ?>
    <html>
        <body>

            <?php $abt->variate('PostingButton', array('A', $abt::CONTROL), function() use($abt) { ?>
                estoy en A o control
            <?php }, function() use($abt) { ?>
                estoy en B
            <?php }); ?>

            <?php foreach ($abt->experiments() as $name) { ?>
                El experimento <?= $name ?> tiene el valor <?= $abt->variation($name) ?>.
            <?php } ?>

        </body>
    </html>
    <?php
});
