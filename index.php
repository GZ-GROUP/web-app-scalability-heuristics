<?php
$heuristicas_principales = [
    [
        "id" => "H01",
        "titulo" => "Usar caché para datos de acceso frecuente",
        "categoria" => "Caché",
        "frecuencia" => 47.1,
        "fuentes" => 8,
        "consenso" => "Alto",
        "descripcion" => "Almacenar temporalmente los resultados de operaciones costosas o datos consultados con frecuencia en una capa de caché (como Redis o Memcached) para evitar recalcularlos o recuperarlos de la base de datos en cada solicitud.",
        "detalle" => "La base de datos es típicamente el cuello de botella más común en aplicaciones web bajo carga. Cada vez que un usuario solicita los mismos datos, ejecutar una consulta completa desperdicia tiempo de CPU, I/O de disco y conexiones de red. La caché resuelve esto guardando el resultado en memoria por un tiempo determinado, de modo que las solicitudes siguientes se resuelven en microsegundos en lugar de milisegundos. Se aplica a resultados de consultas frecuentes, sesiones de usuario, datos de configuración, respuestas de APIs externas y cualquier dato que cambie poco pero se lea mucho. La clave está en definir una política de invalidación adecuada: ¿cuándo caduca el dato? ¿Se invalida por tiempo (TTL), por evento (al actualizar el registro), o ambos? Una caché mal configurada puede servir datos obsoletos, por lo que la estrategia de invalidación es tan importante como la de almacenamiento.",
        "ejemplo" => "Un perfil de usuario se consulta en cada página. En lugar de hacer SELECT * FROM users WHERE id=? en cada petición, se guarda el resultado en Redis con una clave user:123 y un TTL de 10 minutos. El 95% de las peticiones se resuelven desde memoria sin tocar la base de datos.",
        "academicas" => 3,
        "industriales" => 5
    ],
    [
        "id" => "H02",
        "titulo" => "Usar CDN o edge computing para servir contenido cerca del usuario",
        "categoria" => "Caché",
        "frecuencia" => 47.1,
        "fuentes" => 8,
        "consenso" => "Alto",
        "descripcion" => "Distribuir los activos estáticos (imágenes, CSS, JS, videos) y en algunos casos respuestas de API a través de una red de servidores geográficamente distribuidos (CDN), de modo que cada usuario recibe el contenido desde el nodo más cercano a su ubicación.",
        "detalle" => "La latencia de red es proporcional a la distancia física entre el usuario y el servidor. Si tu servidor está en Virginia y tu usuario está en Tokio, cada solicitud tiene un viaje de ida y vuelta de cientos de milisegundos solo por la velocidad de la luz. Un CDN replica tus activos en docenas o cientos de puntos de presencia (PoPs) alrededor del mundo, de modo que el usuario en Tokio recibe la imagen desde un servidor en Osaka. Esto reduce la latencia percibida, descarga al servidor de origen de servir archivos estáticos (que pueden representar el 70-80% de las solicitudes totales), y mejora la disponibilidad porque el contenido sigue accesible aunque el servidor de origen tenga problemas. Servicios como Cloudflare, AWS CloudFront o Fastly permiten configurar esto con cambios mínimos en el código de la aplicación.",
        "ejemplo" => "Netflix desplegó su propia CDN (Open Connect) dentro de los ISPs para eliminar el tráfico de internet público. Esto le permite servir miles de millones de horas de video al mes con latencia mínima y sin depender de CDNs de terceros.",
        "academicas" => 2,
        "industriales" => 6
    ],
    [
        "id" => "H03",
        "titulo" => "Implementar logging y trazabilidad distribuida",
        "categoria" => "Monitoreo y observabilidad",
        "frecuencia" => 35.3,
        "fuentes" => 6,
        "consenso" => "Alto",
        "descripcion" => "Registrar sistemáticamente los eventos del sistema y correlacionar esos registros con un identificador único de solicitud que atraviese todos los servicios involucrados en procesarla, permitiendo reconstruir el camino completo de cualquier petición.",
        "detalle" => "En un sistema distribuido, una solicitud del usuario puede pasar por un balanceador de carga, un servidor web, tres microservicios distintos y dos bases de datos antes de devolver una respuesta. Cuando algo falla o es lento, saber exactamente dónde ocurrió el problema es imposible sin trazabilidad. El logging registra qué pasó; el tracing correlaciona esos eventos bajo un mismo ID. Herramientas como OpenTelemetry, Jaeger o Zipkin permiten visualizar el árbol completo de una solicitud, ver cuánto tardó cada servicio, y detectar el servicio responsable de una degradación. Sin esto, depurar problemas en producción se convierte en trabajo de adivinanza. Esta heurística no es solo para cuando las cosas fallan: los traces también revelan ineficiencias latentes que no producen errores pero sí degradan la experiencia del usuario.",
        "ejemplo" => "Una solicitud llega al API gateway en 800ms cuando debería tardar 50ms. Con distributed tracing se descubre que el microservicio de inventario está llamando a la base de datos 47 veces en lugar de una, gracias a un problema de N+1 queries introducido en el último deploy.",
        "academicas" => 3,
        "industriales" => 3
    ],
    [
        "id" => "H04",
        "titulo" => "Diseñar servicios sin estado (stateless)",
        "categoria" => "Arquitectura",
        "frecuencia" => 29.4,
        "fuentes" => 5,
        "consenso" => "Medio-alto",
        "descripcion" => "Diseñar cada servicio o instancia de servidor de modo que no almacene información de sesión o estado local del usuario entre solicitudes. Todo el estado necesario debe residir en un almacén externo compartido (base de datos, caché, token JWT).",
        "detalle" => "Si un servidor guarda la sesión del usuario en su memoria local, ese usuario siempre debe ser dirigido al mismo servidor. Esto crea afinidad de sesión (sticky sessions), que impide distribuir la carga libremente entre instancias. Si ese servidor falla, la sesión se pierde. Si necesitas agregar capacidad agregando más servidores, el balanceador de carga debe rastrear qué usuario va a cuál servidor, añadiendo complejidad. Un servicio sin estado elimina todos estos problemas: cualquier instancia puede atender cualquier solicitud porque no depende de memoria local. El estado del usuario vive en Redis o en un token autofirmado (JWT) que el cliente envía en cada petición. Esto hace que el escalado horizontal sea trivial: agregar más instancias es simplemente levantar más copias del mismo servicio.",
        "ejemplo" => 'En lugar de guardar la sesión en $_SESSION de PHP (que vive en el servidor local), se emite un JWT firmado al hacer login. Cada petición incluye ese token, el servidor lo verifica criptográficamente y extrae la identidad del usuario sin consultar ningún almacén de sesión.',
        "academicas" => 2,
        "industriales" => 3
    ],
    [
        "id" => "H05",
        "titulo" => "Dividir la aplicación en módulos débilmente acoplados",
        "categoria" => "Arquitectura",
        "frecuencia" => 29.4,
        "fuentes" => 5,
        "consenso" => "Medio-alto",
        "descripcion" => "Organizar el código y los servicios en unidades independientes con responsabilidades bien delimitadas, que se comunican entre sí a través de interfaces explícitas (APIs, eventos), minimizando las dependencias directas entre módulos.",
        "detalle" => "Un sistema monolítico donde todo está interconectado escala mal porque cualquier cambio puede afectar cualquier otra parte, los despliegues son de todo o nada, y un fallo en un componente puede tumbar el sistema completo. La modularidad resuelve esto estableciendo límites claros: cada módulo (o microservicio) tiene una responsabilidad única, expone una interfaz estable, y puede ser desplegado, escalado y actualizado de forma independiente. El bajo acoplamiento significa que si el módulo de pagos necesita escalar porque hay una promoción, puedes levantar más instancias de ese módulo sin tocar el resto del sistema. La alta cohesión significa que cada módulo agrupa lógica relacionada, haciendo el código más fácil de entender y mantener. Esta decisión debe tomarse desde el diseño inicial: separar módulos en un sistema ya construido es mucho más costoso que diseñarlos separados desde el principio.",
        "ejemplo" => "Un e-commerce divide su lógica en módulos de catálogo, carrito, pagos y envíos. Durante el Black Friday, solo el módulo de pagos necesita escalar x10. El resto del sistema permanece sin cambios. Si el servicio de envíos falla, los usuarios aún pueden navegar y comprar.",
        "academicas" => 2,
        "industriales" => 3
    ],
    [
        "id" => "H06",
        "titulo" => "Indexar correctamente las columnas consultadas con frecuencia",
        "categoria" => "Base de datos",
        "frecuencia" => 29.4,
        "fuentes" => 5,
        "consenso" => "Medio-alto",
        "descripcion" => "Crear índices de base de datos en las columnas que aparecen frecuentemente en cláusulas WHERE, JOIN, ORDER BY o GROUP BY, de modo que el motor de base de datos pueda localizar los registros relevantes sin escanear toda la tabla.",
        "detalle" => "Sin índices, una consulta SELECT * FROM orders WHERE user_id = 123 en una tabla con 10 millones de filas implica leer cada una de ellas para encontrar las que coinciden. Con un índice en user_id, el motor de base de datos puede localizar directamente las filas relevantes en tiempo logarítmico. Esto puede reducir el tiempo de una consulta de segundos a milisegundos. Sin embargo, los índices no son gratuitos: ocupan espacio en disco y ralentizan las operaciones de escritura (INSERT, UPDATE, DELETE) porque cada escritura debe actualizar también los índices correspondientes. La clave es indexar selectivamente: las columnas consultadas con frecuencia en lecturas, y evitar indexar columnas que se escriben mucho pero se leen poco. Herramientas como EXPLAIN en MySQL/PostgreSQL revelan si una consulta está usando índices o haciendo full table scans.",
        "ejemplo" => "Una aplicación de analytics consulta eventos por user_id y timestamp. Sin índice, la consulta tarda 4.2 segundos en una tabla de 50M registros. Agregando un índice compuesto (user_id, timestamp), la misma consulta tarda 8 milisegundos.",
        "academicas" => 3,
        "industriales" => 2
    ],
    [
        "id" => "H07",
        "titulo" => "Diseñar asumiendo que los componentes van a fallar",
        "categoria" => "Tolerancia a fallos",
        "frecuencia" => 23.5,
        "fuentes" => 4,
        "consenso" => "Medio",
        "descripcion" => "Construir el sistema desde el principio bajo la premisa de que cualquier componente —servidores, bases de datos, servicios externos, conexiones de red— puede fallar en cualquier momento. El diseño debe garantizar que el sistema continúe funcionando (aunque sea de forma degradada) cuando ocurran esos fallos.",
        "detalle" => "En sistemas distribuidos, el fallo parcial no es la excepción sino la norma. AWS, Google y Netflix publican regularmente post-mortems de incidentes en los que componentes individuales fallaron. La diferencia entre un sistema bien diseñado y uno mal diseñado no es si falla, sino cómo falla. Un sistema frágil falla completamente cuando un componente falla. Un sistema resiliente falla de forma degradada: si el servicio de recomendaciones está caído, el usuario aún puede ver el catálogo, aunque sin recomendaciones personalizadas. Las técnicas concretas incluyen: circuit breakers (dejar de llamar a un servicio que está fallando para no colapsar la cadena), retries con backoff exponencial (reintentar con esperas crecientes), bulkheads (aislar recursos para que un fallo no consuma todos los threads), y fallbacks (respuestas por defecto cuando un servicio no responde).",
        "ejemplo" => "Netflix implementa chaos engineering (Chaos Monkey) apagando instancias aleatoriamente en producción durante horas de trabajo. Esto obliga a los equipos a diseñar servicios que sobrevivan fallos, porque saben que ocurrirán.",
        "academicas" => 2,
        "industriales" => 2
    ],
    [
        "id" => "H08",
        "titulo" => "Usar balanceo de carga para distribuir tráfico",
        "categoria" => "Balanceo de carga",
        "frecuencia" => 23.5,
        "fuentes" => 4,
        "consenso" => "Medio",
        "descripcion" => "Colocar un balanceador de carga frente a las instancias del servidor que distribuya las solicitudes entrantes entre ellas según algún algoritmo (round-robin, least connections, IP hash), evitando que una sola instancia reciba más tráfico del que puede manejar.",
        "detalle" => "Un único servidor tiene un límite físico de solicitudes concurrentes que puede manejar. El balanceo de carga permite superar ese límite distribuyendo el trabajo entre múltiples instancias. Además de distribuir carga, el balanceador actúa como punto de control: puede detectar instancias que no responden y dejar de enviarles tráfico (health checks), facilitar deploys sin downtime dirigiendo tráfico solo a instancias ya actualizadas (rolling deployments), y terminar conexiones SSL centralizadamente. Existen balanceadores a nivel de red (L4, como AWS NLB) que operan sobre TCP/UDP, y balanceadores a nivel de aplicación (L7, como AWS ALB o nginx) que pueden enrutar basándose en rutas URL, headers o contenido del cuerpo. La elección depende de la complejidad del enrutamiento requerido.",
        "ejemplo" => "Tres instancias del servidor web atienden tráfico. El balanceador detecta que la instancia 2 no responde a los health checks y deja de enviarle tráfico en menos de 30 segundos, sin que los usuarios noten interrupción del servicio.",
        "academicas" => 1,
        "industriales" => 3
    ],
    [
        "id" => "H09",
        "titulo" => "Usar replicación de base de datos para separar lecturas de escrituras",
        "categoria" => "Base de datos",
        "frecuencia" => 23.5,
        "fuentes" => 4,
        "consenso" => "Medio",
        "descripcion" => "Mantener un nodo primario de base de datos que recibe todas las escrituras, y uno o más nodos réplica que replican los datos del primario y atienden las consultas de lectura, distribuyendo así la carga entre múltiples servidores.",
        "detalle" => "En la mayoría de las aplicaciones web, el 80-90% de las operaciones son lecturas. Si todas esas lecturas y todas las escrituras van al mismo servidor, ese servidor se convierte rápidamente en un cuello de botella. La replicación separa estas cargas: el nodo primario se enfoca en escrituras (que requieren consistencia y transacciones), mientras que varios nodos réplica sirven lecturas de forma paralela. El resultado es que la capacidad de lectura escala horizontalmente agregando más réplicas, sin aumentar la carga sobre el primario. La consideración principal es la replicación asíncrona: existe un pequeño retraso (lag de replicación) entre cuando algo se escribe en el primario y cuando aparece en las réplicas. Para la mayoría de los casos de uso esto es aceptable, pero operaciones que requieren leer inmediatamente lo que acaban de escribir deben dirigirse al primario.",
        "ejemplo" => "Una plataforma de noticias tiene un nodo primario para que los editores publiquen artículos, y tres réplicas de lectura que sirven las páginas públicas. Un pico de tráfico por una noticia viral solo afecta a las réplicas; el sistema de publicación permanece estable.",
        "academicas" => 2,
        "industriales" => 2
    ],
    [
        "id" => "H10",
        "titulo" => "Preferir escalado horizontal sobre vertical",
        "categoria" => "Escalado horizontal/vertical",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Diseñar el sistema para crecer agregando más instancias (servidores, contenedores) en lugar de hacer el servidor existente más potente. El escalado horizontal permite crecimiento prácticamente ilimitado y mayor tolerancia a fallos.",
        "detalle" => "El escalado vertical (scale-up) tiene un límite físico: hay un servidor máximo que puedes comprar, y cada salto de capacidad es un evento de downtime o un costo desproporcionado. El escalado horizontal (scale-out) no tiene ese límite: si necesitas más capacidad, agregas más instancias iguales. Además, un único servidor grande representa un punto único de falla; diez servidores pequeños pueden perder uno sin interrumpir el servicio. El prerequisito para escalar horizontalmente es que el sistema sea stateless (H04) y que los datos compartidos estén externalizados. No siempre es la respuesta correcta para todo: una base de datos transaccional es más difícil de escalar horizontalmente que un servidor web. Por eso esta heurística aplica principalmente a la capa de aplicación, donde es más natural y menos costosa de implementar.",
        "ejemplo" => "Durante el Mundial de Fútbol, una aplicación de estadísticas pasa de 5 a 50 instancias en 10 minutos usando autoescalado. Al terminar el evento, vuelve a 5. Con escalado vertical, ese pico habría requerido migrar a un servidor 10x más potente, con horas de downtime.",
        "academicas" => 2,
        "industriales" => 1
    ],
    [
        "id" => "H11",
        "titulo" => "Procesar tareas pesadas de forma asíncrona",
        "categoria" => "Escalado horizontal/vertical",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Mover las operaciones que consumen tiempo (envío de emails, generación de reportes, procesamiento de imágenes, llamadas a APIs externas lentas) fuera del ciclo de request-response, colocándolas en una cola que las ejecuta en background.",
        "detalle" => "Cuando un usuario hace una solicitud HTTP, mantenerlo esperando 30 segundos mientras se procesa un video es inaceptable y bloquea un thread del servidor durante todo ese tiempo. Las colas de mensajes (RabbitMQ, AWS SQS, Redis Queue) separan la aceptación del trabajo de su ejecución. El servidor acepta la tarea, la encola, y responde inmediatamente al usuario (202 Accepted). Workers independientes toman tareas de la cola y las procesan en segundo plano. Esto tiene múltiples beneficios: el usuario recibe respuesta inmediata, los threads del servidor web se liberan para atender otras solicitudes, los workers pueden escalarse independientemente según el volumen de la cola, y si un worker falla, la tarea vuelve a la cola para ser procesada por otro. También permite absorber picos de tráfico: si llegan mil solicitudes simultáneas, la cola las almacena y los workers las procesan a su ritmo.",
        "ejemplo" => "Al registrarse, un usuario recibe un email de bienvenida. En lugar de llamar al servidor SMTP durante el request (300ms adicionales), se encola el envío. El usuario ve la confirmación de registro en 50ms; el email llega segundos después.",
        "academicas" => 1,
        "industriales" => 2
    ],
    [
        "id" => "H12",
        "titulo" => "Usar computación serverless para escalar automáticamente",
        "categoria" => "Escalado horizontal/vertical",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Implementar componentes de la aplicación como funciones serverless (AWS Lambda, Google Cloud Functions, Vercel Functions) que se ejecutan bajo demanda, escalan automáticamente de cero a miles de instancias según el tráfico, y solo generan costo cuando se ejecutan.",
        "detalle" => "El modelo serverless transfiere la responsabilidad del escalado de infraestructura al proveedor cloud. En lugar de mantener servidores corriendo 24/7 esperando solicitudes, cada invocación de la función levanta su propio entorno de ejecución, procesa la solicitud y termina. El escalado es automático e instantáneo: si llegan mil solicitudes simultáneas, se ejecutan mil instancias de la función en paralelo. Esto es especialmente útil para cargas de trabajo variables o impredecibles, APIs de baja frecuencia donde mantener un servidor encendido no es rentable, y procesamiento de eventos en background. Las limitaciones incluyen la latencia de cold start (la primera invocación puede ser más lenta), límites de tiempo de ejecución, y mayor costo por invocación comparado con instancias dedicadas cuando el tráfico es muy alto y constante.",
        "ejemplo" => "Una startup procesa imágenes subidas por usuarios con una función Lambda. Con 10 usuarios al día, el costo es prácticamente cero. Cuando una campaña viral lleva el tráfico a 100,000 subidas en una hora, la función escala automáticamente sin intervención manual.",
        "academicas" => 1,
        "industriales" => 2
    ],
    [
        "id" => "H13",
        "titulo" => "Monitorear continuamente métricas clave del sistema",
        "categoria" => "Monitoreo y observabilidad",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Recolectar y visualizar en tiempo real las métricas fundamentales del sistema —latencia de respuesta, tasa de errores, uso de CPU y memoria, throughput— con alertas automáticas que notifiquen cuando se superan umbrales críticos.",
        "detalle" => "No se puede escalar lo que no se mide. El monitoreo continuo permite detectar problemas antes de que los usuarios los reporten, identificar tendencias de crecimiento para planificar capacidad, y confirmar que los cambios de arquitectura realmente mejoran el rendimiento. Las métricas fundamentales se agrupan en los cuatro señales doradas de Google SRE: latencia (cuánto tarda el sistema en responder), tráfico (cuántas solicitudes por segundo procesa), errores (qué porcentaje de solicitudes falla) y saturación (cuán lleno está el sistema, en CPU, memoria o disco). Herramientas como Prometheus, Grafana, Datadog o AWS CloudWatch permiten recolectar estas métricas, visualizarlas en dashboards y configurar alertas. Sin monitoreo, se opera a ciegas: los problemas se descubren cuando los usuarios se quejan, no cuando comienzan.",
        "ejemplo" => "Una alerta se dispara cuando la latencia del percentil 95 supera 500ms. El equipo investiga y descubre que una consulta sin índice fue introducida en el último deploy. Se revierte el cambio antes de que el 99% de los usuarios lo noten.",
        "academicas" => 1,
        "industriales" => 2
    ],
    [
        "id" => "H14",
        "titulo" => "Elegir el tipo de base de datos adecuado según el caso de uso",
        "categoria" => "Base de datos",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Seleccionar el motor de base de datos cuyo modelo de datos y garantías de consistencia se alineen con los patrones de acceso del sistema, en lugar de usar siempre una base de datos relacional por defecto.",
        "detalle" => "No existe una base de datos universalmente óptima. Las relacionales (PostgreSQL, MySQL) ofrecen transacciones ACID, joins complejos y esquemas rígidos, ideales cuando la consistencia de datos es crítica (pagos, inventario). Las de documentos (MongoDB) permiten esquemas flexibles y datos anidados, útiles cuando la estructura de los datos varía mucho entre registros. Las de clave-valor (Redis) son extremadamente rápidas para acceso por clave, perfectas para caché y sesiones. Las columnares (Cassandra) escalan horizontalmente de forma nativa y están optimizadas para escrituras masivas y consultas por rangos de tiempo. Las de grafos (Neo4j) modelan relaciones complejas entre entidades de forma natural. Usar PostgreSQL para todo porque es lo conocido puede funcionar al principio, pero llegar a millones de registros con el modelo de datos equivocado puede requerir una migración completa.",
        "ejemplo" => "Un sistema de analítica de eventos usa Cassandra para almacenar millones de eventos por día (optimizado para escrituras) y Redis para los contadores en tiempo real que se muestran en el dashboard (optimizado para lecturas rápidas), mientras que los datos de usuarios siguen en PostgreSQL.",
        "academicas" => 2,
        "industriales" => 1
    ],
    [
        "id" => "H15",
        "titulo" => "Adoptar arquitectura de microservicios sobre monolitos",
        "categoria" => "Arquitectura",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Dividir la aplicación en servicios pequeños e independientes, cada uno con su propia base de datos y responsabilidad de negocio bien definida, desplegados y escalados de forma autónoma.",
        "detalle" => "Los microservicios permiten escalar componentes individuales según sus necesidades específicas, en lugar de escalar todo el sistema. Un servicio de búsqueda con alta demanda puede tener 20 instancias mientras el servicio de reportes tiene 2. Cada equipo puede desarrollar, desplegar y escalar su servicio de forma independiente sin coordinar con otros equipos. Sin embargo, los microservicios introducen complejidad operativa significativa: comunicación en red entre servicios (con su latencia y posibilidad de fallo), gestión de múltiples bases de datos, trazabilidad distribuida, y mayor overhead de infraestructura. La literatura coincide en una advertencia importante: empezar con microservicios en un proyecto nuevo sin conocer aún los límites naturales del dominio suele resultar en una arquitectura mal dividida que es más difícil de mantener que un monolito bien estructurado. La recomendación es empezar con un monolito modular y extraer servicios cuando los cuellos de botella sean evidentes.",
        "ejemplo" => "Amazon comenzó como un monolito en la década de 1990. Migró a microservicios durante los 2000s cuando el crecimiento hizo imposible que cientos de equipos trabajaran en el mismo codebase sin bloquearse mutuamente.",
        "academicas" => 1,
        "industriales" => 2
    ],
    [
        "id" => "H16",
        "titulo" => "Usar contenedores para consistencia de despliegue",
        "categoria" => "CI/CD y despliegue",
        "frecuencia" => 17.6,
        "fuentes" => 3,
        "consenso" => "Medio",
        "descripcion" => "Empaquetar la aplicación y todas sus dependencias en contenedores (Docker) que garantizan que el mismo artefacto se comporta de forma idéntica en desarrollo, pruebas y producción, y que puede desplegarse y escalarse en segundos.",
        "detalle" => "Los contenedores eliminan la clase de problemas 'en mi máquina funciona'. Al empaquetar el código junto con su runtime, dependencias del sistema operativo y configuración en una imagen inmutable, se garantiza que lo que se prueba es exactamente lo que se despliega. Para la escalabilidad, los contenedores son fundamentales porque hacen que cada instancia del servicio sea idéntica e intercambiable: el orquestador (Kubernetes, ECS) puede levantar nuevas instancias en segundos copiando la imagen, sin tiempos de instalación o configuración. También facilitan el escalado horizontal porque cada contenedor es autocontenido y no requiere configuración adicional al levantarse. La inmutabilidad de las imágenes también mejora la seguridad y la auditabilidad: siempre se sabe exactamente qué está corriendo en producción.",
        "ejemplo" => "Un equipo levanta un entorno de 10 instancias del mismo servicio en 45 segundos usando Docker y Kubernetes. Cada instancia es idéntica. Al detectar un bug, hacer rollback a la versión anterior toma 30 segundos: solo cambia la etiqueta de la imagen.",
        "academicas" => 2,
        "industriales" => 1
    ]
];

