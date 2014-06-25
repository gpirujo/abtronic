<?php
require 'datos.php';
$a = $anuncios[$id = $_GET['id']];
?>
<html>
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>

        <h1><?= htmlspecialchars($a->titulo) ?></h1>

        <div><img src="<?= $a->foto ?>"/></div>

        <p><?= nl2br(htmlspecialchars($a->descripcion)) ?></p>

        <ul>
            <li><a href="contactar.php?id=<?= $id ?>">Contactar al anunciante</a></li>
            <li><a href="index.php">Volver al listado</a></li>
            <li><a href="publicar.php">Publicar un anuncio</a></li>
        </ul>

    </body>
</html>
