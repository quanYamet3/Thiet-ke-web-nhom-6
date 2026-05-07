
CREATE TABLE IF NOT EXISTS orders (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NULL,                      -- NULL if guest checkout
    fullname        VARCHAR(120) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    address         TEXT         NOT NULL,
    note            TEXT         NULL,
    payment_method  ENUM('cod','bank','momo') DEFAULT 'cod',
    subtotal        DECIMAL(12,0) NOT NULL DEFAULT 0,
    shipping        DECIMAL(12,0) NOT NULL DEFAULT 0,
    total           DECIMAL(12,0) NOT NULL DEFAULT 0,
    status          ENUM('pending','confirmed','shipping','delivered','cancelled')
                    DEFAULT 'pending',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user   (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id    INT UNSIGNED NOT NULL,
    product_id  INT UNSIGNED NOT NULL,
    qty         SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    price       DECIMAL(12,0) NOT NULL,       -- snapshot price at time of order
    INDEX idx_order   (order_id),
    INDEX idx_product (product_id),
    CONSTRAINT fk_oi_order   FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