$heuristicas_secundarias = [
    [
        "id" => "S01",
        "titulo" => "Implementar autoescalado según demanda",
        "categoria" => "Escalado horizontal/vertical",
        "frecuencia" => 11.8,
        "descripcion" => "Configurar reglas que aumenten o disminuyan automáticamente el número de instancias en ejecución según métricas de carga en tiempo real, sin intervención manual.",
        "detalle" => "El autoescalado combina el monitoreo continuo (H13) con el escalado horizontal (H10) de forma automática. Se definen umbrales: si el CPU supera el 70% por más de 5 minutos, agregar 2 instancias; si baja del 30% por 10 minutos, eliminar 1. Esto optimiza costos (no se paga por capacidad ociosa) y garantiza disponibilidad ante picos imprevistos."
    ],
    [
        "id" => "S02",
        "titulo" => "Aplicar sharding de base de datos",
        "categoria" => "Base de datos",
        "frecuencia" => 11.8,
        "descripcion" => "Particionar horizontalmente los datos de una base de datos en múltiples servidores independientes, donde cada servidor contiene un subconjunto de los datos según alguna clave de partición.",
        "detalle" => "Cuando una base de datos supera la capacidad de un solo servidor, el sharding distribuye los datos entre varios. Por ejemplo, usuarios con ID 1-1M en el shard 1, ID 1M-2M en el shard 2. Esto distribuye tanto el almacenamiento como la carga de escritura. La complejidad está en las consultas que necesitan datos de múltiples shards, que deben agregarse en la aplicación."
    ],
    [
        "id" => "S03",
        "titulo" => "Adoptar un enfoque API-first en el diseño",
        "categoria" => "Diseño de APIs",
        "frecuencia" => 11.8,
        "descripcion" => "Diseñar y documentar la API antes de implementar la lógica de negocio, tratando la interfaz como el contrato principal del servicio.",
        "detalle" => "API-first garantiza que los servicios exponen interfaces bien definidas desde el inicio, facilitando el desacoplamiento entre equipos y servicios. La documentación (OpenAPI/Swagger) se convierte en la fuente de verdad. Esto reduce dependencias implícitas y permite que múltiples equipos desarrollen en paralelo contra la misma especificación."
    ],
    [
        "id" => "S04",
        "titulo" => "Usar un API gateway como punto de entrada único",
        "categoria" => "Diseño de APIs",
        "frecuencia" => 11.8,
        "descripcion" => "Centralizar todas las solicitudes entrantes en un único punto que gestiona autenticación, autorización, rate limiting, logging y enrutamiento hacia los servicios internos.",
        "detalle" => "El API gateway evita que cada microservicio tenga que implementar autenticación, rate limiting y logging de forma independiente. Centraliza estas preocupaciones transversales, reduce la superficie de ataque al no exponer servicios internos directamente, y permite cambios en el enrutamiento sin modificar los clientes."
    ],
    [
        "id" => "S05",
        "titulo" => "Aplicar rate limiting en las APIs",
        "categoria" => "Diseño de APIs",
        "frecuencia" => 11.8,
        "descripcion" => "Limitar el número de solicitudes que un cliente o usuario puede hacer en un período de tiempo determinado, protegiendo el sistema de abuso, ataques de denegación de servicio y clientes defectuosos.",
        "detalle" => "Sin rate limiting, un solo cliente mal configurado puede hacer miles de solicitudes por segundo, saturando los recursos del sistema y degradando la experiencia de todos los usuarios. El rate limiting puede aplicarse por IP, por API key, por usuario autenticado, o por ruta específica. Los clientes legítimos raramente superan los límites razonables."
    ],
    [
        "id" => "S06",
        "titulo" => "Usar replicación multi-zona de disponibilidad",
        "categoria" => "Tolerancia a fallos",
        "frecuencia" => 11.8,
        "descripcion" => "Distribuir las instancias del servicio en múltiples zonas de disponibilidad o regiones geográficas para garantizar que un fallo de infraestructura en una zona no interrumpa el servicio completo.",
        "detalle" => "Los proveedores cloud dividen su infraestructura en zonas de disponibilidad (datacenters independientes con energía, red y refrigeración separadas). Desplegar en múltiples zonas garantiza que una falla eléctrica o de red en una zona no afecte a las instancias en otras. Es el fundamento de la alta disponibilidad en entornos cloud."
    ],
    [
        "id" => "S07",
        "titulo" => "Aplicar circuit breakers para evitar fallos en cascada",
        "categoria" => "Tolerancia a fallos",
        "frecuencia" => 11.8,
        "descripcion" => "Implementar el patrón circuit breaker que detecta cuando un servicio dependiente está fallando y deja de llamarlo temporalmente, devolviendo una respuesta de fallback en lugar de esperar timeouts indefinidamente.",
        "detalle" => "Sin circuit breakers, si el servicio A llama al servicio B que está caído, A espera el timeout (varios segundos) en cada solicitud. Esto agota los threads de A, que comienza a fallar también, propagando el fallo en cascada. El circuit breaker detecta la tasa de errores y abre el circuito: en lugar de llamar a B, devuelve inmediatamente una respuesta por defecto. Periódicamente prueba si B se recuperó para cerrar el circuito."
    ],
    [
        "id" => "S08",
        "titulo" => "Diseñar operaciones idempotentes",
        "categoria" => "Tolerancia a fallos",
        "frecuencia" => 5.9,
        "descripcion" => "Diseñar operaciones de modo que ejecutarlas múltiples veces con los mismos parámetros produzca el mismo resultado que ejecutarlas una sola vez, permitiendo reintentos seguros ante fallos de red.",
        "detalle" => "En sistemas distribuidos, las redes fallan y los timeouts ocurren. Cuando un cliente no recibe respuesta, no sabe si la operación se ejecutó o no. Si la operación es idempotente, puede reintentarla con seguridad. Ejemplo: en lugar de POST /orders (crea una orden cada vez), usar PUT /orders/{idempotency-key} que crea la orden si no existe o devuelve la orden existente si ya fue procesada."
    ],
    [
        "id" => "S09",
        "titulo" => "Adoptar arquitectura multi-tier separando capas",
        "categoria" => "Arquitectura",
        "frecuencia" => 5.9,
        "descripcion" => "Separar la aplicación en capas físicamente distintas: presentación (servidor web), lógica de negocio (servidor de aplicación) y datos (base de datos), cada una escalable de forma independiente.",
        "detalle" => "La separación en tiers permite escalar cada capa según sus necesidades: más servidores web para manejar más conexiones HTTP, más servidores de aplicación para más procesamiento de lógica, y más servidores de base de datos para más datos. También mejora la seguridad al limitar qué capa tiene acceso a qué recursos."
    ],
    [
        "id" => "S10",
        "titulo" => "Usar connection pooling para la base de datos",
        "categoria" => "Base de datos",
        "frecuencia" => 5.9,
        "descripcion" => "Mantener un conjunto de conexiones abiertas a la base de datos que se reutilizan entre solicitudes, en lugar de abrir y cerrar una conexión nueva en cada request.",
        "detalle" => "Establecer una conexión a la base de datos tiene un costo no trivial: handshake TCP, autenticación, negociación de parámetros. Con miles de solicitudes por segundo, ese overhead se acumula. El connection pool mantiene N conexiones abiertas permanentemente y las presta a los requests que las necesitan. Cuando el request termina, devuelve la conexión al pool en lugar de cerrarla."
    ],
    [
        "id" => "S11",
        "titulo" => "Implementar CQRS para separar lecturas de escrituras",
        "categoria" => "Base de datos",
        "frecuencia" => 11.8,
        "descripcion" => "Usar modelos y rutas de datos distintos para las operaciones de lectura (queries) y escritura (commands), optimizando cada uno para sus patrones de acceso específicos.",
        "detalle" => "CQRS (Command Query Responsibility Segregation) reconoce que leer y escribir datos tienen requisitos muy distintos. Las escrituras necesitan consistencia y validación; las lecturas necesitan velocidad y flexibilidad de presentación. Separando los modelos, las lecturas pueden usar vistas desnormalizadas optimizadas para consultas específicas, mientras las escrituras usan el modelo normalizado correcto."
    ],
    [
        "id" => "S12",
        "titulo" => "Implementar CI/CD para despliegues frecuentes",
        "categoria" => "CI/CD y despliegue",
        "frecuencia" => 5.9,
        "descripcion" => "Automatizar el pipeline de integración, pruebas y despliegue de modo que cada cambio de código se valide automáticamente y pueda llegar a producción en minutos con mínimo riesgo.",
        "detalle" => "CI/CD no es solo comodidad: es una práctica de escalabilidad organizacional. Cuando los deploys son frecuentes y pequeños, cada cambio tiene un impacto limitado y los errores son fáciles de identificar y revertir. Los deploys grandes y espaciados acumulan riesgos y hacen que los rollbacks sean traumáticos."
    ],
    [
        "id" => "S13",
        "titulo" => "Aplicar lazy loading de recursos no esenciales",
        "categoria" => "Frontend/cliente",
        "frecuencia" => 11.8,
        "descripcion" => "Retrasar la carga de recursos (imágenes, scripts, componentes) que no son necesarios para el renderizado inicial de la página, descargándolos solo cuando el usuario los necesita.",
        "detalle" => "El tiempo de carga inicial de una página determina si el usuario se queda o abandona. Cargar todo desde el principio (imágenes fuera del viewport, componentes de funciones que el usuario quizás nunca use) penaliza ese tiempo de forma innecesaria. Lazy loading prioriza lo visible y difiere el resto."
    ],
    [
        "id" => "S14",
        "titulo" => "Usar orquestación de contenedores para autoescalado",
        "categoria" => "Escalado horizontal/vertical",
        "frecuencia" => 11.8,
        "descripcion" => "Usar plataformas como Kubernetes o Amazon ECS para gestionar automáticamente el ciclo de vida de los contenedores, incluyendo escalado, autorrecuperación ante fallos y distribución de carga.",
        "detalle" => "La orquestación de contenedores automatiza lo que sería imposible hacer manualmente a escala: detectar que una instancia falló y levantar otra en segundos, distribuir instancias entre nodos según recursos disponibles, escalar según métricas de carga, y gestionar actualizaciones sin downtime. Kubernetes se ha convertido en el estándar de facto para esto."
    ],
    [
        "id" => "S15",
        "titulo" => "Basar decisiones de escalado en métricas reales",
        "categoria" => "Monitoreo y observabilidad",
        "frecuencia" => 5.9,
        "descripcion" => "Tomar decisiones de arquitectura y escalado basadas en datos medidos del comportamiento real del sistema, no en suposiciones o tendencias tecnológicas.",
        "detalle" => "La optimización prematura es costosa en tiempo y complejidad. Adoptar microservicios, sharding, o caching sin evidencia de que son necesarios introduce complejidad sin beneficio. El enfoque correcto es medir primero, identificar el cuello de botella real con datos, y luego aplicar la solución específica para ese problema."
    ],
    [
        "id" => "S16",
        "titulo" => "Usar mensajería basada en eventos entre servicios",
        "categoria" => "Arquitectura",
        "frecuencia" => 5.9,
        "descripcion" => "Comunicar servicios mediante la publicación y consumo de eventos a través de un broker de mensajes (Kafka, RabbitMQ), en lugar de llamadas sincrónicas directas entre servicios.",
        "detalle" => "La comunicación sincrónica crea acoplamiento temporal: el servicio A no puede completar su trabajo sin que el servicio B responda. Si B está lento o caído, A sufre. La mensajería por eventos desacopla esta dependencia: A publica un evento 'orden creada' y continúa; B, C y D consumen ese evento cuando pueden. Esto mejora la resiliencia y permite que cada servicio escale independientemente."
    ],
    [
        "id" => "S17",
        "titulo" => "Aplicar chaos engineering para validar resiliencia",
        "categoria" => "Tolerancia a fallos",
        "frecuencia" => 11.8,
        "descripcion" => "Inyectar fallos deliberados en el sistema en condiciones controladas para descubrir debilidades antes de que ocurran en producción de forma no planificada.",
        "detalle" => "El chaos engineering, popularizado por Netflix con Chaos Monkey, parte de la premisa de que la única forma de saber cómo falla un sistema es hacerlo fallar de forma controlada. Se apagan instancias aleatoriamente, se introduce latencia artificial, se cortan conexiones de red. Los problemas descubiertos así tienen solución planificada; los descubiertos en un incidente real tienen presión de tiempo."
    ],
    [
        "id" => "S18",
        "titulo" => "Evitar over-fetching de datos en consultas",
        "categoria" => "Base de datos",
        "frecuencia" => 5.9,
        "descripcion" => "Diseñar las consultas para recuperar únicamente los datos que se necesitan, evitando SELECT * o traer miles de registros cuando solo se necesita saber si existe uno.",
        "detalle" => "Cada byte transferido entre la base de datos y la aplicación consume CPU, memoria y ancho de banda de red. SELECT * en una tabla con 50 columnas cuando solo se necesitan 3 es un desperdicio multiplicado por cada solicitud. Usar EXISTS en lugar de COUNT(*) para verificar existencia, LIMIT para paginar resultados, y seleccionar solo las columnas necesarias son prácticas que reducen la carga de forma proporcional al volumen."
    ],
    [
        "id" => "S19",
        "titulo" => "Implementar paginación en lugar de devolver todos los registros",
        "categoria" => "Diseño de APIs",
        "frecuencia" => 5.9,
        "descripcion" => "Diseñar las APIs para devolver los datos en páginas de tamaño limitado, con mecanismos para navegar entre páginas, en lugar de devolver todos los registros en una sola respuesta.",
        "detalle" => "Una API que devuelve todos los registros de una tabla sin límite es una bomba de tiempo. Con 100 registros funciona bien. Con 100,000 registros, el servidor consume memoria serializing todos los registros, la red transfiere megabytes, y el cliente se cuelga procesando la respuesta. La paginación por offset (LIMIT/OFFSET) o por cursor (WHERE id > last_seen_id) mantiene las respuestas acotadas independientemente del volumen total."
    ],
    [
        "id" => "S20",
        "titulo" => "Definir SLA, SLO y SLI para el sistema",
        "categoria" => "Monitoreo y observabilidad",
        "frecuencia" => 5.9,
        "descripcion" => "Establecer formalmente los acuerdos de nivel de servicio (SLA), objetivos de nivel de servicio (SLO) y los indicadores que los miden (SLI) para tener criterios objetivos de cuándo el sistema está funcionando bien o mal.",
        "detalle" => "Sin definiciones formales de 'qué es aceptable', es imposible saber cuándo hay un problema que merece atención vs. variación normal. Un SLO como 'el P99 de latencia debe ser menor a 500ms' convierte el monitoreo en algo accionable. El SLI es la métrica medida (latencia P99 real). El SLA es el compromiso con el cliente sobre ese objetivo. Juntos crean una cultura de confiabilidad basada en datos."
    ]
];

