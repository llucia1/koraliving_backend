# 🏡 Community Spaces Booking — MVP

Prototipo funcional para un módulo de reservas de espacios comunes en una comunidad de vecinos.  
Incluye backend (Symfony + PHP) y frontend (pendiente), orquestado con Docker.



## 🚀 Stack Tecnológico

- **Backend**: PHP 8.2+, Symfony 5.4+, Arquitectura Hexagonal
- **Base de datos**: MariaDB (o PostgreSQL)
- **Testing**: PHPUnit + TDD
- **Fixtures**: DoctrineFixturesBundle
- **Contenedores**: Docker + docker-compose
- **Frontend**: Vue 3 + TypeScript (📌 pendiente)





## 🛠️ Instrucciones para levantar el entorno

### 📄 Requisitos previos

- Tener instalado [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/).
- Puerto `8000` (backend) y `5173` (frontend) libres.

### 1️⃣ Clonar el repositorio


git clone https://github.com/llucia1/FynkusTest.git
cd FynkusTest





### 2️⃣ Levantar los contenedores
Levanta el entorno completo con Docker:

docker-compose up -d --build



### 🗄️ Preparar la base de datos

Una vez que los contenedores están levantados correctamente, es necesario ejecutar las migraciones y cargar los fixtures para inicializar la base de datos con datos de prueba.

### 3️⃣ Acceder al contenedor del backend con el comando `make`:

make docker-access-backend

### 4️⃣ Dentro del contenedor del backend ejecutar migraciones:

php bin/console doctrine:migrations:migrate

### 5️⃣ Dentro del contenedor del backend cargar fixtures:

php bin/console doctrine:fixtures:load

### 6️⃣ Probar la aplicación

Una vez ejecutadas las migraciones y, opcionalmente, cargadas las fixtures, ya puedes acceder a la aplicación desde tu navegador o con herramientas como `curl` o Postman.

