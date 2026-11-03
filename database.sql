CREATE DATABASE IF NOT EXISTS crm_6012 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crm_6012;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 phone VARCHAR(20) NOT NULL,
 address VARCHAR(255) NULL,
 join_date DATE NOT NULL DEFAULT (CURRENT_DATE),
 member_status ENUM('Member','Non-member') NOT NULL DEFAULT 'Member',
 points INT UNSIGNED NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_customer_name(name),
 INDEX idx_customer_phone(phone)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transactions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id INT UNSIGNED NULL,
 menu_name VARCHAR(150) NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 total_amount BIGINT UNSIGNED NOT NULL,
 points_awarded INT UNSIGNED NOT NULL DEFAULT 0,
 transaction_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_transaction_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE,
 INDEX idx_transaction_date(transaction_date),
 INDEX idx_transaction_customer(customer_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS complaints (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 customer_id INT UNSIGNED NULL,
 complaint_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 category ENUM('Keluhan','Saran') NOT NULL,
 description TEXT NOT NULL,
 pic VARCHAR(100) NULL,
 status ENUM('Baru','Diproses','Selesai') NOT NULL DEFAULT 'Baru',
 CONSTRAINT fk_complaint_customer FOREIGN KEY(customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE,
 INDEX idx_complaint_date(complaint_date),
 INDEX idx_complaint_status(status)
) ENGINE=InnoDB;

-- 20 pelanggan dummy/simulasi. Bukan data pelanggan asli.
INSERT INTO customers (name,phone,address,join_date,member_status,points) VALUES
('Andi Pratama','081234560001','Yogyakarta','2026-01-05','Member',12),
('Siti Rahma','081234560002','Sleman','2026-01-08','Member',8),
('Bima Saputra','081234560003','Bantul','2026-01-12','Non-member',0),
('Nadia Putri','081234560004','Yogyakarta','2026-02-01','Member',15),
('Rizky Maulana','081234560005','Depok, Sleman','2026-02-10','Member',4),
('Dewi Lestari','081234560006','Kasihan, Bantul','2026-02-14','Non-member',0),
('Fajar Nugroho','081234560007','Yogyakarta','2026-03-03','Member',21),
('Intan Permata','081234560008','Mlati, Sleman','2026-03-07','Member',6),
('Dimas Arya','081234560009','Yogyakarta','2026-03-15','Non-member',0),
('Aulia Safitri','081234560010','Gamping, Sleman','2026-03-20','Member',10),
('Yoga Firmansyah','081234560011','Yogyakarta','2026-04-02','Member',3),
('Citra Amelia','081234560012','Bantul','2026-04-09','Member',5),
('Rangga Wijaya','081234560013','Yogyakarta','2026-04-18','Non-member',0),
('Maya Anggraini','081234560014','Sleman','2026-05-01','Member',9),
('Arif Setiawan','081234560015','Yogyakarta','2026-05-12','Member',2),
('Laila Nurfadila','081234560016','Bantul','2026-05-20','Non-member',0),
('Kevin Alexander','081234560017','Yogyakarta','2026-06-02','Member',18),
('Nisa Khairunnisa','081234560018','Sleman','2026-06-11','Member',7),
('Bagas Ramadhan','081234560019','Yogyakarta','2026-06-22','Non-member',0),
('Putri Maharani','081234560020','Kulon Progo','2026-07-01','Member',11);
