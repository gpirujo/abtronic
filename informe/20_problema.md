# Definición del problema

## La técnica de A/B testing

La técnica de A/B Testing se utiliza habitualmente en el desarrollo web para contrastar hipótesis de impacto de cambios del producto en variables de negocio. Consiste en la experimentación aleatorizada y controlada con dos variantes: la variante A o _control_ es el producto original, la variante B o _tratamiento_ es el producto modificado. Se plantea la hipótesis de que una variación particular del producto tendrá una influencia positiva en una variable de negocio y se la expone a un subconjunto de los usuarios, mientras se mide la variable en el conjunto completo. Cuando los resultados alcanzan un umbral de confianza, llamado significatividad estadística, la hipótesis se da por comprobada o refutada según corresponda y la variación se hace permanente o se abandona [1].

La forma usual de aplicación sigue el principio _ceteris paribus_ (que significa "todo lo demás igual"): se aplica una sola variación y todo lo demás permanece invariante. De esta manera, alcanzando la significatividad estadística, se puede atribuir, con el grado de confianza elegido, la variación de las variables de negocio a la variación evaluada en el producto. La técnica permite, sin embargo, llevar a cabo experimentos simultáneos aunque, cuando las variaciones se solapan en el espacio del producto, es necesario tomar precauciones en el procedimiento para evitar dar por comprobada una hipótesis con información insuficiente [2,3]. Asimismo, en la medición de las variables de negocio, se debe tener en cuenta la distribución de las variaciones entre los usuarios para diferenciar la correlación entre los experimentos de los resultados marginales [4].

Todas las implementaciones comparten las siguientes características. Se elige una variación en el producto, una variable de negocio con la cual evaluar el producto variado, y el porcentaje de la población con la cual se va a hacer el experimento. Para cada usuario que llega al punto del producto donde tiene lugar la variación, se "tira un dado" con las probabilidades elegidas para definir si es asignado al grupo de tratamiento o al grupo de control, y se le entrega la versión correspondiente del producto. La correspondencia entre ese usuario particular y su asignación se almacena para entregarle la misma versión del producto en visitas posteriores y que su experiencia sea consistente. En todos los casos se evalúa la variable de negocio y, acumulada una cantidad suficiente de visitas, la comprobación o refutación de la hipótesis se hace observando su valor en cada uno de los grupos.

## A/B testing como requisito

