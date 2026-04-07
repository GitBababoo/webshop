-- ============================================================
--  SEED DATA  –  shopee_db
-- ============================================================

USE shopee_db;

-- ============================================================
-- USERS (2 admins, 3 sellers, 5 buyers)
-- ============================================================
INSERT INTO users (username, email, phone, password_hash, full_name, gender, birth_date, role, is_verified, is_active) VALUES
('admin1',    'admin1@shopee.th',     '0800000001', '$2b$12$adminHash1', 'Admin One',      'male',   '1990-01-01', 'admin',  1, 1),
('admin2',    'admin2@shopee.th',     '0800000002', '$2b$12$adminHash2', 'Admin Two',      'female', '1992-05-10', 'admin',  1, 1),
('seller_a',  'seller_a@mail.com',    '0811111111', '$2b$12$sellerA',   'Somchai Thong',  'male',   '1988-03-15', 'seller', 1, 1),
('seller_b',  'seller_b@mail.com',    '0822222222', '$2b$12$sellerB',   'Nipa Kaew',      'female', '1993-07-22', 'seller', 1, 1),
('seller_c',  'seller_c@mail.com',    '0833333333', '$2b$12$sellerC',   'Pisan Dee',      'male',   '1985-11-30', 'seller', 1, 1),
('buyer1',    'buyer1@mail.com',      '0841111111', '$2b$12$buyer1',    'Manee Suk',      'female', '1995-04-18', 'buyer',  1, 1),
('buyer2',    'buyer2@mail.com',      '0842222222', '$2b$12$buyer2',    'Wanchai Porn',   'male',   '1998-09-05', 'buyer',  1, 1),
('buyer3',    'buyer3@mail.com',      '0843333333', '$2b$12$buyer3',    'Lalita Chan',    'female', '2000-12-25', 'buyer',  1, 1),
('buyer4',    'buyer4@mail.com',      '0844444444', '$2b$12$buyer4',    'Natthapong K',   'male',   '1997-06-14', 'buyer',  1, 1),
('buyer5',    'buyer5@mail.com',      '0845555555', '$2b$12$buyer5',    'Porntip W',      'female', '2001-02-28', 'buyer',  1, 1);

-- ============================================================
-- USER ADDRESSES
-- ============================================================
INSERT INTO user_addresses (user_id, label, recipient_name, phone, address_line1, district, province, postal_code, is_default) VALUES
(6,  'Home',   'Manee Suk',     '0841111111', '123 Sukhumvit Rd.',     'Watthana',    'Bangkok',     '10110', 1),
(6,  'Office', 'Manee Suk',     '0841111111', '456 Silom Rd.',         'Bang Rak',    'Bangkok',     '10500', 0),
(7,  'Home',   'Wanchai Porn',  '0842222222', '78 Nimman Rd.',         'Suthep',      'Chiang Mai',  '50200', 1),
(8,  'Home',   'Lalita Chan',   '0843333333', '9 Ratchadamnoen Ave.',  'Phra Nakhon', 'Bangkok',     '10200', 1),
(9,  'Home',   'Natthapong K',  '0844444444', '55 Mueang District',    'Mueang',      'Khon Kaen',   '40000', 1),
(10, 'Home',   'Porntip W',     '0845555555', '12 Beach Rd.',          'Mueang',      'Phuket',      '83000', 1);

-- ============================================================
-- SHOPS
-- ============================================================
INSERT INTO shops (owner_user_id, shop_name, shop_slug, description, shop_type, is_verified, is_active) VALUES
(3, 'TechGadget TH',    'techgadget-th',    'อุปกรณ์อิเล็กทรอนิกส์คุณภาพสูง',    'mall',       1, 1),
(4, 'FashionNipa',      'fashion-nipa',     'เสื้อผ้าแฟชั่นสตรีทรีบีค',           'individual', 1, 1),
(5, 'HomeStuff Pro',    'homestuff-pro',    'ของใช้ในบ้านและตกแต่งบ้าน',           'individual', 0, 1);

