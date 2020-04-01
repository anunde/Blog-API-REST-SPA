# CREAMOS LA BASE DE DATOS #

CREATE DATABASE IF NOT EXISTS ng_blog_laravel;

USE ng_blog_laravel;

# CREAMOS TABLAS #

CREATE TABLE IF NOT EXISTS users(
id int(255) NOT NULL auto_increment,
name varchar(50) NOT NULL,
surname varchar(100),
role varchar(20),
email varchar(255) NOT NULL,
password varchar(255) NOT NULL,
description text,
image varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
remember_token varchar(255),
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDB;

# INTRODUCIMOS VALORES DE PRUEBA PARA USUARIOS #
INSERT INTO users VALUES(NULL, 'admin', 'admin', 'ROLE_ADMIN', 'admin@admin.com', 'admin', 'descripcion admin', null, CURDATE(), CURDATE(), null);

CREATE TABLE IF NOT EXISTS categories(
id int(255) not null auto_increment,
name varchar(100),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDB;

# INTRODUCIMOS VALORES DE PRUEBA PARA CATEGORIAS #
INSERT INTO categories VALUES(NULL, 'Ordenadores', CURDATE(), CURDATE());
INSERT INTO categories VALUES(NULL, 'Móviles y tablets', CURDATE(), CURDATE());

CREATE TABLE IF NOT EXISTS posts(
id int(255) not null auto_increment,
user_id int(255) NOT NULL,
category_id int(255) NOT NULL,
title varchar(255) NOT NULL,
content text NOT NULL,
image varchar(255),
created_at datetime DEFAULT NULL,
updated_at datetime DEFAULT NULL,
CONSTRAINT pk_posts PRIMARY KEY(id),
CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDB;

# INTRODUCIMOS VALORES DE PRUEBA PARA PUBLICACIONES #
INSERT INTO posts VALUES(NULL, 1, 2, 'Samsung Galaxy s8', 'Contenido Samsung Galaxy s8', NULL, CURDATE(), CURDATE());
INSERT INTO posts VALUES(NULL, 1, 1, 'Asus Rog Strix', 'Contenido Asus Rog Strix', NULL, CURDATE(), CURDATE());
INSERT INTO posts VALUES(NULL, 1, 1, 'MSI Power', 'Contenido MSI Power', NULL, CURDATE(), CURDATE());