// =============================================
// I18N (EN default / ES toggle) — real translations, not machine translated
// =============================================
$I18N_JSON = <<<'I18NJSON'
{"H01.titulo": {"es": "Usar caché para datos de acceso frecuente", "en": "Use caching for frequently accessed data"}, "H01.descripcion": {"es": "Almacenar temporalmente los resultados de operaciones costosas o datos consultados con frecuencia en una capa de caché (como Redis o Memcached) para evitar recalcularlos o recuperarlos de la base de datos en cada solicitud.", "en": "Temporarily store the results of expensive operations or frequently queried data in a caching layer (such as Redis or Memcached) to avoid recalculating them or fetching them from the database on every request."}, "H01.detalle": {"es": "La base de datos es típicamente el cuello de botella más común en aplicaciones web bajo carga. Cada vez que un usuario solicita los mismos datos, ejecutar una consulta completa desperdicia tiempo de CPU, I/O de disco y conexiones de red. La caché resuelve esto guardando el resultado en memoria por un tiempo determinado, de modo que las solicitudes siguientes se resuelven en microsegundos en lugar de milisegundos. Se aplica a resultados de consultas frecuentes, sesiones de usuario, datos de configuración, respuestas de APIs externas y cualquier dato que cambie poco pero se lea mucho. La clave está en definir una política de invalidación adecuada: ¿cuándo caduca el dato? ¿Se invalida por tiempo (TTL), por evento (al actualizar el registro), o ambos? Una caché mal configurada puede servir datos obsoletos, por lo que la estrategia de invalidación es tan importante como la de almacenamiento.", "en": "The database is typically the most common bottleneck in web applications under load. Every time a user requests the same data, running a full query wastes CPU time, disk I/O, and network connections. Caching solves this by storing the result in memory for a set period, so subsequent requests resolve in microseconds instead of milliseconds. It applies to frequent query results, user sessions, configuration data, responses from external APIs, and any data that changes rarely but is read often. The key is defining a proper invalidation policy: when does the data expire? Is it invalidated by time (TTL), by event (when the record updates), or both? A poorly configured cache can serve stale data, so the invalidation strategy is just as important as the storage strategy."}, "H01.ejemplo": {"es": "Un perfil de usuario se consulta en cada página. En lugar de hacer SELECT * FROM users WHERE id=? en cada petición, se guarda el resultado en Redis con una clave user:123 y un TTL de 10 minutos. El 95% de las peticiones se resuelven desde memoria sin tocar la base de datos.", "en": "A user profile is queried on every page. Instead of running SELECT * FROM users WHERE id=? on every request, the result is stored in Redis under a key like user:123 with a 10-minute TTL. 95% of requests are resolved from memory without touching the database."}, "H02.titulo": {"es": "Usar CDN o edge computing para servir contenido cerca del usuario", "en": "Use a CDN or edge computing to serve content close to the user"}, "H02.descripcion": {"es": "Distribuir los activos estáticos (imágenes, CSS, JS, videos) y en algunos casos respuestas de API a través de una red de servidores geográficamente distribuidos (CDN), de modo que cada usuario recibe el contenido desde el nodo más cercano a su ubicación.", "en": "Distribute static assets (images, CSS, JS, videos) and, in some cases, API responses across a network of geographically distributed servers (CDN), so each user receives content from the node closest to their location."}, "H02.detalle": {"es": "La latencia de red es proporcional a la distancia física entre el usuario y el servidor. Si tu servidor está en Virginia y tu usuario está en Tokio, cada solicitud tiene un viaje de ida y vuelta de cientos de milisegundos solo por la velocidad de la luz. Un CDN replica tus activos en docenas o cientos de puntos de presencia (PoPs) alrededor del mundo, de modo que el usuario en Tokio recibe la imagen desde un servidor en Osaka. Esto reduce la latencia percibida, descarga al servidor de origen de servir archivos estáticos (que pueden representar el 70-80% de las solicitudes totales), y mejora la disponibilidad porque el contenido sigue accesible aunque el servidor de origen tenga problemas. Servicios como Cloudflare, AWS CloudFront o Fastly permiten configurar esto con cambios mínimos en el código de la aplicación.", "en": "Network latency is proportional to the physical distance between the user and the server. If your server is in Virginia and your user is in Tokyo, every request incurs a round trip of hundreds of milliseconds just from the speed of light. A CDN replicates your assets across dozens or hundreds of points of presence (PoPs) around the world, so a user in Tokyo receives the image from a server in Osaka. This reduces perceived latency, offloads the origin server from serving static files (which can account for 70-80% of total requests), and improves availability because content stays accessible even if the origin server has issues. Services like Cloudflare, AWS CloudFront, or Fastly let you configure this with minimal changes to application code."}, "H02.ejemplo": {"es": "Netflix desplegó su propia CDN (Open Connect) dentro de los ISPs para eliminar el tráfico de internet público. Esto le permite servir miles de millones de horas de video al mes con latencia mínima y sin depender de CDNs de terceros.", "en": "Netflix built its own CDN (Open Connect) inside ISPs to eliminate public internet traffic. This lets it serve billions of hours of video per month with minimal latency without depending on third-party CDNs."}, "H03.titulo": {"es": "Implementar logging y trazabilidad distribuida", "en": "Implement logging and distributed tracing"}, "H03.descripcion": {"es": "Registrar sistemáticamente los eventos del sistema y correlacionar esos registros con un identificador único de solicitud que atraviese todos los servicios involucrados en procesarla, permitiendo reconstruir el camino completo de cualquier petición.", "en": "Systematically log system events and correlate those logs with a unique request identifier that travels through every service involved in processing it, allowing the full path of any request to be reconstructed."}, "H03.detalle": {"es": "En un sistema distribuido, una solicitud del usuario puede pasar por un balanceador de carga, un servidor web, tres microservicios distintos y dos bases de datos antes de devolver una respuesta. Cuando algo falla o es lento, saber exactamente dónde ocurrió el problema es imposible sin trazabilidad. El logging registra qué pasó; el tracing correlaciona esos eventos bajo un mismo ID. Herramientas como OpenTelemetry, Jaeger o Zipkin permiten visualizar el árbol completo de una solicitud, ver cuánto tardó cada servicio, y detectar el servicio responsable de una degradación. Sin esto, depurar problemas en producción se convierte en trabajo de adivinanza. Esta heurística no es solo para cuando las cosas fallan: los traces también revelan ineficiencias latentes que no producen errores pero sí degradan la experiencia del usuario.", "en": "In a distributed system, a user's request can pass through a load balancer, a web server, three different microservices, and two databases before returning a response. When something fails or is slow, knowing exactly where the problem occurred is impossible without traceability. Logging records what happened; tracing correlates those events under a single ID. Tools like OpenTelemetry, Jaeger, or Zipkin let you visualize a request's full tree, see how long each service took, and pinpoint the service responsible for a degradation. Without this, debugging production issues becomes guesswork. This heuristic isn't only for when things break: traces also reveal latent inefficiencies that don't produce errors but do degrade the user experience."}, "H03.ejemplo": {"es": "Una solicitud llega al API gateway en 800ms cuando debería tardar 50ms. Con distributed tracing se descubre que el microservicio de inventario está llamando a la base de datos 47 veces en lugar de una, gracias a un problema de N+1 queries introducido en el último deploy.", "en": "A request reaches the API gateway in 800ms when it should take 50ms. Distributed tracing reveals that the inventory microservice is calling the database 47 times instead of once, due to an N+1 query problem introduced in the last deploy."}, "H04.titulo": {"es": "Diseñar servicios sin estado (stateless)", "en": "Design stateless services"}, "H04.descripcion": {"es": "Diseñar cada servicio o instancia de servidor de modo que no almacene información de sesión o estado local del usuario entre solicitudes. Todo el estado necesario debe residir en un almacén externo compartido (base de datos, caché, token JWT).", "en": "Design each service or server instance so it doesn't store session information or local user state between requests. All necessary state should live in a shared external store (database, cache, JWT token)."}, "H04.detalle": {"es": "Si un servidor guarda la sesión del usuario en su memoria local, ese usuario siempre debe ser dirigido al mismo servidor. Esto crea afinidad de sesión (sticky sessions), que impide distribuir la carga libremente entre instancias. Si ese servidor falla, la sesión se pierde. Si necesitas agregar capacidad agregando más servidores, el balanceador de carga debe rastrear qué usuario va a cuál servidor, añadiendo complejidad. Un servicio sin estado elimina todos estos problemas: cualquier instancia puede atender cualquier solicitud porque no depende de memoria local. El estado del usuario vive en Redis o en un token autofirmado (JWT) que el cliente envía en cada petición. Esto hace que el escalado horizontal sea trivial: agregar más instancias es simplemente levantar más copias del mismo servicio.", "en": "If a server keeps a user's session in its local memory, that user must always be routed to the same server. This creates session affinity (sticky sessions), which prevents load from being freely distributed across instances. If that server fails, the session is lost. If you need to add capacity by adding more servers, the load balancer has to track which user goes to which server, adding complexity. A stateless service eliminates all these problems: any instance can handle any request because it doesn't depend on local memory. User state lives in Redis or in a self-signed token (JWT) that the client sends with every request. This makes horizontal scaling trivial: adding more instances is simply spinning up more copies of the same service."}, "H04.ejemplo": {"es": "En lugar de guardar la sesión en $_SESSION de PHP (que vive en el servidor local), se emite un JWT firmado al hacer login. Cada petición incluye ese token, el servidor lo verifica criptográficamente y extrae la identidad del usuario sin consultar ningún almacén de sesión.", "en": "Instead of storing the session in PHP's $_SESSION (which lives on the local server), a signed JWT is issued at login. Every request includes that token; the server verifies it cryptographically and extracts the user's identity without querying any session store."}, "H05.titulo": {"es": "Dividir la aplicación en módulos débilmente acoplados", "en": "Split the application into loosely coupled modules"}, "H05.descripcion": {"es": "Organizar el código y los servicios en unidades independientes con responsabilidades bien delimitadas, que se comunican entre sí a través de interfaces explícitas (APIs, eventos), minimizando las dependencias directas entre módulos.", "en": "Organize code and services into independent units with well-defined responsibilities that communicate through explicit interfaces (APIs, events), minimizing direct dependencies between modules."}, "H05.detalle": {"es": "Un sistema monolítico donde todo está interconectado escala mal porque cualquier cambio puede afectar cualquier otra parte, los despliegues son de todo o nada, y un fallo en un componente puede tumbar el sistema completo. La modularidad resuelve esto estableciendo límites claros: cada módulo (o microservicio) tiene una responsabilidad única, expone una interfaz estable, y puede ser desplegado, escalado y actualizado de forma independiente. El bajo acoplamiento significa que si el módulo de pagos necesita escalar porque hay una promoción, puedes levantar más instancias de ese módulo sin tocar el resto del sistema. La alta cohesión significa que cada módulo agrupa lógica relacionada, haciendo el código más fácil de entender y mantener. Esta decisión debe tomarse desde el diseño inicial: separar módulos en un sistema ya construido es mucho más costoso que diseñarlos separados desde el principio.", "en": "A monolithic system where everything is interconnected scales poorly because any change can affect any other part, deployments are all-or-nothing, and a failure in one component can bring down the entire system. Modularity solves this by establishing clear boundaries: each module (or microservice) has a single responsibility, exposes a stable interface, and can be deployed, scaled, and updated independently. Low coupling means that if the payments module needs to scale because of a promotion, you can spin up more instances of just that module without touching the rest of the system. High cohesion means each module groups related logic, making the code easier to understand and maintain. This decision should be made from the initial design: separating modules in an already-built system is much more costly than designing them separately from the start."}, "H05.ejemplo": {"es": "Un e-commerce divide su lógica en módulos de catálogo, carrito, pagos y envíos. Durante el Black Friday, solo el módulo de pagos necesita escalar x10. El resto del sistema permanece sin cambios. Si el servicio de envíos falla, los usuarios aún pueden navegar y comprar.", "en": "An e-commerce platform splits its logic into catalog, cart, payments, and shipping modules. During Black Friday, only the payments module needs to scale 10x. The rest of the system remains unchanged. If the shipping service fails, users can still browse and buy."}, "H06.titulo": {"es": "Indexar correctamente las columnas consultadas con frecuencia", "en": "Properly index frequently queried columns"}, "H06.descripcion": {"es": "Crear índices de base de datos en las columnas que aparecen frecuentemente en cláusulas WHERE, JOIN, ORDER BY o GROUP BY, de modo que el motor de base de datos pueda localizar los registros relevantes sin escanear toda la tabla.", "en": "Create database indexes on columns that frequently appear in WHERE, JOIN, ORDER BY, or GROUP BY clauses, so the database engine can locate relevant rows without scanning the entire table."}, "H06.detalle": {"es": "Sin índices, una consulta SELECT * FROM orders WHERE user_id = 123 en una tabla con 10 millones de filas implica leer cada una de ellas para encontrar las que coinciden. Con un índice en user_id, el motor de base de datos puede localizar directamente las filas relevantes en tiempo logarítmico. Esto puede reducir el tiempo de una consulta de segundos a milisegundos. Sin embargo, los índices no son gratuitos: ocupan espacio en disco y ralentizan las operaciones de escritura (INSERT, UPDATE, DELETE) porque cada escritura debe actualizar también los índices correspondientes. La clave es indexar selectivamente: las columnas consultadas con frecuencia en lecturas, y evitar indexar columnas que se escriben mucho pero se leen poco. Herramientas como EXPLAIN en MySQL/PostgreSQL revelan si una consulta está usando índices o haciendo full table scans.", "en": "Without indexes, a query like SELECT * FROM orders WHERE user_id = 123 on a table with 10 million rows means reading every single row to find the matches. With an index on user_id, the database engine can directly locate the relevant rows in logarithmic time. This can reduce a query's runtime from seconds to milliseconds. However, indexes aren't free: they take up disk space and slow down write operations (INSERT, UPDATE, DELETE) because every write must also update the corresponding indexes. The key is to index selectively: columns frequently read, while avoiding indexing columns that are written often but rarely read. Tools like EXPLAIN in MySQL/PostgreSQL reveal whether a query is using indexes or doing full table scans."}, "H06.ejemplo": {"es": "Una aplicación de analytics consulta eventos por user_id y timestamp. Sin índice, la consulta tarda 4.2 segundos en una tabla de 50M registros. Agregando un índice compuesto (user_id, timestamp), la misma consulta tarda 8 milisegundos.", "en": "An analytics application queries events by user_id and timestamp. Without an index, the query takes 4.2 seconds on a table of 50M records. Adding a composite index (user_id, timestamp), the same query takes 8 milliseconds."}, "H07.titulo": {"es": "Diseñar asumiendo que los componentes van a fallar", "en": "Design assuming components will fail"}, "H07.descripcion": {"es": "Construir el sistema desde el principio bajo la premisa de que cualquier componente —servidores, bases de datos, servicios externos, conexiones de red— puede fallar en cualquier momento. El diseño debe garantizar que el sistema continúe funcionando (aunque sea de forma degradada) cuando ocurran esos fallos.", "en": "Build the system from the start under the assumption that any component — servers, databases, external services, network connections — can fail at any moment. The design must ensure the system keeps working (even if in a degraded form) when those failures occur."}, "H07.detalle": {"es": "En sistemas distribuidos, el fallo parcial no es la excepción sino la norma. AWS, Google y Netflix publican regularmente post-mortems de incidentes en los que componentes individuales fallaron. La diferencia entre un sistema bien diseñado y uno mal diseñado no es si falla, sino cómo falla. Un sistema frágil falla completamente cuando un componente falla. Un sistema resiliente falla de forma degradada: si el servicio de recomendaciones está caído, el usuario aún puede ver el catálogo, aunque sin recomendaciones personalizadas. Las técnicas concretas incluyen: circuit breakers (dejar de llamar a un servicio que está fallando para no colapsar la cadena), retries con backoff exponencial (reintentar con esperas crecientes), bulkheads (aislar recursos para que un fallo no consuma todos los threads), y fallbacks (respuestas por defecto cuando un servicio no responde).", "en": "In distributed systems, partial failure isn't the exception, it's the norm. AWS, Google, and Netflix regularly publish post-mortems of incidents where individual components failed. The difference between a well-designed system and a poorly designed one isn't whether it fails, but how it fails. A fragile system fails completely when one component fails. A resilient system fails gracefully: if the recommendations service is down, the user can still browse the catalog, just without personalized recommendations. Concrete techniques include: circuit breakers (stop calling a failing service to avoid collapsing the chain), retries with exponential backoff (retry with increasing wait times), bulkheads (isolate resources so a failure doesn't consume all threads), and fallbacks (default responses when a service doesn't respond)."}, "H07.ejemplo": {"es": "Netflix implementa chaos engineering (Chaos Monkey) apagando instancias aleatoriamente en producción durante horas de trabajo. Esto obliga a los equipos a diseñar servicios que sobrevivan fallos, porque saben que ocurrirán.", "en": "Netflix practices chaos engineering (Chaos Monkey), randomly shutting down instances in production during work hours. This forces teams to design services that survive failures, because they know failures will happen."}, "H08.titulo": {"es": "Usar balanceo de carga para distribuir tráfico", "en": "Use load balancing to distribute traffic"}, "H08.descripcion": {"es": "Colocar un balanceador de carga frente a las instancias del servidor que distribuya las solicitudes entrantes entre ellas según algún algoritmo (round-robin, least connections, IP hash), evitando que una sola instancia reciba más tráfico del que puede manejar.", "en": "Place a load balancer in front of server instances that distributes incoming requests among them using some algorithm (round-robin, least connections, IP hash), preventing a single instance from receiving more traffic than it can handle."}, "H08.detalle": {"es": "Un único servidor tiene un límite físico de solicitudes concurrentes que puede manejar. El balanceo de carga permite superar ese límite distribuyendo el trabajo entre múltiples instancias. Además de distribuir carga, el balanceador actúa como punto de control: puede detectar instancias que no responden y dejar de enviarles tráfico (health checks), facilitar deploys sin downtime dirigiendo tráfico solo a instancias ya actualizadas (rolling deployments), y terminar conexiones SSL centralizadamente. Existen balanceadores a nivel de red (L4, como AWS NLB) que operan sobre TCP/UDP, y balanceadores a nivel de aplicación (L7, como AWS ALB o nginx) que pueden enrutar basándose en rutas URL, headers o contenido del cuerpo. La elección depende de la complejidad del enrutamiento requerido.", "en": "A single server has a physical limit on the concurrent requests it can handle. Load balancing lets you exceed that limit by distributing work across multiple instances. Besides distributing load, the balancer acts as a control point: it can detect unresponsive instances and stop sending them traffic (health checks), enable zero-downtime deploys by routing traffic only to already-updated instances (rolling deployments), and terminate SSL connections centrally. There are network-level balancers (L4, like AWS NLB) that operate over TCP/UDP, and application-level balancers (L7, like AWS ALB or nginx) that can route based on URL paths, headers, or body content. The choice depends on the complexity of routing required."}, "H08.ejemplo": {"es": "Tres instancias del servidor web atienden tráfico. El balanceador detecta que la instancia 2 no responde a los health checks y deja de enviarle tráfico en menos de 30 segundos, sin que los usuarios noten interrupción del servicio.", "en": "Three web server instances handle traffic. The balancer detects that instance 2 isn't responding to health checks and stops sending it traffic within 30 seconds, without users noticing any service interruption."}, "H09.titulo": {"es": "Usar replicación de base de datos para separar lecturas de escrituras", "en": "Use database replication to separate reads from writes"}, "H09.descripcion": {"es": "Mantener un nodo primario de base de datos que recibe todas las escrituras, y uno o más nodos réplica que replican los datos del primario y atienden las consultas de lectura, distribuyendo así la carga entre múltiples servidores.", "en": "Maintain a primary database node that receives all writes, and one or more replica nodes that replicate data from the primary and handle read queries, distributing load across multiple servers."}, "H09.detalle": {"es": "En la mayoría de las aplicaciones web, el 80-90% de las operaciones son lecturas. Si todas esas lecturas y todas las escrituras van al mismo servidor, ese servidor se convierte rápidamente en un cuello de botella. La replicación separa estas cargas: el nodo primario se enfoca en escrituras (que requieren consistencia y transacciones), mientras que varios nodos réplica sirven lecturas de forma paralela. El resultado es que la capacidad de lectura escala horizontalmente agregando más réplicas, sin aumentar la carga sobre el primario. La consideración principal es la replicación asíncrona: existe un pequeño retraso (lag de replicación) entre cuando algo se escribe en el primario y cuando aparece en las réplicas. Para la mayoría de los casos de uso esto es aceptable, pero operaciones que requieren leer inmediatamente lo que acaban de escribir deben dirigirse al primario.", "en": "In most web applications, 80-90% of operations are reads. If all reads and writes go to the same server, that server quickly becomes a bottleneck. Replication separates these workloads: the primary node focuses on writes (which require consistency and transactions), while several replica nodes serve reads in parallel. The result is that read capacity scales horizontally by adding more replicas, without increasing load on the primary. The main consideration is asynchronous replication: there's a small delay (replication lag) between when something is written to the primary and when it appears on the replicas. For most use cases this is acceptable, but operations that need to immediately read what they just wrote should be directed to the primary."}, "H09.ejemplo": {"es": "Una plataforma de noticias tiene un nodo primario para que los editores publiquen artículos, y tres réplicas de lectura que sirven las páginas públicas. Un pico de tráfico por una noticia viral solo afecta a las réplicas; el sistema de publicación permanece estable.", "en": "A news platform has a primary node for editors to publish articles, and three read replicas serving the public pages. A traffic spike from a viral story only affects the replicas; the publishing system remains stable."}, "H10.titulo": {"es": "Preferir escalado horizontal sobre vertical", "en": "Prefer horizontal over vertical scaling"}, "H10.descripcion": {"es": "Diseñar el sistema para crecer agregando más instancias (servidores, contenedores) en lugar de hacer el servidor existente más potente. El escalado horizontal permite crecimiento prácticamente ilimitado y mayor tolerancia a fallos.", "en": "Design the system to grow by adding more instances (servers, containers) rather than making the existing server more powerful. Horizontal scaling allows for practically unlimited growth and greater fault tolerance."}, "H10.detalle": {"es": "El escalado vertical (scale-up) tiene un límite físico: hay un servidor máximo que puedes comprar, y cada salto de capacidad es un evento de downtime o un costo desproporcionado. El escalado horizontal (scale-out) no tiene ese límite: si necesitas más capacidad, agregas más instancias iguales. Además, un único servidor grande representa un punto único de falla; diez servidores pequeños pueden perder uno sin interrumpir el servicio. El prerequisito para escalar horizontalmente es que el sistema sea stateless (H04) y que los datos compartidos estén externalizados. No siempre es la respuesta correcta para todo: una base de datos transaccional es más difícil de escalar horizontalmente que un servidor web. Por eso esta heurística aplica principalmente a la capa de aplicación, donde es más natural y menos costosa de implementar.", "en": "Vertical scaling (scale-up) has a physical limit: there's a maximum server you can buy, and each capacity jump is a downtime event or a disproportionate cost. Horizontal scaling (scale-out) has no such limit: if you need more capacity, you add more identical instances. Also, a single large server is a single point of failure; ten small servers can lose one without interrupting service. The prerequisite for horizontal scaling is that the system be stateless (H04) and that shared data be externalized. It isn't always the right answer for everything: a transactional database is harder to scale horizontally than a web server. That's why this heuristic mainly applies to the application layer, where it's more natural and less costly to implement."}, "H10.ejemplo": {"es": "Durante el Mundial de Fútbol, una aplicación de estadísticas pasa de 5 a 50 instancias en 10 minutos usando autoescalado. Al terminar el evento, vuelve a 5. Con escalado vertical, ese pico habría requerido migrar a un servidor 10x más potente, con horas de downtime.", "en": "During the World Cup, a stats application goes from 5 to 50 instances in 10 minutes using autoscaling. Once the event ends, it scales back to 5. With vertical scaling, that spike would have required migrating to a server 10x more powerful, with hours of downtime."}, "H11.titulo": {"es": "Procesar tareas pesadas de forma asíncrona", "en": "Process heavy tasks asynchronously"}, "H11.descripcion": {"es": "Mover las operaciones que consumen tiempo (envío de emails, generación de reportes, procesamiento de imágenes, llamadas a APIs externas lentas) fuera del ciclo de request-response, colocándolas en una cola que las ejecuta en background.", "en": "Move time-consuming operations (sending emails, generating reports, processing images, calls to slow external APIs) out of the request-response cycle by placing them in a queue that executes them in the background."}, "H11.detalle": {"es": "Cuando un usuario hace una solicitud HTTP, mantenerlo esperando 30 segundos mientras se procesa un video es inaceptable y bloquea un thread del servidor durante todo ese tiempo. Las colas de mensajes (RabbitMQ, AWS SQS, Redis Queue) separan la aceptación del trabajo de su ejecución. El servidor acepta la tarea, la encola, y responde inmediatamente al usuario (202 Accepted). Workers independientes toman tareas de la cola y las procesan en segundo plano. Esto tiene múltiples beneficios: el usuario recibe respuesta inmediata, los threads del servidor web se liberan para atender otras solicitudes, los workers pueden escalarse independientemente según el volumen de la cola, y si un worker falla, la tarea vuelve a la cola para ser procesada por otro. También permite absorber picos de tráfico: si llegan mil solicitudes simultáneas, la cola las almacena y los workers las procesan a su ritmo.", "en": "When a user makes an HTTP request, keeping them waiting 30 seconds while a video is processed is unacceptable and blocks a server thread the entire time. Message queues (RabbitMQ, AWS SQS, Redis Queue) separate accepting the work from executing it. The server accepts the task, enqueues it, and responds to the user immediately (202 Accepted). Independent workers pull tasks from the queue and process them in the background. This has multiple benefits: the user gets an immediate response, web server threads are freed up to handle other requests, workers can scale independently based on queue volume, and if a worker fails, the task goes back to the queue to be processed by another. It also absorbs traffic spikes: if a thousand simultaneous requests arrive, the queue stores them and workers process them at their own pace."}, "H11.ejemplo": {"es": "Al registrarse, un usuario recibe un email de bienvenida. En lugar de llamar al servidor SMTP durante el request (300ms adicionales), se encola el envío. El usuario ve la confirmación de registro en 50ms; el email llega segundos después.", "en": "Upon signing up, a user receives a welcome email. Instead of calling the SMTP server during the request (adding 300ms), the send is queued. The user sees the signup confirmation in 50ms; the email arrives seconds later."}, "H12.titulo": {"es": "Usar computación serverless para escalar automáticamente", "en": "Use serverless computing to scale automatically"}, "H12.descripcion": {"es": "Implementar componentes de la aplicación como funciones serverless (AWS Lambda, Google Cloud Functions, Vercel Functions) que se ejecutan bajo demanda, escalan automáticamente de cero a miles de instancias según el tráfico, y solo generan costo cuando se ejecutan.", "en": "Implement application components as serverless functions (AWS Lambda, Google Cloud Functions, Vercel Functions) that run on demand, automatically scale from zero to thousands of instances based on traffic, and only incur cost when they execute."}, "H12.detalle": {"es": "El modelo serverless transfiere la responsabilidad del escalado de infraestructura al proveedor cloud. En lugar de mantener servidores corriendo 24/7 esperando solicitudes, cada invocación de la función levanta su propio entorno de ejecución, procesa la solicitud y termina. El escalado es automático e instantáneo: si llegan mil solicitudes simultáneas, se ejecutan mil instancias de la función en paralelo. Esto es especialmente útil para cargas de trabajo variables o impredecibles, APIs de baja frecuencia donde mantener un servidor encendido no es rentable, y procesamiento de eventos en background. Las limitaciones incluyen la latencia de cold start (la primera invocación puede ser más lenta), límites de tiempo de ejecución, y mayor costo por invocación comparado con instancias dedicadas cuando el tráfico es muy alto y constante.", "en": "The serverless model transfers infrastructure scaling responsibility to the cloud provider. Instead of keeping servers running 24/7 waiting for requests, each function invocation spins up its own execution environment, processes the request, and terminates. Scaling is automatic and instant: if a thousand simultaneous requests arrive, a thousand instances of the function run in parallel. This is especially useful for variable or unpredictable workloads, low-frequency APIs where keeping a server running isn't cost-effective, and background event processing. Limitations include cold-start latency (the first invocation can be slower), execution time limits, and higher cost per invocation compared to dedicated instances when traffic is very high and constant."}, "H12.ejemplo": {"es": "Una startup procesa imágenes subidas por usuarios con una función Lambda. Con 10 usuarios al día, el costo es prácticamente cero. Cuando una campaña viral lleva el tráfico a 100,000 subidas en una hora, la función escala automáticamente sin intervención manual.", "en": "A startup processes user-uploaded images with a Lambda function. With 10 users a day, the cost is practically zero. When a viral campaign drives traffic to 100,000 uploads in an hour, the function scales automatically without manual intervention."}, "H13.titulo": {"es": "Monitorear continuamente métricas clave del sistema", "en": "Continuously monitor key system metrics"}, "H13.descripcion": {"es": "Recolectar y visualizar en tiempo real las métricas fundamentales del sistema —latencia de respuesta, tasa de errores, uso de CPU y memoria, throughput— con alertas automáticas que notifiquen cuando se superan umbrales críticos.", "en": "Collect and visualize the system's core metrics in real time — response latency, error rate, CPU and memory usage, throughput — with automated alerts that notify when critical thresholds are exceeded."}, "H13.detalle": {"es": "No se puede escalar lo que no se mide. El monitoreo continuo permite detectar problemas antes de que los usuarios los reporten, identificar tendencias de crecimiento para planificar capacidad, y confirmar que los cambios de arquitectura realmente mejoran el rendimiento. Las métricas fundamentales se agrupan en los cuatro señales doradas de Google SRE: latencia (cuánto tarda el sistema en responder), tráfico (cuántas solicitudes por segundo procesa), errores (qué porcentaje de solicitudes falla) y saturación (cuán lleno está el sistema, en CPU, memoria o disco). Herramientas como Prometheus, Grafana, Datadog o AWS CloudWatch permiten recolectar estas métricas, visualizarlas en dashboards y configurar alertas. Sin monitoreo, se opera a ciegas: los problemas se descubren cuando los usuarios se quejan, no cuando comienzan.", "en": "You can't scale what you don't measure. Continuous monitoring lets you catch problems before users report them, spot growth trends for capacity planning, and confirm that architectural changes actually improve performance. Core metrics are grouped into Google SRE's four golden signals: latency (how long the system takes to respond), traffic (how many requests per second it processes), errors (what percentage of requests fail), and saturation (how full the system is, in CPU, memory, or disk). Tools like Prometheus, Grafana, Datadog, or AWS CloudWatch let you collect these metrics, visualize them in dashboards, and set up alerts. Without monitoring, you're operating blind: problems are discovered when users complain, not when they start."}, "H13.ejemplo": {"es": "Una alerta se dispara cuando la latencia del percentil 95 supera 500ms. El equipo investiga y descubre que una consulta sin índice fue introducida en el último deploy. Se revierte el cambio antes de que el 99% de los usuarios lo noten.", "en": "An alert fires when P95 latency exceeds 500ms. The team investigates and finds that a query without an index was introduced in the last deploy. The change is reverted before 99% of users notice."}, "H14.titulo": {"es": "Elegir el tipo de base de datos adecuado según el caso de uso", "en": "Choose the right database type for the use case"}, "H14.descripcion": {"es": "Seleccionar el motor de base de datos cuyo modelo de datos y garantías de consistencia se alineen con los patrones de acceso del sistema, en lugar de usar siempre una base de datos relacional por defecto.", "en": "Select the database engine whose data model and consistency guarantees align with the system's access patterns, rather than always defaulting to a relational database."}, "H14.detalle": {"es": "No existe una base de datos universalmente óptima. Las relacionales (PostgreSQL, MySQL) ofrecen transacciones ACID, joins complejos y esquemas rígidos, ideales cuando la consistencia de datos es crítica (pagos, inventario). Las de documentos (MongoDB) permiten esquemas flexibles y datos anidados, útiles cuando la estructura de los datos varía mucho entre registros. Las de clave-valor (Redis) son extremadamente rápidas para acceso por clave, perfectas para caché y sesiones. Las columnares (Cassandra) escalan horizontalmente de forma nativa y están optimizadas para escrituras masivas y consultas por rangos de tiempo. Las de grafos (Neo4j) modelan relaciones complejas entre entidades de forma natural. Usar PostgreSQL para todo porque es lo conocido puede funcionar al principio, pero llegar a millones de registros con el modelo de datos equivocado puede requerir una migración completa.", "en": "There's no universally optimal database. Relational databases (PostgreSQL, MySQL) offer ACID transactions, complex joins, and rigid schemas, ideal when data consistency is critical (payments, inventory). Document databases (MongoDB) allow flexible schemas and nested data, useful when data structure varies a lot between records. Key-value stores (Redis) are extremely fast for key-based access, perfect for caching and sessions. Columnar databases (Cassandra) scale horizontally natively and are optimized for massive writes and time-range queries. Graph databases (Neo4j) naturally model complex relationships between entities. Using PostgreSQL for everything because it's familiar can work at first, but reaching millions of records with the wrong data model may require a complete migration."}, "H14.ejemplo": {"es": "Un sistema de analítica de eventos usa Cassandra para almacenar millones de eventos por día (optimizado para escrituras) y Redis para los contadores en tiempo real que se muestran en el dashboard (optimizado para lecturas rápidas), mientras que los datos de usuarios siguen en PostgreSQL.", "en": "An event analytics system uses Cassandra to store millions of events per day (optimized for writes) and Redis for the real-time counters shown on the dashboard (optimized for fast reads), while user data stays in PostgreSQL."}, "H15.titulo": {"es": "Adoptar arquitectura de microservicios sobre monolitos", "en": "Adopt microservices architecture over monoliths"}, "H15.descripcion": {"es": "Dividir la aplicación en servicios pequeños e independientes, cada uno con su propia base de datos y responsabilidad de negocio bien definida, desplegados y escalados de forma autónoma.", "en": "Split the application into small, independent services, each with its own database and well-defined business responsibility, deployed and scaled autonomously."}, "H15.detalle": {"es": "Los microservicios permiten escalar componentes individuales según sus necesidades específicas, en lugar de escalar todo el sistema. Un servicio de búsqueda con alta demanda puede tener 20 instancias mientras el servicio de reportes tiene 2. Cada equipo puede desarrollar, desplegar y escalar su servicio de forma independiente sin coordinar con otros equipos. Sin embargo, los microservicios introducen complejidad operativa significativa: comunicación en red entre servicios (con su latencia y posibilidad de fallo), gestión de múltiples bases de datos, trazabilidad distribuida, y mayor overhead de infraestructura. La literatura coincide en una advertencia importante: empezar con microservicios en un proyecto nuevo sin conocer aún los límites naturales del dominio suele resultar en una arquitectura mal dividida que es más difícil de mantener que un monolito bien estructurado. La recomendación es empezar con un monolito modular y extraer servicios cuando los cuellos de botella sean evidentes.", "en": "Microservices let you scale individual components based on their specific needs, instead of scaling the whole system. A high-demand search service might have 20 instances while the reporting service has 2. Each team can develop, deploy, and scale its service independently without coordinating with other teams. However, microservices introduce significant operational complexity: network communication between services (with its latency and possibility of failure), managing multiple databases, distributed tracing, and greater infrastructure overhead. The literature agrees on an important warning: starting a new project with microservices before knowing the domain's natural boundaries usually results in a poorly divided architecture that's harder to maintain than a well-structured monolith. The recommendation is to start with a modular monolith and extract services once bottlenecks become evident."}, "H15.ejemplo": {"es": "Amazon comenzó como un monolito en la década de 1990. Migró a microservicios durante los 2000s cuando el crecimiento hizo imposible que cientos de equipos trabajaran en el mismo codebase sin bloquearse mutuamente.", "en": "Amazon started as a monolith in the 1990s. It migrated to microservices during the 2000s when growth made it impossible for hundreds of teams to work on the same codebase without blocking each other."}, "H16.titulo": {"es": "Usar contenedores para consistencia de despliegue", "en": "Use containers for deployment consistency"}, "H16.descripcion": {"es": "Empaquetar la aplicación y todas sus dependencias en contenedores (Docker) que garantizan que el mismo artefacto se comporta de forma idéntica en desarrollo, pruebas y producción, y que puede desplegarse y escalarse en segundos.", "en": "Package the application and all its dependencies into containers (Docker) that guarantee the same artifact behaves identically in development, testing, and production, and that can be deployed and scaled in seconds."}, "H16.detalle": {"es": "Los contenedores eliminan la clase de problemas 'en mi máquina funciona'. Al empaquetar el código junto con su runtime, dependencias del sistema operativo y configuración en una imagen inmutable, se garantiza que lo que se prueba es exactamente lo que se despliega. Para la escalabilidad, los contenedores son fundamentales porque hacen que cada instancia del servicio sea idéntica e intercambiable: el orquestador (Kubernetes, ECS) puede levantar nuevas instancias en segundos copiando la imagen, sin tiempos de instalación o configuración. También facilitan el escalado horizontal porque cada contenedor es autocontenido y no requiere configuración adicional al levantarse. La inmutabilidad de las imágenes también mejora la seguridad y la auditabilidad: siempre se sabe exactamente qué está corriendo en producción.", "en": "Containers eliminate the 'works on my machine' class of problems. By packaging code together with its runtime, OS dependencies, and configuration into an immutable image, you guarantee that what's tested is exactly what's deployed. For scalability, containers are fundamental because they make each service instance identical and interchangeable: the orchestrator (Kubernetes, ECS) can spin up new instances in seconds by copying the image, with no installation or configuration time. They also make horizontal scaling easier because each container is self-contained and requires no additional configuration on startup. Image immutability also improves security and auditability: you always know exactly what's running in production."}, "H16.ejemplo": {"es": "Un equipo levanta un entorno de 10 instancias del mismo servicio en 45 segundos usando Docker y Kubernetes. Cada instancia es idéntica. Al detectar un bug, hacer rollback a la versión anterior toma 30 segundos: solo cambia la etiqueta de la imagen.", "en": "A team spins up an environment of 10 instances of the same service in 45 seconds using Docker and Kubernetes. Each instance is identical. When a bug is found, rolling back to the previous version takes 30 seconds: only the image tag changes."}, "S01.titulo": {"es": "Implementar autoescalado según demanda", "en": "Implement demand-based autoscaling"}, "S01.descripcion": {"es": "Configurar reglas que aumenten o disminuyan automáticamente el número de instancias en ejecución según métricas de carga en tiempo real, sin intervención manual.", "en": "Configure rules that automatically increase or decrease the number of running instances based on real-time load metrics, without manual intervention."}, "S01.detalle": {"es": "El autoescalado combina el monitoreo continuo (H13) con el escalado horizontal (H10) de forma automática. Se definen umbrales: si el CPU supera el 70% por más de 5 minutos, agregar 2 instancias; si baja del 30% por 10 minutos, eliminar 1. Esto optimiza costos (no se paga por capacidad ociosa) y garantiza disponibilidad ante picos imprevistos.", "en": "Autoscaling combines continuous monitoring (H13) with horizontal scaling (H10) automatically. Thresholds are defined: if CPU exceeds 70% for more than 5 minutes, add 2 instances; if it drops below 30% for 10 minutes, remove 1. This optimizes costs (you don't pay for idle capacity) and ensures availability during unexpected spikes."}, "S02.titulo": {"es": "Aplicar sharding de base de datos", "en": "Apply database sharding"}, "S02.descripcion": {"es": "Particionar horizontalmente los datos de una base de datos en múltiples servidores independientes, donde cada servidor contiene un subconjunto de los datos según alguna clave de partición.", "en": "Horizontally partition a database's data across multiple independent servers, where each server holds a subset of the data based on some partition key."}, "S02.detalle": {"es": "Cuando una base de datos supera la capacidad de un solo servidor, el sharding distribuye los datos entre varios. Por ejemplo, usuarios con ID 1-1M en el shard 1, ID 1M-2M en el shard 2. Esto distribuye tanto el almacenamiento como la carga de escritura. La complejidad está en las consultas que necesitan datos de múltiples shards, que deben agregarse en la aplicación.", "en": "When a database exceeds the capacity of a single server, sharding distributes data across several. For example, users with ID 1-1M on shard 1, ID 1M-2M on shard 2. This distributes both storage and write load. The complexity lies in queries that need data from multiple shards, which must be aggregated at the application level."}, "S03.titulo": {"es": "Adoptar un enfoque API-first en el diseño", "en": "Adopt an API-first design approach"}, "S03.descripcion": {"es": "Diseñar y documentar la API antes de implementar la lógica de negocio, tratando la interfaz como el contrato principal del servicio.", "en": "Design and document the API before implementing business logic, treating the interface as the service's primary contract."}, "S03.detalle": {"es": "API-first garantiza que los servicios exponen interfaces bien definidas desde el inicio, facilitando el desacoplamiento entre equipos y servicios. La documentación (OpenAPI/Swagger) se convierte en la fuente de verdad. Esto reduce dependencias implícitas y permite que múltiples equipos desarrollen en paralelo contra la misma especificación.", "en": "API-first ensures services expose well-defined interfaces from the start, making decoupling between teams and services easier. Documentation (OpenAPI/Swagger) becomes the source of truth. This reduces implicit dependencies and allows multiple teams to develop in parallel against the same specification."}, "S04.titulo": {"es": "Usar un API gateway como punto de entrada único", "en": "Use an API gateway as a single entry point"}, "S04.descripcion": {"es": "Centralizar todas las solicitudes entrantes en un único punto que gestiona autenticación, autorización, rate limiting, logging y enrutamiento hacia los servicios internos.", "en": "Centralize all incoming requests through a single point that handles authentication, authorization, rate limiting, logging, and routing to internal services."}, "S04.detalle": {"es": "El API gateway evita que cada microservicio tenga que implementar autenticación, rate limiting y logging de forma independiente. Centraliza estas preocupaciones transversales, reduce la superficie de ataque al no exponer servicios internos directamente, y permite cambios en el enrutamiento sin modificar los clientes.", "en": "The API gateway prevents each microservice from having to implement authentication, rate limiting, and logging independently. It centralizes these cross-cutting concerns, reduces the attack surface by not exposing internal services directly, and allows routing changes without modifying clients."}, "S05.titulo": {"es": "Aplicar rate limiting en las APIs", "en": "Apply rate limiting on APIs"}, "S05.descripcion": {"es": "Limitar el número de solicitudes que un cliente o usuario puede hacer en un período de tiempo determinado, protegiendo el sistema de abuso, ataques de denegación de servicio y clientes defectuosos.", "en": "Limit the number of requests a client or user can make within a given time period, protecting the system from abuse, denial-of-service attacks, and misbehaving clients."}, "S05.detalle": {"es": "Sin rate limiting, un solo cliente mal configurado puede hacer miles de solicitudes por segundo, saturando los recursos del sistema y degradando la experiencia de todos los usuarios. El rate limiting puede aplicarse por IP, por API key, por usuario autenticado, o por ruta específica. Los clientes legítimos raramente superan los límites razonables.", "en": "Without rate limiting, a single misconfigured client can make thousands of requests per second, overwhelming system resources and degrading the experience for all users. Rate limiting can be applied by IP, by API key, by authenticated user, or by specific route. Legitimate clients rarely exceed reasonable limits."}, "S06.titulo": {"es": "Usar replicación multi-zona de disponibilidad", "en": "Use multi-availability-zone replication"}, "S06.descripcion": {"es": "Distribuir las instancias del servicio en múltiples zonas de disponibilidad o regiones geográficas para garantizar que un fallo de infraestructura en una zona no interrumpa el servicio completo.", "en": "Distribute service instances across multiple availability zones or geographic regions to ensure an infrastructure failure in one zone doesn't interrupt the entire service."}, "S06.detalle": {"es": "Los proveedores cloud dividen su infraestructura en zonas de disponibilidad (datacenters independientes con energía, red y refrigeración separadas). Desplegar en múltiples zonas garantiza que una falla eléctrica o de red en una zona no afecte a las instancias en otras. Es el fundamento de la alta disponibilidad en entornos cloud.", "en": "Cloud providers divide their infrastructure into availability zones (independent data centers with separate power, network, and cooling). Deploying across multiple zones ensures that a power or network failure in one zone doesn't affect instances in others. It's the foundation of high availability in cloud environments."}, "S07.titulo": {"es": "Aplicar circuit breakers para evitar fallos en cascada", "en": "Apply circuit breakers to prevent cascading failures"}, "S07.descripcion": {"es": "Implementar el patrón circuit breaker que detecta cuando un servicio dependiente está fallando y deja de llamarlo temporalmente, devolviendo una respuesta de fallback en lugar de esperar timeouts indefinidamente.", "en": "Implement the circuit breaker pattern, which detects when a dependent service is failing and temporarily stops calling it, returning a fallback response instead of waiting indefinitely for timeouts."}, "S07.detalle": {"es": "Sin circuit breakers, si el servicio A llama al servicio B que está caído, A espera el timeout (varios segundos) en cada solicitud. Esto agota los threads de A, que comienza a fallar también, propagando el fallo en cascada. El circuit breaker detecta la tasa de errores y abre el circuito: en lugar de llamar a B, devuelve inmediatamente una respuesta por defecto. Periódicamente prueba si B se recuperó para cerrar el circuito.", "en": "Without circuit breakers, if service A calls service B and B is down, A waits for the timeout (several seconds) on every request. This exhausts A's threads, which then start failing too, cascading the failure. The circuit breaker detects the error rate and opens the circuit: instead of calling B, it immediately returns a default response. It periodically tests whether B has recovered to close the circuit again."}, "S08.titulo": {"es": "Diseñar operaciones idempotentes", "en": "Design idempotent operations"}, "S08.descripcion": {"es": "Diseñar operaciones de modo que ejecutarlas múltiples veces con los mismos parámetros produzca el mismo resultado que ejecutarlas una sola vez, permitiendo reintentos seguros ante fallos de red.", "en": "Design operations so that executing them multiple times with the same parameters produces the same result as executing them once, enabling safe retries after network failures."}, "S08.detalle": {"es": "En sistemas distribuidos, las redes fallan y los timeouts ocurren. Cuando un cliente no recibe respuesta, no sabe si la operación se ejecutó o no. Si la operación es idempotente, puede reintentarla con seguridad. Ejemplo: en lugar de POST /orders (crea una orden cada vez), usar PUT /orders/{idempotency-key} que crea la orden si no existe o devuelve la orden existente si ya fue procesada.", "en": "In distributed systems, networks fail and timeouts happen. When a client doesn't get a response, it doesn't know whether the operation ran or not. If the operation is idempotent, it can be safely retried. Example: instead of POST /orders (which creates an order every time), use PUT /orders/{idempotency-key}, which creates the order if it doesn't exist or returns the existing order if it was already processed."}, "S09.titulo": {"es": "Adoptar arquitectura multi-tier separando capas", "en": "Adopt a multi-tier architecture with separated layers"}, "S09.descripcion": {"es": "Separar la aplicación en capas físicamente distintas: presentación (servidor web), lógica de negocio (servidor de aplicación) y datos (base de datos), cada una escalable de forma independiente.", "en": "Separate the application into physically distinct layers: presentation (web server), business logic (application server), and data (database), each independently scalable."}, "S09.detalle": {"es": "La separación en tiers permite escalar cada capa según sus necesidades: más servidores web para manejar más conexiones HTTP, más servidores de aplicación para más procesamiento de lógica, y más servidores de base de datos para más datos. También mejora la seguridad al limitar qué capa tiene acceso a qué recursos.", "en": "Separating into tiers lets you scale each layer according to its needs: more web servers to handle more HTTP connections, more application servers for more logic processing, and more database servers for more data. It also improves security by limiting which layer has access to which resources."}, "S10.titulo": {"es": "Usar connection pooling para la base de datos", "en": "Use connection pooling for the database"}, "S10.descripcion": {"es": "Mantener un conjunto de conexiones abiertas a la base de datos que se reutilizan entre solicitudes, en lugar de abrir y cerrar una conexión nueva en cada request.", "en": "Maintain a set of open connections to the database that are reused across requests, instead of opening and closing a new connection on every request."}, "S10.detalle": {"es": "Establecer una conexión a la base de datos tiene un costo no trivial: handshake TCP, autenticación, negociación de parámetros. Con miles de solicitudes por segundo, ese overhead se acumula. El connection pool mantiene N conexiones abiertas permanentemente y las presta a los requests que las necesitan. Cuando el request termina, devuelve la conexión al pool en lugar de cerrarla.", "en": "Establishing a database connection has a non-trivial cost: TCP handshake, authentication, parameter negotiation. With thousands of requests per second, that overhead adds up. The connection pool keeps N connections permanently open and lends them out to requests that need them. When the request finishes, it returns the connection to the pool instead of closing it."}, "S11.titulo": {"es": "Implementar CQRS para separar lecturas de escrituras", "en": "Implement CQRS to separate reads from writes"}, "S11.descripcion": {"es": "Usar modelos y rutas de datos distintos para las operaciones de lectura (queries) y escritura (commands), optimizando cada uno para sus patrones de acceso específicos.", "en": "Use separate models and data paths for read operations (queries) and write operations (commands), optimizing each for its specific access patterns."}, "S11.detalle": {"es": "CQRS (Command Query Responsibility Segregation) reconoce que leer y escribir datos tienen requisitos muy distintos. Las escrituras necesitan consistencia y validación; las lecturas necesitan velocidad y flexibilidad de presentación. Separando los modelos, las lecturas pueden usar vistas desnormalizadas optimizadas para consultas específicas, mientras las escrituras usan el modelo normalizado correcto.", "en": "CQRS (Command Query Responsibility Segregation) recognizes that reading and writing data have very different requirements. Writes need consistency and validation; reads need speed and presentation flexibility. By separating the models, reads can use denormalized views optimized for specific queries, while writes use the correct normalized model."}, "S12.titulo": {"es": "Implementar CI/CD para despliegues frecuentes", "en": "Implement CI/CD for frequent deployments"}, "S12.descripcion": {"es": "Automatizar el pipeline de integración, pruebas y despliegue de modo que cada cambio de código se valide automáticamente y pueda llegar a producción en minutos con mínimo riesgo.", "en": "Automate the integration, testing, and deployment pipeline so every code change is automatically validated and can reach production in minutes with minimal risk."}, "S12.detalle": {"es": "CI/CD no es solo comodidad: es una práctica de escalabilidad organizacional. Cuando los deploys son frecuentes y pequeños, cada cambio tiene un impacto limitado y los errores son fáciles de identificar y revertir. Los deploys grandes y espaciados acumulan riesgos y hacen que los rollbacks sean traumáticos.", "en": "CI/CD isn't just convenience: it's an organizational scalability practice. When deploys are frequent and small, each change has a limited impact and errors are easy to identify and revert. Large, infrequent deploys accumulate risk and make rollbacks painful."}, "S13.titulo": {"es": "Aplicar lazy loading de recursos no esenciales", "en": "Apply lazy loading for non-essential resources"}, "S13.descripcion": {"es": "Retrasar la carga de recursos (imágenes, scripts, componentes) que no son necesarios para el renderizado inicial de la página, descargándolos solo cuando el usuario los necesita.", "en": "Delay loading resources (images, scripts, components) that aren't needed for the initial page render, downloading them only when the user needs them."}, "S13.detalle": {"es": "El tiempo de carga inicial de una página determina si el usuario se queda o abandona. Cargar todo desde el principio (imágenes fuera del viewport, componentes de funciones que el usuario quizás nunca use) penaliza ese tiempo de forma innecesaria. Lazy loading prioriza lo visible y difiere el resto.", "en": "A page's initial load time determines whether the user stays or leaves. Loading everything upfront (off-screen images, components for features the user may never use) unnecessarily penalizes that time. Lazy loading prioritizes what's visible and defers the rest."}, "S14.titulo": {"es": "Usar orquestación de contenedores para autoescalado", "en": "Use container orchestration for autoscaling"}, "S14.descripcion": {"es": "Usar plataformas como Kubernetes o Amazon ECS para gestionar automáticamente el ciclo de vida de los contenedores, incluyendo escalado, autorrecuperación ante fallos y distribución de carga.", "en": "Use platforms like Kubernetes or Amazon ECS to automatically manage container lifecycles, including scaling, self-healing after failures, and load distribution."}, "S14.detalle": {"es": "La orquestación de contenedores automatiza lo que sería imposible hacer manualmente a escala: detectar que una instancia falló y levantar otra en segundos, distribuir instancias entre nodos según recursos disponibles, escalar según métricas de carga, y gestionar actualizaciones sin downtime. Kubernetes se ha convertido en el estándar de facto para esto.", "en": "Container orchestration automates what would be impossible to do manually at scale: detecting that an instance failed and spinning up another in seconds, distributing instances across nodes based on available resources, scaling based on load metrics, and managing updates with no downtime. Kubernetes has become the de facto standard for this."}, "S15.titulo": {"es": "Basar decisiones de escalado en métricas reales", "en": "Base scaling decisions on real metrics"}, "S15.descripcion": {"es": "Tomar decisiones de arquitectura y escalado basadas en datos medidos del comportamiento real del sistema, no en suposiciones o tendencias tecnológicas.", "en": "Make architecture and scaling decisions based on measured data about the system's actual behavior, not on assumptions or technology trends."}, "S15.detalle": {"es": "La optimización prematura es costosa en tiempo y complejidad. Adoptar microservicios, sharding, o caching sin evidencia de que son necesarios introduce complejidad sin beneficio. El enfoque correcto es medir primero, identificar el cuello de botella real con datos, y luego aplicar la solución específica para ese problema.", "en": "Premature optimization is costly in time and complexity. Adopting microservices, sharding, or caching without evidence they're needed introduces complexity without benefit. The right approach is to measure first, identify the real bottleneck with data, and then apply the specific solution for that problem."}, "S16.titulo": {"es": "Usar mensajería basada en eventos entre servicios", "en": "Use event-based messaging between services"}, "S16.descripcion": {"es": "Comunicar servicios mediante la publicación y consumo de eventos a través de un broker de mensajes (Kafka, RabbitMQ), en lugar de llamadas sincrónicas directas entre servicios.", "en": "Communicate between services by publishing and consuming events through a message broker (Kafka, RabbitMQ), instead of direct synchronous calls between services."}, "S16.detalle": {"es": "La comunicación sincrónica crea acoplamiento temporal: el servicio A no puede completar su trabajo sin que el servicio B responda. Si B está lento o caído, A sufre. La mensajería por eventos desacopla esta dependencia: A publica un evento 'orden creada' y continúa; B, C y D consumen ese evento cuando pueden. Esto mejora la resiliencia y permite que cada servicio escale independientemente.", "en": "Synchronous communication creates temporal coupling: service A can't complete its work without service B responding. If B is slow or down, A suffers. Event-based messaging decouples this dependency: A publishes an 'order created' event and continues; B, C, and D consume that event whenever they can. This improves resilience and lets each service scale independently."}, "S17.titulo": {"es": "Aplicar chaos engineering para validar resiliencia", "en": "Apply chaos engineering to validate resilience"}, "S17.descripcion": {"es": "Inyectar fallos deliberados en el sistema en condiciones controladas para descubrir debilidades antes de que ocurran en producción de forma no planificada.", "en": "Deliberately inject failures into the system under controlled conditions to discover weaknesses before they occur unplanned in production."}, "S17.detalle": {"es": "El chaos engineering, popularizado por Netflix con Chaos Monkey, parte de la premisa de que la única forma de saber cómo falla un sistema es hacerlo fallar de forma controlada. Se apagan instancias aleatoriamente, se introduce latencia artificial, se cortan conexiones de red. Los problemas descubiertos así tienen solución planificada; los descubiertos en un incidente real tienen presión de tiempo.", "en": "Chaos engineering, popularized by Netflix with Chaos Monkey, starts from the premise that the only way to know how a system fails is to make it fail in a controlled way. Instances are randomly shut down, artificial latency is introduced, network connections are cut. Problems discovered this way have planned solutions; those discovered during a real incident come with time pressure."}, "S18.titulo": {"es": "Evitar over-fetching de datos en consultas", "en": "Avoid over-fetching data in queries"}, "S18.descripcion": {"es": "Diseñar las consultas para recuperar únicamente los datos que se necesitan, evitando SELECT * o traer miles de registros cuando solo se necesita saber si existe uno.", "en": "Design queries to retrieve only the data that's needed, avoiding SELECT * or fetching thousands of records when you only need to know if one exists."}, "S18.detalle": {"es": "Cada byte transferido entre la base de datos y la aplicación consume CPU, memoria y ancho de banda de red. SELECT * en una tabla con 50 columnas cuando solo se necesitan 3 es un desperdicio multiplicado por cada solicitud. Usar EXISTS en lugar de COUNT(*) para verificar existencia, LIMIT para paginar resultados, y seleccionar solo las columnas necesarias son prácticas que reducen la carga de forma proporcional al volumen.", "en": "Every byte transferred between the database and the application consumes CPU, memory, and network bandwidth. SELECT * on a table with 50 columns when only 3 are needed is waste multiplied by every request. Using EXISTS instead of COUNT(*) to check existence, LIMIT to paginate results, and selecting only the needed columns are practices that reduce load proportionally to volume."}, "S19.titulo": {"es": "Implementar paginación en lugar de devolver todos los registros", "en": "Implement pagination instead of returning all records"}, "S19.descripcion": {"es": "Diseñar las APIs para devolver los datos en páginas de tamaño limitado, con mecanismos para navegar entre páginas, en lugar de devolver todos los registros en una sola respuesta.", "en": "Design APIs to return data in limited-size pages, with mechanisms to navigate between pages, instead of returning all records in a single response."}, "S19.detalle": {"es": "Una API que devuelve todos los registros de una tabla sin límite es una bomba de tiempo. Con 100 registros funciona bien. Con 100,000 registros, el servidor consume memoria serializing todos los registros, la red transfiere megabytes, y el cliente se cuelga procesando la respuesta. La paginación por offset (LIMIT/OFFSET) o por cursor (WHERE id > last_seen_id) mantiene las respuestas acotadas independientemente del volumen total.", "en": "An API that returns all of a table's records with no limit is a time bomb. With 100 records it works fine. With 100,000 records, the server burns memory serializing all of them, the network transfers megabytes, and the client chokes processing the response. Offset-based pagination (LIMIT/OFFSET) or cursor-based pagination (WHERE id > last_seen_id) keeps responses bounded regardless of total volume."}, "S20.titulo": {"es": "Definir SLA, SLO y SLI para el sistema", "en": "Define SLA, SLO, and SLI for the system"}, "S20.descripcion": {"es": "Establecer formalmente los acuerdos de nivel de servicio (SLA), objetivos de nivel de servicio (SLO) y los indicadores que los miden (SLI) para tener criterios objetivos de cuándo el sistema está funcionando bien o mal.", "en": "Formally establish service level agreements (SLA), service level objectives (SLO), and the indicators that measure them (SLI) to have objective criteria for when the system is performing well or poorly."}, "S20.detalle": {"es": "Sin definiciones formales de 'qué es aceptable', es imposible saber cuándo hay un problema que merece atención vs. variación normal. Un SLO como 'el P99 de latencia debe ser menor a 500ms' convierte el monitoreo en algo accionable. El SLI es la métrica medida (latencia P99 real). El SLA es el compromiso con el cliente sobre ese objetivo. Juntos crean una cultura de confiabilidad basada en datos.", "en": "Without formal definitions of 'what's acceptable,' it's impossible to know when there's a problem that deserves attention versus normal variation. An SLO like 'P99 latency must be under 500ms' turns monitoring into something actionable. The SLI is the measured metric (actual P99 latency). The SLA is the commitment to the customer around that objective. Together they create a data-driven reliability culture."}, "ui.logo_text": {"es": "Escalabilidad Web", "en": "Web Scalability"}, "ui.sidebar_sub": {"es": "Catálogo de heurísticas sistematizadas", "en": "A systematized heuristics catalog"}, "ui.nav_principales": {"es": "Heurísticas Principales", "en": "Primary Heuristics"}, "ui.nav_secundarias": {"es": "Heurísticas Secundarias", "en": "Secondary Heuristics"}, "ui.footer_threshold": {"es": "Umbral de consenso: ≥ 15%", "en": "Consensus threshold: ≥ 15%"}, "ui.home_eyebrow": {"es": "Investigación · Universidad Tecnológica de Panamá", "en": "Research · Technological University of Panama"}, "ui.home_title": {"es": "Heurísticas de Escalabilidad<br>para Aplicaciones Web", "en": "Web Application<br>Scalability Heuristics"}, "ui.home_cta_hint": {"es": "Selecciona una heurística en el panel lateral para comenzar.", "en": "Select a heuristic from the sidebar to get started."}, "ui.categories_title": {"es": "Categorías", "en": "Categories"}, "ui.design_categories": {"es": "Categorías de<br><small>diseño</small>", "en": "Design<br><small>categories</small>"}, "ui.stat_principales": {"es": "Heurísticas principales<br><small>consenso ≥ 15%</small>", "en": "Primary heuristics<br><small>consensus ≥ 15%</small>"}, "ui.stat_secundarias": {"es": "Heurísticas secundarias<br><small>consenso &lt; 15%</small>", "en": "Secondary heuristics<br><small>consensus &lt; 15%</small>"}, "ui.detailed_explanation": {"es": "Explicación detallada", "en": "Detailed explanation"}, "ui.practical_example": {"es": "Ejemplo práctico", "en": "Practical example"}, "ui.explanation": {"es": "Explicación", "en": "Explanation"}, "ui.consensus_index": {"es": "Índice de consenso", "en": "Consensus index"}, "ui.badge_principal": {"es": "Principal", "en": "Primary"}, "ui.badge_secundaria": {"es": "Secundaria", "en": "Secondary"}, "ui.open_menu": {"es": "Abrir menú", "en": "Open menu"}, "ui.lang_label": {"es": "IDIOMA DE LECTURA", "en": "READING LANGUAGE"}, "ui.acad": {"es": "académicas", "en": "academic"}, "ui.ind": {"es": "industriales", "en": "industry"}, "ui.mentioned_in": {"es": "Mencionada en", "en": "Mentioned in"}, "ui.of_sources": {"es": "de 17 fuentes", "en": "of 17 sources"}, "ui.footer_corpus_prefix": {"es": "Corpus:", "en": "Corpus:"}, "ui.footer_corpus_suffix": {"es": "fuentes · 145 heurísticas brutas", "en": "sources · 145 raw heuristics"}, "cat.Caché": {"es": "Caché", "en": "Cache"}, "cat.Monitoreo y observabilidad": {"es": "Monitoreo y observabilidad", "en": "Monitoring & Observability"}, "cat.Arquitectura": {"es": "Arquitectura", "en": "Architecture"}, "cat.Base de datos": {"es": "Base de datos", "en": "Database"}, "cat.Tolerancia a fallos": {"es": "Tolerancia a fallos", "en": "Fault Tolerance"}, "cat.Balanceo de carga": {"es": "Balanceo de carga", "en": "Load Balancing"}, "cat.Escalado horizontal/vertical": {"es": "Escalado horizontal/vertical", "en": "Horizontal/Vertical Scaling"}, "cat.CI/CD y despliegue": {"es": "CI/CD y despliegue", "en": "CI/CD & Deployment"}, "cat.Diseño de APIs": {"es": "Diseño de APIs", "en": "API Design"}, "cat.Frontend/cliente": {"es": "Frontend/cliente", "en": "Frontend/Client"}}
I18NJSON;
$I18N = json_decode($I18N_JSON, true);


