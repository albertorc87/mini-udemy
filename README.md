# Mini Udemy - Protocolo de Desarrollo

Este documento describe el protocolo para desarrollar nuevos módulos siguiendo los principios de **DDD (Domain-Driven Design)**, **Arquitectura Hexagonal**, **CQRS** y **SOLID**.

## 📋 Tabla de Contenidos

1. [Estructura de Carpetas](#estructura-de-carpetas)
2. [Flujo Completo de una Operación](#flujo-completo-de-una-operación)
3. [Paso a Paso: Crear un Módulo](#paso-a-paso-crear-un-módulo)
4. [Principios Aplicados](#principios-aplicados)
5. [Ejemplo Completo: Módulo User](#ejemplo-completo-módulo-user)

---

## 🗂️ Estructura de Carpetas

### Formato: `ModuleName/EntityName`

**¿Por qué esta estructura?** (DDD)

En DDD, organizamos el código por **Bounded Context** (Contexto Delimitado). Cada módulo representa un contexto de negocio independiente. La estructura `User/User` permite:

- **Escalabilidad**: Cada módulo es independiente y puede evolucionar sin afectar otros
- **Claridad**: El nombre del módulo y la entidad principal quedan explícitos
- **Separación de responsabilidades**: Cada módulo encapsula su propio dominio

**Ejemplo de estructura completa:**

```
src/
└── User/
    └── User/
        ├── Application/          # Capa de Aplicación (CQRS)
        │   ├── Command/          # Comandos (Write operations)
        │   ├── Query/            # Queries (Read operations) - si aplica
        │   ├── EventHandler/     # Handlers de eventos de dominio
        │   └── Service/          # Servicios de aplicación
        ├── Domain/               # Capa de Dominio (DDD)
        │   ├── Event/            # Eventos de dominio
        │   ├── Repository/       # Interfaces de repositorios
        │   ├── Service/          # Servicios de dominio
        │   └── [Entity].php      # Entidad raíz del agregado
        └── Infrastructure/      # Capa de Infraestructura (Hexagonal)
            ├── Persistence/      # Implementaciones de repositorios
            └── Service/          # Implementaciones de servicios externos
```

---

## 🔄 Flujo Completo de una Operación

### Diagrama de Flujo

```
HTTP Request
    ↓
[1] Controller (Http Layer)
    ↓ Validación con Symfony Validator
[2] Request DTO
    ↓
[3] Command (CQRS)
    ↓
[4] CommandBus (CQRS)
    ↓
[5] CommandHandler (CQRS)
    ↓
[6] Application Service
    ↓
[7] Domain Service (si aplica)
    ↓
[8] Aggregate Root (Domain)
    ↓ Registra eventos
[9] Repository (Domain Interface)
    ↓
[10] Repository Implementation (Infrastructure)
    ↓
[11] EventBus (CQRS)
    ↓
[12] EventHandler (CQRS)
    ↓
[13] Infrastructure Service
```

---

## 📝 Paso a Paso: Crear un Módulo

### Paso 1: Crear el Controller (Capa HTTP)

**Ubicación**: `src/Http/V1/Controller/[Module]/[Entity]/[Action][Entity]Controller.php`

**Responsabilidades** (SOLID - Single Responsibility):
- Recibir la petición HTTP
- Validar los datos de entrada con Symfony Validator
- Crear el Command
- Despachar el Command al CommandBus
- Manejar excepciones y retornar respuestas HTTP

**Ejemplo**:

```php
<?php

namespace Udemy\Http\V1\Controller\User\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Udemy\Http\V1\Request\User\User\CreateUserRequest;
use Udemy\Shared\Domain\Bus\Command\CommandBus;
use Udemy\User\User\Application\Command\CreateUserCommand;

final class CreateUserController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        // 1. Parsear JSON
        $data = json_decode($request->getContent(), true);
        
        // 2. Crear Request DTO
        $createUserRequest = CreateUserRequest::fromArray($data);
        
        // 3. Validar con Symfony Validator (SOLID - Single Responsibility)
        $violations = $this->validator->validate($createUserRequest);
        if (count($violations) > 0) {
            // Retornar errores de validación
        }
        
        // 4. Crear Command (CQRS)
        $command = new CreateUserCommand(
            email: $createUserRequest->email,
            password: $createUserRequest->password,
            name: $createUserRequest->name,
            avatarUrl: $createUserRequest->avatarUrl
        );
        
        // 5. Despachar Command (CQRS)
        $this->commandBus->dispatch($command);
        
        // 6. Retornar respuesta
        return new JsonResponse(['message' => 'User created'], 201);
    }
}
```

**¿Por qué validar aquí?** (SOLID - Single Responsibility)
- El Controller es responsable de validar la **forma** de los datos (formato, tipos, longitud)
- El Domain es responsable de validar las **reglas de negocio** (unicidad, consistencia)

---

### Paso 2: Crear el Request DTO

**Ubicación**: `src/Http/V1/Request/[Module]/[Entity]/[Action][Entity]Request.php`

**Responsabilidades**:
- Definir la estructura de datos esperada
- Aplicar validaciones de formato con atributos de Symfony Validator

**Ejemplo**:

```php
<?php

namespace Udemy\Http\V1\Request\User\User;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Email must be a valid email address')]
    #[Assert\Length(max: 255)]
    public readonly string $email;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public readonly string $password;

    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(max: 255)]
    public readonly string $name;

    #[Assert\Optional([new Assert\Url()])]
    public readonly ?string $avatarUrl;

    public function __construct(
        string $email,
        string $password,
        string $name,
        ?string $avatarUrl = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->avatarUrl = $avatarUrl;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            name: $data['name'] ?? '',
            avatarUrl: $data['avatarUrl'] ?? null
        );
    }
}
```

---

### Paso 3: Crear el Command (CQRS)

**Ubicación**: `src/[Module]/[Entity]/Application/Command/[Action][Entity]Command.php`

**¿Qué es un Command?** (CQRS)
- Representa una **intención** de modificar el estado del sistema
- Es un objeto de valor (Value Object) inmutable
- Solo contiene datos, no lógica

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Application\Command;

use Udemy\Shared\Domain\Bus\Command\Command;

final class CreateUserCommand implements Command
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $name,
        public readonly ?string $avatarUrl = null
    ) {
    }
}
```

**¿Por qué Command?** (CQRS)
- **Separación de responsabilidades**: Los Commands representan "qué quiero hacer"
- **Desacoplamiento**: El Controller no conoce la implementación del negocio
- **Testeable**: Fácil de mockear y testear

---

### Paso 4: Crear el CommandHandler (CQRS)

**Ubicación**: `src/[Module]/[Entity]/Application/Command/[Action][Entity]CommandHandler.php`

**Responsabilidades**:
- Recibir el Command
- Delegar la lógica al Application Service
- No debe contener lógica de negocio

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Application\Command;

use Udemy\Shared\Domain\Bus\Command\Command;
use Udemy\Shared\Domain\Bus\Command\CommandHandler;
use Udemy\User\User\Application\Service\UserCreator;

final class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(
        private readonly UserCreator $userCreator
    ) {
    }

    public function handle(Command $command): void
    {
        if (!$command instanceof CreateUserCommand) {
            throw new \InvalidArgumentException('Invalid command type');
        }

        $this->userCreator->create(
            $command->email,
            $command->password,
            $command->name,
            $command->avatarUrl
        );
    }

    // Método __invoke para que Symfony Messenger lo detecte automáticamente
    public function __invoke(CreateUserCommand $command): void
    {
        $this->handle($command);
    }
}
```

**¿Por qué CommandHandler?** (CQRS)
- **Mediador**: Conecta el Command con el Application Service
- **Punto de extensión**: Permite agregar middleware, logging, etc.
- **Desacoplamiento**: El CommandBus no conoce el Service directamente

---

### Paso 5: Crear el Application Service

**Ubicación**: `src/[Module]/[Entity]/Application/Service/[Action][Entity]Service.php`

**Responsabilidades** (Arquitectura Hexagonal):
- Orquestar la lógica de aplicación
- Coordinar entre Domain Services y Repositories
- Manejar aspectos técnicos (hashing, generación de IDs, etc.)
- **NO debe contener lógica de negocio** (esa va en Domain)

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Application\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Ulid;
use Udemy\Shared\Domain\Bus\Event\EventBus;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\Service\UserEmailUniquenessChecker;
use Udemy\User\User\Domain\User;

final class UserCreator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserEmailUniquenessChecker $emailUniquenessChecker,
        private readonly EventBus $eventBus
    ) {
    }

    public function create(
        string $email,
        string $password,
        string $name,
        ?string $avatarUrl = null
    ): void {
        // 1. Crear Value Objects (DDD)
        $userId = new UserId(Ulid::generate());
        $userEmail = new UserEmail($email);
        $userName = new UserName($name);
        $userAvatarUrl = $avatarUrl ? new UserAvatarUrl($avatarUrl) : null;

        // 2. Hashear contraseña (Infraestructura - aspecto técnico)
        $temporaryUser = new User(/* ... */);
        $hashedPassword = $this->passwordHasher->hashPassword($temporaryUser, $password);
        $userPasswordHash = new UserPasswordHash($hashedPassword);

        // 3. Crear usuario usando factory method del dominio (DDD)
        $user = User::create(
            $userId,
            $userEmail,
            $userPasswordHash,
            $userName,
            $userAvatarUrl,
            $this->emailUniquenessChecker  // Domain Service inyectado
        );

        // 4. Persistir (Arquitectura Hexagonal - Repository pattern)
        $this->userRepository->save($user);

        // 5. Publicar eventos de dominio (CQRS)
        $domainEvents = $user->pullDomainEvents();
        $this->eventBus->publish(...$domainEvents);
    }
}
```

**¿Por qué separar la validación de email?** (DDD + SOLID)

La validación de unicidad del email se hace en un **Domain Service** (`UserEmailUniquenessChecker`) porque:

1. **DDD - Domain Service**: Es una regla de negocio que requiere acceso al repositorio
2. **SOLID - Single Responsibility**: El Aggregate Root (`User`) no debe conocer el repositorio directamente
3. **SOLID - Dependency Inversion**: El dominio define la interfaz, la aplicación inyecta la implementación
4. **Testeable**: Fácil de mockear en tests unitarios

---

### Paso 6: Crear el Aggregate Root (Domain)

**Ubicación**: `src/[Module]/[Entity]/Domain/[Entity].php`

**Responsabilidades** (DDD):
- Encapsular la lógica de negocio
- Garantizar la consistencia del agregado
- Registrar eventos de dominio cuando ocurren cambios importantes

**Características importantes**:
- Debe extender `AggregateRoot` para poder registrar eventos
- Usa factory methods para crear instancias (encapsula validaciones)
- Usa Value Objects para los atributos

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Domain;

use Udemy\Shared\Domain\Aggregate\AggregateRoot;
use Udemy\User\User\Domain\Event\UserCreated;
use Udemy\User\User\Domain\Service\UserEmailUniquenessChecker;

class User extends AggregateRoot
{
    private UserId $id;
    private UserEmail $email;
    // ... otros atributos

    /**
     * Factory method para crear un nuevo usuario
     * Encapsula la lógica de creación y validación de reglas de negocio
     */
    public static function create(
        UserId $id,
        UserEmail $email,
        UserPasswordHash $password,
        UserName $name,
        ?UserAvatarUrl $avatarUrl,
        UserEmailUniquenessChecker $emailUniquenessChecker
    ): self {
        // 1. Validar regla de negocio: email debe ser único (DDD)
        $emailUniquenessChecker->ensureEmailIsUnique($email);

        // 2. Crear instancia
        $user = new self($id, $email, $password, $name, $avatarUrl);

        // 3. Registrar evento de dominio (DDD + CQRS)
        $user->record(new UserCreated(
            $id->value(),
            $email->value(),
            $name->value()
        ));

        return $user;
    }
}
```

**¿Por qué registrar eventos aquí?** (DDD + CQRS)
- **DDD**: Los eventos se registran donde ocurre el cambio de estado
- **CQRS**: Los eventos permiten desacoplar efectos secundarios (enviar email, notificaciones, etc.)
- **Single Responsibility**: El servicio solo crea el usuario, los efectos secundarios se manejan en EventHandlers

---

### Paso 7: Crear el Domain Service (si aplica)

**Ubicación**: `src/[Module]/[Entity]/Domain/Service/[ServiceName].php`

**¿Cuándo usar un Domain Service?** (DDD)
- Cuando la lógica de negocio requiere acceso a múltiples agregados
- Cuando la lógica no pertenece naturalmente a un solo agregado
- Cuando necesitas validar reglas que requieren consultar el repositorio

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Domain\Service;

use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\UserEmail;

final class UserEmailUniquenessChecker
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function ensureEmailIsUnique(UserEmail $email): void
    {
        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser !== null) {
            throw new \DomainException(
                sprintf('User with email "%s" already exists', $email->value())
            );
        }
    }
}
```

**¿Por qué Domain Service?** (DDD)
- La validación de unicidad requiere consultar el repositorio
- No pertenece al Aggregate Root porque necesitaría inyectar el repositorio (violaría principios)
- Es una regla de negocio que debe estar en el dominio

---

### Paso 8: Crear el Repository Interface (Domain)

**Ubicación**: `src/[Module]/[Entity]/Domain/Repository/[Entity]Repository.php`

**¿Por qué interface?** (Arquitectura Hexagonal)
- El dominio define **qué** necesita, no **cómo** se implementa
- Permite cambiar la implementación (Doctrine, Eloquent, MongoDB, etc.) sin afectar el dominio

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Domain\Repository;

use Udemy\User\User\Domain\User;
use Udemy\User\User\Domain\UserEmail;
use Udemy\User\User\Domain\UserId;

interface UserRepository
{
    public function save(User $user): void;
    public function findByEmail(UserEmail $email): ?User;
    public function findById(UserId $id): ?User;
}
```

