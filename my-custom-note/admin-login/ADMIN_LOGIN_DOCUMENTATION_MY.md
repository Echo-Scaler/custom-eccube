# EC-CUBE Admin Login UI ပြင်ဆင်မှု နည်းပညာဆိုင်ရာ အသေးစိတ်မှတ်တမ်း (Documentation)

ဤမှတ်တမ်းသည် **EC-CUBE 4.x** စနစ်၏ စီမံခန့်ခွဲသူဝင်ရောက်ရာ စာမျက်နှာ (**Admin Login Page - `http://localhost:8080/admin/login`**) အား ခေတ်မီပြီး အဆင့်မြင့်သော 3D Isometric E-Commerce Platform UI ဒီဇိုင်းပုံစံသို့ အသစ်ပြန်လည်ရေးဆွဲတည်ဆောက်ခဲ့သည့် နည်းပညာဆိုင်ရာ အသေးစိတ်အဆင့်ဆင့် ဆောင်ရွက်ချက်များ ဖြစ်ပါသည်။

---

## ၁။ စီမံကိန်း အကျဉ်းချုပ် (Project Overview)

- **ရည်ရွယ်ချက်** - ပေးပို့ထားသော UI Mockup ဒီဇိုင်းအတိုင်း သန့်ရှင်းသပ်ရပ်ပြီး ခေတ်မီဆန်းသစ်သော 3D Isometric Visual အနုပညာလက်ရာနှင့်အတူ စီမံခန့်ခွဲသူများ အသုံးပြုရ လွယ်ကူလျင်မြန်စေမည့် Admin Login Portal တစ်ခုကို ဖန်တီးတည်ဆောက်ရန်။
- **အဓိကနည်းပညာများ** -
  - **Backend / Template Engine**: PHP 8.1 / Symfony Framework / Twig Template Engine (EC-CUBE 4.x)
  - **Styling**: Vanilla CSS (Custom Design System - `login_custom.css`)
  - **Typography**: Google Fonts (*Plus Jakarta Sans* & *Inter*)
  - **Interactivity**: Vanilla JavaScript (Password Visibility Toggle, LocalStorage Remember ID, Submit Loading State)
  - **Container & Environment**: Docker / Apache / MySQL 8.0

---

## ၂။ အဓိက ပါဝင်သော ဒီဇိုင်းနှင့် လုပ်ဆောင်ချက်များ (Key Features)

```
+---------------------------------------------------+---------------------------------------------------+
|            ဘယ်ဘက်ခြမ်း Visual Hero Panel          |          ညာဘက်ခြမ်း Authentication Panel          |
+---------------------------------------------------+---------------------------------------------------+
| • Live Status Badge (EC-CUBE Platform)            | • Shop Logo & Back to Storefront Link             |
| • Heading: Next-Gen E-Commerce Management         | • Title: ログイン (Sign In)                       |
| • Subtitle: E-commerce operations & catalog       | • Login ID Field (with User SVG Icon)             |
| • 3D Isometric Smart Warehouse Artwork            | • Password Field (with Lock Icon + Eye Toggle)    |
| • Feature Badges:                                 | • Remember Login ID Checkbox (localStorage)       |
|   - Order Processing                              | • Primary Submit Button (Royal Blue + Animation)  |
|   - Product & Inventory                           | • Security Indicator (256-bit Encrypted)          |
|   - Real-time Analytics                           | • Copyright Notice                                |
+---------------------------------------------------+---------------------------------------------------+
```

### (က) ဘယ်ဘက်ခြမ်း Visual Hero Panel (3D Isometric E-Commerce Hub)
1. **Status Badge**:
   - အစိမ်းရောင် အချက်ပြမီးလုံး Pulse Animation (`pulse-green`) ပါဝင်သော `"EC-CUBE E-Commerce Platform"` Badge ကို ဖန်တီးထားပါသည်။
2. **ခေါင်းစဉ်နှင့် ရှင်းလင်းချက် (Heading & Subtitle)**:
   - **Heading**: `Next-Gen E-Commerce Management`
   - **Subtitle**: `Streamline your online store operations, manage product catalogs, fulfill customer orders, and track real-time sales growth effortlessly.`
