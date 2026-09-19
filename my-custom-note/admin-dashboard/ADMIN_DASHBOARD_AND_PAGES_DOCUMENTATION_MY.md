# EC-CUBE Admin Dashboard & Admin Pages UI ပြင်ဆင်မှုနှင့် Default English သတ်မှတ်ခြင်း အဆင့်ဆင့် နည်းပညာဆိုင်ရာ အသေးစိတ်မှတ်တမ်း (Documentation)

ဤမှတ်တမ်းသည် **EC-CUBE 4.x** စနစ်၏ စီမံခန့်ခွဲမှုစနစ် တစ်ခုလုံး (**Admin Dashboard & Admin Management Pages**) အား ခေတ်မီဆန်းသစ်ပြီး Premium ဖြစ်သော Design System သို့ ပြောင်းလဲပြင်ဆင်ခြင်း၊ မူလဘာသာစကား (**Default Language**) အား ဂျပန်ဘာသာမှ အင်္ဂလိပ်ဘာသာ (**English**) သို့ ပြောင်းလဲသတ်မှတ်ခြင်းနှင့် ဖုန်း/တက်ဘလက်များအတွက် **Responsive Layout** တည်ဆောက်ခဲ့သည့် လုပ်ငန်းစဉ်များကို အဆင့်ဆင့် အသေးစိတ် မှတ်တမ်းတင်ထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ စီမံကိန်း အကျဉ်းချုပ် (Project Overview)

- **ရည်ရွယ်ချက်များ** -
  1. **Default Language to English**: Admin Panel တစ်ခုလုံး (ခေါင်းစဉ်များ၊ မီနူးများ၊ ခလုတ်များ၊ ဇယားများ) ၏ မူလဘာသာစကားကို အင်္ဂလိပ်စာ (English) အဖြစ် အလိုအလျောက် ပြောင်းလဲသတ်မှတ်ရန်။
  2. **Dashboard UI Redesign**: `http://localhost:8080/admin/` ပင်မစာမျက်နှာကို ခေတ်မီ KPI Cards, Revenue Metrics, Welcome Banner, Sales Analytics Graphs, Status Cards များဖြင့် ပြင်ဆင်ရန်။
  3. **Consistent Design Across All Admin Pages**: Dashboard ဒီဇိုင်းစနစ်အတိုင်း ကုန်ပစ္စည်းစီမံခန့်ခွဲမှု (Products), အမှာစာစီမံခန့်ခွဲမှု (Orders), သုံးစွဲသူစီမံခန့်ခွဲမှု (Customers), စနစ်ဆိုင်ရာဆက်တင်များ (Settings), Two-Factor Authentication (2FA) စာမျက်နှာအားလုံးကို ဆက်စပ်ညီညွတ်သော ဒီဇိုင်းအဖြစ် ပြင်ဆင်ရန်။
  4. **Full Responsiveness**: Desktop, Tablet, Landscape Mobile နှင့် Smartphone မျက်နှာပြင်အားလုံးတွင် ချောမွေ့စွာ အသုံးပြုနိုင်စေရန်။

- **အဓိကနည်းပညာများ** -
  - **Backend / Engine**: PHP 8.1 / Symfony 6.4 / Twig Template Engine (EC-CUBE 4.x)
  - **Styling**: Modern CSS Design System Tokens (`html/template/admin/assets/css/admin_dashboard.css`)
  - **Typography**: Google Fonts (*Inter*)
  - **Icons**: FontAwesome 6
  - **Container & Database**: Docker / Apache / MySQL 8.0

---

## ၂။ မူလဘာသာစကားကို အင်္ဂလိပ်စာ (Default Language to English) သတ်မှတ်ခြင်း

EC-CUBE တွင် ဘာသာစကား ဘာသာပြန်စနစ် (Symfony Translation Framework) ကို အသုံးပြုထားပြီး၊ မူလ Locale မှာ ဂျပန်စာ (`ja`) ဖြစ်နေပါသည်။ ၎င်းကို အင်္ဂလိပ်စာ (`en`) သို့ ပြောင်းလဲရန် အောက်ပါ အဆင့် ၃ ဆင့်ကို လုပ်ဆောင်ခဲ့ပါသည်-

