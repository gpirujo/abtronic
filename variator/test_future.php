<?php

require 'Future.php';
require 'Future/PHP.php';
require 'Future/ESI.php';
require 'Future/JS.php';

$g = new Future_PHP;

$actions = array(
    $g->assign(
        'x',
        $g->literal('hola')
    ),
    $g->assign(
        'y',
        $g->literal(intval($_GET['y']))
    ),
    $g->choose(
        array(
            $g->when(
                $g->_and(
                    $g->eq(
                        $g->variable('x'),
                        $g->literal('hola')
                    ),
                    $g->eq(
                        $g->variable('y'),
                        $g->literal(8)
                    )
                ),
                function() {
                    ?>ocho<?php
                }
            ),
            $g->when(
                $g->eq(
                    $g->variable('y'),
                    $g->literal(9)
                ), function() {
                    ?>nueve<?php
                }
            )
        ),
        function() {
            ?>default<?php
        }
    )
);

foreach ($actions as $action) call_user_func($action);
