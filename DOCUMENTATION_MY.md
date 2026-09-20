# EC-CUBE UI ပြင်ဆင်မှုနှင့် Product Detail Page နည်းပညာဆိုင်ရာ မှတ်တမ်း (Documentation)

ဤမှတ်တမ်းသည် **EC-CUBE 4.x** စနစ်တွင် အသုံးပြုသူအတွေ့အကြုံ (User Experience) နှင့် ဒီဇိုင်းအသွင်အပြင် (Modern UI Design) ကောင်းမွန်စေရန် **Home Page**, **Product Details Page (`/products/detail/2`)** နှင့် **Related Products (ဆက်စပ်ပစ္စည်းများ)** အပိုင်းတို့ကို အသစ်ပြန်လည်ရေးဆွဲတည်ဆောက်ခဲ့သည့် နည်းပညာဆိုင်ရာ အသေးစိတ်အချက်အလက်များ ဖြစ်ပါသည်။

---

## ၁။ စီမံကိန်းအကျဉ်းချုပ် (Project Overview)

- **ရည်ရွယ်ချက်** - ပေးပို့ထားသော UI Design Mockup (Apple AirPods Max ဒီဇိုင်း) အတိုင်း ခေတ်မီပြီး အသုံးပြုရလွယ်ကူသော E-commerce Product Detail စာမျက်နှာနှင့် ပင်မစာမျက်နှာ (Home Page) ကို ဖန်တီးတည်ဆောက်ရန်။
- **အဓိကနည်းပညာများ** -
  - **Backend / Template Engine**: PHP 8.1 / Symfony Framework / Twig Template Engine (EC-CUBE 4.x)
  - **Styling**: Vanilla CSS (Custom Design System - `nav_shopcart.css`)
  - **Interactivity**: Vanilla JavaScript (Live Variant Switching, Dynamic Quantity Control, Form Sync)
  - **Database & Container**: MySQL 8.0 / Docker Environment

---

## ၂။ အဓိက ပါဝင်သော လုပ်ဆောင်ချက်များ (Key Features)

### (က) Gallery Showcase နှင့် Interactive Variant Switcher
1. **ပင်မဓာတ်ပုံပြသမှု (Main Showcase)**:
   - ထောင့်ချိုးဝိုင်းပြီး သန့်ရှင်းသော `#f8fafc` နောက်ခံ Box ဖြင့် ပင်မဓာတ်ပုံကို အရည်အသွေးမြင့်မားစွာ ပြသထားပါသည်။
2. **အရောင်ရွေးချယ်မှုစနစ် (Color Swatches - Choose a Color)**:
   - Dual-tone အရောင်စပ်ဒီဇိုင်းဖြင့် အဝိုင်းပုံစံ Swatches (၅) မျိုး ထည့်သွင်းထားပါသည် (Pink/Coral, Space Gray, Mint Green, Silver, Sky Blue)။
   - လက်ရှိရွေးချယ်ထားသော အရောင်တွင် Active Indicator Ring ပြသပေးပါသည်။
3. **တိုက်ရိုက်ဓာတ်ပုံပြောင်းလဲမှု (Live Image Switching)**:
   - Color Swatch သို့မဟုတ် အောက်ဘက်ရှိ Thumbnail ပုံများကို နှိပ်လိုက်ပါက စာမျက်နှာ Reload လုပ်စရာမလိုဘဲ ပင်မပုံသည် ချောမွေ့စွာ ချက်ချင်းပြောင်းလဲသွားမည် ဖြစ်ပါသည်။

### (ခ) ကုန်ပစ္စည်းအချက်အလက်နှင့် ဝယ်ယူမှုအပိုင်း (Product Info & Purchase Controls)
1. **ခေါင်းစဉ်နှင့် အဆင့်သတ်မှတ်ချက် (Rating)**:
   - `Airpods- Max` ခေါင်းစဉ်၊ ရှင်းလင်းချက်နှင့် ကြယ် (၅) ပွင့် အဆင့်သတ်မှတ်ချက် `★★★★★ (121 reviews)`။
2. **ဈေးနှုန်းနှင့် အရစ်ကျစနစ် (Pricing & Financing)**:
   - ပုံမှန်ဈေးနှုန်း `$549.00` နှင့် အရစ်ကျပေးချေမှု `$99.99/month` စာသားများကို သပ်ရပ်စွာ ဖော်ပြထားပါသည်။
