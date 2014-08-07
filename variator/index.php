<?php
require 'ESI.php';
require 'ABTronic.php';

$esi = new ESI;
$esi->start(function($esi) {

    $experiments = array(
        'HomePageMap' => array(
            'dimension' => 1,
            'options' => array(
                'A, tu hermana' => 20,
                'B, tu vieja' => 20,
            ),
        ),
        'PostingButton' => array(
            'dimension' => 2,
            'options' => array(
                'A' => 40,
                'B' => 40,
            ),
        ),
    );
    ABTronic::run($esi, $experiments, function($esi, $ab) use($experiments) {

    ?>
    <html>
        <head>
            <title>ABTronic</title>
        </head>
        <body>

            <?php $ab->variate('PostingButton', array('A', $ab::CONTROL), function() use($ab) { ?>
                estoy en A o control
            <?php }, function() use($ab) { ?>
                estoy en B
            <?php }); ?>

            <?php foreach ($experiments as $exp_name => $def) { ?>
                El experimento <?= $exp_name ?> tiene el valor
                <?php foreach (array_keys($def['options']) as $opt_name) $ab->variate($exp_name, $opt_name, function() use($exp_name, $opt_name) { ?>
                    <span class="result" id="<?= $exp_name ?>"><?= $opt_name ?></span>
                <?php }); ?>
                <?php $ab->variate($exp_name, $ab::CONTROL, function() use($exp_name, $ab) { ?>
                    <span class="result" id="<?= $exp_name ?>"><?= $ab::CONTROL ?></span>
                <?php }); ?>
            <?php } ?>

            <?php $ab->track(); ?>

        </body>
    </html>
    <?php

    });

});
