-- ============================================================
--  BIG SEED DATA  –  shopee_db  (หลากหลาย, จำนวนมาก)
-- ============================================================
USE shopee_db;

-- ============================================================
-- USERS  (50 users: 5 sellers, 45 buyers, หลากหลาย)
-- ============================================================
INSERT INTO users (username,email,phone,password_hash,full_name,gender,birth_date,role,is_verified,is_active) VALUES
-- Extra Sellers
('seller_d','seller_d@mail.com','0844444441','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Wanchai Jaidee','male','1990-04-20','seller',1,1),
('seller_e','seller_e@mail.com','0855555551','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Siriporn Kaew','female','1992-08-15','seller',1,1),
-- Buyers (various ages, genders, locations)
('buyer006','b006@mail.com','0861111116','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ariya Sombat','female','1996-02-12','buyer',1,1),
('buyer007','b007@mail.com','0861111117','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Krit Panit','male','1988-07-30','buyer',1,1),
('buyer008','b008@mail.com','0861111118','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Malee Srisuk','female','1993-11-05','buyer',1,1),
('buyer009','b009@mail.com','0861111119','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Tawan Chai','male','2000-03-18','buyer',1,1),
('buyer010','b010@mail.com','0861111110','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nattida Park','female','1997-09-25','buyer',1,1),
('buyer011','b011@mail.com','0871111111','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Somsak Dee','male','1985-06-10','buyer',1,1),
('buyer012','b012@mail.com','0871111112','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Chanida Wan','female','1999-01-22','buyer',1,1),
('buyer013','b013@mail.com','0871111113','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Pakorn Lek','male','1994-12-08','buyer',1,1),
('buyer014','b014@mail.com','0871111114','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Sirada Na','female','2001-05-16','buyer',1,1),
('buyer015','b015@mail.com','0871111115','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Mongkol Rod','male','1990-08-28','buyer',1,1),
('buyer016','b016@mail.com','0881111116','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Pimchanok W','female','1995-04-03','buyer',1,1),
('buyer017','b017@mail.com','0881111117','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Chatchai Boon','male','1987-11-19','buyer',1,1),
('buyer018','b018@mail.com','0881111118','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jirawan Porn','female','2002-07-07','buyer',1,1),
('buyer019','b019@mail.com','0881111119','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Nantapong S','male','1998-02-14','buyer',0,1),
('buyer020','b020@mail.com','0881111110','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Ratana Kul','female','1993-09-09','buyer',1,1),
('buyer021','b021@mail.com','0891111121','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Supawit K','male','1996-06-06','buyer',1,1),
('buyer022','b022@mail.com','0891111122','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Lalita Ang','female','1999-12-25','buyer',1,1),
('buyer023','b023@mail.com','0891111123','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Thatchai M','male','2003-03-03','buyer',0,1),
('buyer024','b024@mail.com','0891111124','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Vareeya C','female','1991-10-10','buyer',1,1),
('buyer025','b025@mail.com','0891111125','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Jirasak P','male','1984-01-01','buyer',1,1);

-- ============================================================
-- USER ADDRESSES for new buyers
-- ============================================================
INSERT INTO user_addresses (user_id,label,recipient_name,phone,address_line1,district,province,postal_code,is_default)
SELECT u.user_id,'Home',u.full_name,u.phone,CONCAT(FLOOR(RAND()*999)+1,' ถนนสุขุมวิท'),'วัฒนา','กรุงเทพมหานคร','10110',1
FROM users u WHERE u.role='buyer' AND u.user_id > 12;

-- ============================================================
-- SHOPS (2 more shops)
-- ============================================================
INSERT INTO shops (owner_user_id,shop_name,shop_slug,description,shop_type,rating,total_products,total_sales,is_verified,is_active) VALUES
((SELECT user_id FROM users WHERE username='seller_d'),'BeautyGlow TH','beautyglow-th','เครื่องสำอางและสกินแคร์คัดสรร','individual',4.72,0,0,1,1),
((SELECT user_id FROM users WHERE username='seller_e'),'SportZone TH','sportzone-th','อุปกรณ์กีฬาและฟิตเนสครบวงจร','individual',4.45,0,0,0,1);

