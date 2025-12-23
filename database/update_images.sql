-- =============================================
-- CẬP NHẬT ẢNH SẢN PHẨM TỪ INTERNET
-- Chạy sau khi đã chạy sample_data.sql
-- =============================================

USE shop_giay_db;

-- Cập nhật thumbnail với ảnh từ internet
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=400' WHERE id = 1; -- Nike AF1
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=400' WHERE id = 2; -- Nike Air Max 90
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=400' WHERE id = 3; -- Nike Dunk
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=400' WHERE id = 4; -- Jordan 1
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400' WHERE id = 5; -- Nike Pegasus
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400' WHERE id = 6; -- Stan Smith
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400' WHERE id = 7; -- Superstar
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400' WHERE id = 8; -- Ultraboost
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400' WHERE id = 9; -- Samba
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=400' WHERE id = 10; -- Puma Suede
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=400' WHERE id = 11; -- Puma RS-X
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=400' WHERE id = 12; -- NB 550
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400' WHERE id = 13; -- NB 990
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=400' WHERE id = 14; -- Converse Chuck
UPDATE products SET thumbnail = 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=400' WHERE id = 15; -- Converse 70

SELECT 'Đã cập nhật ảnh sản phẩm thành công!' AS Result;
