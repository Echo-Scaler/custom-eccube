# EC-CUBE Navigation Bar UI ပြင်ဆင်ခြင်းဆိုင်ရာ လေ့လာရန် မှတ်တမ်း (Shopcart Style)

ဤစာတမ်းသည် EC-CUBE တွင် Navigation Bar / Header UI ကို **Shopcart Design** အဖြစ် ပြင်ဆင်ရေးသားခဲ့သည့် အဆင့်များနှင့် ဖိုင်များ၏ အလုပ်လုပ်ပုံကို အသေးစိတ် ရှင်းပြထားသော မြန်မာဘာသာ လမ်းညွှန်ဖြစ်ပါသည်။

---

## ၁။ ပြင်ဆင်/ဖန်တီးခဲ့သော ဖိုင်များ စာရင်း (Updated & Created File Paths)

### က။ Template ဖိုင်များ (Twig Templates)
1. **`src/Eccube/Resource/template/default/Block/header.twig`**
   - **အဓိက အခန်းကဏ္ဍ**: Top Bar (အစိမ်းရောင် အပေါ်ဘား) နှင့် Main Navigation Bar (အဖြူရောင် အောက်ဘား) တစ်ခုလုံးကို တာဝန်ယူ တည်ဆောက်ထားသော ဖိုင်ဖြစ်ပါသည်။
2. **`src/Eccube/Resource/template/default/Block/logo.twig`**
   - **အဓိက အခန်းကဏ္ဍ**: မူလ Logo block ကို header.twig အတွင်းသို့ ပေါင်းထည့်လိုက်သည့်အတွက် redundant (ထပ်နေသော) အရာများကို ရှင်းလင်းထားသည့် ဖိုင်ဖြစ်ပါသည်။
3. **`src/Eccube/Resource/template/default/Block/category_nav_pc.twig`**
   - **အဓိက အခန်းကဏ္ဍ**: မူလ Category menu bar ကို header.twig ၏ Categories Dropdown အတွင်းသို့ ပေါင်းထည့်ထားသဖြင့် သီးခြား bar မပေါ်စေရန် ရှင်းလင်းထားသည့် ဖိုင်ဖြစ်ပါသည်။
4. **`src/Eccube/Resource/template/default/default_frame.twig`**
   - **အဓိက အခန်းကဏ္ဍ**: HTML `<head>` အတွင်း Google Fonts (`Plus Jakarta Sans`) နှင့် `nav_shopcart.css`၊ `nav_shopcart.js` တို့ကို ချိတ်ဆက်ပေးသော Main Layout Frame ဖိုင်ဖြစ်ပါသည်။

### ခ။ Styling နှင့် Javascript ဖိုင်များ (CSS & JS Assets)
1. **`html/template/default/assets/css/nav_shopcart.css`**
   - **အဓိက အခန်းကဏ္ဍ**: Top Bar၊ Logo၊ Pill Search Input၊ Categories Dropdown၊ Cart Badge၊ Account Menu နှင့် Mobile Drawer များအတွက် လိုအပ်သော Modern CSS Styling အားလုံးကို ရေးသားထားပါသည်။
2. **`html/template/default/assets/js/nav_shopcart.js`**
   - **အဓိက အခန်းကဏ္ဍ**: Mobile Hamburger Drawer ဖွင့်/ပိတ်ခြင်း၊ Location ရွေးချယ်မှုအား `localStorage` တွင် သိမ်းဆည်းခြင်း၊ Dropdown Click Toggle များကို ထိန်းချုပ်ထားသော Javascript ဖိုင်ဖြစ်ပါသည်။
3. **`html/user_data/assets/css/customize.css`** & **`html/user_data/assets/js/customize.js`**
   - **အဓိက အခန်းကဏ္ဍ**: EC-CUBE ရဲ့ User-level customization အတွက် အရန်ထားရှိသော CSS/JS ဖိုင်များ ဖြစ်ပါသည်။

---

## ၂။ EC-CUBE Header အလုပ်လုပ်ပုံ သဘောတရား (Architecture)