-- ============================================================
-- PRODUCTS (25+ products, หลากหลายหมวดหมู่)
-- ============================================================
-- Get shop IDs
SET @tech_shop = (SELECT shop_id FROM shops WHERE shop_slug='techgadget-th');
SET @fashion_shop = (SELECT shop_id FROM shops WHERE shop_slug='fashion-nipa');
SET @home_shop = (SELECT shop_id FROM shops WHERE shop_slug='homestuff-pro');
SET @beauty_shop = (SELECT shop_id FROM shops WHERE shop_slug='beautyglow-th');
SET @sport_shop = (SELECT shop_id FROM shops WHERE shop_slug='sportzone-th');

-- Electronics
INSERT INTO products (shop_id,category_id,name,slug,description,base_price,discount_price,condition_type,brand,sku,weight_grams,total_stock,status,is_featured) VALUES
(@tech_shop,6,'iPhone 15 Pro Max 256GB','iphone-15-pro-max-256','สมาร์ทโฟน Apple รุ่นล่าสุด ชิป A17 Pro',49900.00,46900.00,'new','Apple','AAPL-IP15PM-256',221,25,'active',1),
(@tech_shop,6,'OPPO Find X7 Ultra','oppo-find-x7-ultra','กล้อง Hasselblad ความละเอียดสูงสุด',32990.00,29990.00,'new','OPPO','OPP-FX7U',226,40,'active',0),
(@tech_shop,7,'ASUS ROG Zephyrus G16','asus-rog-zephyrus-g16','Laptop Gaming RTX 4090, Core i9',89900.00,84900.00,'new','ASUS','ASU-ROG-G16',2200,15,'active',1),
(@tech_shop,7,'Lenovo ThinkPad X1 Carbon','lenovo-thinkpad-x1','โน้ตบุ๊คธุรกิจบางเบา ชิป Intel Core Ultra',52900.00,NULL,'new','Lenovo','LNV-X1C-11',1120,20,'active',0),
(@tech_shop,8,'AirPods Pro 2nd Gen','airpods-pro-2nd','หูฟัง True Wireless ANC ชั้นนำ',9490.00,8490.00,'new','Apple','AAPL-ADP2',56,60,'active',1),
(@tech_shop,8,'JBL Flip 6 Bluetooth','jbl-flip-6','ลำโพง Bluetooth กันน้ำ IP67 พกพาง่าย',3490.00,2990.00,'new','JBL','JBL-FLIP6',550,90,'active',0),
(@tech_shop,6,'Samsung Galaxy Tab S9','samsung-tab-s9','แท็บเล็ต AMOLED 11 นิ้ว S Pen รุ่นใหม่',25900.00,23900.00,'new','Samsung','SAM-TS9-256',498,35,'active',0),
-- Fashion
(@fashion_shop,9,'กางเกง Cargo Baggy ผ้าคอตตอน','cargo-baggy-cotton','กางเกงคาร์โก้ทรงแบ็กกี้ ผ้าคอตตอนหนา',790.00,690.00,'new',NULL,'CGO-BGY-001',450,150,'active',1),
(@fashion_shop,10,'ชุดเซ็ต 2 ชิ้น เสื้อ+กางเกง','set-2pcs-summer','ชุดเซ็ตฤดูร้อน เนื้อผ้า Linen เย็นสบาย',890.00,750.00,'new',NULL,'SET-2P-LIN',350,120,'active',0),
(@fashion_shop,11,'รองเท้าบู๊ทหนัง Chelsea','chelsea-boot-leather','รองเท้าบู๊ทหนังแท้ สไตล์ Chelsea ทรงสุภาพ',1590.00,NULL,'new',NULL,'CLB-LTH-BLK',900,80,'active',1),
(@fashion_shop,10,'เดรสลูกไม้ Vintage Style','dress-lace-vintage','เดรสลูกไม้สไตล์วินเทจ ชุดออกงานได้',1290.00,990.00,'new',NULL,'DRS-LCE-VTG',380,60,'active',0),
(@fashion_shop,11,'รองเท้าผ้าใบ Slip On','slip-on-canvas-shoes','รองเท้าผ้าใบ Slip On ผ้าแคนวาส ใส่สบาย',490.00,NULL,'new',NULL,'SLP-CVS-001',450,200,'active',0),
-- Home & Living
(@home_shop,12,'โต๊ะทำงานไม้ยางพารา','desk-rubberwood-120','โต๊ะทำงานไม้ยางพาราธรรมชาติ 120×60 ซม.',4500.00,3990.00,'new',NULL,'DSK-RBW-120',15000,20,'active',1),
(@home_shop,13,'เครื่องปั่นสมูทตี้ Philips','philips-blender-hr2221','เครื่องปั่น 700W ฝาปิดกันกระเด็น',1290.00,990.00,'new','Philips','PHL-BLD-7W',1200,60,'active',0),
(@home_shop,14,'ชุดผ้าปูที่นอน 6 ฟุต Tencel','tencel-bedset-6ft','ผ้าปูที่นอน Tencel Lyocell 600TC ชุด 5 ชิ้น',1890.00,1590.00,'new',NULL,'BED-TNL-6F',1500,40,'active',0),
(@home_shop,12,'เก้าอี้โฮมออฟฟิศ Ergonomic','ergonomic-chair-mesh','เก้าอี้ออฟฟิศ Mesh หลังพนักพิงปรับได้',4900.00,4200.00,'new',NULL,'CHR-ERG-MSH',12000,25,'active',1),
-- Beauty
(@beauty_shop,5,'เซรั่มวิตามินซี Skinsation','serum-vitc-skinsation','เซรั่มวิตามิน C 20% เข้มข้น หน้าใสลดจุดด่างดำ',890.00,750.00,'new','Skinsation','SKS-VTC-30',80,200,'active',1),
(@beauty_shop,5,'ครีมกันแดด SPF50+ PA++++','sunscreen-spf50-pa4','กันแดดน้ำหนักเบา ไม่อุดตัน เหมาะทุกสภาพผิว',490.00,NULL,'new','Sunplay','SPY-SPF50',100,300,'active',1),
(@beauty_shop,5,'ลิปสติก 3CE Mini Kit Set','3ce-lipstick-mini-set','ลิปสติกมินิ 5 เฉดสี ยอดนิยมจากเกาหลี',1290.00,990.00,'new','3CE','3CE-LPS-SET',200,150,'active',0),
(@beauty_shop,5,'มาสก์หน้าคอลลาเจน 10 แผ่น','collagen-mask-10pcs','มาสก์หน้าคอลลาเจนเกาหลี เพิ่มความชุ่มชื้น',350.00,280.00,'new',NULL,'MSK-COL-10',300,500,'active',0),
-- Sports
(@sport_shop,4,'ดัมเบลน้ำหนักปรับได้ 2-24kg','adjustable-dumbbell-24','ดัมเบลปรับน้ำหนักได้ ประหยัดพื้นที่ คุ้มค่า',3900.00,3490.00,'new',NULL,'DBL-ADJ-24',24000,30,'active',1),
(@sport_shop,4,'เสื้อกีฬาระบายอากาศ Dry Fit','dryfit-sport-shirt','เสื้อกีฬาผ้า Dry Fit ระบายเหงื่อดี ไม่อับชื้น',390.00,NULL,'new',NULL,'SPT-DRY-001',200,250,'active',0),
(@sport_shop,4,'เสื่อโยคะหนา 8mm','yoga-mat-8mm-tpe','เสื่อโยคะ TPE ไม่ลื่น หนา 8mm กันกระแทก',690.00,590.00,'new',NULL,'YGA-MAT-8M',1800,120,'active',0),
(@sport_shop,4,'นาฬิกาออกกำลังกาย Garmin Forerunner','garmin-forerunner-265','นาฬิกา GPS Running สำหรับนักวิ่ง',14900.00,13500.00,'new','Garmin','GRM-FR265-BLK',47,40,'active',1),
(@sport_shop,4,'ลู่วิ่งพับได้ 3HP','treadmill-foldable-3hp','ลู่วิ่งพับเก็บได้ มอเตอร์ 3HP ระดับ 1-12 ขั้น',15900.00,13900.00,'new',NULL,'TRD-FLD-3HP',45000,8,'active',0);