OLX (http://www.olx.com.ar) es una plataforma web de anuncios clasificados, donde los usuarios pueden publicar sus anuncios y contactar a otros anunciantes. Cuenta con un sitio web, un sitio móvil y varias aplicaciones para dispositivos móviles. Está presente en más de cien países y es visitada varios millones de veces por día. Para definir la evolución de su producto, decidió aplicar la técnica de A/B testing.

Existen ya productos en el mercado que implementan la técnica de A/B testing y que pueden ser integrados para aplicar la técnica sin un desarrollo particular. Algunas implementaciones funcionan en el lado servidor de la arquitectura web, tanto como bibliotecas de funciones para lenguajes particulares como en forma de servicios que otros componentes pueden consultar, y se encargan de automáticamente seleccionar las variaciones definidas en la misma aplicación servidor. Otras funcionan en el lado cliente de la arquitectura web, como _scripts_ que se integran en los documentos HTML del producto y aplican las variaciones sobre el documento original una vez generado y recibido por el usuario.

	Ejemplos de implementaciones de A/B testing disponibles en el mercado

	* Del lado del servidor
		* Como bibliotecas de funciones de PHP
			* php A/B test: http://phpabtest.com/
			* phpScenario: http://phpscenario.org/
			* kumite: https://github.com/lwc/kumite
			* php-redis A/B testing: https://github.com/aliasaria/PHP-redis-A-B-Testing
		* Como servicios internos
			* SixPack: http://chairnerd.seatgeek.com/sixpack/
	* Del lado del cliente
		* Optimizely: https://www.optimizely.com/

A continuación se detalla el análisis de estas opciones y la experiencia que se tuvo con ellas en el escenario particular de OLX.

## A/B testing en el servidor

La performance de una aplicación web, desde el punto de vista del usuario, se mide habitualmente con los tiempos que transcurren entre una acción de este y la respuesta de aquella. Una métrica muy usada es el tiempo de transferencia de los objetos que conforman la aplicación, que se descompone en la suma del producto del tamaño del objeto por la velocidad de transferencia, más el tiempo que transcurre desde que se emite el pedido hasta que llega la primera unidad de información, o primer _byte_.

	tiempo_transferencia(objeto) = tamaño(objeto) * velocidad + tiempo_a_primer_byte

Los tres componentes de esta métrica pueden ser optimizados. Los objetos son habitualmente comprimidos en el servidor y descomprimidos en el cliente, para minimizar el tamaño de la transferencia. La velocidad depende del ancho de banda disponible en todos los enlaces del camino entre el servidor y el cliente, y es habitual que el enlace más lento sea el último, el que une al cliente con su ISP (_Internet Service Provider_), y que su optimización esté fuera de alcance. Finalmente, el tiempo hasta el primer byte es la suma del tiempo que la aplicación tarda en generar la respuesta más el tiempo que tardan el pedido y el primer paquete de la respuesta en viajar por la red que la comunica con el cliente: cuanto menor sea la distancia de red entre ellos, cuanto menos saltos haya y menos reenvíos de paquetes ocurran, menor será el tiempo hasta el primer byte.

	tiempo_a_primer_byte = latencia(pedido) + tiempo_generacion + latencia(respuesta)

Hay una fuerte razón comercial para minimizar el tiempo al primer byte: el tiempo que transcurre entre que el usuario hace "click" y que algo comienza a aparecer en la pantalla, está directamente relacionado con la tasa de abandono de un sitio web.

Una forma de disminuir el tiempo promedio de generación de la respuesta es hacer offload al caché. La condición es que los objetos no sean privados ni personalizados.

Una forma de disminuir la latencia es poner el origen cerca del cliente, pero hay clientes en todo el mundo y los orígenes son pocos o uno solo: caching distribuido globalmente.

Una optimización de esa capa de caching es que cada caché, si no tiene una copia de un objeto que le pidieron, y tiene a otro caché cerca, se lo puede pedir antes que al origen. Los cachés en red forman una CDN.

	Una manera de reducir el tiempo hasta el primer byte es, entonces, disminuir la distancia de red entre el servidor y el cliente. El _origen de datos_, el host que genera los objetos de una aplicación web, suele sin embargo estar ubicado en un solo punto geográfico de la Internet. No es infrecuente tener más de un origen, pero tiene un costo transaccional alto y sólo se hace por cuestiones legales o de disponibilidad: nunca de performance. Lo que se ubica cerca del cliente es un _servidor subrogante_, que no genera los objetos de la aplicación sino que se los pide al origen, pero que los guarda para futuros pedidos (esto se llama _caching_). De la segunda vez que le es pedido un objeto en adelante, puede responder con su propia copia, desde una distancia menor, en un tiempo menor. Los servidores subrogantes se distribuyen globalmente, cerca de los grandes focos de clientes de Internet, formando una red que se llama _Red de Entrega de Contenidos_ (o _Content Delivery Network_ en inglés, o _CDN_) [5].

Para que el caching sea transparente, la key de caché debe ser la misma por la que el cliente pide los objetos: la url. Si en una misma url tenemos que dar respuestas distintas dependiendo del cliente y la decisión de cuál respuesta dar se toma en el origen, luego la respuesta no se puede cachear y la CDN no se puede usar. El producto puede funcionar correctamente, pero el tiempo de transferencia crecerá significativamente y la carga en el origen será intolerable.

Una forma que probamos inicialmente fue usar poblaciones estáticas: en lugar de tirar un dado a cada usuario y ponerlo en la población que le toque, mandamos todos los de tal país al tratamiento, y el resto al control. Fue un avance, comparado con no hacer nada, pero rápidamente fue insuficiente. Las diferencias culturales le quitaban a la vez confianza y utilidad a los resultados.

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

La otra alternativa es tomar la decisión del otro lado de la CDN: en el cliente. Un ejemplo de esto es optimizely, que es lo segundo que probamos. Se incluye un script en cada página que, en función del dado, varía el contenido de la página. El código fuente, lo que el cliente recibe, es constante y, por lo tanto, cacheable en todo el camino. Hay, sin embargo, otras limitaciones.

### Flickering

	Los navegadores web usualmente dividen la tarea de presentación de una página entre varios hilos de ejecución, entre los cuales se encuentran, por separado, uno que parsea el documento HTML recibido del servidor y construye un modelo de objetos del documento (_document object model_, o DOM), y otro que lee el DOM y dibuja en la pantalla (_rendering_) [7]. No hay primitivas de sincronización con esos hilos en espacio de usuario.

	Una herramienta de A/B testing que genere las variaciones en el cliente deberá necesariamente esperar a que los elementos a variar estén accesibles en el DOM, y modificarlos antes de que el hilo de _rendering_ los dibuje. Dado que no hay una interfaz de sincronización, la espera debe ser activa y periódica, dejando una condición de carrera que no garantiza que el contenido original no sea visto por el usuario.

### Doble implementación

### Exposición del test al usuario

	Aunque con menor criticidad, que la información completa del experimento quede expuesta al usuario sin una razón de negocio también es inconveniente.

## Entonces...

Queremos A/B testing, que se hace en el servidor o en el cliente, y no nos gusta ni el servidor ni el cliente. Qué hacemos?