EC-CUBE တွင် Page Layout များကို Section အလိုက် ခွဲထားပြီး Database (`dtb_block_position`) တွင် သတ်မှတ်ထားပါသည်-
- `section = 3` သည် **Header Section** ဖြစ်သည်။
- Header Section တွင် ပုံမှန်အားဖြင့် `header`, `logo`, `category_nav_pc` စသည့် Block သုံးခု ပါဝင်ပါသည်။
- ကျွန်ုပ်တို့၏ ဒီဇိုင်းအသစ်တွင် `Block/header.twig` တစ်ခုတည်း၌ Top Bar + Logo + Menu + Search + Account + Cart အားလုံးကို စုစည်းပြီး ခေတ်မီသော Component အဖြစ် ပြောင်းလဲတည်ဆောက်ခဲ့ပါသည်။

---

## ၃။ အစိတ်အပိုင်းတစ်ခုချင်းစီ၏ တည်ဆောက်ပုံ (Component Breakdown)

### (၁) Top Bar (အစိမ်းရောင် အပေါ်ဘား - `#003d29`)
- **ဖုန်းနံပါတ် (Phone Contact)**:
  - `BaseInfo.phone_number` ကို စစ်ဆေးပြီး ရှိပါက ထုတ်ပြသည် (မရှိပါက fallback အနေဖြင့် `+001234567890` ပြသည်)။
  - `<a href="tel:...">` ဖြင့် တိုက်ရိုက် ဖုန်းခေါ်ဆိုနိုင်ပါသည်။
- **ပရိုမိုးရှင်း စာသား (Promo Announcement)**:
  - `Get 50% Off on Selected Items | Shop Now` စာသားပါရှိပြီး `Shop Now` သည် `/products/list` သို့ ချိတ်ဆက်ထားပါသည်။
- **Language Switcher (ဘာသာစကား ရွေးချယ်မှု)**:
  - `app.request.locale` အပေါ် မူတည်၍ လက်ရှိဘာသာစကား (Eng, 日本語, မြန်မာ) ကို ပြသပေးသည်။
  - ရွေးချယ်လိုက်ပါက URL တွင် `?_locale=en`, `?_locale=ja`, `?_locale=my` parameter ဖြင့် ဘာသာစကား ချက်ချင်း ပြောင်းလဲစေပါသည်။
- **Location Selector**:
  - နိုင်ငံ/ဒေသ ရွေးချယ်နိုင်သော Dropdown ဖြစ်ပြီး ရွေးချယ်မှုကို `localStorage` တွင် မှတ်ထားပေးပါသည်။

### (၂) Main Navigation Bar (အဖြူရောင် အောက်ဘား)
- **Shopcart Brand Logo**:
  - Shopping Cart နှင့် အသီးအရွက်/သစ်ရွက် အစိမ်းရောင် ပေါင်းစပ်ထားသော SVG Vector Icon ကို ရေးဆွဲထည့်သွင်းထားပါသည်။
  - Font ကို `font-weight: 800` ဖြင့် Modern Sans Typeface အဖြစ် ဖန်တီးထားပါသည်။
- **Categories Dropdown (အမျိုးအစား ခွဲခြားမှုများ)**:
  - `{% set Categories = repository('Eccube\\Entity\\Category').getList() %}` ကို အသုံးပြု၍ Database ထဲရှိ Category များနှင့် ၎င်းတို့၏ Sub-categories များကို Dynamic ထုတ်ပြထားပါသည်။
- **Navigation Links**:
  - `Deals` &rarr; Promotion/Discount ပစ္စည်းများဆီသို့
  - `What’s New` &rarr; အသစ်ရောက်ပစ္စည်းများ (`/products/list?orderby=1`)
  - `Delivery` &rarr; ပို့ဆောင်မှုဆိုင်ရာ လမ်းညွှန် (`/help/guide`)