-- ============================================================
-- PRODUCT IMAGES (primary images for new products)
-- ============================================================
INSERT INTO product_images (product_id,image_url,sort_order,is_primary)
SELECT p.product_id,
       CONCAT('https://cdn.shopee.th/img/',p.slug,'-1.jpg'),
       0, 1
FROM products p
WHERE p.product_id NOT IN (SELECT DISTINCT product_id FROM product_images WHERE is_primary=1);

-- ============================================================
-- PRODUCT SPECIFICATIONS
-- ============================================================
INSERT INTO product_specifications (product_id,spec_key,spec_value,sort_order)
SELECT p.product_id,'แบรนด์',COALESCE(p.brand,'No Brand'),1 FROM products p WHERE p.product_id > 7
UNION ALL
SELECT p.product_id,'สภาพ',IF(p.condition_type='new','ใหม่','มือสอง'),2 FROM products p WHERE p.product_id > 7
UNION ALL
SELECT p.product_id,'น้ำหนัก',CONCAT(p.weight_grams,' กรัม'),3 FROM products p WHERE p.product_id > 7 AND p.weight_grams IS NOT NULL;

-- ============================================================
-- Resolve buyer user IDs by username (safe, DB-independent)
-- ============================================================
SET @b006 = (SELECT user_id FROM users WHERE username='buyer006');
SET @b007 = (SELECT user_id FROM users WHERE username='buyer007');
SET @b008 = (SELECT user_id FROM users WHERE username='buyer008');
SET @b009 = (SELECT user_id FROM users WHERE username='buyer009');
SET @b010 = (SELECT user_id FROM users WHERE username='buyer010');
SET @b011 = (SELECT user_id FROM users WHERE username='buyer011');
SET @b012 = (SELECT user_id FROM users WHERE username='buyer012');
SET @b013 = (SELECT user_id FROM users WHERE username='buyer013');
SET @b014 = (SELECT user_id FROM users WHERE username='buyer014');
SET @b015 = (SELECT user_id FROM users WHERE username='buyer015');
SET @b016 = (SELECT user_id FROM users WHERE username='buyer016');
SET @b017 = (SELECT user_id FROM users WHERE username='buyer017');
SET @b018 = (SELECT user_id FROM users WHERE username='buyer018');
SET @b019 = (SELECT user_id FROM users WHERE username='buyer019');
SET @b020 = (SELECT user_id FROM users WHERE username='buyer020');
SET @b021 = (SELECT user_id FROM users WHERE username='buyer021');

