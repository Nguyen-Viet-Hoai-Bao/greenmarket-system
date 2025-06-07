// Use DBML to define your database structure
// Docs: https://dbml.dbdiagram.io/docs

Table admins {
  id bigint [primary key, increment]
  name varchar
  email varchar
  email_verified_at timestamp
  password varchar
  token varchar
  photo varchar
  phone varchar
  address varchar
  role varchar [not null, default: 'admin']
  status varchar [not null, default: '1']
  remember_token varchar(100)
  created_at timestamp
  updated_at timestamp
}

Table admin_wallets {
  id bigint [primary key, increment]
  type enum('income','expense') [not null, note: 'Loại giao dịch: thu hoặc chi']
  amount bigint
  description varchar
  total_income bigint [not null, default: 0]
  total_expense bigint [not null, default: 0]
  balance bigint [not null, default: 0]
  created_at timestamp
  updated_at timestamp
}

Table banners {
  id bigint [primary key, increment]
  image varchar
  url varchar
  created_at timestamp
  updated_at timestamp
}

Table categories {
  id bigint [primary key, increment]
  menu_id integer
  category_name varchar
  image varchar
  created_at timestamp
  updated_at timestamp
}

Table cities {
  id bigint [primary key, increment]
  city_name varchar
  city_slug varchar
  created_at timestamp
  updated_at timestamp
}

Table clients {
  id bigint [primary key, increment]
  name varchar
  email varchar
  email_verified_at timestamp
  password varchar
  token varchar
  photo varchar
  phone varchar
  address varchar
  ward_id bigint
  role varchar [not null, default: 'client']
  status varchar [not null, default: '1']
  remember_token varchar(100)
  created_at timestamp
  updated_at timestamp
  city_id bigint
  shop_info text
  cover_photo varchar
}

Table coupons {
  id bigint [primary key, increment]
  coupon_name varchar
  coupon_desc varchar
  image_path varchar
  quantity integer [not null, default: 0]
  quantity_apply integer [not null, default: 0]
  discount integer
  max_discount_amount bigint [not null, default: 0]
  validity varchar
  client_id varchar
  status integer
  created_at timestamp
  updated_at timestamp
}

Table districts {
  id bigint [primary key, increment]
  city_id bigint [not null]
  district_name varchar
  district_slug varchar
  created_at timestamp
  updated_at timestamp
}

Table galleries {
  id bigint [primary key, increment]
  client_id varchar
  gallery_img varchar
  created_at timestamp
  updated_at timestamp
}

Table menus {
  id bigint [primary key, increment]
  menu_name varchar
  image varchar
  created_at timestamp
  updated_at timestamp
}