3. **အရေအတွက် ရွေးချယ်မှု (Quantity Stepper)**:
   - Pill ပုံစံ `[ - ]  1  [ + ]` ခလုတ်ဖြင့် လွယ်ကူစွာ အရေအတွက် တိုး/လျှော့ ပြုလုပ်နိုင်ပါသည်။
   - လက်ကျန်နည်းပါးမှု သတိပေးစာတန်း (`Only 12 Items Left! Don't miss it`) ထည့်သွင်းထားပါသည်။
   - EC-CUBE ၏ မူလ `<input type="hidden" name="quantity">` နှင့် တိုက်ရိုက်ချိတ်ဆက်ထားသဖြင့် Cart ထဲသို့ အရေအတွက် အမှန်ရောက်ရှိစေပါသည်။
4. **ဝယ်ယူရေး ခလုတ်များ (Action Buttons)**:
   - **`Buy Now`**: အစိမ်းရောင်ရင့် (Emerald Green) Solid Pill ခလုတ်။
   - **`Add to Cart`**: အစိမ်းရောင်လိုင်း (Emerald Outline) Pill ခလုတ်ဖြစ်ပြီး EC-CUBE ၏ မူလ AJAX Cart စနစ်နှင့် အပြည့်အဝ ချိတ်ဆက်ထားပါသည်။

### (ဂ) ပို့ဆောင်မှုနှင့် ပစ္စည်းပြန်အပ်မှုဆိုင်ရာ အချက်အလက် (Delivery & Returns Card)
- **Free Delivery**: အခမဲ့ ပို့ဆောင်မှုအချက်အလက်နှင့် Postal Code စစ်ဆေးရန် Link။
- **Return Delivery**: ပစ္စည်းပြန်လည်အပ်နှံနိုင်သည့် ရက် (၃၀) သတ်မှတ်ချက်နှင့် ဝန်ဆောင်မှု အသေးစိတ် Link။

### (ဃ) ဆက်စပ်ပစ္စည်းများ ကဏ္ဍ (Similar Products You May Like)
- **Product Card ၄ ခုပါ Dynamic Grid Layout**:
  - လက်ရှိကြည့်ရှုနေသော ပစ္စည်းမဟုတ်သည့် အခြားဆက်စပ်ပစ္စည်း (၄) မျိုး (ဥပမာ- AirPods Max ကြည့်နေပါက Wireless Earbuds, Bose BT, VIVEFOX, Gaming Pro) တို့ကို ရွေးချယ်ပြသပေးပါသည်။
  - Card ပုံ သို့မဟုတ် ခေါင်းစဉ် သို့မဟုတ် **`Add to Cart / View`** ခလုတ်ကို နှိပ်လိုက်ပါက သက်ဆိုင်ရာ Product Detail Page (`/products/detail/1`, `/products/detail/2`, `/products/detail/3`, `/products/detail/4`, `/products/detail/5`) သို့ တိုက်ရိုက် ရောက်ရှိသွားမည် ဖြစ်ပါသည်။
  - Card တစ်ခုချင်းစီတွင် ဓာတ်ပုံ၊ အကြိုက်ဆုံးမှတ်သားရန် Wishlist Heart Icon၊ ကြယ်အဆင့်သတ်မှတ်ချက်၊ ဈေးနှုန်းနှင့် တိုက်ရိုက်သွားရောက်နိုင်သော ခလုတ်များ ပါဝင်ပါသည်။

---

## ၃။ ပြင်ဆင်ရေးသားခဲ့သည့် ဖိုင်များစာရင်း (Modified & Created Files)

