<html>
    <head>
        <script>

            function cuandoElDOMEstaListo(f) {
                document.addEventListener('DOMContentLoaded', f);
            }
            function tanProntoAparece(f, g) {
                var elements = f();
                if (elements.length) {
                    for (var i = 0; i < elements.length; i++) {
                        g(elements[i]);
                    }
                } else {
                    setTimeout(function() {
                        tanProntoAparece(f, g);
                    }, 50);
                }
            }
            function desdeQueAparece(f, g) {
                var listo = false;
                cuandoElDOMEstaListo(function() {
                    listo = true;
                });
                (function losQueEsten(f, g) {
                    var elements = f();
                    for (var i = 0; i < elements.length; i++) {
                        g(elements[i]);
                    }
                    if (!listo) {
                        setTimeout(function() {
                            losQueEsten(f, g);
                        }, 50);
                    }
                })(f, g);
            }

            cuandoElDOMEstaListo(function() {
                document.getElementById('dcc').innerText = 'Este texto está reemplazando al original.';
            });
            tanProntoAparece(function() {
                return document.getElementsByClassName('tpa');
            }, function(e) {
                e.style.color = 'red';
            });
            desdeQueAparece(function() {
                return document.getElementsByTagName('li');
            }, function(e) {
                e.innerHTML = '"' + e.innerHTML + '"';
            });

        </script>
        <meta charset="utf-8"/>
    </head>
    <body>

        <h2>Caso 1: cuando el DOM está completamente cargado</h2>
        <p id="dcc">Este es el texto original que será sustituido por otro cuando el DOM esté listo.</p>
        <p>El texto original puede ser visto por el usuario antes de ser sustituido.</p>

        <h2>Caso 2: tan pronto aparece el elemento en el DOM</h2>
        <p class="tpa">Este texto cambiará de color a lo sumo 50 milisegundos después de aparecer en el DOM.</p>
        <script src="delay.php?seconds=1"></script>
        <p class="tpa">Este texto no cambiará de color, porque el evento ya ocurrió antes y la reacción se evalúa una sola vez.</p>

        <h2>Caso 3: todo el tiempo hasta que el DOM esté cargado</h2>
        <p>Los elementos son encontrados todo el tiempo desde que aparecen. Si el cambio no es idempotente, se replica.</p>
        <ul>
            <li>Elemento temprano</li>
            <script src="delay.php?seconds=2"></script>
            <li>Elemento tardío</li>
        </ul>

    </body>
</html>
