<?php
require 'ABTronic.php';

ABTronic::run(array(
    'HomePageMap' => array(
        'affects' => 1/10,
        'options' => array(
            'A' => 1/4,
            'B' => 1/4,
        ),
    ),
    'PostingButton' => array(
        'affects' => 5/10,
        'options' => array(
            'A' => 4/10,
            'B' => 4/10,
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
