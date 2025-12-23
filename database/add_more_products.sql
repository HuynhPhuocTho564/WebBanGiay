-- ==========================================================
-- TẮT SAFE MODE
-- ==========================================================
SET SQL_SAFE_UPDATES = 0;

-- ==========================================================
-- BƯỚC 1: XÓA DỮ LIỆU TRÙNG LẶP
-- ==========================================================

-- Xóa danh mục trùng (giữ lại ID nhỏ nhất)
DELETE c1 FROM categories c1
INNER JOIN categories c2 
WHERE c1.id > c2.id AND c1.name = c2.name;

-- Xóa thương hiệu trùng (giữ lại ID nhỏ nhất)  
DELETE b1 FROM brands b1
INNER JOIN brands b2 
WHERE b1.id > b2.id AND b1.name = b2.name;

-- Bật lại safe mode
SET SQL_SAFE_UPDATES = 1;

-- ==========================================================
-- BƯỚC 2: THÊM DANH MỤC MỚI (chỉ thêm nếu chưa có)
-- ==========================================================
INSERT IGNORE INTO categories (name, slug, status) VALUES
('Sneaker', 'sneaker', 1),
('Giày Chạy Bộ', 'giay-chay-bo', 1),
('Giày Thể Thao', 'giay-the-thao', 1),
('Giày Cao Gót', 'giay-cao-got', 1),
('Giày Búp Bê', 'giay-bup-be', 1),
('Sandal', 'sandal', 1),
('Boot', 'boot', 1),
('Giày Lười', 'giay-luoi', 1);

-- ==========================================================
-- BƯỚC 3: THÊM THƯƠNG HIỆU MỚI (chỉ thêm nếu chưa có)
-- ==========================================================
INSERT IGNORE INTO brands (name, logo, slug) VALUES
('Nike', 'nike.png', 'nike'),
('Adidas', 'adidas.png', 'adidas'),
('Puma', 'puma.png', 'puma'),
('New Balance', 'new-balance.png', 'new-balance'),
('Converse', 'converse.png', 'converse'),
('Vans', 'vans.png', 'vans'),
('Reebok', 'reebok.png', 'reebok'),
('Fila', 'fila.png', 'fila'),
('Asics', 'asics.png', 'asics'),
('Skechers', 'skechers.png', 'skechers');

-- ==========================================================
-- BƯỚC 4: LẤY ID DANH MỤC VÀ THƯƠNG HIỆU
-- ==========================================================
-- Chạy lệnh này để xem ID:
-- SELECT id, name FROM categories;
-- SELECT id, name FROM brands;

-- ==========================================================
-- BƯỚC 5: THÊM 10 SẢN PHẨM NAM
-- Thay @cat_sneaker, @brand_nike... bằng ID thực tế
-- ==========================================================

-- Lấy ID động
SET @cat_sneaker = (SELECT id FROM categories WHERE slug = 'sneaker' LIMIT 1);
SET @cat_chaybo = (SELECT id FROM categories WHERE slug = 'giay-chay-bo' LIMIT 1);
SET @cat_thethao = (SELECT id FROM categories WHERE slug = 'giay-the-thao' LIMIT 1);

SET @brand_nike = (SELECT id FROM brands WHERE slug = 'nike' LIMIT 1);
SET @brand_adidas = (SELECT id FROM brands WHERE slug = 'adidas' LIMIT 1);
SET @brand_puma = (SELECT id FROM brands WHERE slug = 'puma' LIMIT 1);
SET @brand_nb = (SELECT id FROM brands WHERE slug = 'new-balance' LIMIT 1);
SET @brand_converse = (SELECT id FROM brands WHERE slug = 'converse' LIMIT 1);
SET @brand_vans = (SELECT id FROM brands WHERE slug = 'vans' LIMIT 1);
SET @brand_reebok = (SELECT id FROM brands WHERE slug = 'reebok' LIMIT 1);
SET @brand_fila = (SELECT id FROM brands WHERE slug = 'fila' LIMIT 1);
SET @brand_asics = (SELECT id FROM brands WHERE slug = 'asics' LIMIT 1);
SET @brand_skechers = (SELECT id FROM brands WHERE slug = 'skechers' LIMIT 1);

