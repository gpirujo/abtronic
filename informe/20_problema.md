# Problema

* necesitamos hacer ab testing
* ab testing en server side no escala
	* cacheamos y usamos una cdn
	* todos los requests deberían llegar al origen
		* no soportamos esa carga ya
		* inutilizaríamos la cdn
* ab testing en client side no satisface
	* la implementación en js no es la definitiva
		* cuando termina el test hay que reimplementar en server side
		* la implementación en js **no** es más barata
	* el flickering es ineludible
		* hay rendering antes de que el dom esté listo
			* los scripts detienen la construcción del dom sin inhibir el rendering
		* un polling al dom incompleto
			* falla cuando se hace una vez y hay más elementos
			* falla cuando se reitera y no es idempotente
			* una marca de los elementos contamina el dom
			* se incrementan la carga y la demora
