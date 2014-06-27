# Introducción

> La técnica de A/B Testing se utiliza habitualmente en el desarrollo web para contrastar hipótesis de impacto de cambios del producto en variables de negocio. Consiste en la experimentación aleatorizada y controlada con dos variantes: la variante A o _control_ es el producto original, la variante B o _tratamiento_ es el producto modificado. Se plantea la hipótesis de que una variación particular del producto tendrá una influencia positiva en una variable de negocio y se la expone a un subconjunto de los usuarios, mientras se mide la variable en el conjunto completo. Cuando los resultados alcanzan un umbral de confianza, llamado significatividad estadística, la hipótesis se da por comprobada o refutada según corresponda y la variación se hace permanente o se abandona [1].

> OLX (http://www.olx.com.ar) es una plataforma web de anuncios clasificados, donde los usuarios pueden publicar sus anuncios y contactar a otros anunciantes. Cuenta con un sitio web, un sitio móvil y varias aplicaciones para dispositivos móviles. Está presente en más de cien países y es visitada varios millones de veces por día. Para definir la evolución de su producto, usa la técnica de A/B Testing.

* etapa 1: hacíamos cambios y los habilitábamos en un país o una categoría
	* eran pruebas simultáneas pero las poblaciones no aleatorias
* etapa 2: contratamos a optimi$€ly
	* es a/b testing correcto pero con flickering y retrabajo
* etapa 3: a/b testing en la cdn

### lo que se viene

> La propuesta de trabajo consiste en el desarrollo de una plataforma de A/B testing cuya herramienta de variación de contenido se ejecute en la red de entrega de contenidos. El servidor de aplicación podrá entregar el conjunto completo de variaciones a la red de entrega y ésta distribuirá las variaciones a los usuarios según sus asignaciones aleatorias particulares. Así, la mayoría de las solicitudes podrán ser satisfechas en la red, conservando la delegación de la carga, y cada usuario recibirá una sola variación sin saber que está formando parte de un experimento.

> La plataforma se completa con un componente de recolección de datos, que muestree las asignaciones aleatorias de variaciones a usuarios y los valores de las variables de negocio, y un componente de reporte, que sumarice los resultados orientando la presentación a la asistencia en toma de decisiones. Como valor agregado, se incluye la automatización de la distribución de las asignaciones, de manera que la correcta medición de correlaciones y resultados marginales no requiera del operador conocimientos especializados.