Table notifications {
  id char(36) [primary key]
  type varchar [not null]
  notifiable_type varchar [not null]
  notifiable_id bigint [not null]
  data text [not null]
  read_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table orders {
  id bigint [primary key, increment]
  user_id bigint [not null]
  name varchar
  email varchar
  phone varchar
  address text [not null]
  ward_id bigint
  payment_type varchar
  payment_method varchar
  transaction_id varchar
  currency varchar
  amount float [not null]
  total_amount float [not null]
  service_fee double [not null, default: 0]
  shipping_fee bigint [not null, default: 0]
  coupon_code varchar
  net_revenue double
  order_number varchar
  invoice_no varchar
  order_date varchar
  order_month varchar
  order_year varchar
  confirmed_date varchar
  processing_date varchar
  shipped_date varchar
  delivered_date varchar
  cancel_reason text
  status varchar [not null]
  created_at timestamp
  updated_at timestamp
}

Table order_items {
  id bigint [primary key, increment]
  order_id bigint [not null]
  product_id bigint [not null]
  client_id varchar
  qty varchar
  price float [not null]
  created_at timestamp
  updated_at timestamp
}

Table order_reports {
  id bigint [primary key, increment]
  client_id bigint [not null]
  order_id bigint [not null]
  content text [not null]
  issue_type enum('delivery','product_quality','payment','customer_service','other') [not null, default: 'other']
  status enum('pending','resolved','rejected') [not null, default: 'pending']
  created_at timestamp
  updated_at timestamp
}

Table product_details {
  id bigint [primary key, increment]
  product_template_id bigint [not null]
  description text [default: 'Đang cập nhật']
  product_info text [default: 'Đang cập nhật']
  note text [default: 'Đang cập nhật']
  origin varchar [default: 'Đang cập nhật']
  preservation text [default: 'Đang cập nhật']
  weight varchar [default: 'Đang cập nhật']
  usage_instructions text [default: 'Đang cập nhật']
  created_at timestamp
  updated_at timestamp
}

Table product_news {
  id bigint [primary key, increment]
  client_id bigint [not null]
  product_template_id bigint [not null]
  qty integer [not null, default: 0]
  sold integer [not null, default: 0]
  price double [not null]
  cost_price bigint
  discount_price double
  most_popular tinyint [not null, default: 0]
  best_seller tinyint [not null, default: 0]
  status varchar [not null, default: 'active']
  created_at timestamp
  updated_at timestamp
}

Table product_reviews {
  id bigint [primary key, increment]
  product_id bigint [not null]
  user_id bigint [not null]
  client_id bigint
  comment text
  rating varchar
  status varchar [not null, default: '0']
  created_at timestamp
  updated_at timestamp
}

Table product_review_reports {
  id bigint [primary key, increment]
  product_review_id bigint [not null]
  reported_by_client_id bigint [not null]
  reason text
  created_at timestamp
  updated_at timestamp
}

Table product_templates {
  id bigint [primary key, increment]
  name varchar
  slug varchar
  category_id bigint [not null]
  menu_id bigint
  code varchar
  size varchar
  unit varchar
  image varchar
  status integer [not null]
  created_at timestamp
  updated_at timestamp
}

Table reviews {
  id bigint [primary key, increment]
  client_id bigint [not null]
  user_id bigint [not null]
  order_id bigint
  comment text
  rating varchar
  status varchar [not null, default: '0']
  created_at timestamp
  updated_at timestamp
}

Table review_reports {
  id bigint [primary key, increment]
  review_id bigint [not null]
  reported_by_client_id bigint [not null]
  reason text
  created_at timestamp
  updated_at timestamp
}

Table users {
  id bigint [primary key, increment]
  name varchar
  email varchar
  email_verified_at timestamp
  password varchar
  photo varchar
  phone varchar
  address varchar
  ward_id bigint
  role varchar [not null, default: 'user']
  status varchar [not null, default: '1']
  remember_token varchar(100)
  created_at timestamp
  updated_at timestamp
}

Table wards {
  id bigint [primary key, increment]
  district_id bigint [not null]
  ward_name varchar
  ward_slug varchar
  created_at timestamp
  updated_at timestamp
}

Table wishlists {
  id bigint [primary key, increment]
  user_id bigint [not null]
  client_id bigint [not null]
  created_at timestamp
  updated_at timestamp
}

// References (Relationships)

// Clients
Ref: clients.ward_id > wards.id
Ref: clients.city_id > cities.id

// Categories
Ref: categories.menu_id > menus.id

// Districts
Ref: districts.city_id > cities.id

// Orders
Ref: orders.user_id > users.id
Ref: orders.ward_id > wards.id

// Order Items
Ref: order_items.order_id > orders.id
Ref: order_items.product_id > product_news.id
// Ref: order_items.client_id > clients.id // Assuming client_id is numeric and references clients table

// Order Reports
Ref: order_reports.client_id > clients.id
Ref: order_reports.order_id > orders.id

// Product Details
Ref: product_details.product_template_id > product_templates.id

// Product News
Ref: product_news.client_id > clients.id
Ref: product_news.product_template_id > product_templates.id

// Product Reviews
Ref: product_reviews.product_id > product_news.id
Ref: product_reviews.user_id > users.id
Ref: product_reviews.client_id > clients.id

// Product Review Reports
Ref: product_review_reports.product_review_id > product_reviews.id
Ref: product_review_reports.reported_by_client_id > clients.id

// Product Templates
Ref: product_templates.category_id > categories.id
Ref: product_templates.menu_id > menus.id

// Reviews (Client Reviews of Orders/Users)
Ref: reviews.client_id > clients.id
Ref: reviews.user_id > users.id
Ref: reviews.order_id > orders.id

// Review Reports
Ref: review_reports.review_id > reviews.id
Ref: review_reports.reported_by_client_id > clients.id

// Users
Ref: users.ward_id > wards.id

// Wards
Ref: wards.district_id > districts.id

// Wishlists
Ref: wishlists.user_id > users.id
Ref: wishlists.client_id > clients.id

// Galleries
// Ref: galleries.client_id > clients.id // Assuming client_id is numeric and references clients table

// Coupons
// Ref: coupons.client_id > clients.id // Assuming client_id is numeric and references clients TableGroup