- **Pill-shaped Search Bar (ရှာဖွေရန် အကွက်)**:
  - Rounded Pill Shape (`border-radius: 9999px`) ဖြင့် အလင်းရောင် အောက်ခံ (`#f3f5f8`) အသုံးပြုထားပါသည်။
  - Form action ကို `{{ path('product_list') }}` သို့ ချိတ်ထားပြီး `name="name"` ဖြင့် ပစ္စည်းအမည်များကို တိုက်ရိုက် ရှာဖွေနိုင်ပါသည်။
- **Account Dropdown (အကောင့် မီနူး)**:
  - `{% if is_granted('ROLE_USER') %}` ဖြင့် Login ဝင်ထား/မထား စစ်ဆေးသည်-
    - **Login မဝင်ရသေးပါက (Guest)**: `Sign In` ခလုတ်၊ `Create account` လင့်ခ်များ ပြသသည်။
    - **Login ဝင်ထားပါက (Member)**: အသင်းဝင် နာမည်၊ `Order History` (`url('mypage')`)၊ `Edit Profile` (`url('mypage_change')`)၊ `Favorites` (`url('mypage_favorite')`)၊ `Logout` (`url('logout')`) တို့ကို ပြသသည်။
- **Cart Dropdown (စျေးဝယ်ခြင်း မီနူး)**:
  - `get_all_carts()`, `get_carts_total_price()`, `get_carts_total_quantity()` function များဖြင့် ခြင်းတောင်းထဲရှိ ပစ္စည်းအရေအတွက်ကို Cart Badge အနေဖြင့် ပြသသည်။
  - Hover/Click လုပ်ပါက ခြင်းတောင်းထဲရှိ ပစ္စည်းများ၏ ပုံ၊ အမည်၊ ဈေးနှုန်း၊ အရေအတွက်နှင့် `Proceed to Cart` ခလုတ်ကို Mini-Cart Preview အနေဖြင့် ဖော်ပြပေးပါသည်။

### (၃) Mobile Drawer Menu (ဖုန်း/တက်ဘလက် screen အတွက်)
- Screen အရွယ်အစား သေးငယ်သွားပါက Navigation links များကို ဝှက်ထားပြီး Hamburger icon (`fas fa-bars`) အဖြစ် ပြောင်းလဲပေးသည်။
- Hamburger ကို နှိပ်ပါက ဘယ်ဘက်မှ Slide ထွက်လာသော Drawer Menu ပွင့်လာပြီး Categories များကို Accordion ပုံစံဖြင့် ဖွင့်/ပိတ် ကြည့်ရှုနိုင်ပါသည်။

### (၄) Hero Carousel / Banner Section (အလယ်ရှိ Slide Banner)
- **ဖိုင်တည်နေရာ**: `src/Eccube/Resource/template/default/index.twig`
- **ဒီဇိုင်းနှင့် အရောင်**: Soft warm beige/cream background (`#faebe0`)၊ Rounded corners (`border-radius: 20px`)။
- **ပါဝင်သော အကြောင်းအရာများ**:
  - **Slide 1**: Headphone Promotion (`Grab Upto 50% Off On Selected Headphone` + `Buy Now` Button) နှင့် Headphone တပ်ထားသော Model ပုံ။
  - **Slide 2**: Smartwatches & Audio Gear Deal (`Next-Gen Smartwatches & Audio Gear` + `Explore Deals` Button)။
  - **Slide 3**: Seasonal Collections (`Discover Trending Items & Daily Best Deals` + `Shop Collection` Button)။
- **Slider အလုပ်လုပ်ပုံ**: EC-CUBE ရှိ `Slick Slider` ကို အသုံးပြု၍ Autoplay, Smooth Fade Transitions နှင့် Custom Modern Dots Indicator များဖြင့် လှပစွာ ပြသထားပါသည်။

