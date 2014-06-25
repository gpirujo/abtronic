<?php
$anuncios = array();
$file = fopen('anuncios.csv', 'r');
while($a = fgetcsv($file)) $anuncios[] = $a;
fclose($file);
?>
<html>
    <body>
        <h1 id="titulo">Anuncios clasificados</h1>
        <table border=1>
            <?php foreach ($anuncios as $a) { ?>
                <tr>
                    <td>
                        <a href="anuncio.php?id=<?= $a[0] ?>"><img src="<?= $a[1] ?>"/></a>
                    </td>
                    <td>
                        <a href="anuncio.php?id=<?= $a[0] ?>"><?= htmlspecialchars(utf8_decode($a[3])) ?></a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <ul>
            <li><a href="publicar.php">Publicar un anuncio</a></li>
        </ul>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('titulo').innerText = 'Avisos clasificados';
            })
        </script>
        <script src="delay.php?seconds=1"></script>
    </body>
</html>