| ဖိုင်လမ်းကြောင်း (File Path) | အမျိုးအစား | ပြင်ဆင်ဆောင်ရွက်ခဲ့သည့် အချက်များ |
| :--- | :---: | :--- |
| [`src/Eccube/Resource/template/default/Product/detail.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/default/Product/detail.twig) | `MODIFY` | Product Detail စာမျက်နှာ အပြင်အဆင်၊ Swatch Switcher JS၊ Form Synchronization နှင့် Similar Products Grid အားလုံးကို ထည့်သွင်းတည်ဆောက်ခဲ့ခြင်း။ |
| [`src/Eccube/Resource/template/default/index.twig`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/src/Eccube/Resource/template/default/index.twig) | `MODIFY` | ပင်မစာမျက်နှာရှိ Product Card များကို Product Detail Page (`/products/detail/2`) နှင့် တိုက်ရိုက် Link ချိတ်ဆက်ပေးခဲ့ခြင်း။ |
| [`html/template/default/assets/css/nav_shopcart.css`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/html/template/default/assets/css/nav_shopcart.css) | `MODIFY` | Breadcrumbs, Main Showcase, Swatches, Stepper, Delivery Box နှင့် Related Products Card များအတွက် CSS စတိုင်များ ရေးသားခဲ့ခြင်း။ |
| [`html/template/default/assets/css/style.css`](file:///Users/kyawwaiyan/Documents/my-Home-tech/ec-cube-main/html/template/default/assets/css/style.css) | `MODIFY` | `nav_shopcart.css` ကို Global အနေဖြင့် ခေါ်ယူအသုံးပြုနိုင်ရန် `@import` ထည့်သွင်းခြင်း။ |
| `html/template/default/assets/img/product/*` | `NEW` | Pink, Mint Green, Space Gray, Sky Blue, Silver အရောင်များအတွက် High-Definition ပုံများ ထည့်သွင်းခြင်း။ |

---

## ၄။ မျက်နှာပြင်အရွယ်အစားအလိုက် အပြည့်အဝ ကိုက်ညီမှု (Multi-Device Responsive Design)

စနစ်တစ်ခုလုံးကို မိုဘိုင်းဖုန်း၊ တက်ဘလက်၊ လက်ပ်တော့ပ် နှင့် ကွန်ပျူတာ မျက်နှာပြင်အားလုံးတွင် Pixel-perfect ဖြစ်စေရန် အောက်ပါ Responsive Breakpoints များဖြင့် စနစ်တကျ ရေးဆွဲထားပါသည် -

1. **Large Desktop Viewport (1440px / 1200px)**:
   - ဘယ်/ညာ ကော်လံ (၂) ခုခွဲ၍ ဘယ်ဘက်တွင် 520px Showcase Box နှင့် Thumbnails (၅) ခု၊ ညာဘက်တွင် ဈေးနှုန်း၊ Swatches၊ Stepper နှင့် ဝယ်ယူမှုခလုတ်များကို ရှင်းလင်းစွာ ပြသထားပါသည်။
   - အောက်ဘက် Similar Products တွင် (၄) ကော်လံဖြင့် အချိုးကျ ပြသပါသည်။
2. **Tablet Viewport (768px - 992px)**:
   - ဒေါင်လိုက် (Vertical Stack) စီစဉ်ထားပြီး Showcase Gallery နှင့် Thumbnails များကို အလယ်တွင် မျှတစွာ နေရာချထားပါသည်။
   - Similar Products တွင် (၂) ကော်လံဖြင့် သပ်ရပ်စွာ ပြသပေးပါသည်။
3. **Mobile Viewport (390px / 480px / 360px - iPhone & Android)**:
   - **Breadcrumbs**: မျက်နှာပြင် မပြည့်လျှံစေဘဲ ချောမွေ့စွာ Touch Scroll လုပ်နိုင်သော Horizontal Slider ပုံစံ ပြုလုပ်ထားပါသည်။
   - **Gallery Showcase**: 260px - 300px Touch-friendly Box နှင့် Thumbnails Strip။
   - **Action Buttons**: လက်မဖြင့် အလွယ်တကူ နှိပ်နိုင်စေရန် Full-width Stacked Pill Buttons (`Buy Now` နှင့် `Add to Cart`)။
   - **Related Products**: ဖုန်းမျက်နှာပြင်တွင် ကြည့်ရှုရ လွယ်ကူစေသော (၂) ကော်လံ Compact Cards Layout။

---

## ၅။ စနစ်စမ်းသပ်ခြင်းနှင့် ထိန်းသိမ်းခြင်းနည်းလမ်းများ (Testing & Maintenance)

### (က) Template Cache ရှင်းလင်းခြင်း (Clear Cache)
Twig Template သို့မဟုတ် ဖိုင်များ ပြင်ဆင်ပြီးပါက အပြောင်းအလဲများကို ချက်ချင်းမြင်တွေ့နိုင်ရန် Docker Container ထဲတွင် အောက်ပါ Command ဖြင့် Cache ရှင်းလင်းနိုင်ပါသည် -

```bash
docker compose exec ec-cube bin/console cache:clear
```

### (ခ) Browser Asset Caching ကာကွယ်ခြင်း
CSS ဖိုင်အသစ်များ ချက်ချင်းသက်ရောက်စေရန် `detail.twig` တွင် Cache-Busting Version Query Parameter ထည့်သွင်းထားပါသည် -
```twig
<link rel="stylesheet" href="{{ asset('assets/css/nav_shopcart.css') }}?v={{ 'now'|date('U') }}">
```

### (ဂ) ပစ္စည်းအသစ်များနှင့် ပုံများ ထပ်မံထည့်သွင်းခြင်း
- ပစ္စည်းပုံအသစ်များကို `html/template/default/assets/img/product/` လမ်းကြောင်းအောက်တွင် ထည့်သွင်းအသုံးပြုနိုင်ပါသည်။
- EC-CUBE Admin Panel (`/admin/product/product`) မှတစ်ဆင့် ပစ္စည်းအချက်အလက်များကို ပုံမှန်အတိုင်း စီမံခန့်ခွဲနိုင်ပါသည်။

---

## ၆။ Shopping Cart UI ပြန်လည်ရေးဆွဲခြင်း (Cart Redesign - `/cart`)

ပေးပို့ထားသော Modern Checkout/Cart UI Mockup အတိုင်း **Shopping Cart Page (`/cart`)** ကို ခေတ်မီပြီး အဆင့်မြင့်သော ၂-ကော်လံ ကတ်ဒီဇိုင်း (2-Column Card-based Layout) ဖြင့် အသစ်တည်ဆောက်ခဲ့ပါသည် -

### (က) ပါဝင်သော အဓိကကဏ္ဍများ
1. **Review Item And Shipping (ပစ္စည်းစစ်ဆေးမှု ကတ်)**:
   - Cart ထဲရှိ ပစ္စည်းတစ်ခုချင်းစီအတွက် Thumbnail ပုံ (AirPods Max ပန်းရောင်အပါအဝင်)၊ ကုန်ပစ္စည်းအမည်၊ Variant/Color၊ In Stock Tag နှင့် စုစုပေါင်းဈေးနှုန်းတို့ကို ပြသထားပါသည်။
   - `[ - ] 01 [ + ]` Stepper ဖြင့် အရေအတွက် တိုး/လျှော့ခြင်းနှင့် အမှိုက်ပုံး Icon ဖြင့် ပစ္စည်းဖျက်ထုတ်ခြင်း (Delete) တို့ကို EC-CUBE ၏ မူလ Session CSRF နှင့် ချိတ်ဆက်ဆောင်ရွက်ထားပါသည်။
   - Free Delivery သတ်မှတ်ချက်ပြည့်မီမှု အသိပေးချက် (Notice Banner) ပါဝင်ပါသည်။
2. **Delivery Information (ပို့ဆောင်မည့် လိပ်စာအချက်အလက် ကတ်)**:
   - အမည် (Name), လိပ်စာ (Address), မြို့ (City), စာတိုက်ကုဒ် (Zip Code), ဖုန်းနံပါတ် (Mobile) နှင့် အီးမေးလ် (Email) တို့ကို Key-Value ဇယားပုံစံ သန့်ရှင်းစွာ ပြသထားပါသည်။
   - **`Edit Information` Pill Button**: နှိပ်လိုက်ပါက Modal Dialog ပွင့်လာပြီး လိပ်စာအချက်အလက်များကို တိုက်ရိုက်ပြင်ဆင်သိမ်းဆည်းနိုင်ကာ LocalStorage တွင် အလိုအလျောက် မှတ်သားထားပေးပါသည်။
3. **Order Summery (အော်ဒါအကျဉ်းချုပ် ကတ်)**:
   - Coupon Code ထည့်သွင်းနိုင်သော Input Box နှင့် အစိမ်းရောင်ရင့် (`#003d29`) Pill ခလုတ် `Apply coupon` ပါဝင်ပါသည်။
   - Subtotal, Free Shipping, Discount နှင့် စုစုပေါင်းကျသင့်ငွေ (Total Amount) တွက်ချက်ပြသမှု။
4. **Payment Details (ငွေပေးချေမှုနည်းလမ်းများ ကတ်)**:
   - Cash on Delivery, Shopcart Card, Paypal နှင့် **Credit or Debit card** (အစိမ်းရောင် Active Dot ဖြင့် ရွေးချယ်ထားမှု)။
   - Amazon Pay, Mastercard (Red/Yellow overlap) နှင့် VISA Brand Badges များ ထည့်သွင်းထားပါသည်။
   - Email*, Card Holder Name* နှင့် Card Number ထည့်သွင်းရန် Form Fields များ။
   - **`Proceed to Checkout`** Button: အော်ဒါဆက်လက်ဝယ်ယူရန် EC-CUBE ၏ Checkout Step (`cart_buystep`) သို့ ချိတ်ဆက်ပေးထားပါသည်။
5. **Empty Cart State (ပစ္စည်းမရှိသေးသော အခြေအနေ)**:
   - Cart ထဲတွင် ပစ္စည်းမရှိသေးပါက ခေတ်မီသော ခြင်းတောင်း Icon၊ ရှင်းလင်းချက်နှင့် `Start Shopping` Button ကို သပ်ရပ်စွာ ပြသပေးပါသည်။

