<?php
$file = fopen('anuncios.csv', 'r');
while($a = fgetcsv($file)) if ($a[0] == $_GET['id']) break;
fclose($file);
?>
<html>
    <body>
        <h1><?= htmlspecialchars(utf8_decode($a[3])) ?></h1>
        <div><img src="<?= $a[2] ?>"/></div>
        <p><?= nl2br(htmlspecialchars(utf8_decode($a[4]))) ?></p>
        <ul>
            <li><a href="contactar.php?id=<?= $a[0] ?>">Contactar al anunciante</a></li>
            <li><a href="index.php">Volver al listado</a></li>
            <li><a href="publicar.php">Publicar un anuncio</a></li>
        </ul>
    </body>
</html>
