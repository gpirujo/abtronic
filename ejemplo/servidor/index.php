<?php
require 'datos.php';
?>
<html>
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>

        <h1>Anuncios clasificados</h1>

        <table border="1">
            <?php foreach ($anuncios as $id => $a) { ?>
                <tr>
                    <td><img src="<?= $a->thumb ?>"/></td>
                    <td><a href="anuncio.php?id=<?= $id ?>"><?= htmlspecialchars($a->titulo) ?></a></td>
                </tr>
            <?php } ?>
        </table>

        <ul>
            <li><a href="publicar.php">Publicar un anuncio</a></li>
        </ul>

    </body>
</html>
