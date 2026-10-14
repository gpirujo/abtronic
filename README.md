# ABtronic

Plataforma de A/B testing en PHP cuya variación de contenido se ejecuta en la CDN, usando [ESI](https://www.w3.org/TR/esi-lang) (Edge Side Includes) y Google Analytics para el tracking.

Nació como una necesidad real: en un sitio con tráfico global servido detrás de una CDN (como Akamai), ni la variación del lado del servidor (que inutiliza el cacheo) ni la variación del lado del cliente (que introduce *flickering* por condición de carrera con el renderizado) son una buena opción. La solución es correr la variación en la CDN misma: el servidor de origen emite un documento ESI con todas las variantes, la CDN decide cuál mostrar, y el resultado es cacheable en todo el camino.

Este trabajo fue mi tesis de grado de Ingeniería de Sistemas (UNICEN, 2015). El informe completo y las slides de la defensa están en [`informe/`](informe):

- [Informe completo](<informe/Guillermo Pereyra Irujo - Trabajo Final - Informe.pdf>)
- [Slides de la presentación](<informe/Guillermo Pereyra Irujo - Trabajo Final - Presentación.pdf>)

## Estructura

- **`variator/`** — el módulo central, desarrollado como aporte de la tesis.
  - `ESI.php` — interfaz programática de bajo nivel que genera instrucciones ESI (`<esi:choose>`, `<esi:assign>`, etc.) sin que el desarrollador tenga que escribir el lenguaje de etiquetas a mano.
  - `Future.php` + `Future/` — una abstracción de árbol de expresiones (asignación, condicionales, operadores) que se puede compilar a distintos lenguajes de destino: hay implementaciones para ESI, JS y PHP (`Future/ESI.php`, `Future/JS.php`, `Future/PHP.php`).
  - `ABTronic.php` — la API de alto nivel: define experimentos y variaciones, hace el sorteo ("tirar el dado") y envía los resultados a Google Analytics como dimensiones personalizadas.
  - `ABTester.php` + `ABTester/Test.php` — la implementación previa a la tesis, usada en producción, que se mantiene como referencia/comparación.
- **`analytics/`** — demo de la integración con Google Analytics (`index.php`, `simulate.js`).
- **`ejemplo/`** — comparación lado a lado de las alternativas de variación discutidas en el informe (capítulo 2):
  - `cliente/` — variación en el cliente, con JavaScript.
  - `javascript/` — demo del *flickering* causado por la condición de carrera entre la carga de un recurso externo y el renderizado (`delay.php` simula la demora de red).
  - `servidor/` — variación en el servidor con un caso concreto (fotos vs. miniaturas).

## Estado

Es el prototipo de una tesis, no una librería mantenida: funciona y resuelve el problema que se propuso resolver, pero el código no tiene el pulido de un proyecto productivo (ver la sección "Trabajo futuro" del informe para las limitaciones conocidas).
