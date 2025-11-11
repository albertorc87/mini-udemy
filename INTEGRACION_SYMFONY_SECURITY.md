# Integración de Symfony Security con Mapeos XML y DDD

## Resumen

Esta configuración permite usar **Symfony Security** con:
- ✅ **Mapeos XML de Doctrine** (en lugar de atributos PHP)
- ✅ **Estructura DDD** con dominio e infraestructura separados
- ✅ **Roles personalizados** desde tu tabla `user_role`

## Estructura Creada

### 1. Configuración de Doctrine (XML Mappings)

**Archivo:** `config/packages/doctrine.yaml`

```yaml
mappings:
    Udemy:
        type: xml
        dir: '%kernel.project_dir%/config/doctrine'
        prefix: 'Udemy'
```

**Archivos XML de mapeo:**
- `config/doctrine/User.User.orm.xml` - Mapeo de la entidad User
- `config/doctrine/Role.Role.orm.xml` - Mapeo de la entidad Role

### 2. Entidades del Dominio

**Ubicación:** `src/User/Domain/Entity/`

- **User.php**: Implementa `UserInterface` y `PasswordAuthenticatedUserInterface`
  - Mapeo XML en `config/doctrine/User.User.orm.xml`
  - Método `getRoles()` retorna array de strings para Symfony Security
  - Método `getRoleEntities()` retorna array de objetos Role para el dominio

- **Role.php**: Entidad Role simple
  - Mapeo XML en `config/doctrine/Role.Role.orm.xml`

### 3. UserProvider Personalizado

**Ubicación:** `src/User/Infrastructure/Security/UserProvider.php`

- Carga usuarios desde la base de datos usando Doctrine
- Carga automáticamente los roles gracias al mapeo `many-to-many` en XML
- Implementa `UserProviderInterface` de Symfony Security

### 4. Configuración de Security

**Archivo:** `config/packages/security.yaml`

- Configura el `UserProvider` personalizado
- Define jerarquía de roles:
  - `ROLE_ADMIN` → `ROLE_TEACHER` → `ROLE_STUDENT`
- Configura firewall con form_login

## Cómo Funciona

### Flujo de Autenticación

1. **Usuario intenta login** → Symfony Security intercepta
2. **UserProvider** busca el usuario por email en la BD
3. **Doctrine** carga automáticamente los roles desde `user_role` (gracias al XML mapping)
4. **User::getRoles()** convierte los objetos Role a strings (`['ROLE_ADMIN', 'ROLE_STUDENT']`)
5. **Symfony Security** valida la contraseña y crea la sesión

### Ventajas de esta Arquitectura

✅ **Separación de responsabilidades**: Dominio limpio, infraestructura separada
✅ **Mapeos XML**: Sin atributos en el código PHP del dominio
✅ **Flexibilidad**: Puedes usar Value Objects en el dominio sin problemas
✅ **Symfony Security**: Todas las funcionalidades de seguridad de Symfony disponibles

## Próximos Pasos

### 1. Instalar Symfony Security Bundle

```bash
composer require symfony/security-bundle
```

### 2. Crear Value Objects (Opcional)

Puedes crear Value Objects en `src/User/Domain/ValueObject/`:
- `Email.php`
- `Password.php`
- `UserId.php`

Y usarlos en la entidad User sin problemas, ya que el mapeo XML está separado.

### 3. Crear Controladores de Login

```php
// src/User/Http/Controller/LoginController.php
class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        // Symfony Security maneja el login automáticamente
    }
}
```

### 4. Usar en Controladores

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
public function adminAction(): Response
{
    // Solo usuarios con ROLE_ADMIN pueden acceder
}
```

## Ejemplo de Uso

```php
// Obtener usuario actual
$user = $this->getUser(); // Retorna User|null

if ($user instanceof User) {
    $email = $user->getEmail();
    $roles = $user->getRoles(); // ['ROLE_ADMIN', 'ROLE_STUDENT']
    $roleEntities = $user->getRoleEntities(); // [Role, Role]
}
```

## Notas Importantes

1. **Los roles deben empezar con `ROLE_`** para que Symfony Security los reconozca
2. **El mapeo XML carga automáticamente** la relación `many-to-many` entre User y Role
3. **El UserProvider es automáticamente inyectado** gracias a autowire en `services.yaml`
4. **Puedes añadir más métodos** a User sin afectar el mapeo XML

## Troubleshooting

### Error: "User class not found"
- Verifica que el namespace en el XML mapping coincida con la clase PHP
- Ejecuta: `php bin/console cache:clear`

### Error: "Roles not loaded"
- Verifica que el mapeo `many-to-many` en XML esté correcto
- Asegúrate de que los roles en la BD tengan el formato `ROLE_*`

### Error: "UserProvider not found"
- Verifica que el servicio esté registrado en `services.yaml`
- Ejecuta: `php bin/console debug:container UserProvider`

