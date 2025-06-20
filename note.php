CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `email` varchar
  `email_verified_at` timestamp  ,
  `password` varchar
  `token` varchar,
  `photo` varchar,
  `phone` varchar,
  `address` varchar,
  `role` varcharNOT  DEFAULT 'admin',
  `status` varcharNOT  DEFAULT '1',
  `remember_token` varchar(100) ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `admin_wallets` (
  `id` bigint(20) UNSIGNED 
  `type` enum('income','expense') NOT  COMMENT 'Loại giao dịch: thu hoặc chi',
  `amount` bigint(20) 
  `description` varchar,
  `total_income` bigint(20) NOT  DEFAULT 0,
  `total_expense` bigint(20) NOT  DEFAULT 0,
  `balance` bigint(20) NOT  DEFAULT 0,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED 
  `image` varchar
  `url` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `cache` (
  `key` varchar
  `value` mediumtext 
  `expiration` int(11) NOT 
) 

CREATE TABLE `cache_locks` (
  `key` varchar
  `owner` varchar
  `expiration` int(11) NOT 
) 

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED 
  `menu_id` int(11) 
  `category_name` varchar
  `image` varchar,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED 
  `city_name` varchar
  `city_slug` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `email` varchar
  `email_verified_at` timestamp  ,
  `password` varchar
  `token` varchar,
  `photo` varchar,
  `phone` varchar,
  `address` varchar,
  `ward_id` bigint(20) ,
  `role` varcharNOT  DEFAULT 'client',
  `status` varcharNOT  DEFAULT '1',
  `remember_token` varchar(100) ,
  `created_at` timestamp  ,
  `updated_at` timestamp  ,
  `city_id` bigint(20) ,
  `shop_info` text ,
  `cover_photo` varchar
) 

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED 
  `coupon_name` varchar
  `coupon_desc` varchar,
  `image_path` varchar,
  `quantity` int(10) UNSIGNED NOT  DEFAULT 0,
  `quantity_apply` int(10) NOT  DEFAULT 0,
  `discount` int(11) ,
  `max_discount_amount` bigint(20) UNSIGNED NOT  DEFAULT 0,
  `validity` varchar,
  `client_id` varchar,
  `status` int(11) ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED 
  `city_id` bigint(20) UNSIGNED 
  `district_name` varchar
  `district_slug` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED 
  `uuid` varchar
  `connection` text 
  `queue` text 
  `payload` longtext 
  `exception` longtext 
  `failed_at` timestamp NOT  DEFAULT current_timestamp()
) 

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED 
  `client_id` varchar,
  `gallery_img` varchar,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED 
  `queue` varchar
  `payload` longtext 
  `attempts` tinyint(3) UNSIGNED 
  `reserved_at` int(10) UNSIGNED ,
  `available_at` int(10) UNSIGNED 
  `created_at` int(10) UNSIGNED NOT 
) 

CREATE TABLE `job_batches` (
  `id` varchar
  `name` varchar
  `total_jobs` int(11) 
  `pending_jobs` int(11) 
  `failed_jobs` int(11) 
  `failed_job_ids` longtext 
  `options` mediumtext ,
  `cancelled_at` int(11) ,
  `created_at` int(11) 
  `finished_at` int(11) 
) 

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED 
  `menu_name` varchar
  `image` varchar,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED 
  `migration` varchar
  `batch` int(11) NOT 
) 

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED 
  `model_type` varchar
  `model_id` bigint(20) UNSIGNED NOT 
) 

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED 
  `model_type` varchar
  `model_id` bigint(20) UNSIGNED NOT 
) 

