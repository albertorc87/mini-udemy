<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109101334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema for Udemy-like platform: users, roles, courses, lessons, orders, coupons, and ratings';
    }

    public function up(Schema $schema): void
    {
        // Tabla: user
        $this->addSql('CREATE TABLE "user" (
            id VARCHAR(26) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            avatar_url VARCHAR(500) DEFAULT NULL,
            status INTEGER DEFAULT 0,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT check_user_status CHECK (status IN (-1, 0, 1))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');

        // Tabla: role
        $this->addSql('CREATE TABLE role (
            id VARCHAR(26) NOT NULL,
            name VARCHAR(50) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');

        // Tabla: user_role (N a M)
        $this->addSql('CREATE TABLE user_role (
            user_id VARCHAR(26) NOT NULL,
            role_id VARCHAR(26) NOT NULL,
            PRIMARY KEY(user_id, role_id)
        )');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Tabla: course
        $this->addSql('CREATE TABLE course (
            id VARCHAR(26) NOT NULL,
            teacher_id VARCHAR(26) NOT NULL,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(500) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            price NUMERIC(10, 2) NOT NULL,
            average_rating NUMERIC(3, 2) DEFAULT 0.00,
            total_ratings INTEGER DEFAULT 0,
            status VARCHAR(20) DEFAULT \'draft\',
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_169E6FB941807E1D ON course (teacher_id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB941807E1D FOREIGN KEY (teacher_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Tabla: course_lesson
        $this->addSql('CREATE TABLE course_lesson (
            id VARCHAR(26) NOT NULL,
            course_id VARCHAR(26) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            video_url VARCHAR(500) DEFAULT NULL,
            content TEXT DEFAULT NULL,
            "order" INTEGER NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_8C5C4A2E591CC992 ON course_lesson (course_id)');
        $this->addSql('ALTER TABLE course_lesson ADD CONSTRAINT FK_8C5C4A2E591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Tabla: user_course (cursos comprados por usuarios)
        $this->addSql('CREATE TABLE user_course (
            id VARCHAR(26) NOT NULL,
            user_id VARCHAR(26) NOT NULL,
            course_id VARCHAR(26) NOT NULL,
            purchased_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_73A74900A76ED395 ON user_course (user_id)');
        $this->addSql('CREATE INDEX IDX_73A74900591CC992 ON user_course (course_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73A74900A76ED395591CC992 ON user_course (user_id, course_id)');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73A74900A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73A74900591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Tabla: course_rating (valoraciones de cursos)
        $this->addSql('CREATE TABLE course_rating (
            id VARCHAR(26) NOT NULL,
            user_id VARCHAR(26) NOT NULL,
            course_id VARCHAR(26) NOT NULL,
            rating INTEGER NOT NULL,
            comment TEXT DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_5B0A3A0DA76ED395 ON course_rating (user_id)');
        $this->addSql('CREATE INDEX IDX_5B0A3A0D591CC992 ON course_rating (course_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5B0A3A0DA76ED395591CC992 ON course_rating (user_id, course_id)');
        $this->addSql('ALTER TABLE course_rating ADD CONSTRAINT FK_5B0A3A0DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_rating ADD CONSTRAINT FK_5B0A3A0D591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE course_rating ADD CONSTRAINT check_rating_range CHECK (rating >= 1 AND rating <= 5)');

        // Tabla: coupon
        $this->addSql('CREATE TABLE coupon (
            id VARCHAR(26) NOT NULL,
            code VARCHAR(50) NOT NULL,
            discount_type VARCHAR(20) NOT NULL,
            discount_value NUMERIC(10, 2) NOT NULL,
            minimum_price NUMERIC(10, 2) DEFAULT 10.99,
            teacher_id VARCHAR(26) DEFAULT NULL,
            is_general BOOLEAN DEFAULT false,
            valid_from TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            max_uses INTEGER DEFAULT NULL,
            current_uses INTEGER DEFAULT 0,
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64BF3F0277153098 ON coupon (code)');
        $this->addSql('CREATE INDEX IDX_64BF3F0241807E1D ON coupon (teacher_id)');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F0241807E1D FOREIGN KEY (teacher_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT check_minimum_price CHECK (minimum_price >= 10.99)');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT check_discount_value CHECK (discount_value > 0)');

        // Tabla: "order" (pedidos)
        $this->addSql('CREATE TABLE "order" (
            id VARCHAR(26) NOT NULL,
            user_id VARCHAR(26) NOT NULL,
            coupon_id VARCHAR(26) DEFAULT NULL,
            subtotal NUMERIC(10, 2) NOT NULL,
            discount NUMERIC(10, 2) DEFAULT 0.00,
            total NUMERIC(10, 2) NOT NULL,
            status VARCHAR(20) DEFAULT \'pending\',
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
        $this->addSql('CREATE INDEX IDX_F529939866C5951B ON "order" (coupon_id)');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F529939866C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Tabla: order_item (items de pedidos)
        $this->addSql('CREATE TABLE order_item (
            id VARCHAR(26) NOT NULL,
            order_id VARCHAR(26) NOT NULL,
            course_id VARCHAR(26) NOT NULL,
            price NUMERIC(10, 2) NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_52EA1F098D9F6D38 ON order_item (order_id)');
        $this->addSql('CREATE INDEX IDX_52EA1F09591CC992 ON order_item (course_id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Insertar roles iniciales
        $now = date('Y-m-d H:i:s');
        $this->addSql("INSERT INTO role (id, name, created_at, updated_at) VALUES 
            ('01JQZ0K0ABCDEFGHIJKLMNOPQ', 'ROLE_STUDENT', '$now', '$now'),
            ('01JQZ0K0BCDEFGHIJKLMNOPQRS', 'ROLE_TEACHER', '$now', '$now'),
            ('01JQZ0K0CDEFGHIJKLMNOPQRST', 'ROLE_ADMIN', '$now', '$now')
        ");

        // Insertar usuario administrador inicial
        // Password: 'admin123' (debe cambiarse después del primer login)
        // Hash generado con: password_hash('admin123', PASSWORD_BCRYPT)
        // Status: 1 (ACTIVE) - Usuario activo desde el inicio
        $adminId = '01JQZ0K0DEFGHIJKLMNOPQRSTU';
        $adminPasswordHash = '$2y$10$IrZ.3/yfGd0bPUXJkfDXVOe.r80aRe/MPvw110oAJOfGynRT3u1vG'; // admin123
        $this->addSql("INSERT INTO \"user\" (id, email, password, name, avatar_url, status, created_at, updated_at) VALUES 
            ('$adminId', 'alberto.r.caballero.87@gmail.com', '$adminPasswordHash', 'Alberto', NULL, 1, '$now', '$now')
        ");

        // Asignar rol ADMIN al usuario administrador
        $this->addSql("INSERT INTO user_role (user_id, role_id) VALUES 
            ('$adminId', '01JQZ0K0CDEFGHIJKLMNOPQRST')
        ");
    }

    public function down(Schema $schema): void
    {
        // Eliminar tablas en orden inverso (respetando foreign keys)
        $this->addSql('DROP TABLE IF EXISTS order_item');
        $this->addSql('DROP TABLE IF EXISTS "order"');
        $this->addSql('DROP TABLE IF EXISTS coupon');
        $this->addSql('DROP TABLE IF EXISTS course_rating');
        $this->addSql('DROP TABLE IF EXISTS user_course');
        $this->addSql('DROP TABLE IF EXISTS course_lesson');
        $this->addSql('DROP TABLE IF EXISTS course');
        $this->addSql('DROP TABLE IF EXISTS user_role');
        $this->addSql('DROP TABLE IF EXISTS role');
        $this->addSql('DROP TABLE IF EXISTS "user"');
    }
}