3. **3D Isometric Artwork**:
   - E-commerce Supply Chain နှင့် သိုလှောင်ရုံစနစ်ကို ကိုယ်စားပြုသည့် သန့်ရှင်းသော 3D Isometric Render ပုံရိပ်ကို Center Frame တွင် Floating Elevation Effect ဖြင့် ထည့်သွင်းထားပါသည်။
4. **Feature Highlights (အင်္ဂါရပ် အမှတ်အသားများ)**:
   - `Order Processing` (စျေးဝယ်လှည်း Icon)
   - `Product & Inventory` (ကုန်ပစ္စည်းသေတ္တာ Icon)
   - `Real-time Analytics` (တိုးတက်မှုဂရပ် Icon)

### (ခ) ညာဘက်ခြမ်း Authentication Form Panel (လုံခြုံရေးနှင့် ဖောင်စနစ်)
1. **Logo နှင့် လမ်းညွှန်ခလုတ် (Header & Back to Store)**:
   - စတိုးဆိုင် Logo နှင့်အတူ စတိုးဆိုင်ပင်မစာမျက်နှာသို့ အချိန်မရွေး ပြန်သွားနိုင်သည့် `Store Front (ショップ)` Shortcut Button ပါဝင်ပါသည်။
2. **Login ID Field**:
   - အသုံးပြုသူ User Icon ပါဝင်ပြီး Soft Background (`#f1f5f9`) နှင့် Focus ပြုလုပ်ပါက Royal Blue Halo Ring Effect ဖြစ်ပေါ်စေပါသည်။
3. **Password Field & Toggle Switch (စကားဝှက် ဝှက်/ဖော်ခလုတ်)**:
   - Lock Icon နှင့်အတူ မျက်လုံးပုံစံ Eye SVG Icon ကို နှိပ်ရုံဖြင့် Password ကို အလွယ်တကူ စစ်ဆေးကြည့်ရှုနိုင်သည့် Show/Hide Toggle လုပ်ဆောင်ချက် ပါဝင်ပါသည်။
4. **Remember Login ID စနစ်**:
   - Custom Styled Checkbox ဖြစ်ပြီး အမှန်ခြစ်ထားပါက စီမံခန့်ခွဲသူ၏ Login ID ကို Browser ၏ `localStorage` ထဲတွင် အလိုအလျောက် မှတ်သားပေးထားမည် ဖြစ်ပါသည်။
5. **Royal Blue Action Button**:
   - Gradient အရောင်စပ် (`#005ce6` မှ `#0047b3`) ဖြင့် တည်ဆောက်ထားပြီး နှိပ်လိုက်ပါက ချက်ချင်း Duplicate Submit မဖြစ်စေရန် Button ကို Disable ပြုလုပ်ကာ Loading State သို့ ကူးပြောင်းပေးပါသည်။
6. **Error & Alert Banner System**:
   - မှားယွင်းသော ID/Password ရိုက်ထည့်မိပါက အနီရောင် Alert Box ဖြင့် သတိပေးစာတန်းကို ချောမွေ့သော Slide-down Animation ဖြင့် ပြသပေးပါသည်။

---

## ၃။ ပြင်ဆင်ရေးသားခဲ့သည့် ဖိုင်များစာရင်း (Modified & Created Files)