CREATE TABLE `notifications` (
  `id` char(36) 
  `type` varchar
  `notifiable_type` varchar
  `notifiable_id` bigint(20) UNSIGNED 
  `data` text 
  `read_at` timestamp  ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED 
  `user_id` bigint(20) UNSIGNED 
  `name` varchar
  `email` varchar
  `phone` varchar
  `address` text 
  `ward_id` bigint(20) UNSIGNED ,
  `payment_type` varchar,
  `payment_method` varchar,
  `transaction_id` varchar,
  `currency` varchar,
  `amount` float 
  `total_amount` float 
  `service_fee` double NOT  DEFAULT 0,
  `shipping_fee` bigint(20) NOT  DEFAULT 0,
  `coupon_code` varchar,
  `net_revenue` double ,
  `order_number` varchar,
  `invoice_no` varchar,
  `order_date` varchar,
  `order_month` varchar,
  `order_year` varchar,
  `confirmed_date` varchar,
  `processing_date` varchar,
  `shipped_date` varchar,
  `delivered_date` varchar,
  `cancel_reason` text ,
  `status` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED 
  `order_id` bigint(20) UNSIGNED 
  `product_id` bigint(20) UNSIGNED 
  `client_id` varchar,
  `qty` varchar
  `price` float 
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `order_reports` (
  `id` bigint(20) UNSIGNED 
  `client_id` bigint(20) UNSIGNED 
  `order_id` bigint(20) UNSIGNED 
  `content` text 
  `issue_type` enum('delivery','product_quality','payment','customer_service','other') NOT  DEFAULT 'other',
  `status` enum('pending','resolved','rejected') NOT  DEFAULT 'pending',
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `password_reset_tokens` (
  `email` varchar
  `token` varchar
  `created_at` timestamp  
) 

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `guard_name` varchar
  `group_name` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `slug` varchar,
  `category_id` int(11) 
  `city_id` int(11) 
  `menu_id` int(11) 
  `code` varchar,
  `qty` varchar,
  `size` varchar,
  `price` varchar,
  `discount_price` varchar,
  `image` varchar,
  `client_id` varchar,
  `most_populer` varchar,
  `best_seller` varchar,
  `status` varchar,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `product_details` (
  `id` bigint(20) UNSIGNED 
  `product_template_id` bigint(20) UNSIGNED 
  `description` text DEFAULT 'Đang cập nhật',
  `product_info` text DEFAULT 'Đang cập nhật',
  `note` text DEFAULT 'Đang cập nhật',
  `origin` varcharDEFAULT 'Đang cập nhật',
  `preservation` text DEFAULT 'Đang cập nhật',
  `weight` varcharDEFAULT 'Đang cập nhật',
  `usage_instructions` text DEFAULT 'Đang cập nhật',
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `product_news` (
  `id` bigint(20) UNSIGNED 
  `client_id` bigint(20) UNSIGNED 
  `product_template_id` bigint(20) UNSIGNED 
  `qty` int(11) NOT  DEFAULT 0,
  `sold` int(11) NOT  DEFAULT 0,
  `price` double 
  `cost_price` bigint(20) ,
  `discount_price` double ,
  `most_popular` tinyint(1) NOT  DEFAULT 0,
  `best_seller` tinyint(1) NOT  DEFAULT 0,
  `status` varcharNOT  DEFAULT 'active',
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `product_reviews` (
  `id` bigint(20) UNSIGNED 
  `product_id` bigint(20) UNSIGNED 
  `user_id` bigint(20) UNSIGNED 
  `client_id` bigint(20) UNSIGNED ,
  `comment` text ,
  `rating` varchar,
  `status` varcharNOT  DEFAULT '0',
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `product_review_reports` (
  `id` bigint(20) UNSIGNED 
  `product_review_id` bigint(20) UNSIGNED 
  `reported_by_client_id` bigint(20) UNSIGNED 
  `reason` text ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `product_templates` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `slug` varchar,
  `category_id` bigint(20) UNSIGNED 
  `menu_id` bigint(20) UNSIGNED ,
  `code` varchar,
  `size` varchar,
  `unit` varchar,
  `image` varchar,
  `status` int(11) 
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED 
  `client_id` bigint(20) UNSIGNED 
  `user_id` bigint(20) UNSIGNED 
  `order_id` bigint(20) ,
  `comment` text ,
  `rating` varchar,
  `status` varcharNOT  DEFAULT '0',
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `review_reports` (
  `id` bigint(20) UNSIGNED 
  `review_id` bigint(20) UNSIGNED 
  `reported_by_client_id` bigint(20) UNSIGNED 
  `reason` text ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `guard_name` varchar
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED 
  `role_id` bigint(20) UNSIGNED NOT 
) 

CREATE TABLE `sessions` (
  `id` varchar
  `user_id` bigint(20) UNSIGNED ,
  `ip_address` varchar(45) ,
  `user_agent` text ,
  `payload` longtext 
  `last_activity` int(11) NOT 
) 

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED 
  `name` varchar
  `email` varchar
  `email_verified_at` timestamp  ,
  `password` varchar
  `photo` varchar,
  `phone` varchar,
  `address` varchar,
  `ward_id` bigint(20) UNSIGNED ,
  `role` varcharNOT  DEFAULT 'user',
  `status` varcharNOT  DEFAULT '1',
  `remember_token` varchar(100) ,
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `wards` (
  `id` bigint(20) UNSIGNED 
  `district_id` bigint(20) UNSIGNED 
  `ward_name` varchar
  `ward_slug` VARCHAR
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED 
  `user_id` bigint(20) UNSIGNED 
  `client_id` bigint(20) UNSIGNED 
  `created_at` timestamp  ,
  `updated_at` timestamp  
) 




















vendor/bin/pest