-- ALTER TABLE rpos_products
--   ADD COLUMN qr_code VARCHAR(255) DEFAULT NULL,
--   ADD COLUMN category VARCHAR(100) DEFAULT NULL,
--   ADD COLUMN images TEXT DEFAULT NULL,
--   ADD COLUMN status ENUM('active','inactive') DEFAULT 'active',
--   ADD COLUMN min_stocks INT DEFAULT 0,
--   ADD COLUMN quantity INT DEFAULT 0;

-- 1. USERS: Add email, phone, role, notification_prefs
-- ALTER TABLE users
--   ADD COLUMN email VARCHAR(100) DEFAULT NULL,
--   ADD COLUMN phone VARCHAR(30) DEFAULT NULL,
--   ADD COLUMN role ENUM('admin','cashier','customer') DEFAULT 'customer',
--   ADD COLUMN notification_prefs JSON DEFAULT NULL;


-- 2. PRODUCTS: Add category, images, qr_code, status
-- ALTER TABLE products
--   ADD COLUMN category VARCHAR(50) DEFAULT NULL,
--   ADD COLUMN images JSON DEFAULT NULL,
--   ADD COLUMN qr_code VARCHAR(255) DEFAULT NULL,
--   ADD COLUMN status ENUM('active','inactive') DEFAULT 'active';


-- 3. ORDERS: New table for all orders
CREATE TABLE rpos_orders (
 order_id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id VARCHAR(200) DEFAULT NULL,
 order_type ENUM('online','in_person') NOT NULL,
 items JSON NOT NULL,
 status ENUM('pending','packed','delivered','cancelled') DEFAULT 'pending',
 payment_id INT DEFAULT NULL,
 delivery_address TEXT DEFAULT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 -- FOREIGN KEY (customer_id) REFERENCES rpos_customers(customer_id)
);

-- 4. PAYMENTS: New table for all payment attempts
CREATE TABLE rpos_payments (
 payment_id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT,
 method ENUM('cash','momo','airtel','stripe') NOT NULL,
 status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
 transaction_ref VARCHAR(100) DEFAULT NULL,
 amount DECIMAL(18,2) NOT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (order_id) REFERENCES rpos_orders(order_id)
);

-- Add the payment_id foreign key to rpos_orders now that rpos_payments exists
ALTER TABLE rpos_orders ADD CONSTRAINT fk_orders_payment_id FOREIGN KEY (payment_id) REFERENCES rpos_payments(payment_id);

-- 5. MESSAGES: New table for chat, notifications, system alerts
CREATE TABLE rpos_messages (
 message_id INT AUTO_INCREMENT PRIMARY KEY,
 sender_id VARCHAR(200) DEFAULT NULL,
 receiver_id VARCHAR(200) DEFAULT NULL,
 type ENUM('chat','notification','system') NOT NULL,
 content TEXT NOT NULL,
 status ENUM('sent','read') DEFAULT 'sent',
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP
 -- You may add FOREIGN KEYs to rpos_admin, rpos_staff, rpos_customers as needed
);

-- 6. WISHLISTS: New table for customer wishlists/favorites
CREATE TABLE rpos_wishlists (
 wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id VARCHAR(200) NOT NULL,
 items JSON NOT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 -- FOREIGN KEY (customer_id) REFERENCES rpos_customers(customer_id)
);
