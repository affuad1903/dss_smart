-- SQL untuk membuat database SPK SMART
-- Jalankan ini di MySQL/PhpMyAdmin jika belum ada database

CREATE DATABASE IF NOT EXISTS `spk_smart` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `spk_smart`;

-- Setelah database dibuat, jalankan:
-- php artisan migrate:fresh --seed
