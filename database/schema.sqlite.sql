CREATE TABLE menus (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(30) NOT NULL,
    description TEXT,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INTEGER NOT NULL DEFAULT 0,
    is_available INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE INDEX idx_menus_category ON menus(category);
CREATE INDEX idx_menus_availability ON menus(is_available);

INSERT INTO menus (name, category, description, price, stock, is_available, created_at, updated_at) VALUES
('Kopi Susu Senja', 'Kopi', 'Espresso, susu segar, dan gula aren dengan rasa karamel yang lembut.', 24000, 32, 1, '2026-10-20 08:00:00', '2026-10-20 08:00:00'),
('Es Americano', 'Kopi', 'Double shot espresso dengan air dingin dan es; bersih dan menyegarkan.', 22000, 24, 1, '2026-10-20 08:05:00', '2026-10-20 08:05:00'),
('Matcha Cloud', 'Non-Kopi', 'Matcha premium, susu, dan lapisan krim ringan tanpa rasa terlalu manis.', 28000, 18, 1, '2026-10-20 08:10:00', '2026-10-20 08:10:00'),
('Cokelat Klasik', 'Non-Kopi', 'Cokelat pekat dengan susu segar, tersedia dingin maupun hangat.', 25000, 0, 0, '2026-10-20 08:15:00', '2026-10-20 08:15:00'),
('Butter Croissant', 'Pastry', 'Croissant berlapis dengan aroma mentega, dipanggang setiap pagi.', 26000, 12, 1, '2026-10-20 08:20:00', '2026-10-20 08:20:00'),
('Pain au Chocolat', 'Pastry', 'Pastry renyah berisi dua batang cokelat hitam yang lumer.', 29000, 8, 1, '2026-10-20 08:25:00', '2026-10-20 08:25:00'),
('Nasi Ayam Sambal Matah', 'Makanan', 'Nasi hangat, ayam bumbu, sambal matah, dan sayur renyah.', 42000, 15, 1, '2026-10-20 08:30:00', '2026-10-20 08:30:00'),
('Spaghetti Aglio e Olio', 'Makanan', 'Spaghetti dengan minyak zaitun, bawang putih, cabai, dan parsley.', 45000, 6, 1, '2026-10-20 08:35:00', '2026-10-20 08:35:00');