-- ============================================================
-- CATEGORIES (Level 1 & Level 2)
-- ============================================================
INSERT INTO categories (parent_id, name, slug, sort_order, is_active) VALUES
(NULL, 'Electronics',           'electronics',          1, 1),
(NULL, 'Fashion',               'fashion',              2, 1),
(NULL, 'Home & Living',         'home-living',          3, 1),
(NULL, 'Sports & Outdoors',     'sports-outdoors',      4, 1),
(NULL, 'Health & Beauty',       'health-beauty',        5, 1),
(1,   'Mobile & Accessories',   'mobile-accessories',   1, 1),
(1,   'Laptops & Computers',    'laptops-computers',    2, 1),
(1,   'Audio & Headphones',     'audio-headphones',     3, 1),
(2,   "Men's Clothing",         'mens-clothing',        1, 1),
(2,   "Women's Clothing",       'womens-clothing',      2, 1),
(2,   'Shoes',                  'shoes',                3, 1),
(3,   'Furniture',              'furniture',            1, 1),
(3,   'Kitchen',                'kitchen',              2, 1),
(3,   'Bedding',                'bedding',              3, 1);

-- ============================================================
-- PRODUCTS
-- ============================================================
INSERT INTO products (shop_id, category_id, name, slug, description, base_price, discount_price, condition_type, brand, sku, weight_grams, total_stock, status, is_featured) VALUES
(1, 6,  'Samsung Galaxy S24 Ultra 256GB',  'samsung-galaxy-s24-ultra-256',   'สมาร์ทโฟนเรือธงรุ่นล่าสุด RAM 12GB', 45900.00, 42900.00, 'new', 'Samsung', 'SAM-S24U-256', 232,  50, 'active', 1),
(1, 7,  'MacBook Air M3 13-inch',          'macbook-air-m3-13',              'โน้ตบุ๊กบางเบา ชิป Apple M3',         45990.00, NULL,     'new', 'Apple',   'MBP-M3-13',    1240, 30, 'active', 1),
(1, 8,  'Sony WH-1000XM5 Headphones',     'sony-wh1000xm5',                 'หูฟัง Noise-Cancelling ชั้นนำ',       12990.00, 10990.00, 'new', 'Sony',    'SNY-XM5-BLK',  250,  80, 'active', 0),
(2, 10, 'Oversized Pastel Hoodie',         'oversized-pastel-hoodie',        'เสื้อฮู้ดดี้โอเวอร์ไซส์ผ้าฝ้าย 100%', 590.00,  490.00,  'new', NULL,      'HSW-OVS-001',  400,  200,'active', 1),
(2, 11, 'White Chunky Platform Sneakers', 'white-chunky-sneakers',          'รองเท้าผ้าใบโซลหนา สไตล์เกาหลี',    890.00,  NULL,    'new', NULL,      'SNK-CHP-WHT',  600,  150,'active', 0),
(3, 13, 'Ceramic Non-Stick Frying Pan 28cm','ceramic-nonstick-frypan-28',   'กระทะเซรามิคไม่ติด ขนาด 28 ซม.',    699.00,  599.00,  'new', 'Tefal',   'TFL-PAN-28',   850,  120,'active', 0),
(3, 14, 'Premium Cotton Bed Sheet Set',   'cotton-bed-sheet-set',           'ผ้าปูที่นอน Cotton 500 TC ชุด 3 ชิ้น',890.00, 750.00,  'new', NULL,      'BED-CTN-KG',  1200,  60, 'active', 0);

-- ============================================================
-- PRODUCT IMAGES
-- ============================================================
INSERT INTO product_images (product_id, image_url, sort_order, is_primary) VALUES
(1, 'https://cdn.shopee.th/img/s24u-1.jpg', 0, 1),
(1, 'https://cdn.shopee.th/img/s24u-2.jpg', 1, 0),
(2, 'https://cdn.shopee.th/img/mba-m3-1.jpg', 0, 1),
(3, 'https://cdn.shopee.th/img/xm5-1.jpg', 0, 1),
(4, 'https://cdn.shopee.th/img/hoodie-1.jpg', 0, 1),
(4, 'https://cdn.shopee.th/img/hoodie-2.jpg', 1, 0),
(5, 'https://cdn.shopee.th/img/sneaker-1.jpg', 0, 1),
(6, 'https://cdn.shopee.th/img/pan-1.jpg', 0, 1),
(7, 'https://cdn.shopee.th/img/bedsheet-1.jpg', 0, 1);

-- ============================================================
-- VARIANT TYPES & OPTIONS
-- ============================================================
-- Samsung S24 Ultra: Storage x Color
INSERT INTO variant_types (product_id, type_name, sort_order) VALUES
(1, 'Storage', 0),
(1, 'Color',   1),
(4, 'Size',    0),
(4, 'Color',   1),
(5, 'Size',    0);

