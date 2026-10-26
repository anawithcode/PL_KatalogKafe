CREATE DATABASE IF NOT EXISTS katalog_kafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE katalog_kafe;

CREATE TABLE IF NOT EXISTS menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(30) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_menus_category (category),
    INDEX idx_menus_availability (is_available)
) ENGINE=InnoDB;

INSERT INTO menus (name, category, description, price, stock, is_available, created_at, updated_at)
SELECT * FROM (
    SELECT 'Kopi Susu Senja', 'Kopi', 'Espresso, susu segar, dan gula aren dengan rasa karamel yang lembut.', 24000, 32, 1, NOW(), NOW()
    UNION ALL SELECT 'Es Americano', 'Kopi', 'Double shot espresso dengan air dingin dan es; bersih dan menyegarkan.', 22000, 24, 1, NOW(), NOW()
    UNION ALL SELECT 'Matcha Cloud', 'Non-Kopi', 'Matcha premium, susu, dan lapisan krim ringan tanpa rasa terlalu manis.', 28000, 18, 1, NOW(), NOW()
    UNION ALL SELECT 'Cokelat Klasik', 'Non-Kopi', 'Cokelat pekat dengan susu segar, tersedia dingin maupun hangat.', 25000, 0, 0, NOW(), NOW()
    UNION ALL SELECT 'Butter Croissant', 'Pastry', 'Croissant berlapis dengan aroma mentega, dipanggang setiap pagi.', 26000, 12, 1, NOW(), NOW()
    UNION ALL SELECT 'Pain au Chocolat', 'Pastry', 'Pastry renyah berisi dua batang cokelat hitam yang lumer.', 29000, 8, 1, NOW(), NOW()
    UNION ALL SELECT 'Nasi Ayam Sambal Matah', 'Makanan', 'Nasi hangat, ayam bumbu, sambal matah, dan sayur renyah.', 42000, 15, 1, NOW(), NOW()
    UNION ALL SELECT 'Spaghetti Aglio e Olio', 'Makanan', 'Spaghetti dengan minyak zaitun, bawang putih, cabai, dan parsley.', 45000, 6, 1, NOW(), NOW()
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM menus LIMIT 1);