-- Resolve address IDs per buyer
SET @a006 = (SELECT address_id FROM user_addresses WHERE user_id=@b006 LIMIT 1);
SET @a007 = (SELECT address_id FROM user_addresses WHERE user_id=@b007 LIMIT 1);
SET @a008 = (SELECT address_id FROM user_addresses WHERE user_id=@b008 LIMIT 1);
SET @a009 = (SELECT address_id FROM user_addresses WHERE user_id=@b009 LIMIT 1);
SET @a010 = (SELECT address_id FROM user_addresses WHERE user_id=@b010 LIMIT 1);
SET @a011 = (SELECT address_id FROM user_addresses WHERE user_id=@b011 LIMIT 1);
SET @a012 = (SELECT address_id FROM user_addresses WHERE user_id=@b012 LIMIT 1);
SET @a013 = (SELECT address_id FROM user_addresses WHERE user_id=@b013 LIMIT 1);
SET @a014 = (SELECT address_id FROM user_addresses WHERE user_id=@b014 LIMIT 1);
SET @a015 = (SELECT address_id FROM user_addresses WHERE user_id=@b015 LIMIT 1);
SET @a016 = (SELECT address_id FROM user_addresses WHERE user_id=@b016 LIMIT 1);
SET @a017 = (SELECT address_id FROM user_addresses WHERE user_id=@b017 LIMIT 1);
SET @a018 = (SELECT address_id FROM user_addresses WHERE user_id=@b018 LIMIT 1);
SET @a019 = (SELECT address_id FROM user_addresses WHERE user_id=@b019 LIMIT 1);
SET @a020 = (SELECT address_id FROM user_addresses WHERE user_id=@b020 LIMIT 1);
SET @a021 = (SELECT address_id FROM user_addresses WHERE user_id=@b021 LIMIT 1);