### အဆင့် (၁) - Symfony Services Configuration ပြင်ဆင်ခြင်း
ဖိုင်လမ်းကြောင်း: `app/config/eccube/services.yaml`
- Symfony ၏ Default Parameter ဖြစ်သော `env(ECCUBE_LOCALE)` ကို `ja` မှ `en` သို့ ပြောင်းလဲခဲ့ပါသည်။
```yaml
parameters:
    # ec-cube parameters
    env(ECCUBE_LOCALE): 'en'
    env(ECCUBE_TIMEZONE): 'Asia/Tokyo'
    env(ECCUBE_CURRENCY): 'JPY'
    locale: '%env(ECCUBE_LOCALE)%'
```

### အဆင့် (၂) - Docker Environment Variables သတ်မှတ်ခြင်း
ဖိုင်လမ်းကြောင်းများ: `.env` နှင့် `docker-compose.override.yml`
- `.env` တွင်:
```ini
ECCUBE_LOCALE=en
```
- `docker-compose.override.yml` တွင် ကွန်တိန်နာသို့ `ECCUBE_LOCALE: "en"` ကို တိုက်ရိုက် သတ်မှတ်ပေးခဲ့ပါသည်။

### အဆင့် (၃) - Docker Container အတွင်း Cache ရှင်းလင်းခြင်း
Docker ကွန်တိန်နာအတွင်းရှိ Symfony Dev Cache ကို ရှင်းလင်းပြီး ဘာသာစကားအသစ်ကို ချက်ချင်း အသက်ဝင်စေခဲ့ပါသည်-
```bash
docker compose exec ec-cube php bin/console cache:clear
```

**ရရှိလာသော ရလဒ်များ -**
- စာမျက်နှာ Title များ: `Home - Offmarket`, `Products All Products - Offmarket`, `Orders All Orders - Offmarket`
- ဘေးဘက် မီနူးများ: `Overview`, `Management`, `Settings`, `Information`
- ခလုတ်များနှင့် စာသားများ: `Search`, `Sign out`, `Change Password`, `Registration`, `Bulk Actions` အစရှိသည်တို့ အားလုံး အလိုအလျောက် English ဖြစ်သွားပါသည်။

---

## ၃။ စနစ်ဒေတာ မပြောင်းလဲဘဲ UI ဒီဇိုင်းသစ် ပြင်ဆင်ခြင်း (UI Redesign with 100% Real System Data)

စနစ်အတွင်းရှိ အမှန်တကယ် စတိုးဆိုင်ဒေတာများ (Real Products, Orders, Customers, Sales, Order Statuses, Real Logged-in User) ကို မူရင်းအတိုင်း အပြည့်အဝ ထိန်းသိမ်းထားရှိပြီး၊ **UI Visual Design နှင့် Layout** ကိုသာ ခေတ်မီဆန်းသစ်သော Pulse Clinic Style အသွင်သို့ အောက်ပါအတိုင်း ပြင်ဆင်ခဲ့ပါသည်-

```
+-----------------------------------------------------------------------------------------------+
| Top Bar: Toggle | [Shop Name] > [Page Title] | Quick Find (⌘K) | Storefront Link | Admin User |
+-----------------------------------------------------------------------------------------------+
| Hero Header: Dashboard | Welcome back, [User Name] • [Shop Name] | "Manage Orders" & "Add Product"
+-----------------------------------------------------------------------------------------------+
| 4-Column KPI Cards (Real EC-CUBE Metrics):                                                    |
| [ TOTAL PRODUCTS ]     [ OUT OF STOCK ]        [ CUSTOMERS ]          [ TODAY'S ORDERS ]      |
| countProducts          countNonStockProducts   countCustomers         salesToday (Amount)     |
+-----------------------------------------------------------------------------------------------+
| 3-Column Sales Performance Metrics:                                                           |
| [ THIS MONTH Sales ]             [ TODAY Sales ]                [ YESTERDAY Sales ]           |
| salesThisMonth (Amount & Orders) salesToday (Amount & Orders)   salesYesterday (Amount/Orders)|
+-----------------------------------------------------------------------------------------------+
| Main Split Layout:                                                                            |
| [ Left (7 Cols): Real Sales Chart ]                  | [ Right (5 Cols): Order Status Queue ] |
| - Weekly, Monthly, Yearly Interactive Pill Tabs      | - All Real OrderStatuses List          |
| - Chart.js Peak Highlighting Bar Chart               | - Status Pills & Real Order Counts     |
+-----------------------------------------------------------------------------------------------+
| Bottom 2 Columns:                                                                             |
| [ Left: Recommended Plugins (With Modals) ]          | [ Right: Store News (EC-CUBE Info) ]   |
+-----------------------------------------------------------------------------------------------+
```