| ဖိုင်လမ်းကြောင်း (File Path) | အမျိုးအစား | ပြင်ဆင်ဆောင်ရွက်ခဲ့သည့် အချက်များ |
| :--- | :---: | :--- |
| [`src/Eccube/Resource/locale/messages.ja.yaml`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/locale/messages.ja.yaml) | `MODIFY` | `admin.header.to_front`, `admin.login.sub_title`, `admin.login.remember_me`, `admin.login.signing_in` ဂျပန်ဘာသာစကား Translation Key များ ထည့်သွင်းခြင်း။ |
| [`src/Eccube/Resource/locale/messages.en.yaml`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/locale/messages.en.yaml) | `MODIFY` | `admin.header.to_front`, `admin.login.sub_title`, `admin.login.remember_me`, `admin.login.signing_in` အင်္ဂလိပ်ဘာသာစကား Translation Key များ ထည့်သွင်းခြင်း။ |
| [`src/Eccube/Resource/template/admin/login.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/admin/login.twig) | `MODIFY` | Split-screen Hero Panel, E-Commerce Heading, SVG Icons, Form Fields, Password Toggle JS နှင့် Remember ID Script များ ထည့်သွင်းခြင်း။ |
| [`src/Eccube/Resource/template/admin/login_frame.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/admin/login_frame.twig) | `MODIFY` | `login_custom.css` အသစ်ကို ချိတ်ဆက်ပေးခြင်းနှင့် Container Margin Reset များ ပြုလုပ်ခြင်း။ |
| [`src/Eccube/Resource/template/admin/two_factor_auth.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/admin/two_factor_auth.twig) | `MODIFY` | Two-Factor Authentication (2FA) စာမျက်နှာကို Split Screen Modern Layout နှင့် ကိုက်ညီစေရန် ပြင်ဆင်ခြင်း။ |
| [`src/Eccube/Resource/template/admin/two_factor_auth_set.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/admin/two_factor_auth_set.twig) | `MODIFY` | Two-Factor Setup (QR Code) စာမျက်နှာကို ခေတ်မီဒီဇိုင်းစနစ်သို့ ပြောင်းလဲခြင်း။ |
| [`html/template/admin/assets/css/login_custom.css`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/html/template/admin/assets/css/login_custom.css) | `NEW` | Google Fonts, Design Tokens, Split Layout Grid, Form Inputs, Buttons, Responsive Breakpoints စသည့် CSS များ ရေးသားခြင်း။ |
| [`html/template/admin/assets/img/login_bg_3d.jpg`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/html/template/admin/assets/img/login_bg_3d.jpg) | `NEW` | 3D Isometric Minimalist E-Commerce Warehouse Artwork ဓာတ်ပုံ ဖန်တီးထည့်သွင်းခြင်း။ |

---

## ၄။ နည်းပညာဆိုင်ရာ အဆင့်ဆင့် အကောင်အထည်ဖော်မှု (Step-by-Step Process)

### အဆင့် (၁) - 3D Isometric Asset ထုတ်လုပ်ပြီး Public Directory တွင် တပ်ဆင်ခြင်း
- Mockup ဒီဇိုင်းပါ Logistics / Warehouse အပြင်အဆင်အတိုင်း သန့်ရှင်းသော 3D Isometric E-Commerce Warehouse Render Image ကို ဖန်တီးခဲ့ပါသည်။
- ရရှိလာသော ပုံကို EC-CUBE ၏ Static Asset လမ်းကြောင်းဖြစ်သော `html/template/admin/assets/img/login_bg_3d.jpg` သို့ ကူးယူထည့်သွင်းခဲ့ပါသည်။

### အဆင့် (၂) - Modern CSS Architecture (`login_custom.css`) ရေးသားခြင်း
- Google Fonts (`Plus Jakarta Sans` နှင့် `Inter`) တို့ကို Import ပြုလုပ်ခဲ့ပါသည်။
- CSS Custom Variables (`--admin-primary: #0052cc;`, `--admin-bg-base: #f8fafc;`) တို့ဖြင့် Design Token System တည်ဆောက်ခဲ့ပါသည်။
- EC-CUBE ၏ မူလ Admin Frame မှ `.c-container` နှင့် `.c-contentsArea` တို့၏ `margin-top: 65px` နှင့် `margin-left: 220px` များကို `#login-page` အတွက် `margin: 0 !important;` ဖြင့် Reset ချပေးခဲ့ပါသည်။
- Desktop အတွက် 55% / 45% Split Layout နှင့် Mobile (< 992px) အတွက် Centered Card Layout သို့ အလိုအလျောက် ပြောင်းလဲနိုင်သော Responsive Media Queries များ ရေးဆွဲခဲ့ပါသည်။