-- Fallback: if any address is NULL use address_id=1
SET @a006 = COALESCE(@a006, 1); SET @a007 = COALESCE(@a007, 1); SET @a008 = COALESCE(@a008, 1);
SET @a009 = COALESCE(@a009, 1); SET @a010 = COALESCE(@a010, 1); SET @a011 = COALESCE(@a011, 1);
SET @a012 = COALESCE(@a012, 1); SET @a013 = COALESCE(@a013, 1); SET @a014 = COALESCE(@a014, 1);
SET @a015 = COALESCE(@a015, 1); SET @a016 = COALESCE(@a016, 1); SET @a017 = COALESCE(@a017, 1);
SET @a018 = COALESCE(@a018, 1); SET @a019 = COALESCE(@a019, 1); SET @a020 = COALESCE(@a020, 1);
SET @a021 = COALESCE(@a021, 1);

-- ============================================================
-- ORDERS  (25 orders, หลายสถานะ)
-- ============================================================
INSERT INTO orders (order_number,buyer_user_id,shop_id,address_id,provider_id,subtotal,shipping_fee,shop_discount,voucher_discount,total_amount,payment_method,payment_status,order_status,tracking_number,shipped_at,delivered_at,completed_at) VALUES
('ORD-20240301-000006',@b006,@tech_shop,@a006,1,8490.00,0.00,0.00,0.00,8490.00,'shopee_pay','paid','completed','JNT1000000006','2024-03-01','2024-03-04','2024-03-05'),
('ORD-20240302-000007',@b007,@fashion_shop,@a007,3,690.00,40.00,0.00,0.00,730.00,'cod','paid','completed','FLH1000000007','2024-03-02','2024-03-06',NULL),
('ORD-20240310-000008',@b008,@home_shop,@a008,5,3990.00,0.00,200.00,0.00,3790.00,'bank_transfer','paid','completed','SPX1000000008','2024-03-10','2024-03-14','2024-03-15'),
('ORD-20240315-000009',@b009,@tech_shop,@a009,5,46900.00,0.00,0.00,500.00,46400.00,'credit_card','paid','completed','SPX1000000009','2024-03-15','2024-03-18','2024-03-20'),
('ORD-20240320-000010',@b010,@beauty_shop,@a010,1,750.00,40.00,0.00,0.00,790.00,'cod','paid','completed','JNT1000000010','2024-03-20','2024-03-23','2024-03-25'),
('ORD-20240325-000011',@b011,@sport_shop,@a011,3,3490.00,40.00,0.00,0.00,3530.00,'shopee_pay','paid','completed','FLH1000000011','2024-03-25','2024-03-28','2024-03-30'),
('ORD-20240401-000012',@b012,@fashion_shop,@a012,1,990.00,40.00,0.00,0.00,1030.00,'cod','paid','completed','JNT1000000012','2024-04-01','2024-04-04','2024-04-05'),
('ORD-20240405-000013',@b013,@tech_shop,@a013,5,2990.00,0.00,0.00,0.00,2990.00,'shopee_pay','paid','completed','SPX1000000013','2024-04-05','2024-04-08','2024-04-10'),
('ORD-20240410-000014',@b014,@home_shop,@a014,3,1590.00,40.00,0.00,0.00,1630.00,'bank_transfer','paid','completed','FLH1000000014','2024-04-10','2024-04-14','2024-04-15'),
('ORD-20240415-000015',@b015,@beauty_shop,@a015,1,280.00,40.00,0.00,0.00,320.00,'cod','paid','completed','JNT1000000015','2024-04-15','2024-04-18','2024-04-20'),
('ORD-20240420-000016',@b016,@sport_shop,@a016,5,590.00,40.00,0.00,0.00,630.00,'shopee_pay','paid','shipped','SPX1000000016','2024-04-20',NULL,NULL),
('ORD-20240425-000017',@b017,@tech_shop,@a017,1,84900.00,0.00,0.00,0.00,84900.00,'credit_card','paid','delivered','JNT1000000017','2024-04-25','2024-04-28',NULL),
('ORD-20240501-000018',@b018,@fashion_shop,@a018,3,1290.00,40.00,0.00,0.00,1330.00,'cod','pending','pending',NULL,NULL,NULL,NULL),
('ORD-20240502-000019',@b019,@home_shop,@a019,5,4200.00,0.00,0.00,0.00,4200.00,'shopee_pay','paid','processing','SPX1000000019','2024-05-02',NULL,NULL),
('ORD-20240503-000020',@b020,@beauty_shop,@a020,1,990.00,40.00,0.00,0.00,1030.00,'cod','pending','cancelled',NULL,NULL,NULL,NULL),
('ORD-20240504-000021',@b006,@sport_shop,@a006,3,13500.00,0.00,0.00,0.00,13500.00,'bank_transfer','paid','shipped','FLH1000000021','2024-05-04',NULL,NULL),
('ORD-20240505-000022',@b007,@tech_shop,@a007,5,9490.00,0.00,0.00,0.00,9490.00,'shopee_pay','paid','delivered','SPX1000000022','2024-05-05','2024-05-08',NULL),
('ORD-20240506-000023',@b008,@fashion_shop,@a008,1,690.00,40.00,0.00,0.00,730.00,'cod','paid','completed','JNT1000000023','2024-05-06','2024-05-09','2024-05-10'),
('ORD-20240507-000024',@b009,@home_shop,@a009,3,3990.00,0.00,0.00,0.00,3990.00,'shopee_pay','paid','completed','FLH1000000024','2024-05-07','2024-05-10','2024-05-12'),
('ORD-20240508-000025',@b010,@beauty_shop,@a010,5,750.00,40.00,0.00,50.00,740.00,'shopee_pay','paid','completed','SPX1000000025','2024-05-08','2024-05-11','2024-05-13'),
('ORD-20240509-000026',@b011,@sport_shop,@a011,1,13900.00,0.00,0.00,0.00,13900.00,'bank_transfer','paid','return_requested',NULL,NULL,NULL,NULL),
('ORD-20240510-000027',@b012,@tech_shop,@a012,5,2990.00,0.00,0.00,0.00,2990.00,'shopee_pay','paid','completed','SPX1000000027','2024-05-10','2024-05-13','2024-05-14'),
('ORD-20240511-000028',@b013,@fashion_shop,@a013,3,490.00,40.00,0.00,0.00,530.00,'cod','paid','completed','FLH1000000028','2024-05-11','2024-05-14','2024-05-15'),
('ORD-20240512-000029',@b014,@home_shop,@a014,1,1590.00,40.00,0.00,0.00,1630.00,'cod','paid','confirmed',NULL,NULL,NULL,NULL),
('ORD-20240513-000030',@b015,@beauty_shop,@a015,5,280.00,40.00,0.00,0.00,320.00,'shopee_pay','paid','completed','SPX1000000030','2024-05-13','2024-05-16','2024-05-17');