1. **Top Header Bar (`default_frame.twig`)**:
   - Sidebar Toggle ခလုတ်။
   - စတိုးဆိုင်အမည်နှင့် စာမျက်နှာခေါင်းစဉ် Breadcrumb လမ်းကြောင်း: `{{ BaseInfo.shop_name }} > {{ Page Title }}`။
   - စတိုးဆိုင်ရှေ့စာမျက်နှာသို့ သွားရောက်ကြည့်ရှုနိုင်သည့် `Storefront` Quick Link ခလုတ်။
   - Quick Search Pill (`Quick find... ⌘K`)။
   - လက်ရှိ အသုံးပြုနေသော Admin အကောင့်အမည် (`{{ app.user.name }}`), အတိုကောက် Avatar Badge နှင့် Last Login အချိန်၊ စကားဝှက်ပြောင်းလဲရန်နှင့် Logout ပြုလုပ်ရန် Popover စနစ်။

2. **Sidebar Navigation (`nav.twig`)**:
   - စတိုးဆိုင်အမည် (`{{ BaseInfo.shop_name }}`) နှင့် Pulse Icon၊ System Online အစိမ်းရောင် Live Dot။
   - **OVERVIEW**: ပင်မစာမျက်နှာ Home (`admin_homepage`)။
   - **MANAGEMENT / SETTINGS / STORE & PLUGINS**: EC-CUBE မူရင်း Navigation Menu (`eccubeNav`) တစ်ခုချင်းစီကို Dropdown Accordion စနစ်ဖြင့် အပြည့်အစုံ ချိတ်ဆက်ပေးထားသဖြင့် Plugin Menu များနှင့် Submenu အားလုံး အလွယ်တကူ အသုံးပြုနိုင်ခြင်း။
   - **INFORMATION**: တရားဝင်ဆိုက်၊ စာရွက်စာတမ်းများနှင့် လမ်းညွှန်ချက်များ။
   - အောက်ခြေ Profile Card: လက်ရှိ Login ဝင်ထားသော Admin အကောင့်အမည်၊ အခန်းကဏ္ဍ (Login ID) နှင့် လုပ်ဆောင်ချက်များ။

3. **Dashboard Hero Header**:
   - ပင်မခေါင်းစဉ်နှင့်အတူ "Welcome back, [Admin Name] • [Shop Name] • [Date]" အား Live Indicator စိမ်းရောင်အစက်ဖြင့် ပြသခြင်း။
   - အမှာစာများကို တိုက်ရိုက် စီမံနိုင်သည့် "Manage Orders" ခလုတ်နှင့် ကုန်ပစ္စည်းအသစ် တင်သွင်းသည့် "Add Product" ခလုတ်။

4. **4-Metric Top KPI Cards (EC-CUBE စနစ်ဒေတာအစစ်အမှန်များ)**:
   - **TOTAL PRODUCTS**: လက်ရှိ စတိုးဆိုင်တွင် ရောင်းချနေသော ကုန်ပစ္စည်းအရေအတွက်အစစ် (`countProducts`)။
   - **OUT OF STOCK**: ကုန်ပစ္စည်းလက်ကျန် ပြတ်လပ်နေသော အရေအတွက်အစစ် (`countNonStockProducts`)။
   - **REGISTERED CUSTOMERS**: စနစ်အတွင်း စာရင်းသွင်းထားသော ဖောက်သည်အသင်းဝင်ဦးရေအစစ် (`countCustomers`)။
   - **TODAY'S ORDERS**: ယနေ့အတွက် ရရှိထားသော အမှာစာအရေအတွက်နှင့် ရောင်းရငွေတန်ဖိုးအစစ် (`salesToday`)။

5. **Sales Performance Metrics (၃ ကော်လံ)**:
   - **THIS MONTH**: ယခုလအတွင်း စုစုပေါင်းရောင်းရငွေနှင့် အမှာစာအရေအတွက် (`salesThisMonth`)။
   - **TODAY**: ယနေ့ရောင်းရငွေနှင့် အမှာစာအရေအတွက် (`salesToday`)။
   - **YESTERDAY**: မနေ့ကရောင်းရငွေနှင့် အမှာစာအရေအတွက် (`salesYesterday`)။

