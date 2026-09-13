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

---

## ၅။ အနှစ်ချုပ် (Summary)

ဤ Navbar အသစ်သည် EC-CUBE ၏ မူလ Session၊ Authentication၊ Shopping Cart နှင့် Multi-Language System များကို ထိခိုက်မှုမရှိစေဘဲ ခေတ်မီပြီး Premium ဆန်သော E-commerce User Experience (UX) ကို ရရှိစေရန် ရေးဆွဲထားခြင်း ဖြစ်ပါသည်။