-- ============================================================
-- ORDER ITEMS for new orders
-- ============================================================
INSERT INTO order_items (order_id,product_id,product_name,image_url,unit_price,quantity,subtotal)
SELECT o.order_id,
  (SELECT p.product_id FROM products p WHERE p.status='active' ORDER BY RAND() LIMIT 1),
  (SELECT p.name FROM products p WHERE p.status='active' ORDER BY RAND() LIMIT 1),
  'https://cdn.shopee.th/img/product-placeholder.jpg',
  o.subtotal, 1, o.subtotal
FROM orders o WHERE o.order_id > 5 AND o.order_id <= 30;

-- ============================================================
-- PAYMENTS for new orders
-- ============================================================
INSERT INTO payments (order_id,payment_method,amount,currency,status,transaction_ref,paid_at)
SELECT o.order_id, o.payment_method, o.total_amount, 'THB',
  CASE o.payment_status WHEN 'paid' THEN 'success' WHEN 'pending' THEN 'pending' ELSE 'pending' END,
  CONCAT('TXN-',UPPER(LEFT(o.payment_method,3)),'-',LPAD(o.order_id,6,'0')),
  CASE o.payment_status WHEN 'paid' THEN o.created_at ELSE NULL END
FROM orders o WHERE o.order_id > 5 AND o.order_id <= 30;

