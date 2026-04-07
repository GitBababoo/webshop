-- ============================================================
--  REMAINING SEED: Orders, items, reviews, carts, etc.
-- ============================================================
USE shopee_db;

SET @tech_shop    = (SELECT shop_id FROM shops WHERE shop_slug='techgadget-th');
SET @fashion_shop = (SELECT shop_id FROM shops WHERE shop_slug='fashion-nipa');
SET @home_shop    = (SELECT shop_id FROM shops WHERE shop_slug='homestuff-pro');
SET @beauty_shop  = (SELECT shop_id FROM shops WHERE shop_slug='beautyglow-th');
SET @sport_shop   = (SELECT shop_id FROM shops WHERE shop_slug='sportzone-th');

SET @b006=(SELECT user_id FROM users WHERE username='buyer006');
SET @b007=(SELECT user_id FROM users WHERE username='buyer007');
SET @b008=(SELECT user_id FROM users WHERE username='buyer008');
SET @b009=(SELECT user_id FROM users WHERE username='buyer009');
SET @b010=(SELECT user_id FROM users WHERE username='buyer010');
SET @b011=(SELECT user_id FROM users WHERE username='buyer011');
SET @b012=(SELECT user_id FROM users WHERE username='buyer012');
SET @b013=(SELECT user_id FROM users WHERE username='buyer013');
SET @b014=(SELECT user_id FROM users WHERE username='buyer014');
SET @b015=(SELECT user_id FROM users WHERE username='buyer015');
SET @b016=(SELECT user_id FROM users WHERE username='buyer016');
SET @b017=(SELECT user_id FROM users WHERE username='buyer017');
SET @b018=(SELECT user_id FROM users WHERE username='buyer018');
SET @b019=(SELECT user_id FROM users WHERE username='buyer019');
SET @b020=(SELECT user_id FROM users WHERE username='buyer020');

SET @a006=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b006 LIMIT 1),1);
SET @a007=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b007 LIMIT 1),1);
SET @a008=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b008 LIMIT 1),1);
SET @a009=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b009 LIMIT 1),1);
SET @a010=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b010 LIMIT 1),1);
SET @a011=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b011 LIMIT 1),1);
SET @a012=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b012 LIMIT 1),1);
SET @a013=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b013 LIMIT 1),1);
SET @a014=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b014 LIMIT 1),1);
SET @a015=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b015 LIMIT 1),1);
SET @a016=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b016 LIMIT 1),1);
SET @a017=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b017 LIMIT 1),1);
SET @a018=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b018 LIMIT 1),1);
SET @a019=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b019 LIMIT 1),1);
SET @a020=COALESCE((SELECT address_id FROM user_addresses WHERE user_id=@b020 LIMIT 1),1);

-- ============================================================
-- ORDERS (skip if already exists)
-- ============================================================
INSERT IGNORE INTO orders (order_number,buyer_user_id,shop_id,address_id,provider_id,subtotal,shipping_fee,shop_discount,voucher_discount,total_amount,payment_method,payment_status,order_status,tracking_number,shipped_at,delivered_at,completed_at) VALUES
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
-- ORDER ITEMS (link to a real product per order)
-- ============================================================
INSERT IGNORE INTO order_items (order_id,product_id,product_name,image_url,unit_price,quantity,subtotal)
SELECT o.order_id,
  p.product_id,
  p.name,
  CONCAT('https://cdn.shopee.th/img/',p.slug,'.jpg'),
  o.subtotal, 1, o.subtotal
FROM orders o
JOIN products p ON p.shop_id = o.shop_id AND p.status = 'active'
WHERE o.order_id > 5
  AND NOT EXISTS (SELECT 1 FROM order_items oi WHERE oi.order_id = o.order_id)
GROUP BY o.order_id;