### အဆင့် (၃) - Twig Frame Template (`login_frame.twig`) ချိတ်ဆက်ခြင်း
- Header အပိုင်းတွင် `login_custom.css` ကို ချိတ်ဆက်ပေးခဲ့ပြီး `<body>` tag ၏ မူလ Bootstrap background class များကို ရှင်းလင်းပေးခဲ့ပါသည်။

### အဆင့် (၄) - Login Page Template (`login.twig`) ရေးဆွဲခြင်း
- CSRF Token (`_csrf_token`) နှင့် Symfony Form Variables (`form.login_id`, `form.password`) တို့ကို တိုက်ရိုက် ချိတ်ဆက်ပေးခဲ့ပါသည်။
- Password Toggle (Show/Hide) အတွက် Client-side JS Script ထည့်သွင်းခဲ့ပါသည်။
- Login ID ကို မှတ်သားပေးနိုင်ရန် Browser `localStorage` ချိတ်ဆက်မှု Script ရေးသားခဲ့ပါသည်။

### အဆင့် (၅) - Two-Factor Authentication Template များပါ တစ်ပြေးညီ ပြင်ဆင်ခြင်း
- Admin Security အတွက် အသုံးပြုသည့် `two_factor_auth.twig` နှင့် `two_factor_auth_set.twig` စာမျက်နှာများကိုပါ တစ်ဆက်တည်း ပြင်ဆင်ခဲ့သဖြင့် Admin Portal တစ်ခုလုံး တူညီသော ဒီဇိုင်းခံစားမှုကို ရရှိစေပါသည်။

---

## ၅။ စမ်းသပ်စစ်ဆေးခြင်းနှင့် ရလဒ်များ (Testing & Verification)

1. **Authentication Flow စစ်ဆေးခြင်း**:
   - မှန်ကန်သော Admin အကောင့်ဖြင့် စမ်းသပ်ဝင်ရောက်ရာတွင် `http://localhost:8080/admin/` Dashboard သို့ `HTTP 200 OK` ဖြင့် ချောမွေ့စွာ ဝင်ရောက်နိုင်ကြောင်း အတည်ပြုပြီး ဖြစ်ပါသည်။
2. **Error Handling စစ်ဆေးခြင်း**:
   - မမှန်ကန်သော Password ရိုက်ထည့်သည့်အခါ EC-CUBE Validator မှ ပေးပို့သော ဂျပန်ဘာသာ သတိပေးချက်စာတန်း (`ログインできませんでした。入力内容に誤りがないかご確認ください。`) ကို Alert Box ဖြင့် သေသပ်စွာ ပြသနိုင်ကြောင်း စစ်ဆေးအတည်ပြုပြီး ဖြစ်ပါသည်။
3. **Interactive Controls စစ်ဆေးခြင်း**:
   - Password Show/Hide မျက်လုံး Icon အလုပ်လုပ်ခြင်း။
   - Remember Login ID အမှန်ခြစ်ပြီး Reload ပြုလုပ်ပါက ID အလိုအလျောက် ဖြည့်သွင်းပေးခြင်း။
   - "Store Front" ခလုတ်ကို နှိပ်ပါက စတိုးဆိုင်ပင်မစာမျက်နှာသို့ ရောက်ရှိခြင်း။
4. **Responsive Display စစ်ဆေးခြင်း**:
   - Mobile စခရင်များတွင် Form Card သည် အလယ်တည့်တည့်သို့ ရောက်ရှိပြီး အသုံးပြုရ လွယ်ကူမှုရှိစေရန် ချိန်ညှိထားပါသည်။

---

## ၆။ ကြည့်ရှုစစ်ဆေးနိုင်သည့် လိပ်စာများ (Direct Links)

- **Admin Login Page**: [http://localhost:8080/admin/login](http://localhost:8080/admin/login)
- **Storefront Home Page**: [http://localhost:8080/](http://localhost:8080/)