INSERT INTO variant_options (variant_type_id, value, sort_order) VALUES
(1, '256GB', 0), (1, '512GB', 1),
(2, 'Titanium Black', 0), (2, 'Titanium Gray', 1), (2, 'Titanium Violet', 2),
(3, 'S', 0), (3, 'M', 1), (3, 'L', 2), (3, 'XL', 3),
(4, 'Pink', 0), (4, 'Beige', 1), (4, 'Navy', 2),
(5, '36', 0), (5, '37', 1), (5, '38', 2), (5, '39', 3), (5, '40', 4);

-- ============================================================
-- PRODUCT SKUs
-- ============================================================
INSERT INTO product_skus (product_id, sku_code, price, discount_price, stock, sold) VALUES
(1, 'SAM-S24U-256-BLK', 45900.00, 42900.00, 20, 5),
(1, 'SAM-S24U-256-GRY', 45900.00, 42900.00, 15, 3),
(1, 'SAM-S24U-512-BLK', 50900.00, 47900.00, 10, 2),
(2, 'MBP-M3-13-SLV',    45990.00, NULL,      30, 8),
(3, 'SNY-XM5-BLK',      12990.00, 10990.00, 50, 12),
(3, 'SNY-XM5-SLV',      12990.00, 10990.00, 30,  5),
(4, 'HSW-OVS-S-PNK',      590.00,   490.00, 30, 10),
(4, 'HSW-OVS-M-PNK',      590.00,   490.00, 40, 15),
(4, 'HSW-OVS-L-BGE',      590.00,   490.00, 30,  8),
(5, 'SNK-CHP-38',          890.00,   NULL,   25,  4),
(5, 'SNK-CHP-39',          890.00,   NULL,   30,  6),
(6, 'TFL-PAN-28',          699.00,  599.00, 120,  30),
(7, 'BED-CTN-KG',          890.00,  750.00,  60,  18);

-- ============================================================
-- SHIPPING PROVIDERS
-- ============================================================
INSERT INTO shipping_providers (name, code, tracking_url, is_active) VALUES
('J&T Express',    'JNT',    'https://www.jtexpress.th/track?no={tracking_no}',     1),
('Kerry Express',  'KERRY',  'https://th.kerryexpress.com/track?no={tracking_no}',  1),
('Flash Express',  'FLASH',  'https://www.flashexpress.co.th/tracking/{tracking_no}',1),
('Thailand Post',  'THPOST', 'https://track.thailandpost.co.th/?tracknumber={tracking_no}', 1),
('Shopee Xpress',  'SPX',    'https://spx.co.th/track?no={tracking_no}',            1);

-- ============================================================
-- ORDERS
-- ============================================================
INSERT INTO orders (order_number, buyer_user_id, shop_id, address_id, provider_id, subtotal, shipping_fee, shop_discount, total_amount, payment_method, payment_status, order_status, tracking_number, shipped_at, delivered_at, completed_at) VALUES
('ORD-20240401-000001', 6, 1, 1, 5, 42900.00, 0.00,  0.00, 42900.00, 'shopee_pay',   'paid',    'completed', 'SPX1234567890', '2024-04-01 14:00:00', '2024-04-04 10:30:00', '2024-04-05 08:00:00'),
('ORD-20240402-000002', 7, 2, 3, 1, 490.00,  40.00,  0.00, 530.00,  'cod',          'paid',    'completed', 'JNT9876543210', '2024-04-02 09:00:00', '2024-04-05 13:00:00', '2024-04-06 09:00:00'),
('ORD-20240410-000003', 8, 1, 4, 5, 10990.00, 0.00, 500.00,10490.00, 'credit_card',  'paid',    'completed', 'SPX1111111111', '2024-04-10 11:00:00', '2024-04-13 15:00:00', '2024-04-14 10:00:00'),
('ORD-20240501-000004', 9, 3, 5, 3, 599.00,  50.00,  0.00, 649.00,  'bank_transfer','paid',    'shipped',   'FLH0000000001', '2024-05-01 16:00:00', NULL, NULL),
('ORD-20240502-000005', 10,2, 6, 1, 890.00,  40.00,  0.00, 930.00,  'cod',          'pending', 'pending',   NULL,            NULL, NULL, NULL);

