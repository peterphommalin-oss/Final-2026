-- =====================================================
-- ໄຟລ໌ SQL ສຳລັບສ້າງຖານຂໍ້ມູນ "ລະບົບຈັດການລາຍຮັບ-ລາຍຈ່າຍຂອງວັດ"
-- File: database.sql
-- ວິທີໃຊ້: ເປີດ phpMyAdmin ໃນ XAMPP → ກົດ "Import" → ເລືອກໄຟລ໌ນີ້
-- ຫຼື ສ້າງຖານຂໍ້ມູນ project_nonghainoy ກ່ອນ ແລ້ວ run SQL ນີ້
-- =====================================================

-- 1) ສ້າງຖານຂໍ້ມູນ ຖ້າຍັງບໍ່ມີ (ໃຊ້ utf8mb4 ເພື່ອຮອງຮັບໂຕອັກສອນລາວ)
CREATE DATABASE IF NOT EXISTS `project_nonghainoy`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- 2) ເລືອກໃຊ້ຖານຂໍ້ມູນທີ່ສ້າງ
USE `project_nonghainoy`;

-- 3) ສ້າງຕາຕະລາງ transactions ສຳລັບເກັບລາຍການເງິນ
CREATE TABLE IF NOT EXISTS `transactions` (
    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ລະຫັດອັດຕະໂນມັດ',
    `type`       ENUM('income','expense') NOT NULL COMMENT 'ປະເພດ: income=ລາຍຮັບ, expense=ລາຍຈ່າຍ',
    `details`    VARCHAR(255) NOT NULL COMMENT 'ລາຍລະອຽດຂອງລາຍການ',
    `amount`     DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'ຈຳນວນເງິນ (ກີບ)',
    `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'ວັນທີ ແລະ ເວລາທີ່ບັນທຶກ',
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_date_added` (`date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='ຕາຕະລາງເກັບລາຍຮັບ-ລາຍຈ່າຍຂອງວັດ';

-- 4) (ທາງເລືອກ) ໃສ່ຂໍ້ມູນຕົວຢ່າງ ເພື່ອທົດສອບ
INSERT INTO `transactions` (`type`, `details`, `amount`, `date_added`) VALUES
('income',  'ເງິນທຳບຸນຈາກພຸດທະສາສະນິກະຊົນ',          5000000.00, NOW()),
('income',  'ບໍລິຈາກສ້ອມແປງສິມ',                    2500000.00, NOW()),
('expense', 'ຄ່າໄຟຟ້າປະຈຳເດືອນ',                       350000.00, NOW()),
('expense', 'ຄ່າອາຫານພຣະ ແລະ ສາມະເນນ',              1200000.00, NOW());

-- ເພີ່ມຕາຕະລາງ users (ຖ້າຍັງບໍ່ມີ) ຫຼື ເພີ່ມ column role ເຂົ້າໄປ
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;