-- ============================================================
-- PAYMENTS
-- ============================================================
INSERT IGNORE INTO payments (order_id,payment_method,amount,currency,status,transaction_ref,paid_at)
SELECT o.order_id, o.payment_method, o.total_amount, 'THB',
  CASE o.payment_status WHEN 'paid' THEN 'success' ELSE 'pending' END,
  CONCAT('TXN-',UPPER(LEFT(o.payment_method,3)),'-',LPAD(o.order_id,6,'0')),
  CASE o.payment_status WHEN 'paid' THEN o.created_at ELSE NULL END
FROM orders o
WHERE o.order_id > 5
  AND NOT EXISTS (SELECT 1 FROM payments p WHERE p.order_id = o.order_id);

-- ============================================================
-- REVIEWS for completed orders
-- ============================================================
INSERT IGNORE INTO reviews (product_id,order_id,reviewer_id,shop_id,rating,comment)
SELECT oi.product_id, o.order_id, o.buyer_user_id, o.shop_id,
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
  )
FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_status = 'completed' AND o.order_id > 5
  AND NOT EXISTS (SELECT 1 FROM reviews r WHERE r.order_id = o.order_id);

-- ============================================================
-- CARTS
-- ============================================================
INSERT IGNORE INTO carts (user_id)
SELECT user_id FROM users WHERE role='buyer' AND user_id > 12;

INSERT IGNORE INTO cart_items (cart_id,product_id,quantity,is_checked)
SELECT c.cart_id, p.product_id, FLOOR(RAND()*2)+1, 1
FROM carts c
JOIN users u ON c.user_id = u.user_id
JOIN (SELECT product_id FROM products WHERE status='active' LIMIT 8) p ON 1=1
WHERE u.user_id > 12;

-- ============================================================
-- WISHLISTS
-- ============================================================
INSERT IGNORE INTO wishlists (user_id, product_id)
SELECT u.user_id, p.product_id
FROM users u
JOIN (SELECT product_id FROM products WHERE status='active' LIMIT 6) p ON 1=1
WHERE u.role = 'buyer' AND u.user_id > 12;

-- ============================================================
-- SHOP FOLLOWERS
-- ============================================================
INSERT IGNORE INTO shop_followers (user_id, shop_id)
SELECT u.user_id, s.shop_id FROM users u JOIN shops s ON 1=1
WHERE u.role='buyer' AND u.user_id > 12 AND (u.user_id + s.shop_id) % 3 = 0;

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
INSERT INTO notifications (user_id,type,title,body,is_read)
SELECT u.user_id,
  ELT(FLOOR(RAND()*4)+1,'order_update','promotion','flash_sale','review_remind'),
  ELT(FLOOR(RAND()*4)+1,'ออเดอร์ของคุณกำลังเดินทาง!','Flash Sale เริ่มแล้ว!','มีโค้ดส่วนลดรอคุณ','อย่าลืมรีวิวสินค้า'),
  ELT(FLOOR(RAND()*4)+1,'สินค้าของคุณถูกจัดส่งแล้ว','อย่าพลาด ราคาดีแค่วันนี้','ใช้โค้ด SHOPEE50 ลดทันที','สินค้าที่สั่งรอการรีวิว'),
  FLOOR(RAND()*2)
FROM users u WHERE u.role='buyer' AND u.user_id > 12;

-- ============================================================
-- SEARCH HISTORY
-- ============================================================
INSERT INTO search_history (user_id, keyword)
SELECT u.user_id, ELT(FLOOR(RAND()*15)+1,
  'samsung','iphone 15','โน้ตบุ๊คgaming','หูฟัง','เสื้อแฟชั่น',
  'รองเท้า','กระทะ','ครีมกันแดด','ลิปสติก','ดัมเบล',
  'เสื่อโยคะ','garmin watch','ชุดผ้าปูที่นอน','เซรั่มวิตามินซี','cargo pants'
)
FROM users u WHERE u.role='buyer' AND u.user_id > 12;

-- ============================================================
-- WALLETS for new users
-- ============================================================
INSERT IGNORE INTO wallets (user_id, balance, coins)
SELECT user_id, ROUND(RAND()*500, 2), ROUND(RAND()*300, 2)
FROM users WHERE role='buyer' AND user_id > 12;