-- ============================================================
-- ORDER ITEMS
-- ============================================================
INSERT INTO order_items (order_id, product_id, sku_id, product_name, sku_snapshot, image_url, unit_price, quantity, subtotal) VALUES
(1, 1, 1, 'Samsung Galaxy S24 Ultra 256GB', '256GB / Titanium Black', 'https://cdn.shopee.th/img/s24u-1.jpg', 42900.00, 1, 42900.00),
(2, 4, 7, 'Oversized Pastel Hoodie',        'S / Pink',               'https://cdn.shopee.th/img/hoodie-1.jpg', 490.00, 1, 490.00),
(3, 3, 5, 'Sony WH-1000XM5 Headphones',    'Black',                  'https://cdn.shopee.th/img/xm5-1.jpg', 10990.00, 1, 10990.00),
(4, 6, 12,'Ceramic Non-Stick Frying Pan',   '28cm',                   'https://cdn.shopee.th/img/pan-1.jpg', 599.00,  1, 599.00),
(5, 5, 11,'White Chunky Platform Sneakers', 'Size 39',                'https://cdn.shopee.th/img/sneaker-1.jpg', 890.00, 1, 890.00);

-- ============================================================
-- ORDER STATUS HISTORY
-- ============================================================
INSERT INTO order_status_history (order_id, status, note, created_by) VALUES
(1, 'pending',   'Order placed',              6),
(1, 'confirmed', 'Seller confirmed',          3),
(1, 'shipped',   'Handed to SPX',             3),
(1, 'delivered', 'Delivered successfully',    NULL),
(1, 'completed', 'Auto-completed after 7d',   NULL),
(2, 'pending',   'Order placed',              7),
(2, 'confirmed', 'Seller confirmed',          4),
(2, 'shipped',   'Shipped via JNT',           4),
(2, 'delivered', 'Delivered',                NULL),
(2, 'completed', 'Buyer confirmed receipt',   7),
(4, 'pending',   'Order placed',              9),
(4, 'confirmed', 'Seller confirmed',          5),
(4, 'shipped',   'Shipped via Flash',         5);

-- ============================================================
-- PAYMENTS
-- ============================================================
INSERT INTO payments (order_id, payment_method, amount, status, transaction_ref, paid_at) VALUES
(1, 'shopee_pay',   42900.00, 'success', 'TXN-SP-00001', '2024-04-01 10:05:00'),
(2, 'cod',          530.00,   'success', 'TXN-COD-00002','2024-04-05 13:00:00'),
(3, 'credit_card',  10490.00, 'success', 'TXN-CC-00003', '2024-04-10 09:30:00'),
(4, 'bank_transfer',649.00,   'success', 'TXN-BT-00004', '2024-05-01 12:00:00'),
(5, 'cod',          930.00,   'pending', NULL,            NULL);

-- ============================================================
-- REVIEWS
-- ============================================================
INSERT INTO reviews (product_id, sku_id, order_id, reviewer_id, shop_id, rating, comment) VALUES
(1, 1, 1, 6, 1, 5, 'สินค้าของแท้ ส่งเร็วมาก กล่องไม่บุบ ถ่ายรูปสวยมากเลย ประทับใจมาก'),
(4, 7, 2, 7, 2, 4, 'ผ้านุ่มมากเลยค่ะ ไซส์ตรง แต่สีอ่อนกว่าในรูปนิดหน่อย'),
(3, 5, 3, 8, 1, 5, 'ตัด noise ได้ดีมาก เสียงเบสก็ดี เชื่อม Bluetooth ง่าย แนะนำเลยครับ');

-- ============================================================
-- SHOPPING CART
-- ============================================================
INSERT INTO carts (user_id) VALUES (9), (10);

INSERT INTO cart_items (cart_id, product_id, sku_id, quantity, is_checked) VALUES
(1, 2, 4, 1, 1),
(1, 7, 13,1, 1),
(2, 5, 11,1, 1),
(2, 4, 8, 2, 1);

-- ============================================================
-- WISHLISTS
-- ============================================================
INSERT INTO wishlists (user_id, product_id) VALUES
(6, 2), (6, 3), (7, 1), (8, 4), (9, 1), (10, 3);

-- ============================================================
-- SHOP FOLLOWERS
-- ============================================================
INSERT INTO shop_followers (user_id, shop_id) VALUES
(6, 1), (7, 1), (8, 1), (8, 2), (9, 2), (9, 3), (10, 1), (10, 3);