function en(string $key): string {
    global $I18N;
    return $I18N[$key]['en'] ?? $key;
}

$totalHeuristicas = count($heuristicas_principales) + count($heuristicas_secundarias);
$allCats = array_merge(array_column($heuristicas_principales, 'categoria'), array_column($heuristicas_secundarias, 'categoria'));
$categoryCount = count(array_unique($allCats));

// Dynamic (computed) i18n strings that mix numbers with translated text
$I18N['dyn.home_desc'] = [
    'es' => "Un catálogo sistematizado de {$totalHeuristicas} principios de diseño derivados del análisis de frecuencia sobre 17 fuentes académicas e industriales. Las heurísticas están ordenadas por grado de consenso entre la literatura revisada.",
    'en' => "A systematized catalog of {$totalHeuristicas} design principles derived from a frequency analysis across 17 academic and industry sources. The heuristics are ordered by degree of consensus across the reviewed literature."
];
$I18N['ui.footer_corpus'] = [
    'es' => "Corpus: 17 fuentes · 145 heurísticas brutas",
    'en' => "Corpus: 17 sources · 145 raw heuristics"
];
foreach ($heuristicas_principales as $h) {
    $I18N["{$h['id']}.sources"] = [
        'es' => "Mencionada en <strong>{$h['fuentes']}</strong> de 17 fuentes · <span class=\"src-acad\">{$h['academicas']} académicas</span> · <span class=\"src-ind\">{$h['industriales']} industriales</span>",
        'en' => "Mentioned in <strong>{$h['fuentes']}</strong> of 17 sources · <span class=\"src-acad\">{$h['academicas']} academic</span> · <span class=\"src-ind\">{$h['industriales']} industry</span>",
    ];
}
$I18N_OUT = json_encode($I18N, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Application Scalability Heuristics — Web Scalability</title>
    <meta name="description" content="A systematized catalog of <?= $totalHeuristicas ?> web application scalability heuristics, derived from a frequency analysis across 17 academic and industry sources.">
    <meta property="og:title" content="Web Application Scalability Heuristics">
    <meta property="og:description" content="A systematized catalog of design principles for scalable web applications, ranked by consensus across academic and industry literature.">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<script>window.I18N = <?= $I18N_OUT ?>;</script>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-logo" id="logo-home" title="<?= en('ui.logo_text') ?>">
                <span class="logo-mark">⬡</span>
                <span class="logo-text" data-i18n="ui.logo_text"><?= en('ui.logo_text') ?></span>
            </a>
            <p class="sidebar-sub" data-i18n="ui.sidebar_sub"><?= en('ui.sidebar_sub') ?></p>

            <div class="lang-toggle">
                <span class="lang-toggle-label" data-i18n="ui.lang_label"><?= en('ui.lang_label') ?></span>
                <div class="lang-toggle-buttons">
                    <button type="button" class="lang-btn active" data-lang="en">EN <span>English</span></button>
                    <button type="button" class="lang-btn" data-lang="es">ES <span>Español</span></button>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <button class="nav-section-toggle active" data-target="nav-principal">
                    <span data-i18n="ui.nav_principales"><?= en('ui.nav_principales') ?></span>
                    <span class="nav-count"><?= count($heuristicas_principales) ?></span>
                </button>
                <ul class="nav-list" id="nav-principal">
                    <?php foreach ($heuristicas_principales as $h): ?>
                    <li>
                        <a href="#" class="nav-item" data-id="<?= $h['id'] ?>" data-group="principal">
                            <span class="nav-item-id"><?= $h['id'] ?></span>
                            <span class="nav-item-title" data-i18n="<?= $h['id'] ?>.titulo"><?= en("{$h['id']}.titulo") ?></span>
                            <span class="nav-item-cat cat-<?= strtolower(str_replace([' ', '/'], '-', $h['categoria'])) ?>" data-i18n="cat.<?= $h['categoria'] ?>"><?= en("cat.{$h['categoria']}") ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="nav-section">
                <button class="nav-section-toggle" data-target="nav-secundario">
                    <span data-i18n="ui.nav_secundarias"><?= en('ui.nav_secundarias') ?></span>
                    <span class="nav-count"><?= count($heuristicas_secundarias) ?></span>
                </button>
                <ul class="nav-list collapsed" id="nav-secundario">
                    <?php foreach ($heuristicas_secundarias as $h): ?>
                    <li>
                        <a href="#" class="nav-item" data-id="<?= $h['id'] ?>" data-group="secundario">
                            <span class="nav-item-id"><?= $h['id'] ?></span>
                            <span class="nav-item-title" data-i18n="<?= $h['id'] ?>.titulo"><?= en("{$h['id']}.titulo") ?></span>
                            <span class="nav-item-cat cat-<?= strtolower(str_replace([' ', '/'], '-', $h['categoria'])) ?>" data-i18n="cat.<?= $h['categoria'] ?>"><?= en("cat.{$h['categoria']}") ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>

        <div class="sidebar-footer">
            <p data-i18n="ui.footer_corpus"><?= en('ui.footer_corpus') ?></p>
            <p data-i18n="ui.footer_threshold"><?= en('ui.footer_threshold') ?></p>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content" id="main-content">

        <!-- HOME SCREEN -->
        <div class="view active" id="view-home">
            <div class="home-hero">
                <div class="home-hero-eyebrow" data-i18n="ui.home_eyebrow"><?= en('ui.home_eyebrow') ?></div>
                <h1 class="home-title" data-i18n="ui.home_title" data-i18n-html="1"><?= en('ui.home_title') ?></h1>
                <p class="home-desc" data-i18n="dyn.home_desc"><?= en('dyn.home_desc') ?></p>
                <div class="home-stats">
                    <div class="stat">
                        <span class="stat-num"><?= count($heuristicas_principales) ?></span>
                        <span class="stat-label" data-i18n="ui.stat_principales" data-i18n-html="1"><?= en('ui.stat_principales') ?></span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-num"><?= count($heuristicas_secundarias) ?></span>
                        <span class="stat-label" data-i18n="ui.stat_secundarias" data-i18n-html="1"><?= en('ui.stat_secundarias') ?></span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat">
                        <span class="stat-num"><?= $categoryCount ?></span>
                        <span class="stat-label" data-i18n="ui.design_categories" data-i18n-html="1"><?= en('ui.design_categories') ?></span>
                    </div>
                </div>
                <p class="home-cta-hint" data-i18n="ui.home_cta_hint"><?= en('ui.home_cta_hint') ?></p>
            </div>

            <div class="home-categories">
                <h2 class="section-title" data-i18n="ui.categories_title"><?= en('ui.categories_title') ?></h2>
                <div class="cat-grid">
                    <?php
                    $cats = [];
                    foreach (array_merge($heuristicas_principales, $heuristicas_secundarias) as $h) {
                        $cats[$h['categoria']] = ($cats[$h['categoria']] ?? 0) + 1;
                    }
                    arsort($cats);
                    foreach ($cats as $cat => $count):
                        $slug = strtolower(str_replace([' ', '/'], '-', $cat));
                    ?>
                    <div class="cat-card cat-card-<?= $slug ?>">
                        <span class="cat-card-name" data-i18n="cat.<?= $cat ?>"><?= en("cat.{$cat}") ?></span>
                        <span class="cat-card-count"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- HEURISTIC DETAIL VIEW (principal) -->
        <?php foreach ($heuristicas_principales as $h): ?>
        <div class="view" id="view-<?= $h['id'] ?>">
            <div class="detail-header">
                <div class="detail-meta">
                    <span class="detail-id"><?= $h['id'] ?></span>
                    <span class="detail-cat cat-<?= strtolower(str_replace([' ', '/'], '-', $h['categoria'])) ?>" data-i18n="cat.<?= $h['categoria'] ?>"><?= en("cat.{$h['categoria']}") ?></span>
                    <span class="detail-badge" data-i18n="ui.badge_principal"><?= en('ui.badge_principal') ?></span>
                </div>
                <h1 class="detail-title" data-i18n="<?= $h['id'] ?>.titulo"><?= en("{$h['id']}.titulo") ?></h1>
                <p class="detail-summary" data-i18n="<?= $h['id'] ?>.descripcion"><?= en("{$h['id']}.descripcion") ?></p>

                <div class="consensus-bar-wrap">
                    <div class="consensus-bar-label">
                        <span data-i18n="ui.consensus_index"><?= en('ui.consensus_index') ?></span>
                        <span class="consensus-val"><?= $h['frecuencia'] ?>%</span>
                    </div>
                    <div class="consensus-track">
                        <div class="consensus-fill" style="width: <?= $h['frecuencia'] ?>%"></div>
                    </div>
                    <div class="consensus-sources" data-i18n="<?= $h['id'] ?>.sources" data-i18n-html="1">
                        <?= en("{$h['id']}.sources") ?>
                    </div>
                </div>
            </div>

            <div class="detail-body">
                <section class="detail-section">
                    <h2 data-i18n="ui.detailed_explanation"><?= en('ui.detailed_explanation') ?></h2>
                    <p data-i18n="<?= $h['id'] ?>.detalle"><?= en("{$h['id']}.detalle") ?></p>
                </section>

                <section class="detail-section">
                    <h2 data-i18n="ui.practical_example"><?= en('ui.practical_example') ?></h2>
                    <div class="example-block">
                        <p data-i18n="<?= $h['id'] ?>.ejemplo"><?= en("{$h['id']}.ejemplo") ?></p>
                    </div>
                </section>
            </div>

            <div class="detail-nav">
                <?php
                $idx = array_search($h, $heuristicas_principales);
                $prev = $heuristicas_principales[$idx - 1] ?? null;
                $next = $heuristicas_principales[$idx + 1] ?? null;
                ?>
                <?php if ($prev): ?>
                <button class="nav-btn nav-btn-prev" data-id="<?= $prev['id'] ?>" data-group="principal">
                    ← <?= $prev['id'] ?>: <span data-i18n="<?= $prev['id'] ?>.titulo"><?= en("{$prev['id']}.titulo") ?></span>
                </button>
                <?php endif; ?>
                <?php if ($next): ?>
                <button class="nav-btn nav-btn-next" data-id="<?= $next['id'] ?>" data-group="principal">
                    <?= $next['id'] ?>: <span data-i18n="<?= $next['id'] ?>.titulo"><?= en("{$next['id']}.titulo") ?></span> →
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- HEURISTIC DETAIL VIEW (secundario) -->
        <?php foreach ($heuristicas_secundarias as $h): ?>
        <div class="view" id="view-<?= $h['id'] ?>">
            <div class="detail-header">
                <div class="detail-meta">
                    <span class="detail-id"><?= $h['id'] ?></span>
                    <span class="detail-cat cat-<?= strtolower(str_replace([' ', '/'], '-', $h['categoria'])) ?>" data-i18n="cat.<?= $h['categoria'] ?>"><?= en("cat.{$h['categoria']}") ?></span>
                    <span class="detail-badge detail-badge-sec" data-i18n="ui.badge_secundaria"><?= en('ui.badge_secundaria') ?></span>
                </div>
                <h1 class="detail-title" data-i18n="<?= $h['id'] ?>.titulo"><?= en("{$h['id']}.titulo") ?></h1>
                <p class="detail-summary" data-i18n="<?= $h['id'] ?>.descripcion"><?= en("{$h['id']}.descripcion") ?></p>

                <div class="consensus-bar-wrap">
                    <div class="consensus-bar-label">
                        <span data-i18n="ui.consensus_index"><?= en('ui.consensus_index') ?></span>
                        <span class="consensus-val"><?= $h['frecuencia'] ?>%</span>
                    </div>
                    <div class="consensus-track">
                        <div class="consensus-fill consensus-fill-sec" style="width: <?= $h['frecuencia'] ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="detail-body">
                <section class="detail-section">
                    <h2 data-i18n="ui.explanation"><?= en('ui.explanation') ?></h2>
                    <p data-i18n="<?= $h['id'] ?>.detalle"><?= en("{$h['id']}.detalle") ?></p>
                </section>
            </div>

            <div class="detail-nav">
                <?php
                $idx = array_search($h, $heuristicas_secundarias);
                $prev = $heuristicas_secundarias[$idx - 1] ?? null;
                $next = $heuristicas_secundarias[$idx + 1] ?? null;
                ?>
                <?php if ($prev): ?>
                <button class="nav-btn nav-btn-prev" data-id="<?= $prev['id'] ?>" data-group="secundario">
                    ← <?= $prev['id'] ?>: <span data-i18n="<?= $prev['id'] ?>.titulo"><?= en("{$prev['id']}.titulo") ?></span>
                </button>
                <?php endif; ?>
                <?php if ($next): ?>
                <button class="nav-btn nav-btn-next" data-id="<?= $next['id'] ?>" data-group="secundario">
                    <?= $next['id'] ?>: <span data-i18n="<?= $next['id'] ?>.titulo"><?= en("{$next['id']}.titulo") ?></span> →
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </main>
</div>

<!-- Mobile toggle -->
<button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open menu" data-i18n-aria="ui.open_menu">☰</button>

<script src="function.js"></script>
</body>
</html>