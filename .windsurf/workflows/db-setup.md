---
description: Setup and manage shopee_db database on XAMPP MySQL
---

## วิธีใช้งาน Database กับ XAMPP

### 1. ตรวจสอบว่า XAMPP MySQL กำลังรันอยู่
// turbo
Run command: `C:\xampp\mysql\bin\mysqladmin.exe -u root ping`

### 2. สร้าง Database และ Tables (Schema)
// turbo
Run command: `C:\xampp\mysql\bin\mysql.exe -u root shopee_db < c:\xampp\htdocs\webshop\database\shopee_schema.sql`

หากยังไม่มี database ให้รันคำสั่งนี้ก่อน:
// turbo
Run command: `C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS shopee_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`

### 3. เพิ่มข้อมูลตัวอย่าง (Seed Data)
// turbo
Run command: `C:\xampp\mysql\bin\mysql.exe -u root shopee_db < c:\xampp\htdocs\webshop\database\shopee_seed.sql`

### 4. ตรวจสอบตารางทั้งหมด
// turbo
Run command: `C:\xampp\mysql\bin\mysql.exe -u root shopee_db -e "SHOW TABLES;"`

### 5. ล้าง Database และสร้างใหม่ทั้งหมด (Reset)
// turbo
Run command: `C:\xampp\mysql\bin\mysql.exe -u root -e "DROP DATABASE IF EXISTS shopee_db; CREATE DATABASE shopee_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"` && `C:\xampp\mysql\bin\mysql.exe -u root shopee_db < c:\xampp\htdocs\webshop\database\shopee_schema.sql` && `C:\xampp\mysql\bin\mysql.exe -u root shopee_db < c:\xampp\htdocs\webshop\database\shopee_seed.sql`
