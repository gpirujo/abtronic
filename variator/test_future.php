<?php

require 'Future.php';
require 'Future/PHP.php';
require 'Future/ESI.php';
require 'Future/JS.php';

$g = new Future_JS;

$actions[] = $g->assign('x', $g->literal('hola'));
$actions[] = $g->assign('y', $g->literal(intval($_GET['y'])));

$x = $g->variable('x');
$hola = $g->literal('hola');
$x_equals_hola = $g->eq($x, $hola);
$y = $g->variable('y');
$ocho = $g->literal(8);
$y_equals_ocho = $g->eq($y, $ocho);
$cond1 = $g->_and($x_equals_hola, $y_equals_ocho);
$when1 = $g->when($cond1, function() { ?>ocho<?php });
$nueve = $g->literal(9);
$y_equals_nueve = $g->eq($y, $nueve);
$when2 = $g->when($y_equals_nueve, function() { ?>nueve<?php });
$otherwise = function() { ?>default<?php };
$actions[] = $g->choose(array($when1, $when2), $otherwise);

foreach ($actions as $action) call_user_func($action);