-- ============================================================
-- PLATFORM VOUCHERS
-- ============================================================
INSERT INTO platform_vouchers (code, name, description, discount_type, discount_value, min_order_amount, max_discount_cap, total_qty, per_user_limit, start_at, expire_at) VALUES
('SHOPEE50',   'Shopee ลด 50 บาท',       'ส่วนลด 50 บาท สำหรับออเดอร์ 200+ บาท',  'fixed',      50.00,  200.00, NULL,   1000, 1, '2024-01-01 00:00:00', '2024-12-31 23:59:59'),
('FREESHIP',   'ฟรีค่าส่ง',              'ฟรีค่าจัดส่ง ไม่มีขั้นต่ำ',              'free_shipping',0.00, 0.00,   40.00,  5000, 2, '2024-01-01 00:00:00', '2024-12-31 23:59:59'),
('NEW30',      'ยินดีต้อนรับ ลด 30%',    'ลด 30% สำหรับสมาชิกใหม่',               'percentage', 30.00,  100.00, 200.00, 500,  1, '2024-01-01 00:00:00', '2024-06-30 23:59:59');

-- ============================================================
-- SHOP VOUCHERS
-- ============================================================
INSERT INTO shop_vouchers (shop_id, code, name, discount_type, discount_value, min_order_amount, max_discount_cap, total_qty, per_user_limit, start_at, expire_at) VALUES
(1, 'TECH200',   'TechGadget ลด 200 บาท', 'fixed',      200.00, 3000.00, NULL,   200, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59'),
(2, 'FASHION10', 'Fashion ลด 10%',         'percentage',  10.00,  300.00, 100.00, 300, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59'),
(3, 'HOME50',    'HomeStuff ลด 50 บาท',   'fixed',       50.00,  400.00, NULL,   150, 1, '2024-04-01 00:00:00', '2024-07-31 23:59:59');

-- ============================================================
-- FLASH SALES
-- ============================================================
INSERT INTO flash_sales (title, start_at, end_at, is_active) VALUES
('Flash Sale 12.00 น.', '2024-05-10 12:00:00', '2024-05-10 14:00:00', 0),
('Flash Sale กลางคืน',  '2024-05-10 22:00:00', '2024-05-11 00:00:00', 0),
('Flash Sale สุดสัปดาห์','2024-05-11 10:00:00','2024-05-11 16:00:00', 1);

INSERT INTO flash_sale_items (flash_sale_id, product_id, sku_id, flash_price, original_price, qty_available, qty_sold, per_user_limit) VALUES
(1, 1, 1, 39900.00, 45900.00, 10, 10, 1),
(1, 3, 5, 8990.00,  12990.00, 20, 18, 1),
(3, 4, 8, 390.00,   590.00,   50,  5, 2),
(3, 6, 12,499.00,   699.00,   30,  0, 1);

-- ============================================================
-- BANNERS
-- ============================================================
INSERT INTO banners (title, image_url, link_url, position, sort_order, start_at, end_at, is_active) VALUES
('Shopee Birthday Sale',     'https://cdn.shopee.th/banners/bday-2024.jpg',    '/sale/birthday',     'homepage_main', 1, '2024-06-01 00:00:00', '2024-06-30 23:59:59', 1),
('Flash Deals Daily',        'https://cdn.shopee.th/banners/flash-daily.jpg',  '/flash-sale',        'homepage_sub',  1, NULL, NULL, 1),
('Electronics Mega Sale',    'https://cdn.shopee.th/banners/elec-mega.jpg',    '/category/electronics','homepage_sub',2, NULL, NULL, 1);

-- ============================================================
-- WALLETS
-- ============================================================
INSERT INTO wallets (user_id, balance, coins) VALUES
(6,  250.00, 120.00),
(7,    0.00,  50.00),
(8,  100.00, 200.00),
(9,    0.00,  30.00),
(10,  75.00,  80.00);

-- ============================================================
-- WALLET TRANSACTIONS
-- ============================================================
INSERT INTO wallet_transactions (wallet_id, type, amount, balance_before, balance_after, reference_type, reference_id, description) VALUES
(1, 'cashback',   50.00,   200.00, 250.00, 'order', 1, 'Cashback 1% จากออเดอร์ ORD-20240401-000001'),
(3, 'coins_earn', 100.00,  100.00, 200.00, 'order', 3, 'Coins earned from order ORD-20240410-000003'),
(1, 'topup',      200.00,    0.00, 200.00, NULL,    NULL, 'เติมเงิน Shopee Pay');

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
INSERT INTO notifications (user_id, type, title, body, reference_type, reference_id, is_read) VALUES
(6,  'order_update', 'สินค้าของคุณถูกจัดส่งแล้ว!',             'ออเดอร์ ORD-20240401-000001 กำลังเดินทาง',       'order', 1, 1),
(6,  'promotion',    'Flash Sale เริ่มแล้ว!',                   'อย่าพลาด สินค้าลดสูงสุด 70% เฉพาะวันนี้เท่านั้น', NULL, NULL, 0),
(7,  'order_update', 'ออเดอร์ของคุณสำเร็จแล้ว',                'ออเดอร์ ORD-20240402-000002 สำเร็จแล้ว โปรดรีวิว', 'order', 2, 0),
(8,  'promotion',    'คุณมีโค้ดส่วนลดที่ยังไม่ได้ใช้',          'ใช้ TECH200 ก่อนหมดอายุ 31 ก.ค.',               NULL, NULL, 0),
(3,  'new_order',    'คุณมีออเดอร์ใหม่!',                      'ออเดอร์ ORD-20240501-000004 รอการยืนยัน',         'order', 4, 1);

-- ============================================================
-- CONVERSATIONS & MESSAGES
-- ============================================================
INSERT INTO conversations (buyer_user_id, shop_id, last_message_at, seller_unread) VALUES
(8, 1, '2024-04-09 08:30:00', 1),
(9, 3, '2024-05-01 09:00:00', 0);

INSERT INTO messages (conversation_id, sender_id, message_type, content, is_read) VALUES
(1, 8, 'text', 'สวัสดีค่ะ หูฟัง Sony XM5 มีสีขาวไหมคะ?',      1),
(1, 3, 'text', 'สวัสดีครับ ตอนนี้มีแค่สีดำกับสีเงินนะครับ',    1),
(1, 8, 'text', 'โอเคค่ะ งั้นขอสั่งสีดำได้เลยนะคะ',             1),
(1, 3, 'text', 'ได้เลยครับ! กดสั่งได้เลยนะครับ ส่งเร็วแน่นอน', 0),
(2, 9, 'text', 'กระทะขนาดนี้เหมาะกับเตาแก๊สทั่วไปไหมครับ?',   1),
(2, 5, 'text', 'เหมาะมากเลยครับ ใช้ได้กับเตาทุกประเภทครับ',    1);

-- ============================================================
-- SEARCH HISTORY
-- ============================================================
INSERT INTO search_history (user_id, keyword) VALUES
(6, 'samsung s24'),
(6, 'macbook m3'),
(7, 'hoodie oversized'),
(8, 'headphone'),
(8, 'sony xm5'),
(9, 'กระทะ non-stick'),
(10,'รองเท้าผ้าใบ');

-- ============================================================
-- SHOP RATING SUMMARY
-- ============================================================
INSERT INTO shop_rating_summary (shop_id, rating_5, rating_4, rating_3, rating_2, rating_1, avg_rating, total_reviews) VALUES
(1, 25, 8, 2, 1, 0,  4.58, 36),
(2, 15, 6, 1, 0, 0,  4.64, 22),
(3,  8, 3, 1, 0, 0,  4.58, 12);

-- Update shop totals
UPDATE shops SET rating=4.58, total_reviews=36, total_products=3, total_sales=50 WHERE shop_id=1;
UPDATE shops SET rating=4.64, total_reviews=22, total_products=2, total_sales=30 WHERE shop_id=2;
UPDATE shops SET rating=4.58, total_reviews=12, total_products=2, total_sales=15 WHERE shop_id=3;

-- Update product review totals
UPDATE products SET rating=5.00, total_reviews=1, total_sold=5  WHERE product_id=1;
UPDATE products SET rating=0.00, total_reviews=0, total_sold=8  WHERE product_id=2;
UPDATE products SET rating=5.00, total_reviews=1, total_sold=17 WHERE product_id=3;
UPDATE products SET rating=4.00, total_reviews=1, total_sold=33 WHERE product_id=4;
UPDATE products SET rating=0.00, total_reviews=0, total_sold=10 WHERE product_id=5;
UPDATE products SET rating=0.00, total_reviews=0, total_sold=30 WHERE product_id=6;
UPDATE products SET rating=0.00, total_reviews=0, total_sold=18 WHERE product_id=7;
