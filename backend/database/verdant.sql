DROP DATABASE IF EXISTS verdant;
CREATE DATABASE verdant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE verdant;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(50)  NOT NULL,
    last_name     VARCHAR(50)  NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone         VARCHAR(30)  NULL,
    role          ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE restaurant_tables (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    capacity     INT NOT NULL,
    is_active    BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE reservations (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    table_id         INT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests           INT NOT NULL,
    special_requests TEXT NULL,
    status           ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservations_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reservations_table
        FOREIGN KEY (table_id) REFERENCES restaurant_tables(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE menu_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    category_id  INT NOT NULL,
    name         VARCHAR(100)  NOT NULL,
    description  TEXT NULL,
    price        DECIMAL(8, 2) NOT NULL,
    image_url    VARCHAR(255)  NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_menu_items_category
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contact_messages_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO users (first_name, last_name, email, password_hash, phone, role) VALUES
('Admin', 'Verdant', 'admin@verdant.com', '$2y$10$7vPjbOpseyd/8ukXX64zfuh6zkstPlQ4eo4TQoYjVxJ46ijxJPrtO', '+387 33 000 000', 'admin'),
('Amina', 'Hodzic',  'amina@example.com', '$2y$10$2y4SNbI4EnRM2HpJCQ1r6.ZyHh2c.dWxWYFjmUUxPdwSncTndKrAC', '+387 61 111 222', 'user'),
('Haris', 'Begic',   'haris@example.com', '$2y$10$2y4SNbI4EnRM2HpJCQ1r6.ZyHh2c.dWxWYFjmUUxPdwSncTndKrAC', '+387 62 333 444', 'user');

INSERT INTO restaurant_tables (table_number, capacity, is_active) VALUES
(1, 2, TRUE),
(2, 2, TRUE),
(3, 4, TRUE),
(4, 4, TRUE),
(5, 6, TRUE),
(6, 8, FALSE);

INSERT INTO categories (name, description) VALUES
('Starters', 'Light dishes to start your meal'),
('Main Courses', 'Hearty plates from our kitchen'),
('Desserts', 'Sweet endings'),
('Drinks', 'Cold and hot beverages');

INSERT INTO menu_items (category_id, name, description, price, image_url, is_available) VALUES
(1, 'Bruschetta', 'Toasted bread with tomatoes, garlic and basil', 7.50, 'assets/img/menu/bruschetta.jpg', TRUE),
(1, 'Caprese Salad', 'Mozzarella, tomatoes and fresh basil', 9.00, 'assets/img/menu/caprese.jpg', TRUE),
(2, 'Grilled Salmon', 'Salmon fillet with seasonal vegetables', 24.00, 'assets/img/menu/salmon.jpg', TRUE),
(2, 'Beef Steak', 'Ribeye steak with roasted potatoes', 29.50, 'assets/img/menu/steak.jpg', TRUE),
(2, 'Mushroom Risotto', 'Creamy risotto with wild mushrooms', 18.00, 'assets/img/menu/risotto.jpg', TRUE),
(3, 'Tiramisu', 'Classic Italian dessert', 8.00, 'assets/img/menu/tiramisu.jpg', TRUE),
(3, 'Chocolate Lava Cake', 'Warm cake with a molten center', 8.50, 'assets/img/menu/lava-cake.jpg', FALSE),
(4, 'Fresh Lemonade', 'Homemade lemonade with mint', 4.00, 'assets/img/menu/lemonade.jpg', TRUE),
(4, 'Espresso', 'Single shot of espresso', 2.50, 'assets/img/menu/espresso.jpg', TRUE);

INSERT INTO reservations (user_id, table_id, reservation_date, reservation_time, guests, special_requests, status) VALUES
(2, 3,    '2026-12-05', '19:00:00', 4, 'Window seat if possible', 'confirmed'),
(2, NULL, '2026-12-12', '20:30:00', 2, NULL, 'pending'),
(3, 5,    '2026-12-06', '18:00:00', 6, 'Birthday celebration', 'confirmed'),
(3, 1,    '2026-11-15', '13:00:00', 2, NULL, 'completed');

INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES
(2,    'Amina Hodzic', 'amina@example.com', 'Gluten-free options', 'Do you offer gluten-free pasta?'),
(NULL, 'Emir Kovac',   'emir@example.com',  'Private event',      'I would like to book the restaurant for a private event of 30 people.');