-- ============================================================
-- LOYALTY POINTS for new buyers
-- ============================================================
INSERT IGNORE INTO loyalty_points (user_id, total_points, used_points, tier)
SELECT user_id,
  FLOOR(RAND()*2000)+50,
  FLOOR(RAND()*200),
  ELT(FLOOR(RAND()*4)+1,'bronze','bronze','silver','gold')
FROM users WHERE role='buyer' AND user_id > 12;

-- ============================================================
-- RETURN REQUESTS
-- ============================================================
INSERT IGNORE INTO return_requests (order_id,buyer_user_id,reason,description,return_type,status)
VALUES
((SELECT order_id FROM orders WHERE order_number='ORD-20240509-000026'),
 @b011, 'ลู่วิ่งรับมาแล้วมีเสียงผิดปกติ','มอเตอร์มีเสียงผิดปกติตั้งแต่แกะกล่อง', 'return_refund','pending');

-- ============================================================
-- UPDATE SHOP STATS
-- ============================================================
UPDATE shops s SET
  total_products = (SELECT COUNT(*) FROM products WHERE shop_id=s.shop_id AND status='active'),
  total_sales    = (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi JOIN orders o ON oi.order_id=o.order_id WHERE o.shop_id=s.shop_id AND o.payment_status='paid'),
  total_reviews  = (SELECT COUNT(*) FROM reviews WHERE shop_id=s.shop_id),
  rating         = ROUND((SELECT COALESCE(AVG(rating),0) FROM reviews WHERE shop_id=s.shop_id),2);

-- UPDATE PRODUCT STATS
UPDATE products p SET
  total_sold    = (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi JOIN orders o ON oi.order_id=o.order_id WHERE oi.product_id=p.product_id AND o.payment_status='paid'),
  total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id=p.product_id),
  rating        = ROUND((SELECT COALESCE(AVG(rating),0) FROM reviews WHERE product_id=p.product_id),2),
  total_views   = FLOOR(RAND()*5000)+100;

-- ============================================================
-- ORDER STATUS HISTORY
-- ============================================================
INSERT IGNORE INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'pending','Order placed',buyer_user_id FROM orders WHERE order_id > 5;

INSERT IGNORE INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'confirmed','Seller confirmed',11 FROM orders WHERE order_id > 5 AND order_status NOT IN ('pending','cancelled');

INSERT IGNORE INTO order_status_history (order_id,status,note,created_by)
SELECT order_id,'shipped',CONCAT('Shipped – ',COALESCE(tracking_number,'')),11 FROM orders WHERE order_id > 5 AND shipped_at IS NOT NULL;

INSERT IGNORE INTO order_status_history (order_id,status,note)
SELECT order_id,'delivered','Delivered' FROM orders WHERE order_id > 5 AND delivered_at IS NOT NULL;

INSERT IGNORE INTO order_status_history (order_id,status,note)
SELECT order_id,'completed','Auto-completed' FROM orders WHERE order_id > 5 AND order_status='completed';

-- ============================================================
-- FRAUD REPORTS
-- ============================================================
INSERT IGNORE INTO fraud_reports (reporter_id,target_type,target_id,fraud_type,description,status) VALUES
(@b006,'product',5,'counterfeit','รองเท้าไม่ใช่ของแท้','investigating'),
(@b008,'shop',3,'spam','ร้านส่ง spam message','resolved'),
(@b010,'user',(SELECT user_id FROM users WHERE username='buyer009'),'fake_review','รีวิวปลอมซ้ำๆ','pending');

SELECT 'Seed complete' AS status,
  (SELECT COUNT(*) FROM orders) AS orders,
  (SELECT COUNT(*) FROM order_items) AS order_items,
  (SELECT COUNT(*) FROM reviews) AS reviews,
  (SELECT COUNT(*) FROM users) AS users,
  (SELECT COUNT(*) FROM products) AS products;