-- ============================================================
-- REVIEWS for completed orders
-- ============================================================
INSERT INTO reviews (product_id,order_id,reviewer_id,shop_id,rating,comment,seller_reply,replied_at)
SELECT
  oi.product_id,
  o.order_id,
  o.buyer_user_id,
  o.shop_id,
  FLOOR(RAND()*2)+4,
  ELT(FLOOR(RAND()*8)+1,
    'สินค้าคุณภาพดีมาก ส่งเร็ว บรรจุภัณฑ์แน่น',
    'ของดีจริง ใช้งานได้ดี คุ้มค่าราคา แนะนำเลย',
    'ประทับใจมาก ส่งเร็วกว่าที่คิด สินค้าสมราคา',
    'ร้านน่าเชื่อถือ ตอบเร็ว สินค้าตรงตามคำอธิบาย',
    'สินค้าสวยมาก สีตรงกับรูป บรรจุภัณฑ์ดี',
    'ใช้ดีค่ะ แต่ส่งช้านิดนึง สินค้าโอเค',
    'คุณภาพดีกว่าที่คิด ราคาคุ้มมาก จะสั่งอีก',
    'ประทับใจการบริการ ส่งพร้อม tracking ทุกครั้ง'
  ),
  ELT(FLOOR(RAND()*3)+1,
    'ขอบคุณมากครับ/ค่ะ ยินดีให้บริการเสมอ',
    'ขอบพระคุณที่ไว้วางใจนะครับ หวังว่าจะได้รับใช้อีก',
    NULL
  ),
  CASE WHEN RAND() > 0.4 THEN DATE_ADD(o.created_at, INTERVAL 3 DAY) ELSE NULL END
FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_status = 'completed' AND o.order_id > 5 AND o.order_id <= 30
LIMIT 20;

-- ============================================================
-- CARTS for some users
-- ============================================================
INSERT IGNORE INTO carts (user_id)
SELECT user_id FROM users WHERE role='buyer' AND user_id > 12 AND user_id <= 25;

INSERT INTO cart_items (cart_id,product_id,quantity,is_checked)
SELECT c.cart_id,
  p.product_id,
  FLOOR(RAND()*2)+1,
  1
FROM carts c
JOIN users u ON c.user_id = u.user_id
CROSS JOIN (SELECT product_id FROM products WHERE status='active' ORDER BY RAND() LIMIT 3) p
WHERE u.user_id > 12
ON DUPLICATE KEY UPDATE quantity = cart_items.quantity;

-- ============================================================
-- WISHLISTS
-- ============================================================
INSERT IGNORE INTO wishlists (user_id,product_id)
SELECT u.user_id, p.product_id
FROM users u
CROSS JOIN (SELECT product_id FROM products WHERE status='active' ORDER BY RAND() LIMIT 5) p
WHERE u.role='buyer' AND u.user_id > 12 AND u.user_id <= 25;

-- ============================================================
-- SHOP FOLLOWERS
-- ============================================================
INSERT IGNORE INTO shop_followers (user_id, shop_id)
SELECT u.user_id, s.shop_id
FROM users u
CROSS JOIN shops s
WHERE u.role='buyer' AND u.user_id > 12 AND RAND() > 0.6;

-- ============================================================
-- SEARCH HISTORY (หลากหลาย keyword)
-- ============================================================
INSERT INTO search_history (user_id,keyword)
SELECT u.user_id,
  ELT(FLOOR(RAND()*20)+1,
    'samsung','iphone 15','โน้ตบุ๊ค gaming','หูฟัง bluetooth',
    'เสื้อผ้าแฟชั่น','รองเท้า nike','กระทะไม่ติด',
    'ครีมกันแดด','ลิปสติก','ดัมเบล','เสื่อโยคะ',
    'ลู่วิ่ง','เก้าอี้ ergonomic','โต๊ะทำงาน',
    'airpods','garmin watch','ชุดผ้าปูที่นอน',
    'เซรั่มวิตามินซี','กางเกง cargo','เดรสลูกไม้'
  )
FROM users u WHERE u.role='buyer' AND u.user_id > 12;