- 📄 Backend API: [http://localhost:8000](http://localhost:8000)  
  Puedes probar, por ejemplo:

  curl http://localhost:8000/api/v1/space

para obtener la lista de espacios.

    🎨 Frontend: http://localhost:5173
    Desde aquí puedes interactuar con la interfaz gráfica para gestionar reservas y ver la disponibilidad.

✅ Si ambos cargan correctamente, tu entorno está listo y funcionando.




### 🔷 Nota sobre el endpoint de reserva

Actualmente, el endpoint `POST /api/v1/reservation` permite crear una reserva para un espacio en un día seleccionado, especificando una o varias horas.

- Este endpoint solo permite **crear** una reserva.
- Si intentas añadir una reserva para el mismo espacio, día y horas más de una vez, recibirás un error indicando que ya existe.
- Para poder modificar o actualizar una reserva existente, sería necesario implementar en base a REST un endpoint `PATCH`, que todavía no está desarrollado por alta tiempo.







# 📄 Diseño y Arquitectura

## 🧱 Arquitectura
El proyecto está diseñado siguiendo principios de **Arquitectura Hexagonal (Ports & Adapters)** y **Domain-Driven Design (DDD)**.  
Esto permite una separación clara de responsabilidades y facilita el mantenimiento, la escalabilidad y la testabilidad.



## 📦 Bounded Contexts
He definido dos **Bounded Contexts** (BC) principales:  
- `Reservation`
- `Space`

Cada uno encapsula su propia lógica de dominio, sus entidades, repositorios e interfaces.




## 🔗 Comunicación entre Bounded Contexts
La comunicación entre los BC se realiza mediante un **Bus de Eventos**.  
Esto asegura un acoplamiento bajo y permite que los contextos se comuniquen de manera asíncrona o síncrona sin depender directamente uno del otro.



## 📬 Ejemplo de Consulta de Space desde Reservation
En el contexto `Reservation` se hace una consulta para recuperar un `Space` de la siguiente forma, utilizando el **QueryBus** y siguiendo el patrón CQRS:

php
public function getSpace(string $uuid): mixed
{
    $spaceEntityQuery = $this->queryBus->ask(new GetSpaceByUuidQueried($uuid));
    $spaceEntity = $spaceEntityQuery->get();
    if (!$spaceEntity || $spaceEntity instanceof Exception) {
        return null;
    }
    return $spaceEntity;  
}

## 🔷 Query

final readonly class GetSpaceByUuidQueried implements Query
{
    public function __construct(private ?string $uuid) {}

    public function uuid(): ?string {
        return $this->uuid;
    }
}

## 🔷 QueryHandler

#[AsMessageHandler]
final readonly class GetSpaceByUuidHandler implements QueryHandler
{
    public function __construct(private readonly ISpaceRepository $spaceService) {}

    public function __invoke(GetSpaceByUuidQueried $space): SpaceEntityResponse
    {
        try {
            $spaces = $this->spaceService->getByUuid($space->uuid());
            return new SpaceEntityResponse($spaces ?: null);
        } catch (\Exception $ex) {
            throw new SpacesNotFoundException();
        }
    }
}

## 🪄 Bus de Eventos

Tal y como se muestra en la estructura del código (ver imagen adjunta), se ha implementado un Bus de Eventos en el módulo Common\Domain\Bus para manejar:

    Command Bus

    Query Bus

    Event Source

        DomainEvent

        DomainEventSubscriber

        EventBus

Esto permite tanto la ejecución de comandos y consultas como la publicación y manejo de eventos de dominio.







## 🌐 Endpoints implementados

El backend expone los siguientes endpoints para la gestión de reservas y espacios.



### 📋 Espacios (`Space`)

#### 🔷 Obtener todos los espacios disponibles
GET /api/v1/space


#### 📄 Respuesta:
json
[
  {
    "uuid": "aa645f49-94f3-4c63-ab3d-0afec44ebf21",
    "name": "Pista de Padel"
  },
  {
    "uuid": "c2af2336-077d-4cb4-94de-d9537f49266b",
    "name": "Piscina"
  },
  {
    "uuid": "80bdc87c-9be2-4ba9-8457-5908f56aa1fe",
    "name": "Gimnasio"
  }
]


### 📋 Reservas (Reservation)
#### 🔷 Consultar disponibilidad de un espacio en una fecha

GET /api/v1/reservation/space/{spaceUuid}/vailability?date=dd/mm/yyyy

#### 📄 Respuesta cuando existen datos:

[
  {
    "uuid": "8faf258f-dcaf-4b38-9340-c5b0df9156ff",
    "date": "2025-07-22",
    "space": {
      "uuid": "aa645f49-94f3-4c63-ab3d-0afec44ebf21",
      "name": "Pista de Padel"
    },
    "Hour": 9,
    "status": "free"
  },
  ...
]


#### 📄 Respuesta cuando no hay datos:

[]

(En ese caso el frontend genera 13 franjas libres de 09:00 a 21:00.)


### 🔷 Crear o actualizar reservas para un espacio en una fecha

POST /api/v1/reservation

#### 📄 Cuerpo de la petición:

{
  "spaceUuid": "c2af2336-077d-4cb4-94de-d9537f49266b",
  "date": "28/07/2025",
  "slots": [
    { "status": "free",     "hour": 9 },
    { "status": "free",     "hour": 10 },
    { "status": "free",     "hour": 11 },
    { "status": "reserved", "hour": 12 },
    { "status": "free",     "hour": 13 },
    { "status": "free",     "hour": 14 },
    { "status": "free",     "hour": 15 },
    { "status": "free",     "hour": 16 },
    { "status": "free",     "hour": 17 },
    { "status": "free",     "hour": 18 },
    { "status": "free",     "hour": 19 },
    { "status": "free",     "hour": 20 },
    { "status": "free",     "hour": 21 }
  ]
}

#### 📄 Respuesta:

{
  "message": "Reservas actualizadas correctamente"
}



### 🔗 Notas:

    Las fechas deben enviarse siempre en formato dd/mm/yyyy.

    Los horarios son en horas enteras de 09:00 a 21:00.

    Si no existe disponibilidad para el día, el backend devuelve un array vacío y el frontend crea las 13 franjas libres para mostrar.



## ⚖️ Trade-offs y decisiones por límite de tiempo

Durante la implementación de esta prueba técnica se tomaron algunas decisiones conscientes y se hicieron ciertos sacrificios debido a los plazos, que detallo aquí junto con algunas mejoras futuras identificadas.







### 📄 Decisiones y aspectos no abordados por tiempo

- 🚫 **Documentación OpenAPI / Swagger**
  > No se integró Swagger o documentación OpenAPI para los endpoints. Actualmente la documentación de la API se encuentra descrita en este README.
  > *Mejora futura: agregar generación automática de documentación para facilitar integración con consumidores externos.*

- 🚫 **Análisis estático del código**
  > No se ejecutó ningún analizador de código estático (como PHPStan o Psalm en PHP, ESLint en frontend) para detectar posibles problemas o inconsistencias tempranas.
  > *Mejora futura: configurar herramientas de análisis estático en CI/CD.*

- 🧹 **Refactor: lógica de negocio en capas**
  > Algunas reglas de negocio podrían refactorizarse para empujarlas más a la capa de Dominio, reduciendo la lógica en los Handlers o Servicios de Aplicación.
  > *Mejora futura: seguir aplicando DDD en profundidad para mover reglas de validación y decisión al Dominio.*

- 🗄️ **Base de datos para tests**
  > Se optó por utilizar SQLite como motor para las pruebas debido a su rapidez y simplicidad.
  > *Mejora futura: replicar el entorno productivo para las pruebas (por ejemplo con MariaDB o PostgreSQL).*

- 🌳 **Flujo de ramas sencillo**
  > No se trabajó con un flujo de ramas completo (como Git Flow). La idea futura sería mantener `main` como rama principal, `develop` para integración y crear ramas específicas por caso de uso para mergear sobre `develop` y luego a `main` en despliegue.
  > *Mejora futura: formalizar estrategia de ramas y CI/CD para despliegues consistentes.*









### 📝 Otras posibles mejoras futuras

Además, identifico otras áreas que podrían mejorarse en el proyecto si el tiempo y el alcance lo permitieran:

- 📋 **Tests de frontend**
  > Actualmente el TDD se aplicó sólo en el backend (como requería la prueba). Podrían añadirse tests unitarios y end-to-end para el frontend (con Vitest o Cypress).
  
- 📦 **Validaciones más robustas**
  > Mejorar validaciones de entrada y salida en la API, especialmente para asegurar formatos de fecha y valores permitidos en las franjas horarias.

- 📈 **Métricas y monitorización**
  > Incluir herramientas básicas de logging, métricas o monitorización para poder evaluar el comportamiento en un entorno real.

- 🚀 **Optimización del rendimiento**
  > Aunque no fue necesario para la prueba, se podrían implementar consultas más eficientes o cachés para escenarios con mucha concurrencia o mayor volumen de datos.






  ## 🚀 Posibles mejoras

Además de las mejoras mencionadas en los trade-offs anteriores, se identifican otras posibles evoluciones para robustecer y escalar la solución:



### 🔷 Implementar un endpoint `PATCH` para actualizar reservas

Actualmente, el endpoint `POST /api/v1/reservation` solo permite **crear** una reserva para un espacio en un día seleccionado, especificando una o varias horas.

- Si intentas añadir una reserva para el mismo espacio, día y horas más de una vez, recibirás un error indicando que ya existe.
- Para poder modificar o actualizar una reserva existente sería necesario implementar un endpoint `PATCH`, que permita editar una reserva parcial o completamente.

Esta funcionalidad no ha sido implementada por falta de tiempo, pero sería un complemento natural para la gestión completa de las reservas.

### 📋 Nueva entidad para configuración del planning

Actualmente, las franjas horarias del planning están fijas en un rango de 09:00 a 21:00 con incrementos de 1 hora, y esta lógica está embebida en la aplicación.

Una mejora significativa sería introducir una nueva **entidad de dominio** que permita configurar dinámicamente los parámetros del planning. Esta entidad contendría:

- 🕒 **Hora de inicio** del planning.
- 🕕 **Hora de fin** del planning.
- ⏱️ **Incremento** entre franjas (por ejemplo, cada 15, 30, 60 minutos).




### 📝 Beneficios de esta mejora

✅ Permite a un administrador configurar diferentes espacios con distintos horarios y granularidades.  
✅ Centraliza y encapsula la lógica para calcular las franjas válidas.  
✅ Valida que las horas reservadas estén dentro de los rangos definidos.  
✅ Hace el sistema más flexible y preparado para escenarios más complejos.  





### 🔗 Funcionamiento esperado

Cuando se configure un espacio, se asociaría a una instancia de esta nueva entidad de configuración de planning.  
Al consultar la disponibilidad o crear una reserva:
- La aplicación calcularía las franjas horarias en función de esta configuración.
- Validaría que las horas solicitadas para reserva cumplen con el rango y el incremento permitido.
- Si no cumplen, se devolvería un error indicando que la reserva es inválida.

### 📦 Ejemplo de atributos de la entidad `PlanningConfiguration`

| Atributo        | Tipo      | Descripción                          |
|-----------------|-----------|--------------------------------------|
| `startHour`      | int       | Hora de inicio (por ejemplo, 8)     |
| `endHour`        | int       | Hora de fin (por ejemplo, 22)       |
| `increment`      | int       | Incremento entre franjas en minutos (por ejemplo, 30) |





### 🔗 Mejora futura

Esta entidad permitiría definir múltiples configuraciones según el espacio o día, haciendo la solución mucho más versátil para diferentes tipos de comunidades y espacios.

### 📈 Más posibles mejoras
#### 📋 1. Gestión de usuarios y autenticación

Actualmente no hay autenticación ni gestión de usuarios (como pedía la prueba).
🔹 Se podría integrar un sistema de autenticación (JWT, OAuth2, o sesión) para que solo usuarios autorizados puedan realizar reservas o gestionar espacios.
🔹 Se podrían establecer roles (por ejemplo: admin, vecino, invitado) con permisos diferenciados.

### 🌳 2. Multilenguaje (i18n)

Actualmente las respuestas y el frontend están en un solo idioma.
🔹 Soporte multilenguaje tanto en frontend como en mensajes de la API (usando traducciones Symfony y i18n en Vue).
🔹 Facilita despliegue en comunidades con usuarios de distintos idiomas.

### 📆 3. Calendario visual

La interfaz actual usa una grilla simple.
🔹 Mejorar la UX con un componente tipo calendario completo, con drag & drop para mover o extender reservas, usando por ejemplo FullCalendar o similar.

### 🧪 4. Mejora de testing

Aunque ya implementaste TDD en backend:
🔹 Aumentar cobertura en frontend con Cypress (E2E) y Vitest (unitarios).
🔹 Tests de integración reales usando la base de datos configurada para producción, con contenedores desechables (ejemplo: Testcontainers).
### 📄 5. Logs y auditoría

🔹 Guardar un historial/auditoría de todas las reservas y modificaciones.
🔹 Logs con trazabilidad para diagnósticos y cumplimiento normativo.
### 🕒 6. Soporte para franjas solapadas y reservas múltiples

🔹 Permitir reservas de varias franjas consecutivas en un solo clic.
🔹 Validar y gestionar solapamientos correctamente.
### 🔗 7. API pública

🔹 Limitar la API pública con rate-limiting y claves de API para terceros.
### 📦 8. Despliegue y CI/CD

🔹 Integrar un pipeline CI/CD para validar código, ejecutar tests y desplegar automáticamente.
🔹 Añadir ambientes separados para staging y production.




