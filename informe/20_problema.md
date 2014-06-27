# Problema

## Necesitamos hacer A/B testing


> La técnica permite llevar a cabo experimentos simultáneos, aunque, cuando las variaciones se solapan en el espacio del producto, es necesario hacer ajustes en el procedimiento para evitar dar por comprobada una hipótesis con información insuficiente [2,3]. Asimismo, en la medición de las variables de negocio, se debe tener en cuenta la distribución de las variaciones entre los usuarios para diferenciar la correlación entre los experimentos de los resultados marginales [4].

> Existen en el mercado varias herramientas para la aplicación de la técnica de A/B testing. Algunas son provistas en forma de librerías o módulos que se incorporan a la aplicación web, en el lado servidor de la comunicación web. Otras son provistas en forma de scripts que se ejecutan en el navegador web del usuario, en el lado cliente de la comunicación web. En ambos casos, para cada usuario se elige aleatoriamente una variante y se mide su comportamiento respecto de la variable de negocio.

> * ejemplo de server side: http://phpabtest.com/
> * ejemplo de client side: https://www.optimizely.com/

> la implementación requiere guardar una marca en el cliente (cookie) y luego entregarle siempre la versión que le corresponde: alguien tiene que tomar la decisión en función de la cookie y, de ahí en adelante, dar una respuesta particular para ese cliente

## A/B testing en el servidor

La performance de una aplicación web, desde el punto de vista del usuario, se mide habitualmente con los tiempos que transcurren entre una acción de este y la respuesta de aquella. Una métrica muy usada es el tiempo de transferencia de los objetos que conforman la aplicación, que se descompone en la suma del producto del tamaño del objeto por la velocidad de transferencia, más el tiempo que transcurre desde que se emite el pedido hasta que llega la primera unidad de información, o primer _byte_.

	tiempo_transferencia(objeto) = tamaño(objeto) * velocidad + tiempo_a_primer_byte

Los tres componentes de esta métrica pueden ser optimizados. Los objetos son habitualmente comprimidos en el servidor y descomprimidos en el cliente, para minimizar el tamaño de la transferencia. La velocidad depende del ancho de banda disponible en todos los enlaces del camino entre el servidor y el cliente, y es habitual que el enlace más lento sea el último, el que une al cliente con su ISP (_Internet Service Provider_), y que su optimización esté fuera de alcance. Finalmente, el tiempo hasta el primer byte es la suma del tiempo que la aplicación tarda en generar la respuesta más el tiempo que tardan el pedido y el primer paquete de la respuesta en viajar por la red que la comunica con el cliente: cuanto menor sea la distancia de red entre ellos, cuanto menos saltos haya y menos reenvíos de paquetes ocurran, menor será el tiempo hasta el primer byte.

	tiempo_a_primer_byte = latencia(pedido) + tiempo_generacion + latencia(respuesta)

Hay una fuerte razón comercial para minimizar el tiempo al primer byte: el tiempo que transcurre entre que el usuario hace "click" y que algo comienza a aparecer en la pantalla, está directamente relacionado con la tasa de abandono de un sitio web.

Una forma de disminuir el tiempo promedio de generación de la respuesta es hacer offload al caché. La condición es que los objetos no sean privados ni personalizados.

Una forma de disminuir la latencia es poner el origen cerca del cliente, pero hay clientes en todo el mundo y los orígenes son pocos o uno solo: caching distribuido globalmente.

Una optimización de esa capa de caching es que cada caché, si no tiene una copia de un objeto que le pidieron, y tiene a otro caché cerca, se lo puede pedir antes que al origen. Los cachés en red forman una CDN.

> Una manera de reducir el tiempo hasta el primer byte es, entonces, disminuir la distancia de red entre el servidor y el cliente. El _origen de datos_, el host que genera los objetos de una aplicación web, suele sin embargo estar ubicado en un solo punto geográfico de la Internet. No es infrecuente tener más de un origen, pero tiene un costo transaccional alto y sólo se hace por cuestiones legales o de disponibilidad: nunca de performance. Lo que se ubica cerca del cliente es un _servidor subrogante_, que no genera los objetos de la aplicación sino que se los pide al origen, pero que los guarda para futuros pedidos (esto se llama _caching_). De la segunda vez que le es pedido un objeto en adelante, puede responder con su propia copia, desde una distancia menor, en un tiempo menor. Los servidores subrogantes se distribuyen globalmente, cerca de los grandes focos de clientes de Internet, formando una red que se llama _Red de Entrega de Contenidos_ (o _Content Delivery Network_ en inglés, o _CDN_) [5].

Para que el caching sea transparente, la key de caché debe ser la misma por la que el cliente pide los objetos: la url. Si en una misma url tenemos que dar respuestas distintas dependiendo del cliente y la decisión de cuál respuesta dar se toma en el origen, luego la respuesta no se puede cachear y la CDN no se puede usar. El producto puede funcionar correctamente, pero el tiempo de transferencia crecerá significativamente y la carga en el origen será intolerable.

## A/B testing en el cliente

	* el flickering es ineludible
		* hay rendering antes de que el dom esté listo
			* los scripts detienen la construcción del dom sin inhibir el rendering
		* un polling al dom incompleto
			* falla cuando se hace una vez y hay más elementos
			* falla cuando se reitera y no es idempotente
			* una marca de los elementos contamina el dom
			* incrementa la carga y la demora
	* la implementación en js no es la definitiva
		* cuando termina el test hay que reimplementar en server side
		* la implementación en js NO es más barata
	* el test queda expuesto al usuario

La otra alternativa es tomar la decisión del otro lado de la CDN: en el cliente. Se incluye un script en cada página que, en función del dado, varía el contenido de la página. El código fuente, lo que el cliente recibe, es constante y, por lo tanto, cacheable en todo el camino. Hay, sin embargo, otras limitaciones.

### Flickering

> Los navegadores web usualmente dividen la tarea de presentación de una página entre varios hilos de ejecución, entre los cuales se encuentran, por separado, uno que parsea el documento HTML recibido del servidor y construye un modelo de objetos del documento (_document object model_, o DOM), y otro que lee el DOM y dibuja en la pantalla (_rendering_) [7]. No hay primitivas de sincronización con esos hilos en espacio de usuario.

> Una herramienta de A/B testing que genere las variaciones en el cliente deberá necesariamente esperar a que los elementos a variar estén accesibles en el DOM, y modificarlos antes de que el hilo de _rendering_ los dibuje. Dado que no hay una interfaz de sincronización, la espera debe ser activa y periódica, dejando una condición de carrera que no garantiza que el contenido original no sea visto por el usuario.

### Doble implementación

### Exposición del test al usuario

> Aunque con menor criticidad, que la información completa del experimento quede expuesta al usuario sin una razón de negocio también es inconveniente.