---

### Paso 9: Implementar el Repository (Infrastructure)

**Ubicación**: `src/[Module]/[Entity]/Infrastructure/Persistence/Doctrine/Doctrine[Entity]Repository.php`

**Responsabilidades**:
- Implementar la interfaz del repositorio
- Usar la tecnología de persistencia (Doctrine, Eloquent, etc.)

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Udemy\User\User\Domain\Repository\UserRepository;
use Udemy\User\User\Domain\User;

final class DoctrineUserRepository implements UserRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    // ... otros métodos
}
```

**Configuración en `config/services.yaml`**:

```yaml
Udemy\User\User\Domain\Repository\UserRepository:
    alias: Udemy\User\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository
```

---

### Paso 10: Crear el Evento de Dominio (DDD + CQRS)

**Ubicación**: `src/[Module]/[Entity]/Domain/Event/[Action][Entity]Event.php`

**¿Qué es un Domain Event?** (DDD + CQRS)
- Representa algo que **ya ocurrió** en el dominio
- Es inmutable
- Contiene la información necesaria para que otros módulos reaccionen

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Domain\Event;

use Udemy\Shared\Domain\Bus\Event\DomainEvent;

final class UserCreated extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $email,
        private readonly string $name,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'user.created';
    }

    public function email(): string
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toPrimitives(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self {
        return new self(
            $aggregateId,
            $body['email'],
            $body['name'],
            $eventId,
            $occurredOn
        );
    }
}
```