-- Thêm sản phẩm Nam
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender, views) VALUES
(@cat_sneaker, @brand_nike, 'Nike Air Force 1 Low White', 'nike-air-force-1-low-white', 2890000, 2590000, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500', 'Giày Nike Air Force 1 phiên bản trắng cổ điển.', 'Male', 150),
(@cat_sneaker, @brand_nike, 'Nike Dunk Low Retro Black', 'nike-dunk-low-retro-black', 3190000, 2890000, 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=500', 'Nike Dunk Low Retro với phối màu đen trắng.', 'Male', 200),
(@cat_chaybo, @brand_adidas, 'Adidas Ultraboost 22', 'adidas-ultraboost-22', 4500000, 3990000, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=500', 'Giày chạy bộ cao cấp với công nghệ Boost.', 'Male', 180),
(@cat_sneaker, @brand_adidas, 'Adidas Stan Smith', 'adidas-stan-smith', 2500000, 2190000, 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=500', 'Giày Adidas Stan Smith kinh điển.', 'Male', 220),
(@cat_thethao, @brand_puma, 'Puma RS-X Reinvention', 'puma-rs-x-reinvention', 3290000, 2790000, 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500', 'Giày Puma RS-X chunky hiện đại.', 'Male', 95),
(@cat_sneaker, @brand_nb, 'New Balance 574 Classic', 'new-balance-574-classic', 2690000, 2390000, 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=500', 'New Balance 574 biểu tượng.', 'Male', 175),
(@cat_sneaker, @brand_converse, 'Converse Chuck Taylor All Star', 'converse-chuck-taylor-all-star', 1590000, 1390000, 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=500', 'Converse Chuck Taylor cổ cao.', 'Male', 300),
(@cat_sneaker, @brand_vans, 'Vans Old Skool Black White', 'vans-old-skool-black-white', 1890000, 1690000, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500', 'Vans Old Skool với sọc Jazz Stripe.', 'Male', 250),
(@cat_thethao, @brand_reebok, 'Reebok Classic Leather', 'reebok-classic-leather', 2290000, 1990000, 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=500', 'Reebok Classic Leather retro.', 'Male', 120),
(@cat_chaybo, @brand_asics, 'Asics Gel-Kayano 29', 'asics-gel-kayano-29', 4190000, 3690000, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=500', 'Giày chạy bộ Asics GEL.', 'Male', 85);

-- ==========================================================
-- BƯỚC 6: THÊM 30 SẢN PHẨM NỮ
-- ==========================================================
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender, views) VALUES
(@cat_sneaker, @brand_nike, 'Nike Air Max 270 React', 'nike-air-max-270-react-nu', 4290000, 3790000, 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=500', 'Nike Air Max 270 React đệm khí.', 'Female', 280),
(@cat_sneaker, @brand_nike, 'Nike Air Jordan 1 Low', 'nike-air-jordan-1-low-nu', 3490000, 3090000, 'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=500', 'Air Jordan 1 Low pastel.', 'Female', 320),
(@cat_chaybo, @brand_nike, 'Nike Free Run 5.0', 'nike-free-run-5-0-nu', 3090000, 2690000, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=500', 'Nike Free Run nhẹ linh hoạt.', 'Female', 190),
(@cat_sneaker, @brand_nike, 'Nike Blazer Mid 77', 'nike-blazer-mid-77-nu', 2790000, 2490000, 'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=500', 'Nike Blazer Mid 77 vintage.', 'Female', 210),
(@cat_sneaker, @brand_adidas, 'Adidas Superstar White', 'adidas-superstar-white-nu', 2690000, 2390000, 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=500', 'Adidas Superstar mũi vỏ sò.', 'Female', 350),
(@cat_sneaker, @brand_adidas, 'Adidas Gazelle Pink', 'adidas-gazelle-pink-nu', 2490000, 2190000, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=500', 'Adidas Gazelle hồng pastel.', 'Female', 280),
(@cat_chaybo, @brand_adidas, 'Adidas NMD R1 Women', 'adidas-nmd-r1-women', 3990000, 3490000, 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=500', 'Adidas NMD R1 Boost.', 'Female', 165),
(@cat_sneaker, @brand_adidas, 'Adidas Forum Low White', 'adidas-forum-low-white-nu', 2890000, 2590000, 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=500', 'Adidas Forum Low retro.', 'Female', 145),
(@cat_sneaker, @brand_puma, 'Puma Cali Star Women', 'puma-cali-star-women', 2590000, 2290000, 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=500', 'Puma Cali Star platform.', 'Female', 195),
(@cat_sneaker, @brand_puma, 'Puma Mayze Leather', 'puma-mayze-leather-nu', 2890000, 2490000, 'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=500', 'Puma Mayze chunky.', 'Female', 175),
(@cat_thethao, @brand_puma, 'Puma Suede Classic Women', 'puma-suede-classic-women', 2190000, 1890000, 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=500', 'Puma Suede da lộn.', 'Female', 220),
(@cat_sneaker, @brand_nb, 'New Balance 327 Women', 'new-balance-327-women', 2790000, 2490000, 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=500', 'New Balance 327 retro-modern.', 'Female', 240),
(@cat_chaybo, @brand_nb, 'New Balance Fresh Foam', 'new-balance-fresh-foam-nu', 3290000, 2890000, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=500', 'New Balance Fresh Foam.', 'Female', 130),
(@cat_sneaker, @brand_nb, 'New Balance 550 White Green', 'new-balance-550-white-green-nu', 3490000, 3090000, 'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?w=500', 'New Balance 550 basketball.', 'Female', 185),
(@cat_sneaker, @brand_converse, 'Converse Chuck 70 Hi Pink', 'converse-chuck-70-hi-pink-nu', 1990000, 1790000, 'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=500', 'Converse Chuck 70 hồng.', 'Female', 275),
(@cat_sneaker, @brand_converse, 'Converse Run Star Hike', 'converse-run-star-hike-nu', 2690000, 2390000, 'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=500', 'Converse Run Star Hike platform.', 'Female', 310),
(@cat_sneaker, @brand_converse, 'Converse One Star Pro', 'converse-one-star-pro-nu', 1890000, 1690000, 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=500', 'Converse One Star Pro.', 'Female', 165),
(@cat_sneaker, @brand_vans, 'Vans Sk8-Hi Platform', 'vans-sk8-hi-platform-nu', 2290000, 1990000, 'https://images.unsplash.com/photo-1520256862855-398228c41684?w=500', 'Vans Sk8-Hi platform.', 'Female', 230),
(@cat_sneaker, @brand_vans, 'Vans Authentic Pastel', 'vans-authentic-pastel-nu', 1690000, 1490000, 'https://images.unsplash.com/photo-1465453869711-7e174808ace9?w=500', 'Vans Authentic pastel.', 'Female', 195),
(@cat_sneaker, @brand_vans, 'Vans Era Floral', 'vans-era-floral-nu', 1790000, 1590000, 'https://images.unsplash.com/photo-1491553895911-0055uj6a7?w=500', 'Vans Era họa tiết hoa.', 'Female', 145),
(@cat_sneaker, @brand_fila, 'Fila Disruptor 2 White', 'fila-disruptor-2-white-nu', 2490000, 2190000, 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=500', 'Fila Disruptor 2 chunky.', 'Female', 380),
(@cat_sneaker, @brand_fila, 'Fila Ray Tracer', 'fila-ray-tracer-nu', 2290000, 1990000, 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?w=500', 'Fila Ray Tracer retro 90s.', 'Female', 165),
(@cat_thethao, @brand_fila, 'Fila Oakmont TR', 'fila-oakmont-tr-nu', 1990000, 1690000, 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500', 'Fila Oakmont TR trail.', 'Female', 120),
(@cat_thethao, @brand_skechers, 'Skechers DLites Fresh Start', 'skechers-dlites-fresh-start-nu', 1990000, 1690000, 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=500', 'Skechers DLites Memory Foam.', 'Female', 210),
(@cat_chaybo, @brand_skechers, 'Skechers Go Walk 6', 'skechers-go-walk-6-nu', 1790000, 1490000, 'https://images.unsplash.com/photo-1604671801908-6f0c6a092c05?w=500', 'Skechers Go Walk 6 nhẹ.', 'Female', 175),
(@cat_thethao, @brand_skechers, 'Skechers Uno Stand On Air', 'skechers-uno-stand-on-air-nu', 2190000, 1890000, 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=500', 'Skechers Uno Air-Cooled.', 'Female', 195),
(@cat_sneaker, @brand_reebok, 'Reebok Club C 85 Women', 'reebok-club-c-85-women', 2190000, 1890000, 'https://images.unsplash.com/photo-1595909315417-2edd382a56dc?w=500', 'Reebok Club C 85 tennis.', 'Female', 185),
(@cat_thethao, @brand_reebok, 'Reebok Nano X2', 'reebok-nano-x2-nu', 3290000, 2890000, 'https://images.unsplash.com/photo-1606890658317-7d14490b76fd?w=500', 'Reebok Nano X2 CrossFit.', 'Female', 95),
(@cat_chaybo, @brand_asics, 'Asics Gel-Nimbus 24 Women', 'asics-gel-nimbus-24-women', 4490000, 3990000, 'https://images.unsplash.com/photo-1562183241-840b8af0721e?w=500', 'Asics Gel-Nimbus 24 chạy bộ.', 'Female', 110),
(@cat_chaybo, @brand_asics, 'Asics GT-2000 10 Women', 'asics-gt-2000-10-women', 3790000, 3290000, 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500', 'Asics GT-2000 ổn định.', 'Female', 85);

-- ==========================================================
-- BƯỚC 7: THÊM BIẾN THỂ CHO SẢN PHẨM MỚI
-- ==========================================================
DELIMITER //
CREATE PROCEDURE AddVariantsForNewProducts()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE prod_id INT;
    DECLARE prod_gender VARCHAR(10);
    DECLARE cur CURSOR FOR SELECT id, gender FROM products WHERE id > (SELECT COALESCE(MAX(id), 0) - 40 FROM products);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO prod_id, prod_gender;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        IF prod_gender = 'Male' THEN
            INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
            (prod_id, '40', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '41', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '42', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '43', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '40', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '41', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '42', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '43', 'Trắng', FLOOR(10 + RAND() * 20));
        ELSE
            INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
            (prod_id, '35', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '36', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '37', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '38', 'Đen', FLOOR(10 + RAND() * 20)),
            (prod_id, '35', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '36', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '37', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '38', 'Trắng', FLOOR(10 + RAND() * 20)),
            (prod_id, '36', 'Hồng', FLOOR(5 + RAND() * 15)),
            (prod_id, '37', 'Hồng', FLOOR(5 + RAND() * 15));
        END IF;
    END LOOP;
    CLOSE cur;
END //
DELIMITER ;

CALL AddVariantsForNewProducts();
DROP PROCEDURE IF EXISTS AddVariantsForNewProducts;
