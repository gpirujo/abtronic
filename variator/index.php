<?php
require 'ABTronic.php';

$experiments = array(
    'HomePageMap' => array(
        'affects' => 9/10,
        'options' => array(
            'A' => 1/4,
            'B' => 1/4,
        ),
    ),
    'PostingButton' => array(
        'affects' => 9/10,
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

        </body>
    </html>
    <?php
});
