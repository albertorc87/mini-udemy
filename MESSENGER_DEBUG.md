# Guía de Debug para Messenger/RabbitMQ

## Problema Común: Los mensajes llegan a RabbitMQ pero no se procesan

### Síntomas
- Los mensajes aparecen en el panel de RabbitMQ (http://localhost:15672)
- Los emails no se envían
- No hay errores visibles

### Causa
**No hay un worker consumiendo los mensajes de RabbitMQ**

## Solución: Iniciar el Worker

### Opción 1: Usar Makefile (Recomendado)
```bash
make workers
```

### Opción 2: Comando directo
```bash
docker compose exec php php bin/console messenger:consume async -vv
```

### Opción 3: En segundo plano (para desarrollo)
```bash
docker compose exec -d php php bin/console messenger:consume async -vv
```

## Verificar que funciona

### 1. Ver mensajes en RabbitMQ
1. Abre http://localhost:15672
2. Usuario: `guest` / Contraseña: `guest`
3. Ve a **Queues**
4. Deberías ver una cola llamada `messages` (o similar)
5. Los mensajes deberían aparecer y desaparecer cuando el worker los procesa

### 2. Ver logs del worker
```bash
# Ver logs en tiempo real
docker compose logs -f php

# O ver logs específicos del worker
docker compose exec php php bin/console messenger:stats
```

### 3. Verificar emails en MailHog
1. Abre http://localhost:8025
2. Deberías ver los emails enviados

## Comandos útiles

### Ver estadísticas de mensajes
```bash
make messenger-stats
```

### Ver mensajes fallidos
```bash
make messenger-failed
```

### Reintentar mensajes fallidos
```bash
make messenger-retry
```

### Detener workers
```bash
make worker-stop
```

## Configuración de Variables de Entorno

Asegúrate de tener estas variables en tu `.env`:

```env
# RabbitMQ/AMQP
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbit:5672/%2f/messages

# Mailer (MailHog para desarrollo)
MAILER_DSN=smtp://mailhog:1025
MAILER_FROM_EMAIL=noreply@miniudemy.local
```

## Flujo completo

1. **Crear usuario** → Se publica evento `UserCreated`
2. **EventBus** → Envía evento a RabbitMQ (cola `async`)
3. **Worker** → Consume mensaje de RabbitMQ
4. **EventHandler** → `SendUserConfirmationEmailHandler` procesa el evento
5. **EmailSender** → Envía email a través de MailHog
6. **MailHog** → Recibe y muestra el email

## Troubleshooting

### El worker no inicia
```bash
# Verificar que RabbitMQ esté corriendo
docker compose ps

# Verificar conexión
docker compose exec php php bin/console debug:messenger
```

### Los mensajes se acumulan en RabbitMQ
- Verifica que el worker esté corriendo: `docker compose ps`
- Verifica logs del worker: `docker compose logs php`

### Los emails no llegan a MailHog
- Verifica que MailHog esté corriendo: `docker compose ps`
- Verifica configuración de MAILER_DSN
- Revisa logs: `docker compose logs mailhog`

### Ver mensajes en la cola de RabbitMQ
1. Abre http://localhost:15672
2. Ve a **Queues** → **messages**
3. Haz clic en la cola para ver detalles
4. En **Get messages** puedes ver el contenido de los mensajes

## Producción

En producción, necesitarás:
- Un proceso supervisor (supervisord, systemd, etc.) para mantener el worker corriendo
- O usar un servicio como AWS SQS, Google Cloud Tasks, etc.
- Configurar retry y dead letter queues apropiadamente