6. **Sales Analytics Chart & Real Order Status Queue**:
   - **Sales Chart**: အပတ်စဉ်၊ လစဉ်၊ နှစ်စဉ် စာရင်းများကို AJAX ဖြင့် ချိတ်ဆက်ပြသပေးသော Interactive Bar Chart (အမြင့်ဆုံးရက်စွဲတန်ဖိုးအား Dark Highlight ပြုလုပ်ပေးထားပါသည်)။
   - **Order Status Breakdown**: စနစ်အတွင်းရှိ အမှာစာအခြေအနေအားလုံး (OrderStatuses) နှင့် လက်ရှိကျန်ရှိနေသော အမှာစာအရေအတွက်အစစ်အမှန်များကို Status Pill Badge များဖြင့် စီတန်းပြသပေးထားခြင်း။

7. **Recommended Plugins & News**:
   - စနစ်အတွင်း ထည့်သွင်းအသုံးပြုနိုင်သော Recommended Plugins စာရင်းနှင့် EC-CUBE တရားဝင် သတင်းလွှာ iframe။

---

## ၄။ Admin စာမျက်နှာအားလုံး (All Admin Pages) ဆက်စပ်ဒီဇိုင်း ပြင်ဆင်ခြင်း

Dashboard တစ်ခုတည်းသာမက EC-CUBE စနစ်တစ်ခုလုံးရှိ စီမံခန့်ခွဲမှု စာမျက်နှာတိုင်း (Product, Order, Customer, Setting, Two-Factor Authentication) အတွက် `admin_dashboard.css` တွင် အောက်ပါ Global Style Rules များကို ထည့်သွင်းတည်ဆောက်ခဲ့ပါသည်-

### (က) Global Cards & Containers
- မူလက Homepage တစ်ခုတည်းအတွက်သာ ကန့်သတ်ထားသော `#page_admin_homepage .card` စတိုင်များကို `.card` အဖြစ် ပြောင်းလဲလိုက်သဖြင့် ကုန်ပစ္စည်းနှင့် အမှာစာစာမျက်နှာရှိ Card များအားလုံး ပိုမိုလှပသော Border, Soft Shadow နှင့် Rounded Corner (`var(--radius-md)`) များကို ရရှိသွားပါသည်။

### (ခ) Search Filters & Advanced Accordion (`.c-outsideBlock`, `.c-subContents`)
- ကုန်ပစ္စည်းနှင့် အမှာစာ ရှာဖွေရေး panel များကို မူလ မီးခိုးရောင်နောက်ခံမှ သန့်ရှင်းသော White Surface Card ပုံစံသို့ ပြောင်းလဲခဲ့ပါသည်။
- "Detailed Search (詳細検索 / Search Detail)" ဖွင့်ချလိုက်ပါက ချောမွေ့သော Card အကွက်အဖြစ် သပ်ရပ်စွာ ပေါ်လာစေရန် စီမံထားပါသည်။

### (ဂ) Order Status Filter Chips (`#admin_search_order_status`)
- အမှာစာစာရင်း (Order List) ရှိ အခြေအနေ Checkbox များကို ခေတ်မီ SaaS ပလက်ဖောင်းများ (Shopify/Stripe) ကဲ့သို့ **Interactive Filter Pills (Chip Buttons)** အဖြစ် ပြောင်းလဲခဲ့ပါသည်။
- ကလစ်နှိပ်လိုက်ပါက Active State သို့ အရောင်ပြောင်းသွားပြီး သက်ဆိုင်ရာ အမှာစာအရေအတွက်ကို Badge လေးများဖြင့် သေသပ်စွာ ပြသပေးပါသည်။

### (ဃ) Multi-Column Form Layouts (`.c-contentsArea__cols`)
- ကုန်ပစ္စည်းစာရင်းသွင်းခြင်း (Product Edit) နှင့် အမှာစာပြင်ဆင်ခြင်း (Order Edit) စာမျက်နှာများတွင် မူလ `display: table` အစား ခေတ်မီ `display: flex` စနစ်ဖြင့် ပင်မ Form အကွက်နှင့် ညာဘက် Category Tree, Preview, Shop Memo ကော်လံများကို သပ်ရပ်စွာ ခွဲခြမ်းပေးထားပါသည်။

