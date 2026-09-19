-- EC-CUBE Master Data English Localization Migration
-- Updates all Japanese master table values to standard English names

-- 1. Admin Member
UPDATE dtb_member SET name = 'Administrator' WHERE id = 1;

-- 2. Order Statuses (mtb_order_status & mtb_customer_order_status)
UPDATE mtb_order_status SET name = 'New Order' WHERE id = 1;
UPDATE mtb_order_status SET name = 'Cancelled' WHERE id = 3;
UPDATE mtb_order_status SET name = 'In Progress' WHERE id = 4;
UPDATE mtb_order_status SET name = 'Shipped' WHERE id = 5;
UPDATE mtb_order_status SET name = 'Payment Confirmed' WHERE id = 6;
UPDATE mtb_order_status SET name = 'Payment Processing' WHERE id = 7;
UPDATE mtb_order_status SET name = 'Purchase Processing' WHERE id = 8;
UPDATE mtb_order_status SET name = 'Returned' WHERE id = 9;

UPDATE mtb_customer_order_status SET name = 'New Order' WHERE id = 1;
UPDATE mtb_customer_order_status SET name = 'Cancelled' WHERE id = 3;
UPDATE mtb_customer_order_status SET name = 'In Progress' WHERE id = 4;
UPDATE mtb_customer_order_status SET name = 'Shipped' WHERE id = 5;
UPDATE mtb_customer_order_status SET name = 'Payment Confirmed' WHERE id = 6;
UPDATE mtb_customer_order_status SET name = 'Payment Processing' WHERE id = 7;
UPDATE mtb_customer_order_status SET name = 'Purchase Processing' WHERE id = 8;
UPDATE mtb_customer_order_status SET name = 'Returned' WHERE id = 9;

-- 3. Product Statuses
UPDATE mtb_product_status SET name = 'Published' WHERE id = 1;
UPDATE mtb_product_status SET name = 'Unpublished' WHERE id = 2;
UPDATE mtb_product_status SET name = 'Discontinued' WHERE id = 3;

-- 4. Customer Statuses
UPDATE mtb_customer_status SET name = 'Provisional' WHERE id = 1;
UPDATE mtb_customer_status SET name = 'Active Member' WHERE id = 2;
UPDATE mtb_customer_status SET name = 'Withdrawn' WHERE id = 3;

-- 5. Gender / Sex
UPDATE mtb_sex SET name = 'Male' WHERE id = 1;
UPDATE mtb_sex SET name = 'Female' WHERE id = 2;
UPDATE mtb_sex SET name = 'Other' WHERE id = 3;
UPDATE mtb_sex SET name = 'Prefer not to say' WHERE id = 4;

-- 6. Product Tags
UPDATE dtb_tag SET name = 'New Arrival' WHERE id = 1;
UPDATE dtb_tag SET name = 'Recommended' WHERE id = 2;
UPDATE dtb_tag SET name = 'Limited Edition' WHERE id = 3;

-- 7. Payment Methods
UPDATE dtb_payment SET payment_method = 'Postal Transfer' WHERE id = 1;
UPDATE dtb_payment SET payment_method = 'Registered Mail' WHERE id = 2;
UPDATE dtb_payment SET payment_method = 'Bank Transfer' WHERE id = 3;
UPDATE dtb_payment SET payment_method = 'Cash on Delivery' WHERE id = 4;

-- 8. Authorities
UPDATE mtb_authority SET name = 'System Administrator' WHERE id = 0;
UPDATE mtb_authority SET name = 'Store Owner' WHERE id = 1;

-- 9. Sale Types
UPDATE mtb_sale_type SET name = 'Sale Type A' WHERE id = 1;
UPDATE mtb_sale_type SET name = 'Sale Type B' WHERE id = 2;

-- 10. CSV Types
UPDATE mtb_csv_type SET name = 'Product CSV' WHERE id = 1;
UPDATE mtb_csv_type SET name = 'Customer CSV' WHERE id = 2;
UPDATE mtb_csv_type SET name = 'Order CSV' WHERE id = 3;
UPDATE mtb_csv_type SET name = 'Shipping CSV' WHERE id = 4;
UPDATE mtb_csv_type SET name = 'Category CSV' WHERE id = 5;
UPDATE mtb_csv_type SET name = 'Class CSV' WHERE id = 6;
UPDATE mtb_csv_type SET name = 'Class Category CSV' WHERE id = 7;

-- 11. Order Item Types
UPDATE mtb_order_item_type SET name = 'Product' WHERE id = 1;
UPDATE mtb_order_item_type SET name = 'Delivery Fee' WHERE id = 2;
UPDATE mtb_order_item_type SET name = 'Processing Fee' WHERE id = 3;
UPDATE mtb_order_item_type SET name = 'Discount' WHERE id = 4;
UPDATE mtb_order_item_type SET name = 'Tax' WHERE id = 5;
UPDATE mtb_order_item_type SET name = 'Points' WHERE id = 6;