**¿Por qué eventos?** (CQRS + SOLID)
- **CQRS**: Permite desacoplar comandos de queries y efectos secundarios
- **SOLID - Single Responsibility**: El servicio solo crea el usuario, el envío de email es responsabilidad del EventHandler
- **Escalabilidad**: Permite agregar nuevos efectos secundarios sin modificar el código existente

---

### Paso 11: Crear el EventHandler (CQRS)

**Ubicación**: `src/[Module]/[Entity]/Application/EventHandler/[Action]EventHandler.php`

**Responsabilidades**:
- Reaccionar a eventos de dominio
- Ejecutar efectos secundarios (enviar emails, notificaciones, etc.)

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Application\EventHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Udemy\User\User\Application\Service\EmailSender;
use Udemy\User\User\Domain\Event\UserCreated;

#[AsMessageHandler]
final class SendUserConfirmationEmailHandler
{
    public function __construct(
        private readonly EmailSender $emailSender
    ) {
    }

    public function __invoke(UserCreated $event): void
    {
        $this->emailSender->sendConfirmationEmail(
            $event->email(),
            $event->name()
        );
    }
}
```

**Configuración en `config/packages/messenger.yaml`**:

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            Udemy\User\User\Domain\Event\UserCreated: async
```

**¿Por qué EventHandler separado?** (SOLID - Single Responsibility)
- El servicio `UserCreator` solo se encarga de crear el usuario
- El envío de email es una responsabilidad separada
- Permite agregar más handlers sin modificar el servicio