### (၅) Popular Categories Section (ခေတ်စားနေသော ကဏ္ဍများ စာရင်း)
- **ဖိုင်တည်နေရာ**: `src/Eccube/Resource/template/default/index.twig`
- **Section Heading**: `Popular Categories`
- **Card Grid (2-Column Responsive Grid)**:
  1. **Furniture** - `cat_furniture.jpg` (240 Item Available)
  2. **Headphone** - `cat_headphone.jpg` (240 Item Available)
  3. **Shoe** - `cat_shoe.jpg` (240 Item Available)
  4. **Bag** - `cat_bag.jpg` (240 Item Available)
  5. **Laptop** - `cat_laptop.jpg` (240 Item Available)
  6. **Book** - `cat_book.jpg` (240 Item Available)
- **ဒီဇိုင်းနှင့် Micro-interactions**:
  - ကတ်တစ်ခုချင်းစီသည် Rounded Pill Shape (`#f1f3f6`) ဖြစ်ပြီး အဖြူရောင် Thumbnail Box အတွင်း ပုံရိပ်များကို သပ်ရပ်စွာ ထည့်သွင်းထားပါသည်။
  - Hover ပြုလုပ်ပါက Smooth Lift-up Animation, Soft Glow Shadow နှင့် Image Zoom Effect တို့ ပါဝင်ပါသည်။

### (၆) Headphones For You Section (Filter Pills & Product Grid)
- **ဖိုင်တည်နေရာ**: `src/Eccube/Resource/template/default/index.twig`
- **Filter Pills Bar**:
  - `Headphone Type ∨`, `Price ∨`, `Review ∨`, `Color ∨`, `Material ∨`, `Offer ∨`, `All Filters 🎛️` နှင့် ညာဘက်ရှိ `Sort by ∨` စသည့် Pill-shaped dropdown ခလုတ်များ ပါဝင်ပါသည်။
- **Section Heading**:
  - `Headphones For You!` ခေါင်းစဉ်။
- **Product Card Grid (4-Column Grid)**:
  - ကတ်တစ်ခုချင်းစီတွင် Wishlist Heart Toggle ခလုတ် (`<button class="shopcart-wishlist-btn">`)၊ Product Image၊ အမည်၊ ဈေးနှုန်း (`$89.00`, `$559.00`, စသဖြင့်)၊ Subtitle ဖော်ပြချက်၊ Green Star Ratings (`★★★★★ (121)`) နှင့် `Add to Cart` Pill Button များ ပါဝင်ပါသည်။

### (၇) Similar Items You Might Like Section (သင်နှစ်သက်နိုင်သော အလားတူပစ္စည်းများ)
- **ဖိုင်တည်နေရာ**: `src/Eccube/Resource/template/default/index.twig`
- **Section Heading**: `Similar Items You Might Like`
- **Product Card Grid (4-Column Grid)**:
  1. **Gaming Headphone** (`$239.00`) - Neon green & black gaming headset
  2. **Macbook pro 13"** (`$1099.00`) - 256, 8 core GPU, 8 GB
  3. **HomePod mini** (`$59.00`) - 5 Colors Available
  4. **Laptop sleeve MacBook** (`$59.00`) - Organic Cotton, fairtrade certified
- **Scroll Track Indicator**: ကဏ္ဍအောက်ခြေတွင် ခေတ်မီသော Indicator Scroll Track Bar ပါဝင်ပါသည်။

### (၈) Recently Viewed Section (လတ်တလော ကြည့်ရှုခဲ့သော ပစ္စည်းများ)
- **ဖိုင်တည်နေရာ**: `src/Eccube/Resource/template/default/index.twig`
- **Section Heading**: `Recently Viewed`
- **Product Card Grid (4-Column Grid)**:
  1. **Laptop sleeve MacBook** (`$59.00`)
  2. **AirPods Max** (`$559.00`) - Solid green Add to Cart button
  3. **Ipad Mini** (`$569.00`)
  4. **Flower Laptop Sleeve** (`$39.00`)

---

## ၄။ သိထားသင့်သည့် အရေးကြီး နည်းပညာ အချက်များ (Key Gotchas & Tips)