-- 12. Product Sort Orders
UPDATE mtb_product_list_order_by SET name = 'Price: Low to High' WHERE id = 1;
UPDATE mtb_product_list_order_by SET name = 'Newest Arrivals' WHERE id = 2;
UPDATE mtb_product_list_order_by SET name = 'Price: High to Low' WHERE id = 3;

-- 13. Tax Types
UPDATE mtb_tax_type SET name = 'Taxable' WHERE id = 1;
UPDATE mtb_tax_type SET name = 'Non-Taxable' WHERE id = 2;
UPDATE mtb_tax_type SET name = 'Tax Exempt' WHERE id = 3;

-- 14. Prefectures
UPDATE mtb_pref SET name = 'Hokkaido' WHERE id = 1;
UPDATE mtb_pref SET name = 'Aomori' WHERE id = 2;
UPDATE mtb_pref SET name = 'Iwate' WHERE id = 3;
UPDATE mtb_pref SET name = 'Miyagi' WHERE id = 4;
UPDATE mtb_pref SET name = 'Akita' WHERE id = 5;
UPDATE mtb_pref SET name = 'Yamagata' WHERE id = 6;
UPDATE mtb_pref SET name = 'Fukushima' WHERE id = 7;
UPDATE mtb_pref SET name = 'Ibaraki' WHERE id = 8;
UPDATE mtb_pref SET name = 'Tochigi' WHERE id = 9;
UPDATE mtb_pref SET name = 'Gunma' WHERE id = 10;
UPDATE mtb_pref SET name = 'Saitama' WHERE id = 11;
UPDATE mtb_pref SET name = 'Chiba' WHERE id = 12;
UPDATE mtb_pref SET name = 'Tokyo' WHERE id = 13;
UPDATE mtb_pref SET name = 'Kanagawa' WHERE id = 14;
UPDATE mtb_pref SET name = 'Niigata' WHERE id = 15;
UPDATE mtb_pref SET name = 'Toyama' WHERE id = 16;
UPDATE mtb_pref SET name = 'Ishikawa' WHERE id = 17;
UPDATE mtb_pref SET name = 'Fukui' WHERE id = 18;
UPDATE mtb_pref SET name = 'Yamanashi' WHERE id = 19;
UPDATE mtb_pref SET name = 'Nagano' WHERE id = 20;
UPDATE mtb_pref SET name = 'Gifu' WHERE id = 21;
UPDATE mtb_pref SET name = 'Shizuoka' WHERE id = 22;
UPDATE mtb_pref SET name = 'Aichi' WHERE id = 23;
UPDATE mtb_pref SET name = 'Mie' WHERE id = 24;
UPDATE mtb_pref SET name = 'Shiga' WHERE id = 25;
UPDATE mtb_pref SET name = 'Kyoto' WHERE id = 26;
UPDATE mtb_pref SET name = 'Osaka' WHERE id = 27;
UPDATE mtb_pref SET name = 'Hyogo' WHERE id = 28;
UPDATE mtb_pref SET name = 'Nara' WHERE id = 29;
UPDATE mtb_pref SET name = 'Wakayama' WHERE id = 30;
UPDATE mtb_pref SET name = 'Tottori' WHERE id = 31;
UPDATE mtb_pref SET name = 'Shimane' WHERE id = 32;
UPDATE mtb_pref SET name = 'Okayama' WHERE id = 33;
UPDATE mtb_pref SET name = 'Hiroshima' WHERE id = 34;
UPDATE mtb_pref SET name = 'Yamaguchi' WHERE id = 35;
UPDATE mtb_pref SET name = 'Tokushima' WHERE id = 36;
UPDATE mtb_pref SET name = 'Kagawa' WHERE id = 37;
UPDATE mtb_pref SET name = 'Ehime' WHERE id = 38;
UPDATE mtb_pref SET name = 'Kochi' WHERE id = 39;
UPDATE mtb_pref SET name = 'Fukuoka' WHERE id = 40;
UPDATE mtb_pref SET name = 'Saga' WHERE id = 41;
UPDATE mtb_pref SET name = 'Nagasaki' WHERE id = 42;
UPDATE mtb_pref SET name = 'Kumamoto' WHERE id = 43;
UPDATE mtb_pref SET name = 'Oita' WHERE id = 44;
UPDATE mtb_pref SET name = 'Miyazaki' WHERE id = 45;
UPDATE mtb_pref SET name = 'Kagoshima' WHERE id = 46;
UPDATE mtb_pref SET name = 'Okinawa' WHERE id = 47;