---

### Paso 12: Crear Interfaces de Servicios Externos (Arquitectura Hexagonal)

**Ubicación**: `src/[Module]/[Entity]/Application/Service/[ServiceInterface].php`

**¿Por qué interfaces?** (Arquitectura Hexagonal + SOLID - Dependency Inversion)
- El dominio/aplicación no debe depender de implementaciones concretas
- Permite cambiar la implementación (Mailer, SMS, etc.) sin afectar el dominio

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Application\Service;

interface EmailSender
{
    public function sendConfirmationEmail(string $to, string $name): void;
}
```

---

### Paso 13: Implementar Servicios Externos (Infrastructure)

**Ubicación**: `src/[Module]/[Entity]/Infrastructure/Service/[ServiceImplementation].php`

**Ejemplo**:

```php
<?php

namespace Udemy\User\User\Infrastructure\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Udemy\User\User\Application\Service\EmailSender;

final class MailerEmailSender implements EmailSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromEmail
    ) {
    }

    public function sendConfirmationEmail(string $to, string $name): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($to)
            ->subject('Bienvenido a Mini Udemy')
            ->html($this->getEmailTemplate($name));

        $this->mailer->send($email);
    }
}
```

**Configuración en `config/services.yaml`**:

```yaml
Udemy\User\User\Application\Service\EmailSender:
    alias: Udemy\User\User\Infrastructure\Service\MailerEmailSender