-- ============================================================
-- NOTIFICATIONS for new users
-- ============================================================
INSERT INTO notifications (user_id,type,title,body,is_read)
SELECT u.user_id,
  ELT(FLOOR(RAND()*4)+1,'order_update','promotion','flash_sale','review_remind'),
  ELT(FLOOR(RAND()*4)+1,
    'ออเดอร์ของคุณกำลังเดินทาง!',
    'Flash Sale เริ่มแล้ว! ลดสูงสุด 70%',
    'มีโค้ดส่วนลดรอคุณอยู่',
    'อย่าลืมรีวิวสินค้าที่เพิ่งซื้อนะคะ'
  ),
  ELT(FLOOR(RAND()*4)+1,
    'สินค้าของคุณถูกจัดส่งแล้ว กดติดตามพัสดุ',
    'อย่าพลาด! ราคาดีแค่วันนี้วันเดียว',
    'ใช้โค้ด SHOPEE50 ลดทันที 50 บาท',
    'สินค้าที่สั่งซื้อรอการรีวิวจากคุณอยู่'
  ),
  FLOOR(RAND()*2)
FROM users u WHERE u.role='buyer' AND u.user_id > 12;

-- ============================================================
-- UPDATE SHOP STATS
-- ============================================================
UPDATE shops s SET
  total_products = (SELECT COUNT(*) FROM products WHERE shop_id=s.shop_id AND status='active'),
  total_sales    = (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi JOIN orders o ON oi.order_id=o.order_id WHERE o.shop_id=s.shop_id AND o.payment_status='paid'),
  total_reviews  = (SELECT COUNT(*) FROM reviews WHERE shop_id=s.shop_id),
  rating         = (SELECT COALESCE(AVG(rating),0) FROM reviews WHERE shop_id=s.shop_id);

-- UPDATE PRODUCT STATS
UPDATE products p SET
  total_sold   = (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi JOIN orders o ON oi.order_id=o.order_id WHERE oi.product_id=p.product_id AND o.payment_status='paid'),
  total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id=p.product_id),
  rating        = (SELECT COALESCE(AVG(rating),0) FROM reviews WHERE product_id=p.product_id),
  total_views   = FLOOR(RAND()*5000)+100;

-- ============================================================
-- WALLETS for new users
-- ============================================================
INSERT IGNORE INTO wallets (user_id,balance,coins)
SELECT user_id, ROUND(RAND()*500,2), ROUND(RAND()*300,2)
FROM users WHERE role='buyer' AND user_id > 12;

-- ============================================================
-- FRAUD REPORTS (demo)
-- ============================================================
INSERT INTO fraud_reports (reporter_id,target_type,target_id,fraud_type,description,status) VALUES
(13, 'product', 5, 'counterfeit', 'รองเท้าไม่ใช่ของแท้ ดูจากรอยเย็บไม่สม่ำเสมอ', 'investigating'),
(15, 'shop', 3, 'spam', 'ร้านส่ง spam message มาตลอด', 'resolved'),
(17, 'user', 20, 'fake_review', 'รีวิวปลอม โพสซ้ำๆ หลายครั้ง', 'pending');

-- ============================================================
-- RETURN REQUESTS for new orders
-- ============================================================
INSERT INTO return_requests (order_id,buyer_user_id,reason,description,return_type,status,refund_amount,resolved_at) VALUES
(26, 20, 'สินค้าชำรุด', 'ลู่วิ่งรับมาแล้วมอเตอร์มีเสียงผิดปกติ', 'return_refund', 'pending', NULL, NULL);

-- ============================================================
-- ORDER STATUS HISTORY for new orders
-- ============================================================
INSERT INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'pending','Order placed',buyer_user_id FROM orders WHERE order_id > 5 AND order_id <= 30;

INSERT INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'confirmed','Seller confirmed',11 FROM orders WHERE order_id > 5 AND order_id <= 30 AND order_status NOT IN ('pending','cancelled');

INSERT INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'shipped',CONCAT('Shipped via ',tracking_number),11 FROM orders WHERE order_id > 5 AND order_id <= 30 AND shipped_at IS NOT NULL;

INSERT INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'delivered','Delivered to customer',NULL FROM orders WHERE order_id > 5 AND order_id <= 30 AND delivered_at IS NOT NULL;

INSERT INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'completed','Auto-completed after 7 days',NULL FROM orders WHERE order_id > 5 AND order_id <= 30 AND order_status='completed';
