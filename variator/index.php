<?php
require 'ABTronic.php';

$experiments = array(
    'HomePageMap' => array(
        'affects' => 7/8,
        'options' => array(
            'A' => 1/4,
            'B' => 1/4,
        ),
    ),
    'PostingButton' => array(
        'affects' => 4/5,
        'options' => array(
            'A' => 4/10,
            'B' => 4/10,
        ),
    ),
);

ABTronic::run($experiments, function($ab) {
    global $experiments;
    ?>
    <html>
        <body>

            <?php $ab->variate('PostingButton', array('A', $ab::CONTROL), function() use($ab) { ?>
                estoy en A o control
            <?php }, function() use($ab) { ?>
                estoy en B
            <?php }); ?>

            <?php foreach ($experiments as $exp_name => $def) { ?>
                El experimento <?= $exp_name ?> tiene el valor
                <?php foreach (array_keys($def['options']) as $opt_name) $ab->variate($exp_name, $opt_name, function() use($opt_name) { ?>
                    <?= $opt_name ?>
                <?php }); ?>
                <?php $ab->variate($exp_name, $ab::CONTROL, function() use($ab) { echo $ab::CONTROL; }); ?>
            <?php } ?>

        </body>
    </html>
    <?php
});