```

---

### Paso 14: Configurar Routing

**Ubicación**: `config/routes/[Module]/[entity].yaml`

**Ejemplo**:

```yaml
create_user:
    path: /v1/users
    controller: Udemy\Http\V1\Controller\User\User\CreateUserController
    methods: [POST]
```

---

## 🎯 Principios Aplicados

### DDD (Domain-Driven Design)

✅ **Agregados**: `User` es un Aggregate Root que encapsula la lógica de negocio  
✅ **Value Objects**: `UserId`, `UserEmail`, `UserName`, etc.  
✅ **Domain Events**: `UserCreated` representa algo que ocurrió en el dominio  
✅ **Domain Services**: `UserEmailUniquenessChecker` para validaciones que requieren repositorio  
✅ **Repository Pattern**: Interface en Domain, implementación en Infrastructure  
✅ **Bounded Context**: Cada módulo (`User/User`, `Course/Course`) es un contexto independiente  

### Arquitectura Hexagonal

✅ **Puertos (Interfaces)**: `UserRepository`, `EmailSender` en Domain/Application  
✅ **Adaptadores (Implementaciones)**: `DoctrineUserRepository`, `MailerEmailSender` en Infrastructure  
✅ **Inversión de Dependencias**: El dominio no depende de infraestructura  
✅ **Separación de Capas**: Application, Domain, Infrastructure claramente separadas  

### CQRS (Command Query Responsibility Segregation)

✅ **Commands**: `CreateUserCommand` representa una intención de modificar  
✅ **CommandBus**: Despacha comandos de forma desacoplada  
✅ **CommandHandlers**: Manejan los comandos  
✅ **Events**: `UserCreated` representa algo que ya ocurrió  
✅ **EventBus**: Publica eventos de forma asíncrona  
✅ **EventHandlers**: Reaccionan a eventos  

### SOLID

✅ **Single Responsibility**: Cada clase tiene una única responsabilidad  
✅ **Open/Closed**: Fácil agregar nuevos EventHandlers sin modificar código existente  
✅ **Liskov Substitution**: Las implementaciones respetan las interfaces  
✅ **Interface Segregation**: Interfaces específicas y pequeñas  
✅ **Dependency Inversion**: Dependemos de abstracciones, no de implementaciones  

---

## 📚 Ejemplo Completo: Módulo User

### Estructura de Archivos

```
src/
├── Http/
│   └── V1/
│       ├── Controller/
│       │   └── User/
│       │       └── User/
│       │           └── CreateUserController.php
│       └── Request/
│           └── User/
│               └── User/
│                   └── CreateUserRequest.php
└── User/
    └── User/
        ├── Application/
        │   ├── Command/
        │   │   ├── CreateUserCommand.php
        │   │   └── CreateUserCommandHandler.php
        │   ├── EventHandler/
        │   │   └── SendUserConfirmationEmailHandler.php
        │   └── Service/
        │       ├── EmailSender.php (interface)
        │       └── UserCreator.php
        ├── Domain/
        │   ├── Event/
        │   │   └── UserCreated.php
        │   ├── Repository/
        │   │   └── UserRepository.php (interface)
        │   ├── Service/
        │   │   └── UserEmailUniquenessChecker.php
        │   ├── User.php (Aggregate Root)
        │   ├── UserId.php (Value Object)
        │   ├── UserEmail.php (Value Object)
        │   ├── UserName.php (Value Object)
        │   └── ...
        └── Infrastructure/
            ├── Persistence/
            │   └── Doctrine/
            │       └── DoctrineUserRepository.php
            └── Service/
                └── MailerEmailSender.php