### (င) Category Directory Tree (`.c-directoryTree`)
- ကုန်ပစ္စည်း ကဏ္ဍခွဲများ ရွေးချယ်သည့် သစ်ပင်ပုံစံ Folder Tree ကို သန့်ရှင်းသော လိုင်းများ၊ Hover Highlight များနှင့် စတိုင်လ်ကျသော Checkbox စနစ်များ ထည့်သွင်းထားပါသည်။

### (စ) Drag & Drop Image Upload Dropzone (`.border-ec-dashed`, `.filepond--root`)
- ပုံတင်သည့်နေရာများကို Dashed Border, Soft Background နှင့် Hover ပြုလုပ်ပါက Highlight ပြပေးသော ခေတ်မီ Dropzone ဒီဇိုင်း ဖန်တီးထားပါသည်။

### (ဆ) Fixed Bottom Action Bar (`.c-conversionArea`)
- စာမျက်နှာ၏ အောက်ခြေတွင် အမြဲကပ်ပါလာသည့် သိမ်းဆည်းရန်/စာရင်းသွင်းရန် Bar ကို **Glassmorphic Effect (Backdrop-filter blur 10px)** ဖြင့် ဖန်တီးထားပြီး မိုဘိုင်းတွင်လည်း ခလုတ်များ ထပ်မသွားဘဲ သပ်ရပ်စွာ အလုပ်လုပ်စေပါသည်။

---

## ၅။ စက်ပစ္စည်းစုံ အသုံးပြုနိုင်မှု (Mobile & Responsive System) တည်ဆောက်ခြင်း

အသုံးပြုသူ စခရင်အရွယ်အစားအလိုက် Breakpoints ၅ ခုကို တိကျစွာ ခွဲခြားရေးဆွဲထားပါသည်-

| Breakpoint | စက်ပစ္စည်းအမျိုးအစား | အဓိက ဒီဇိုင်းပြောင်းလဲပုံ |
|---|---|---|
| **>1280px** | Desktop ပုံမှန် | Full Sidebar (220px), ၄ ကော်လံ KPI, ကော်လံစုံ ဖောင်ပုံစံ |
| **768px – 1279px** | Large Tablet / Laptop အသေး | Narrow Sidebar (180px), ၂ ကော်လံ KPI, Content ချိန်ညှိမှု |
| **≤992px** | Tablet ဒေါင်လိုက် | Multi-column Forms များသည် ဒေါင်လိုက် Stack အဖြစ် အလိုအလျောက်ပြောင်းခြင်း |
| **≤767px** | Landscape Mobile & Smartphone | Sidebar အား ဘေးဘက်သို့ ဝှက်ထားပြီး Hamburger Menu နှိပ်မှ Slide-in Drawer အဖြစ် ထွက်လာခြင်း၊ Curtain Overlay အမည်းရောင်နောက်ခံ၊ ဇယားများ Horizontal Scroll အလိုအလျောက် ပြုလုပ်နိုင်ခြင်း |
| **≤576px / ≤400px** | Smartphone အသေးစားများ | Card များနှင့် ခလုတ်များ Compact Padding သို့ ပြောင်းလဲခြင်း |

---

## ၆။ စမ်းသပ်စစ်ဆေးခြင်းနှင့် အသုံးပြုပုံ (Verification & How to Test)

### ၁။ အင်တာနက် Browser တွင် စစ်ဆေးခြင်း
1. သင့် Browser (Chrome / Safari / Edge) တွင် **`http://localhost:8080/admin/`** သို့ ဝင်ရောက်ပါ။
2. အကယ်၍ Login မဝင်ရသေးပါက `http://localhost:8080/admin/login` သို့ ရောက်ရှိမည်ဖြစ်ပြီး အောက်ပါ အချက်အလက်ဖြင့် ဝင်ရောက်ပါ-
   - **Login ID**: `admin`
   - **Password**: `password`
3. အောက်ပါ စာမျက်နှာများကို စစ်ဆေးနိုင်ပါသည်-
   - **Dashboard**: `http://localhost:8080/admin/`
   - **Product List (ကုန်ပစ္စည်းစာရင်း)**: `http://localhost:8080/admin/product`
   - **Product Registration (ကုန်ပစ္စည်းအသစ်သွင်းခြင်း)**: `http://localhost:8080/admin/product/product/new`
   - **Order List (အမှာစာစာရင်း)**: `http://localhost:8080/admin/order`
   - **Customer List (သုံးစွဲသူစာရင်း)**: `http://localhost:8080/admin/customer`
   - **Password Change**: `http://localhost:8080/admin/change_password`
   - **Two-Factor Setup**: `http://localhost:8080/admin/two_factor_auth_set`

