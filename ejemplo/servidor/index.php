<?php

require 'datos.php';
require '../../variator/ESI.php';
require '../../variator/ABTronic.php';

function l($p, $i = null) {
    return 'index.php?' . http_build_query(array_merge(array('p' => $p), !is_null($i) ? array('i' => $i) : array()));
}

$p = $_GET['p'];
if (!$p) header('Location: ' . l('listado')) and exit();

$i = $_GET['i'];

$esi = new ESI;
$esi->start(function($esi) use($p, $i, $anuncios) {

    $ab = new ABTronic($esi, array(
        'Aviso' => array(
            'dimension' => 1,
            'options' => array(
                'Aviso' => 50,
            ),
        ),
    ));

    ?>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title><?= htmlspecialchars($p) ?></title>
        </head>
        <body>

            <?php
                if ($p == 'listado') {
            ?>

                <h1>Listado de <?php $ab->variate('Aviso', 'Aviso', function() {
                    ?>avisos<?php
                }, function() {
                    ?>anuncios<?php
                }); ?></h1>

                <table border="1">
                    <?php foreach ($anuncios as $i => $a) { ?>
                        <tr>
                            <td><img src="<?= $a->thumb ?>"/></td>
                            <td><a rel="3" href="<?= l('anuncio', $i) ?>"><?= htmlspecialchars($a->titulo) ?></a></td>
                        </tr>
                    <?php } ?>
                </table>

                <ul>
                    <li><a rel="1" href="<?= l('publicar') ?>">Publicar un anuncio</a></li>
                </ul>

            <?php
                } else if ($p == 'anuncio') {
                    $a = $anuncios[$i];
            ?>

                <h1>Anuncio: <?= htmlspecialchars($a->titulo) ?></h1>
                <div><img src="<?= $a->foto ?>"/></div>
                <p><?= nl2br(htmlspecialchars($a->descripcion)) ?></p>

                <ul>
                    <li><a rel="2" href="<?= l('contactar', $i) ?>">Contactar al anunciante</a></li>
                    <li><a rel="13" href="<?= l('listado') ?>">Volver al listado</a></li>
                    <li><a rel="1" href="<?= l('publicar') ?>">Publicar un anuncio</a></li>
                </ul>

            <?php
                } else if ($p == 'publicar') {
            ?>

                <h1>Publicar un anuncio</h1>
                <p>Un anuncio a sido publicado</p>

                <ul>
                    <li><a rel="1" href="<?= l('listado') ?>">Volver al listado</a></li>
                </ul>

            <?php
                } else if ($p == 'contactar') {
            ?>

                <h1>Contactar al anunciante</h1>
                <p>El anunciante ha sido contactado</p>

                <ul>
                    <li><a rel="1" href="<?= l('anuncio', $i) ?>">Volver al anuncio</a></li>
                </ul>

            <?php
                }
            ?>

            <?php $ab->track(); ?>
        </body>
    </html>
    <?php
});