```

### Flujo Completo

1. **HTTP Request** → `POST /v1/users`
2. **Controller** → Valida con Symfony Validator, crea `CreateUserCommand`
3. **CommandBus** → Despacha el comando
4. **CommandHandler** → Recibe el comando, llama a `UserCreator`
5. **UserCreator** → Crea Value Objects, hashea password, llama a `User::create()`
6. **User::create()** → Valida unicidad con `UserEmailUniquenessChecker`, registra `UserCreated` event
7. **UserRepository** → Persiste el usuario
8. **EventBus** → Publica `UserCreated` a RabbitMQ
9. **EventHandler** → Consume el evento, envía email de confirmación

---

## ✅ Checklist para Crear un Nuevo Módulo

- [ ] Crear estructura de carpetas `ModuleName/EntityName`
- [ ] Crear Controller con validación Symfony Validator
- [ ] Crear Request DTO con validaciones
- [ ] Crear Command (CQRS)
- [ ] Crear CommandHandler (CQRS)
- [ ] Crear Application Service
- [ ] Crear Aggregate Root extendiendo `AggregateRoot`
- [ ] Crear Value Objects para atributos
- [ ] Crear Domain Service si se necesita validación con repositorio
- [ ] Crear Repository Interface (Domain)
- [ ] Implementar Repository (Infrastructure)
- [ ] Crear Domain Event si hay efectos secundarios
- [ ] Crear EventHandler para efectos secundarios
- [ ] Crear interfaces de servicios externos (si aplica)
- [ ] Implementar servicios externos (Infrastructure)
- [ ] Configurar servicios en `config/services.yaml`
- [ ] Configurar routing en `config/routes/`
- [ ] Configurar messenger routing si hay eventos

---

## 🔍 Preguntas Frecuentes

### ¿Por qué validar en el Controller y también en el Domain?

- **Controller**: Valida la **forma** (formato, tipos, longitud) - validación técnica
- **Domain**: Valida las **reglas de negocio** (unicidad, consistencia) - validación de negocio

### ¿Cuándo usar Domain Service vs método en Aggregate Root?

- **Aggregate Root**: Lógica que solo involucra al agregado mismo
- **Domain Service**: Lógica que requiere múltiples agregados o acceso al repositorio

### ¿Por qué solo un evento por operación?

- **Single Responsibility**: El servicio solo hace una cosa (crear usuario)
- **Desacoplamiento**: Los efectos secundarios se manejan en EventHandlers separados
- **Escalabilidad**: Fácil agregar nuevos efectos sin modificar el servicio

### ¿Cómo se conecta el CommandHandler con el Command?

Symfony Messenger detecta automáticamente el handler por:
1. El método `__invoke()` que recibe el tipo de Command
2. O el atributo `#[AsMessageHandler]`

---

## 📖 Referencias

- [Domain-Driven Design (Eric Evans)](https://www.domainlanguage.com/ddd/)
- [Hexagonal Architecture (Alistair Cockburn)](https://alistair.cockburn.us/hexagonal-architecture/)
- [CQRS Pattern (Martin Fowler)](https://martinfowler.com/bliki/CQRS.html)
- [SOLID Principles (Robert C. Martin)](https://en.wikipedia.org/wiki/SOLID)

---

**Última actualización**: 11-11-2025

