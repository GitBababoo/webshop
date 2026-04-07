# Shopee-like E-Commerce Database Schema

## วิธีติดตั้ง (XAMPP)

```bash
# 1. เปิด phpMyAdmin หรือ MySQL CLI
# 2. รันไฟล์ตามลำดับ

mysql -u root -p < shopee_schema.sql
mysql -u root -p < shopee_seed.sql
```

---

## โครงสร้างตาราง (34 Tables)

### 👤 Users & Accounts
| Table | คำอธิบาย |
|---|---|
| `users` | ข้อมูลผู้ใช้ทั้งหมด (buyer / seller / admin) |
| `user_addresses` | ที่อยู่จัดส่งของผู้ใช้ (หลายที่อยู่ได้) |
| `wallets` | กระเป๋าเงิน Shopee Pay + Coins |
| `wallet_transactions` | ประวัติการเคลื่อนไหวกระเป๋าเงิน |

### 🏪 Shops
| Table | คำอธิบาย |
|---|---|
| `shops` | ร้านค้าของ seller |
| `shop_followers` | ผู้ติดตามร้านค้า |
| `shop_rating_summary` | cache สรุปคะแนนรีวิวของร้าน |

### 📦 Products & Inventory
| Table | คำอธิบาย |
|---|---|
| `categories` | หมวดหมู่สินค้า (Hierarchical, รองรับหลายระดับ) |
| `products` | สินค้าหลัก |
| `product_images` | รูปภาพสินค้า (หลายรูปต่อ 1 สินค้า) |
| `variant_types` | ประเภท variant เช่น Color, Size |
| `variant_options` | ตัวเลือกย่อย เช่น Red, XL |
| `product_skus` | SKU = การรวม variant (ราคา, stock แต่ละ combination) |
| `sku_option_map` | mapping SKU ↔ variant options |
| `product_specifications` | spec/attribute ของสินค้า |

### 🛒 Shopping
| Table | คำอธิบาย |
|---|---|
| `carts` | ตะกร้าสินค้า (1 user = 1 cart) |
| `cart_items` | รายการในตะกร้า |
| `wishlists` | สินค้าที่ถูกใจ (Like) |

### 📋 Orders & Fulfillment
| Table | คำอธิบาย |
|---|---|
| `orders` | ออเดอร์หลัก (1 ออเดอร์ = 1 ร้านค้า) |
| `order_items` | รายการสินค้าในออเดอร์ พร้อม snapshot |
| `order_status_history` | ประวัติสถานะออเดอร์ทุก step |
| `shipping_providers` | บริษัทขนส่ง (J&T, Kerry, Flash, SPX, ฯลฯ) |
| `payments` | ข้อมูลการชำระเงิน + gateway response |
| `return_requests` | คำขอคืนสินค้า / คืนเงิน |
| `return_request_images` | รูปประกอบคำขอคืนสินค้า |

### ⭐ Reviews
| Table | คำอธิบาย |
|---|---|
| `reviews` | รีวิวสินค้า พร้อม seller reply |
| `review_images` | รูปภาพประกอบรีวิว |
| `review_likes` | กดถูกใจรีวิว (Helpful) |

### 🎟️ Vouchers & Promotions
| Table | คำอธิบาย |
|---|---|
| `platform_vouchers` | โค้ดส่วนลดจาก Shopee (ลด%, ลดตัวเลข, ฟรีค่าส่ง) |
| `shop_vouchers` | โค้ดส่วนลดจากร้านค้า |
| `user_vouchers` | โค้ดที่ผู้ใช้เก็บ/ใช้แล้ว |
| `flash_sales` | Flash Sale events |
| `flash_sale_items` | สินค้าใน Flash Sale (จำนวนจำกัด, per-user limit) |
| `banners` | Banner โฆษณาหน้าเว็บ |

### 💬 Communication
| Table | คำอธิบาย |
|---|---|
| `conversations` | การสนทนา buyer ↔ seller |
| `messages` | ข้อความในการสนทนา (text, image, product link, order) |
| `notifications` | การแจ้งเตือนของผู้ใช้ |

### 🔧 System
| Table | คำอธิบาย |
|---|---|
| `search_history` | ประวัติการค้นหา |
| `product_reports` | รายงานสินค้าที่ไม่เหมาะสม |
| `admin_logs` | Log การกระทำของ Admin |

---

## Order Status Flow

```
pending → confirmed → processing → shipped → delivered → completed
                                                ↓
                                       return_requested → returned
       ↓
   cancelled
```

## Payment Methods
`cod` | `credit_card` | `debit_card` | `bank_transfer` | `e_wallet` | `shopee_pay` | `coins`

## ตัวอย่าง Useful Queries

### หาสินค้าขายดีแต่ละหมวดหมู่
```sql
SELECT c.name AS category, p.name, p.total_sold
FROM products p
JOIN categories c ON p.category_id = c.category_id
WHERE p.status = 'active'
ORDER BY c.category_id, p.total_sold DESC;
```

### ยอดขายรวมของแต่ละร้านค้า
```sql
SELECT s.shop_name,
       COUNT(o.order_id)      AS total_orders,
       SUM(o.total_amount)    AS total_revenue
FROM orders o
JOIN shops s ON o.shop_id = s.shop_id
WHERE o.order_status = 'completed'
GROUP BY s.shop_id;
```

### สินค้าใน Flash Sale ที่ยังเหลืออยู่
```sql
SELECT p.name, fsi.flash_price, fsi.original_price,
       (fsi.qty_available - fsi.qty_sold) AS remaining,
       fs.end_at
FROM flash_sale_items fsi
JOIN flash_sales fs    ON fsi.flash_sale_id = fs.flash_sale_id
JOIN products p        ON fsi.product_id    = p.product_id
WHERE fs.is_active = 1 AND NOW() BETWEEN fs.start_at AND fs.end_at
  AND fsi.qty_sold < fsi.qty_available;
```

### รีวิวล่าสุดพร้อมข้อมูลผู้รีวิว
```sql
SELECT p.name AS product, u.username, r.rating, r.comment, r.created_at
FROM reviews r
JOIN products p ON r.product_id = p.product_id
JOIN users u    ON r.reviewer_id = u.user_id
ORDER BY r.created_at DESC
LIMIT 20;
```

### ลูกค้าที่มียอดซื้อสูงสุด
```sql
SELECT u.username, u.full_name,
       COUNT(o.order_id)   AS order_count,
       SUM(o.total_amount) AS total_spent
FROM orders o
JOIN users u ON o.buyer_user_id = u.user_id
WHERE o.payment_status = 'paid'
GROUP BY u.user_id
ORDER BY total_spent DESC
LIMIT 10;
```