1. **Route Parameter သတိပြုရန် (`mypage` vs `mypage_history`)**:
   - EC-CUBE တွင် `mypage` route သည် အသုံးပြုသူ၏ ယခင်မှာယူခဲ့သော Order စာရင်း အားလုံးကို ဖော်ပြသော Index Page ဖြစ်ပြီး Parameter မလိုပါ။
   - `mypage_history` route သည် သီးခြား Order တစ်ခု၏ အသေးစိတ် (Detail) ကို ကြည့်ခြင်းဖြစ်၍ `{order_no}` parameter မဖြစ်မနေ လိုအပ်ပါသည်။ ထို့ကြောင့် Navigation link များတွင် `url('mypage')` ကို သုံးရပါသည်။

2. **Cache ရှင်းလင်းခြင်း (Cache Clear)**:
   - Template သို့မဟုတ် Config များ ပြင်ဆင်ပြီးတိုင်း Docker container အတွင်း Symfony Cache ကို အောက်ပါ command ဖြင့် ရှင်းလင်းပေးရပါသည်-
     ```bash
     docker compose exec ec-cube bin/console cache:clear
     ```

3. **EC-CUBE Default Blocks များကို ရှင်းလင်းခြင်း**:
   - မူလ EC-CUBE Default Sample Block များဖြစ်သော `eyecatch.twig` (CUBE Gelato Ice Feature), `new_item.twig` (FEATURED) နှင့် `topic.twig` တို့ကို Shopcart Custom Theme သန့်ရှင်းစေရန် `dtb_block_position` (Layout 1) နှင့် Block Templates များမှ ဖယ်ရှားရှင်းလင်းထားပါသည်။

---

## ၅။ Product Filter Pills & Dropdown JS အလုပ်လုပ်ပုံ (Interactive Features)