### ၂။ Terminal Command ဖြင့် Cache ရှင်းလင်းလိုပါက
အပြောင်းအလဲများ ပြုလုပ်ပြီးနောက် Cache အသစ်ဖြစ်စေရန် အောက်ပါ Command ကို Run နိုင်ပါသည်-
```bash
docker compose exec ec-cube php bin/console cache:clear
```

---

## ၇။ ပြင်ဆင်ထိန်းသိမ်းခဲ့သော အဓိကဖိုင်များ (Modified Files Summary)

1. `app/config/eccube/services.yaml` - Default Locale အား `en` အဖြစ် သတ်မှတ်ခဲ့ခြင်း။
2. `docker-compose.override.yml` & `.env` - `ECCUBE_LOCALE=en` ပတ်ဝန်းကျင်ကိန်းရှင် သတ်မှတ်ခဲ့ခြင်း။
3. `src/Eccube/Resource/template/admin/default_frame.twig` - Inter Fonts နှင့် `admin_dashboard.css` ကို Admin စာမျက်နှာတိုင်းသို့ Global ချိတ်ဆက်ခဲ့ခြင်း။
4. `src/Eccube/Resource/template/admin/nav.twig` - စတိုးဆိုင် မူရင်းမီနူးများအားလုံးကို Dropdown Accordion စနစ်ဖြင့် ထိန်းသိမ်းပြီး Admin User အချက်အလက်များ ချိတ်ဆက်ခဲ့ခြင်း။
5. `src/Eccube/Resource/template/admin/index.twig` - EC-CUBE စနစ်ဒေတာအစစ်အမှန်များ (Products, Out of Stock, Customers, Sales, Order Statuses) ဖြင့် ခေတ်မီ Pulse Dashboard တည်ဆောက်ခဲ့ခြင်း။
6. `html/template/admin/assets/css/admin_dashboard.css` - Dashboard နှင့် စာမျက်နှာအားလုံးဆိုင်ရာ Global Design System၊ Card၊ Table၊ Pill Badge နှင့် Responsive CSS များ ရေးသားခဲ့ခြင်း။
7. `app/config/eccube/packages/exercise_html_purifier.yaml` - HTMLPurifier Serializer Cache လမ်းကြောင်းအား Persistent Directory သို့ သတ်မှတ်ပေးခဲ့ခြင်း။
8. `src/Eccube/EventListener/TwigInitializeListener.php` - HTMLPurifier Cache Directory အား Template Rendering မတိုင်မီ အလိုအလျောက် စစ်ဆေးဖန်တီးပေးသည့် Safeguard ထည့်သွင်းခဲ့ခြင်း။

---

## ၈။ တွေ့ကြုံရတတ်သော ပြဿနာများနှင့် ဖြေရှင်းနည်း (Troubleshooting)

### HTMLPurifier SerializerPath Warning Error
- **ဖြစ်ပေါ်ရသည့် အကြောင်းရင်း**:
  `cache:clear` သို့မဟုတ် Branch အပြောင်းအလဲ ပြုလုပ်သည့်အခါ Symfony မှ `var/cache/dev` ကို ရှင်းလင်းလိုက်ပြီး `htmlpurifier` cache folder ပါ ဖျက်ဆီးခံရခြင်းကြောင့် Template များတွင် `|purify` filter ခေါ်ယူသည့်အခါ အောက်ပါ Error ပေါ်ပေါက်တတ်ပါသည်-
  ```
  User Warning: Base directory /var/www/html/var/cache/dev/htmlpurifier does not exist,
  please create or change using %Cache.SerializerPath
  ```
- **ဖြေရှင်းပြီးစီးပုံ**:
  1. `app/config/eccube/packages/exercise_html_purifier.yaml` တွင် persistent cache လမ်းကြောင်းဖြစ်သော `%kernel.project_dir%/var/htmlpurifier` အဖြစ် သတ်မှတ်ပေးခဲ့ပါသည်။
  2. `src/Eccube/EventListener/TwigInitializeListener.php` တွင် request တိုင်း၌ htmlpurifier cache directory မရှိပါက `mkdir(..., 0777, true)` ဖြင့် အလိုအလျောက် ဖန်တီးပေးစေရန် စီမံထားသဖြင့် နောင်တွင် ဤ Error ထပ်မံ မဖြစ်ပေါ်တော့ပါ။

