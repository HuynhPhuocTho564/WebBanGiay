-- =============================================
-- SỬA LỖI HÌNH ẢNH SẢN PHẨM
-- Cập nhật tất cả thumbnail với ảnh từ Unsplash
-- =============================================

USE shop_giay_db;

-- Tắt safe mode
SET SQL_SAFE_UPDATES = 0;

-- Cập nhật ảnh theo thương hiệu và tên sản phẩm

-- NIKE
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500' 
WHERE name LIKE '%Air Force%' OR name LIKE '%AF1%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=500' 
WHERE name LIKE '%Air Max%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=500' 
WHERE name LIKE '%Dunk%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=500' 
WHERE name LIKE '%Jordan%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=500' 
WHERE name LIKE '%Free Run%' OR name LIKE '%Pegasus%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=500' 
WHERE name LIKE '%Blazer%';

-- ADIDAS
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=500' 
WHERE name LIKE '%Stan Smith%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=500' 
WHERE name LIKE '%Superstar%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=500' 
WHERE name LIKE '%Ultraboost%' OR name LIKE '%Ultra Boost%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=500' 
WHERE name LIKE '%Gazelle%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=500' 
WHERE name LIKE '%NMD%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=500' 
WHERE name LIKE '%Forum%' OR name LIKE '%Samba%';

-- PUMA
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500' 
WHERE name LIKE '%RS-X%' OR name LIKE '%RSX%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=500' 
WHERE name LIKE '%Cali%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=500' 
WHERE name LIKE '%Mayze%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=500' 
WHERE name LIKE '%Suede%';

-- NEW BALANCE
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=500' 
WHERE name LIKE '%574%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=500' 
WHERE name LIKE '%327%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?w=500' 
WHERE name LIKE '%550%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=500' 
WHERE name LIKE '%Fresh Foam%' OR name LIKE '%990%';

-- CONVERSE
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=500' 
WHERE name LIKE '%Chuck Taylor%' OR name LIKE '%Chuck 70%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=500' 
WHERE name LIKE '%Chuck%Pink%' OR (name LIKE '%Chuck%' AND name LIKE '%Hi%');

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=500' 
WHERE name LIKE '%Run Star%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=500' 
WHERE name LIKE '%One Star%';

-- VANS
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500' 
WHERE name LIKE '%Old Skool%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1520256862855-398228c41684?w=500' 
WHERE name LIKE '%Sk8-Hi%' OR name LIKE '%Sk8 Hi%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1465453869711-7e174808ace9?w=500' 
WHERE name LIKE '%Authentic%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1499013819532-e4ff41b00669?w=500' 
WHERE name LIKE '%Era%';

-- FILA
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=500' 
WHERE name LIKE '%Disruptor%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1515955656352-a1fa3ffcd111?w=500' 
WHERE name LIKE '%Ray Tracer%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500' 
WHERE name LIKE '%Oakmont%';

-- REEBOK
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1595909315417-2edd382a56dc?w=500' 
WHERE name LIKE '%Club C%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=500' 
WHERE name LIKE '%Classic Leather%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1606890658317-7d14490b76fd?w=500' 
WHERE name LIKE '%Nano%';

-- SKECHERS
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=500' 
WHERE name LIKE '%DLites%' OR name LIKE '%D%Lites%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1604671801908-6f0c6a092c05?w=500' 
WHERE name LIKE '%Go Walk%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=500' 
WHERE name LIKE '%Uno%';

-- ASICS
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=500' 
WHERE name LIKE '%Gel-Kayano%' OR name LIKE '%Kayano%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1562183241-840b8af0721e?w=500' 
WHERE name LIKE '%Gel-Nimbus%' OR name LIKE '%Nimbus%';

UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500' 
WHERE name LIKE '%GT-2000%';

-- Cập nhật các sản phẩm còn lại chưa có ảnh hoặc ảnh lỗi
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500' 
WHERE thumbnail IS NULL OR thumbnail = '' OR thumbnail NOT LIKE 'https://%';

-- Cập nhật theo brand_id cho các sản phẩm còn thiếu
UPDATE products p
JOIN brands b ON p.brand_id = b.id
SET p.thumbnail = CASE 
    WHEN b.name = 'Nike' THEN 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500'
    WHEN b.name = 'Adidas' THEN 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=500'
    WHEN b.name = 'Puma' THEN 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500'
    WHEN b.name = 'New Balance' THEN 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=500'
    WHEN b.name = 'Converse' THEN 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=500'
    WHEN b.name = 'Vans' THEN 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500'
    WHEN b.name = 'Fila' THEN 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=500'
    ELSE 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500'
END
WHERE p.thumbnail IS NULL OR p.thumbnail = '' OR p.thumbnail NOT LIKE 'https://%';

-- Bật lại safe mode
SET SQL_SAFE_UPDATES = 1;

SELECT 'Đã cập nhật ảnh sản phẩm thành công!' AS Result;
SELECT COUNT(*) AS 'Tổng sản phẩm đã cập nhật' FROM products WHERE thumbnail LIKE 'https://%';