"Headphones For You!" အပိုင်းရှိ Filter Pills များအတွက် Interactive Dropdown Menu များနှင့် Live Product Filtering/Sorting စနစ်ကို [nav_shopcart.js](file:///Users/kyawwaiyan/Downloads/ec-cube-main/html/template/default/assets/js/nav_shopcart.js) တွင် အောက်ပါအတိုင်း အသေးစိတ် ထည့်သွင်းပေးထားပါသည်-

1. **Dropdown Toggle & Outside Click**:
   - `.shopcart-filter-toggle` ခလုတ်ကို နှိပ်ပါက သက်ဆိုင်ရာ `.shopcart-filter-dropdown` တွင် `.is-open` class ကို toggle လုပ်ပေးပါသည်။
   - အခြား Dropdown တစ်ခုခု ဖွင့်ထားပါက အလိုအလျောက် ပိတ်သွားပြီး လက်ရှိနှိပ်လိုက်သော တစ်ခုတည်းသာ ပွင့်စေပါသည်။
   - Dropdown ပြင်ပ (Outside) ကို နှိပ်လျှင်သော်လည်းကောင်း၊ ကီးဘုတ်မှ `Escape (ESC)` ခလုတ်ကို နှိပ်လျှင်သော်လည်းကောင်း ပွင့်နေသော Dropdown အားလုံး အလိုအလျောက် ပိတ်သွားစေပါသည်။

2. **Real-time Product Filtering**:
   - **Headphone Type** (Earbuds, Over-Ear, On-Ear, Bone Conduction)
   - **Price** (Under $50, $50-$100, $100-$300, Over $300)
   - **Review** (4.8 & up, 4.5 & up, 4.0 & up)
   - **Color** (Black, Pink, Red, Blue)
   - **Material** (Leather, Metal, Plastic)
   - **Offer** (50% Off, 30% Off, Free Delivery)
   - ရွေးချယ်လိုက်သော Filter အလိုက် Filter Pill ပေါ်ရှိ Text ပြောင်းလဲသွားပြီး Pill သည် Active Green Highlight ဖြစ်သွားပါသည်။ Product Grid ပေါ်ရှိ ပစ္စည်းများသည် Reload လုပ်စရာမလိုဘဲ တိုက်ရိုက် Filter လုပ်ပြပေးပါသည်။

3. **Sorting & Reset All Filters**:
   - **Sort by Dropdown**: Featured, Price: Low to High, Price: High to Low, Customer Rating, Newest Arrivals အလိုက် ချက်ချင်း အစဉ်လိုက် စီပေးပါသည်။
   - **All Filters Button**: နှိပ်လိုက်ပါက Filter အားလုံးကို မူလအခြေအနေ (Default) သို့ ပြန်လည် Reset လုပ်ပေးပြီး ပစ္စည်းအားလုံးကို ပြန်ဖော်ပြပေးပါသည်။

4. **Wishlist & Add to Cart Feedback**:
   - Wishlist အသဲပုံလေးကို နှိပ်ပါက အနီရောင် solid heart သို့ toggle ဖြစ်သွားပါသည်။
   - "Add to Cart" ခလုတ်ကို နှိပ်ပါက "✓ Added!" ဟူသော micro-interaction animation ပေါ်လာပါသည်။

---

## ၆။ Categories Multi-Language ဘာသာစကား ပြောင်းလဲခြင်း စနစ် (Category Localization)

EC-CUBE ၏ မူလ Database (`dtb_category`) တွင် သိမ်းဆည်းထားသော Category Name များကို အသုံးပြုသူ ရွေးချယ်ထားသော ဘာသာစကား (English / 日本語 / မြန်မာ) အလိုက် အလိုအလျောက် ဘာသာပြန်ပြသပေးသော စနစ်ကို အောက်ပါအတိုင်း ဖွဲ့စည်းထားပါသည် -

1. **Database (`dtb_category`) Update**:
   - မူလ Default Language (English) အတွက် Category အမည်များကို English အဖြစ် သိမ်းဆည်းထားပါသည် (ဥပမာ - `New Arrivals`, `Gelato`, `Color Desserts`, `CUBE`, `Ice Cream Sandwiches`, `Fruits`)။

2. **Multi-Language Mapping ([header.twig](file:///Users/kyawwaiyan/Downloads/ec-cube-main/src/Eccube/Resource/template/default/Block/header.twig))**:
   - `current_loc = app.request.get('_locale')|default(app.request.locale)` ဖြင့် လက်ရှိ Locale ကို ဖတ်ယူပါသည်။
   - `category_en_map`၊ `category_ja_map` နှင့် `category_my_map` တို့ဖြင့် အောက်ပါအတိုင်း Dynamic Translate လုပ်ပေးပါသည်-

| Category ID | English (en) | 日本語 (ja) | မြန်မာ (my) |
| :--- | :--- | :--- | :--- |
| **ID: 2** | New Arrivals | 新入荷 | ပစ္စည်းအသစ်များ |
| **ID: 1** | Gelato | ジェラート | ဂျယ်လာတို |
| **ID: 3** | Color Desserts | 彩のデザート | အချိုပွဲစုံ |
| **ID: 4** | CUBE | CUBE | CUBE |
| **ID: 5** | Ice Cream Sandwiches | アイスサンド | အအေးမုန့်ညှပ် |
| **ID: 6** | Fruits | フルーツ | သစ်သီးများ |

3. **Breadcrumbs Topicpath ([list.twig](file:///Users/kyawwaiyan/Downloads/ec-cube-main/src/Eccube/Resource/template/default/Product/list.twig))**:
   - Product list စာမျက်နှာရှိ လမ်းကြောင်းပြ Breadcrumbs (Topicpath) တွင်လည်း Category အမည်များကို ဘာသာစကားအလိုက် ချိတ်ဆက် ဘာသာပြန်ပေးထားပါသည်။

---

## ၇။ Redesigned Shopcart Footer (ခေတ်မီဆန်းသစ်သော အောက်ခြေ Footer ပိုင်း)

မူလ EC-CUBE ၏ အမည်းရောင် ရိုးရိုး Footer ကို ဖယ်ရှားပြီး Premium E-commerce စတိုင်လ် အပြည့်အဝ ပါဝင်သော **Shopcart Footer** အဖြစ် အောက်ပါအတိုင်း ပြင်ဆင်ဖွဲ့စည်းခဲ့ပါသည်-

- **ဖိုင်တည်နေရာများ**:
  - Template: `src/Eccube/Resource/template/default/Block/footer.twig`
  - Styling: `html/template/default/assets/css/nav_shopcart.css` & `html/user_data/assets/css/customize.css`

### ပါဝင်သော အဓိက အပိုင်း (၄) ပိုင်း (Footer Architecture)

1. **Feature Guarantees Strip (ဝန်ဆောင်မှု အာမခံချက် ၄ မျိုး)**:
   - **Free Delivery**: For all orders over $50 (`fas fa-truck-fast`)
   - **Safe Payment**: 100% secure payment (`fas fa-shield-halved`)
   - **24/7 Support**: Dedicated assistance (`fas fa-headset`)
   - **Easy Returns**: 30-day money back guarantee (`fas fa-rotate-left`)
   - Smooth hover lift effect နှင့် subtle glow animation များ ပါဝင်ပါသည်။

2. **Newsletter Subscription Banner (သတင်းလွှာ စာရင်းသွင်းရန် အပိုင်း)**:
   - Gradient green background (`#003d29` &rarr; `#064e3b`) ဖြင့် အလင်းရောင် စာတန်းနှင့် Paper plane icon ပါဝင်သည်။
   - Pill-shaped subscription input box နှင့် Orange gradient "Subscribe" button ပါဝင်ပါသည်။

3. **4-Column Main Links Area (အဓိက ချိတ်ဆက်မှု ကော်လံ ၄ ခု)**:
   - **Column 1: Brand Info & Contacts**:
     - Shopcart SVG Logo နှင့် Brand Bio
     - Contact နံပါတ် (`+001 234 567 890`)၊ Support Email (`support@shopcart.com`)၊ လိပ်စာ (`Tokyo, Japan`)
     - Social Icons (Facebook, Twitter, Instagram, YouTube, LinkedIn)
   - **Column 2: Shop Categories**:
     - Headphones & Audio, Wireless Earbuds, Laptops, Smartwatches, Bags & Sleeves, New Arrivals သို့ အမြန်သွားနိုင်သော လင့်ခ်များ
   - **Column 3: Customer Service & Legal**:
     - About Us (`url('help_about')`), Shipping & Delivery (`url('help_guide')`), Privacy Policy (`url('help_privacy')`), Terms (`url('help_agreement')`), Commercial Transactions (`url('help_tradelaw')`), Contact Us (`url('contact')`)
   - **Column 4: My Account & Accepted Payments**:
     - Order History (`url('mypage')`), Sign In/Register (`url('mypage_login')`), Cart (`url('cart')`), Settings (`url('mypage_change')`)
     - Accepted Payment Badges (Visa, Mastercard, Amex, PayPal, Apple Pay)

4. **Footer Copyright Bottom Bar (အောက်ခြေ မူပိုင်ခွင့် ဘား)**:
   - Deep charcoal black (`#03100b`) အောက်ခံဖြင့် `© 2026 EC-CUBE SHOP. All rights reserved.` စာသားနှင့် Legal links (Privacy Policy, Terms of Service, Help Guide) များ သန့်ရှင်းသပ်ရပ်စွာ ပါဝင်ပါသည်။

---

## ၈။ အနှစ်ချုပ် (Summary)

ဤ Storefront (Header, Hero Slider, Popular Categories, Filterable Product Section, Similar Items, Recently Viewed နှင့် Redesigned Footer) အားလုံးသည် EC-CUBE ၏ မူလ Session၊ Authentication၊ Shopping Cart နှင့် Multi-Language System များကို ထိခိုက်မှုမရှိစေဘဲ ခေတ်မီပြီး Premium ဆန်သော E-commerce User Experience (UX) ကို ရရှိစေရန် ရေးဆွဲထားခြင်း ဖြစ်ပါသည်။
