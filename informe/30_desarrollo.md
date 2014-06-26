# Desarrollo

* tenemos una cdn activa que interpreta código esi
	* ya usamos esi para otras cosas y es parte del escenario
* ejecutar en la cdn soluciona los problemas de los dos casos
	* no hace falta que los requests lleguen al origen (se conserva el offload)
	* no hace falta variar el contenido en el cliente (no hay flickering)
* tracking con google analytics
	* integración con más analytics que hacemos
	* offload del tracking
* arquitectura
* alternativas
	* ```if (variate) statement``` > ```variate(function() {statement})```
	* un dado > muchos dados
	* página entera > variaciones
	* track de participación con user vars > con custom dimensions
	* track de dado > de test
	* evaluar dado y test siempre o ante el primer uso
* asunciones
	* el javascript está siempre después del html
* pruebas
	* esi en ```url``` y variaciones en ```url?var```
	* esi embebido en página con todas las variaciones
	* track de participación con user vars
	* track de dado
* desarrollo
	* librería de abtesting
	* sitio de pruebas
	* cliente automatizado