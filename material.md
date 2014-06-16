A/B Testing en HackDesign: una introducción al tema, la optimización de la campaña de Obama, notas sobre la significatividad estadística, defensa del a/b testing
https://hackdesign.org/lessons/44

The complete guide to a/b testing
https://visualwebsiteoptimizer.com/ab-testing/

Apunte sobre tests multivariados de un curso de Berkeley
http://www.stat.berkeley.edu/~mgoldman/Section0402.pdf

Factorial experiment, experimento multidimensional, del cual el multivariado es una forma (si no entiendo mal)
http://en.wikipedia.org/wiki/Factorial_experiment

Testing multivariado
http://en.wikipedia.org/wiki/Multivariate_testing

How browsers work, de donde puedo sacar la explicación del funcionamiento de los hilos de rendering y scripting y de por qué la variación en client-side es una condición de carrera
http://taligarsiel.com/Projects/howbrowserswork1.htm

Applied multivariate statistical analysis
http://www.im.ufrj.br/ralph/multivariada/livro.pdf

Etsy Feature, una librería de Etsy para hacer feature toggling, pero con probabilidades
https://github.com/etsy/feature

Bug report de la implementación actual, para no olvidar
> Hicimos un test hace un par de semanas con Alan. El test era en Brasil y tenía A, B y control. Dentro de los ```outputIf```, seteamos el channel de AdSense para ver cómo afectaba cada opción la rentabilidad (o como se llame). El tema es que queríamos comparar A, B y control en Brasil, y en control nos estaban entrando los requests a todos los otros países, en los cuales el test estaba apagado.
> 
> Encontramos que sería útil saber, cuando estamos en un ```outputIf($testname, "control")```, si estamos cayendo en la opción de control porque nos tocó esa opción en el dado o porque estamos fuera del test. En nuestro caso, habríamos puesto el channel de control sólo cuando nos tocó control en el dado dentro del test.
> 
> Tendría que ser un condicionamiento con un ```<esi:when>```, como el mismo ```outputIf```, porque también cambia en cada request. Creo que conviene hacerlo separado: una especie de ```outputIfActive($testname, $then, $else)```. Se puede usar para condicionar un bloque específico dentro de un ```outputIf``` de control, o simplemente para diferenciar un bloque entre el país en el que está el test y todos los demás, sin cablear en la vista el país en el que el test aplica.

Multi-armed bandit experiments, un método para alcanzar utilidad de los datos aún sin alcanzar la significatividad estadística clásica (dice Google)
https://support.google.com/analytics/answer/2844870?hl=en

Excel sheet para calcular la significatividad estadística
http://www.kaushik.net/avinash/excellent-analytics-tip1-statistical-significance/

Statistical power, cálculo de la cantidad de muestras necesaria para alcanzar la significatividad estadística
http://en.wikipedia.org/wiki/Statistical_power

Available statistical tests according to goal and type of data
http://www.graphpad.com/support/faqid/1790/

## Papers

1. A Modelled Study of Content Delivery Networks (LEIDO)
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=1642406

2. Towards exploratory hypothesis testing and analysis (LEIDO)
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=5767907

3. Content Delivery Networks: Overlay Networks for Scaling and Enhancing the Web
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=4760416

4. Content delivery networks and their roles in eLearning
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=1338334

5. Modeling object characteristics of dynamic Web content
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=1189026

6. Common fallacies in applying hypothesis testing
http://ieeexplore.ieee.org/xpl/articleDetails.jsp?tp=&arnumber=4632331

7. A precise and efficient evaluation of the proximity of web clients and their local DNS servers (cited in 1)
http://chuck.cranor.org/p/dns-usenix.pdf

8. The probable error of a mean (cited in 2)
http://www.york.ac.uk/depts/maths/histstat/student.pdf
