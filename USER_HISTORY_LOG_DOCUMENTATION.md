# EC-CUBE 4.x - User History Logging & Admin Private Log Page Implementation Guide
(အသုံးပြုသူလှုပ်ရှားမှုမှတ်တမ်းနှင့် Admin Private စာမျက်နှာ တည်ဆောက်ခြင်းဆိုင်ရာ အသေးစိတ်လက်စွဲ)

ဤမှတ်တမ်းသည် **EC-CUBE 4.x (Symfony 6.4)** စနစ်တွင် **EventListener** နှင့် **EventSubscriber** တို့ကို အသုံးပြု၍ ဝယ်ယူသူ (Customer) နှင့် ဧည့်သည် (Guest) များ၏ လှုပ်ရှားမှုများ (Page Views, Logins, Logouts, Product Views, Purchases, Cart actions) ကို File-based Log အဖြစ် သိမ်းဆည်းခြင်းနှင့် Admin Panel တွင် လုံခြုံစိတ်ချရသော Private Log Viewer စာမျက်နှာ တည်ဆောက်ခြင်းဆိုင်ရာ အဆင့်ဆင့် နည်းပညာမှတ်တမ်း ဖြစ်ပါသည်။

---

## ၁။ စနစ်၏ ဗိသုကာနှင့် အလုပ်လုပ်ပုံ (System Architecture)

```
                            ┌──────────────────────────────────────────────┐
                            │                 HTTP Request                 │
                            └──────────────────────┬───────────────────────┘
                                                   │
                   ┌───────────────────────────────┴──────────────────────────────┐
                   │                                                              │
                   ▼                                                              ▼
      ┌───────────────────────────────┐                            ┌───────────────────────────────┐
      │     UserHistorySubscriber     │                            │      UserHistoryListener      │
      │  (EventSubscriberInterface)   │                            │       (EventListener)         │
      ├───────────────────────────────┤                            ├───────────────────────────────┤
      │ • KernelEvents::RESPONSE      │                            │ • front.product.detail.*      │
      │   (Page Views & Navigation)   │                            │   (Product Views)             │
      │ • SecurityEvents::LOGIN       │                            │ • front.shopping.complete.*   │
      │   (Customer/Admin Login)      │                            │   (Order/Purchase Complete)   │
      │ • LogoutEvent::class          │                            │ • front.entry.complete        │
      │   (User Logout)               │                            │   (Customer Registration)     │
      │                               │                            │ • UserActionEvent::class      │
      │                               │                            │   (Custom Dispatched Actions) │
      └──────────────┬────────────────┘                            └──────────────┬────────────────┘
                     │                                                            │
                     └──────────────────────────────┬─────────────────────────────┘
                                                    ▼
                                   ┌──────────────────────────────────┐
                                   │        UserHistoryLogger         │
                                   │  Writes JSONL to:                │
                                   │  var/log/user_history/           │
                                   │  user_history_YYYY-MM-DD.log     │
                                   └────────────────┬─────────────────┘
                                                    │
                                                    ▼
                                   ┌──────────────────────────────────┐
                                   │    UserHistoryLogController      │
                                   │  (Private Admin Interface)       │
                                   │  /%eccube_admin_route%/setting/  │
                                   │  system/user_history             │
                                   └──────────────────────────────────┘
```

### EventSubscriber နှင့် EventListener ကွာခြားချက်
1. **EventSubscriber (`UserHistorySubscriber`)**:
   - `EventSubscriberInterface` ကို implement လုပ်ထားပြီး မည်သည့် Event များကို နားထောင်မည်ကို Class ကိုယ်တိုင်က `getSubscribedEvents()` method ဖြင့် တိုက်ရိုက် ကြေညာသတ်မှတ်ပါသည်။
   - Framework အဆင့် System / Security Lifecycle ဖြစ်ရပ်များ (ဥပမာ- HTTP Response, Login, Logout) ကို စောင့်ကြည့်ရန် အသုံးပြုပါသည်။
2. **EventListener (`UserHistoryListener`)**:
   - Event method များကို သီးသန့် ရေးသားထားပြီး မည်သည့် Event နှင့် ချိတ်ဆက်မည်ကို `app/config/eccube/services.yaml` တွင် `kernel.event_listener` tag ဖြင့် သတ်မှတ်ပါသည်။
   - EC-CUBE ၏ စီးပွားရေးလုပ်ငန်းဆိုင်ရာ Domain Events များ (ဥပမာ- Product Detail ကြည့်ရှုခြင်း၊ Order ဝယ်ယူမှုပြီးဆုံးခြင်း၊ အကောင့်အသစ်ဖွင့်ခြင်း) ကို သီးခြား စောင့်ကြည့်ရန် အသုံးပြုပါသည်။

---

## ၂။ အဆင့်ဆင့် တည်ဆောက်ခဲ့သည့် ဖိုင်များစာရင်း (File Structure)

| ဖိုင်လမ်းကြောင်း (File Path) | အခန်းကဏ္ဍ (Role) | ဖော်ပြချက် |
| :--- | :--- | :--- |
| `app/Customize/Event/UserActionEvent.php` | Custom Event | အခြား Controller သို့မဟုတ် Plugin များမှ User Action များကို စိတ်ကြိုက် dispatch ပြုလုပ်နိုင်ရန် Event class |
| `app/Customize/Service/UserHistoryLogger.php` | Logger Service | Log file ရေးသားခြင်း၊ ဖတ်ရှုခြင်း၊ Filter/Search စစ်ထုတ်ခြင်း၊ KPI တွက်ချက်ခြင်းနှင့် CSV ထုတ်ယူခြင်း |
| `app/Customize/EventListener/UserHistorySubscriber.php` | Event Subscriber | Page View, User Login, User Logout ဖြစ်ရပ်များကို အလိုအလျောက် မှတ်တမ်းတင်ခြင်း |
| `app/Customize/EventListener/UserHistoryListener.php` | Event Listener | Product Detail View, Shopping Complete, Registration ဖြစ်ရပ်များကို မှတ်တမ်းတင်ခြင်း |
| `app/Customize/Nav/UserHistoryNav.php` | Admin Navigation | Admin Menu ရှိ **設定 > システム設定 > ユーザー履歴ログ** အောက်သို့ Menu Item ထည့်သွင်းခြင်း |
| `app/Customize/Controller/Admin/UserHistoryLogController.php` | Admin Controller | Admin Private Page Controller (View, Search, Filter, Pagination, CSV Export, Clear Log) |
| `app/template/admin/Setting/System/user_history.twig` | Twig UI Template | KPI Cards, Data Table, Color-coded Badges, JSON Detail Modal နှင့် Pagination ပါဝင်သော UI |
| `app/Customize/Resource/locale/messages.ja.yaml` | Translations (JA) | ဂျပန်ဘာသာ UI စာသားများ |
| `app/Customize/Resource/locale/messages.en.yaml` | Translations (EN) | အင်္ဂလိပ်ဘာသာ UI စာသားများ |
| `app/config/eccube/services.yaml` | Service Config | Service များနှင့် Event Listener/Subscriber Tag များကို Dependency Injection တွင် မှတ်ပုံတင်ခြင်း |

---

## ၃။ အဆင့်ဆင့် အကောင်အထည်ဖော်ခြင်း (Step-by-Step Implementation)

### အဆင့် (၁) - Custom User Action Event ဖန်တီးခြင်း
**ဖိုင်**: `app/Customize/Event/UserActionEvent.php`
- မည်သည့်နေရာမှမဆို `$eventDispatcher->dispatch(new UserActionEvent('ACTION_NAME', ['key' => 'value']))` ဖြင့် လှုပ်ရှားမှုများကို ပေးပို့နိုင်စေရန် ဖန်တီးထားပါသည်။

```php
namespace Customize\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserActionEvent extends Event
{
    private string $action;
    private array $details;

    public function __construct(string $action, array $details = [])
    {
        $this->action = $action;
        $this->details = $details;
    }

    public function getAction(): string { return $this->action; }
    public function getDetails(): array { return $this->details; }
}
```

---

### အဆင့် (၂) - User History Logger Service တည်ဆောက်ခြင်း
**ဖိုင်**: `app/Customize/Service/UserHistoryLogger.php`
- **တည်နေရာ**: `var/log/user_history/user_history_YYYY-MM-DD.log`
- **Format**: JSON-Lines (JSONL) တစ်ကြောင်းချင်းစီ သိမ်းဆည်းပါသည်။
- **အဓိကလုပ်ဆောင်ချက်များ**:
  1. `log(string $event, array $data)`: `FILE_APPEND | LOCK_EX` ဖြင့် thread-safe ဖြစ်အောင် File ထဲသို့ ရေးသားခြင်း။
  2. `getLogFiles()`: ရှိပြီးသား log file စာရင်းများကို ရက်စွဲအလိုက် စီပေးခြင်း။
  3. `getLogs($filename, $filters, $page, $perPage)`: Log များကို နောက်ဆုံးဖြစ်ရပ်မှစ၍ အစဉ်လိုက်ဖတ်ရှုပြီး Event, User Type, Keyword ဖြင့် Filter လုပ်ကာ Pagination ခွဲပေးခြင်း။
  4. `getStats($filename)`: Total Events, Unique Users, Unique IPs အရေအတွက်များကို တွက်ချက်ခြင်း။
  5. `exportCsv($filename, $filters)`: Microsoft Excel တွင် ဂျပန်စာလုံးမပျက်စေရန် **UTF-8 BOM (`\xEF\xBB\xBF`)** ထည့်သွင်းပြီး CSV ဖိုင်အဖြစ် Download ပြုလုပ်ပေးခြင်း။
  6. `clearLog($filename)`: Log ဖိုင်ကို လုံခြုံစွာ ရှင်းလင်းခြင်း။

---

### အဆင့် (၃) - EventSubscriber တည်ဆောက်ခြင်း
**ဖိုင်**: `app/Customize/EventListener/UserHistorySubscriber.php`
- `EventSubscriberInterface` ကို အသုံးပြုထားပါသည်။
- **Subscribed Events**:
  1. `KernelEvents::RESPONSE`:
     - Image, CSS, JS, Profiler, Font စသည့် static file များကို ချန်လှပ်ပြီး Main Request ဖြစ်သော Page View များကိုသာ မှတ်တမ်းတင်ပါသည်။
     - ဝယ်ယူသူအကောင့်ဝင်ထားပါက `Customer ID`, `Email`, `Name` ကို ရယူပြီး မဝင်ထားပါက `Guest` အဖြစ် မှတ်သားပါသည်။
  2. `SecurityEvents::INTERACTIVE_LOGIN`:
     - အသုံးပြုသူ အကောင့်ဝင်ရောက်မှု (`USER_LOGIN` သို့မဟုတ် `ADMIN_LOGIN`) ကို IP, User Agent, Time တို့နှင့်တကွ မှတ်သားပါသည်။
  3. `LogoutEvent::class`:
     - အသုံးပြုသူ အကောင့်ထွက်မှု (`USER_LOGOUT`) ကို မှတ်သားပါသည်။

---

### အဆင့် (၄) - EventListener တည်ဆောက်ခြင်း
**ဖိုင်**: `app/Customize/EventListener/UserHistoryListener.php`
- EC-CUBE ၏ စီးပွားရေးလုပ်ငန်းဆိုင်ရာ Events များကို တိုက်ရိုက်ဖမ်းယူပါသည်:
  1. `front.product.detail.initialize`: ကုန်ပစ္စည်းအသေးစိတ်ကြည့်ရှုမှု (`PRODUCT_VIEW` - Product ID, Product Name, Price)
  2. `front.shopping.complete.initialize`: ဝယ်ယူမှုအောင်မြင်ခြင်း (`PURCHASE_COMPLETE` - Order ID, Order No, Total Amount, Payment Method)
  3. `front.entry.complete`: အသင်းဝင်အသစ်မှတ်ပုံတင်ခြင်း (`CUSTOMER_REGISTER` - Customer ID, Email)
  4. `Customize\Event\UserActionEvent`: စိတ်ကြိုက်ထည့်သွင်းထားသော Action များ။

---

### အဆင့် (၅) - Admin Sidebar Menu ချိတ်ဆက်ခြင်း
**ဖိုင်**: `app/Customize/Nav/UserHistoryNav.php`
- `Eccube\Common\EccubeNav` interface ကို implement ပြုလုပ်ပြီး Admin Sidebar ၏ **設定 (Settings) > システム設定 (System Settings)** အောက်တွင် `user_history` menu ကို ချိတ်ဆက်ပေးပါသည်။

```php
namespace Customize\Nav;

use Eccube\Common\EccubeNav;

class UserHistoryNav implements EccubeNav
{
    public static function getNav(): array
    {
        return [
            'setting' => [
                'children' => [
                    'system' => [
                        'children' => [
                            'user_history' => [
                                'name' => 'admin.setting.system.user_history_log',
                                'url' => 'admin_setting_system_user_history',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
```

---

### အဆင့် (၆) - Admin Private Page Controller တည်ဆောက်ခြင်း
**ဖိုင်**: `app/Customize/Controller/Admin/UserHistoryLogController.php`
- **လုံခြုံရေး (Private Access)**:
  - Route လမ်းကြောင်းကို `/%eccube_admin_route%/setting/system/user_history` ဖြင့် စတင်ထားသောကြောင့် EC-CUBE Firewall အရ `ROLE_ADMIN` ရှိသော Admin များသာ ဝင်ရောက်ခွင့်ရရှိပါသည်။
- **လုပ်ဆောင်ချက်များ**:
  1. `index()`: Log စာရင်းပြသခြင်း၊ Search & Filter ပြုလုပ်ခြင်း၊ KPI Metrics ပြသခြင်း၊ Pagination ထိန်းချုပ်ခြင်း။
  2. `download()`: ရွေးချယ်ထားသော Log ကို CSV သို့မဟုတ် Raw Log အဖြစ် Download ပြုလုပ်ခြင်း။
  3. `clear()`: EC-CUBE CSRF Token စစ်ဆေးပြီး Log ဖိုင်ကို ရှင်းလင်းခြင်း။
  4. `generateSample()`: စနစ်စမ်းသပ်ရန်အတွက် Sample Activity Log များကို ချက်ချင်းထုတ်ပေးခြင်း။

---

### အဆင့် (၇) - Twig UI Template ရေးသားခြင်း
**ဖိုင်**: `app/template/admin/Setting/System/user_history.twig`
- `@admin/default_frame.twig` ကို extend ပြုလုပ်ထားပါသည်။
- **အဓိက UI အစိတ်အပိုင်းများ**:
  1. **KPI Metric Cards (၄) ခု**: Total Events, Unique Users, Unique Client IPs, Active Log File Size
  2. **Filter & Search Toolbar**:
     - Log File ရွေးချယ်မှု Dropdown
     - Event Type ရွေးချယ်မှု (`PAGE_VIEW`, `USER_LOGIN`, `USER_LOGOUT`, `PRODUCT_VIEW`, `PURCHASE_COMPLETE`, `CART_ACTION`, `CUSTOMER_REGISTER`)
     - User Type ရွေးချယ်မှု (`Customer`, `Guest`, `Administrator`)
     - Keyword Search (အမည်၊ အီးမေးလ်၊ IP၊ URL၊ Route စသည်တို့ဖြင့် ရှာဖွေနိုင်ခြင်း)
  3. **Data Table**:
     - Time, Event (အရောင်ခွဲထားသော Badges များ), User (Avatar + Name + Email), Request (HTTP Method, Route, URL), IP (Copy Icon ပါဝင်သည်), Status Code (200/302/404), Details Icon
  4. **Modals**:
     - **Details Modal**: JSON Data ကို Syntax Highlighting ဖြင့် သပ်ရပ်စွာ ပြသပေးပြီး Copy ခလုတ် ပါဝင်ပါသည်။
     - **Clear Log Modal**: CSRF Token ဖြင့် ကာကွယ်ထားသော သတိပေးချက် Modal ဖြစ်ပါသည်။
  5. **Empty State & Sample Generator**:
     - Log မရှိသေးချိန်တွင် Friendly Message နှင့် စမ်းသပ်ရန် Sample Log Generator ခလုတ် ပါဝင်ပါသည်။

---

### အဆင့် (၈) - ဘာသာစကားဖိုင်များ ထည့်သွင်းခြင်း (Localization)
- `app/Customize/Resource/locale/messages.ja.yaml` (ဂျပန်ဘာသာ)
- `app/Customize/Resource/locale/messages.en.yaml` (အင်္ဂလိပ်ဘာသာ)
- Admin UI ရှိ စာသားများ၊ Tooltips များနှင့် Success/Error Alert စာသားများအားလုံးကို ဘာသာစကားနှစ်မျိုးလုံးဖြင့် ပြည့်စုံစွာ ထည့်သွင်းထားပါသည်။

---

### အဆင့် (၉) - Service များကို `services.yaml` တွင် မှတ်ပုံတင်ခြင်း
**ဖိုင်**: `app/config/eccube/services.yaml` (lines 252-275)

```yaml
    # Custom: User History Logger service
    Customize\Service\UserHistoryLogger:
        public: true
        arguments:
            $projectDir: '%kernel.project_dir%'

    # Custom: User History Event Subscriber (kernel request/response, login, logout)
    Customize\EventListener\UserHistorySubscriber:
        tags:
            - { name: kernel.event_subscriber }

    # Custom: User History Event Listener (product detail, shopping complete, registration, user action)
    Customize\EventListener\UserHistoryListener:
        tags:
            - { name: kernel.event_listener, event: front.product.detail.initialize, method: onProductDetail }
            - { name: kernel.event_listener, event: front.shopping.complete.initialize, method: onShoppingComplete }
            - { name: kernel.event_listener, event: front.entry.complete, method: onCustomerRegister }
            - { name: kernel.event_listener, event: Customize\Event\UserActionEvent, method: onUserAction }

    # Custom: User History Navigation in Admin Menu
    Customize\Nav\UserHistoryNav:
        tags:
            - { name: eccube.nav }
```

---

## ၄။ စနစ်စမ်းသပ်စစ်ဆေးခြင်း (Testing & Verification)

### ၁။ Cache ရှင်းလင်းခြင်း (Cache Clear)
```bash
php -d memory_limit=1G bin/console cache:clear --no-warmup
```

### ၂။ Event Dispatcher ချိတ်ဆက်မှု စစ်ဆေးခြင်း
```bash
# UserHistorySubscriber ကို စစ်ဆေးရန်
php bin/console debug:event-dispatcher "kernel.response"

# UserHistoryListener ကို စစ်ဆေးရန်
php bin/console debug:event-dispatcher "front.product.detail.initialize"
```

### ၃။ Route စာရင်း စစ်ဆေးခြင်း
```bash
php bin/console debug:router admin_setting_system_user_history
```
*ရလဒ်: `/admin/setting/system/user_history` လမ်းကြောင်း အောင်မြင်စွာ တည်ဆောက်ပြီးဖြစ်ကြောင်း တွေ့ရပါမည်။*

### ၄။ Log ရေးသားမှုနှင့် Admin Page ဝင်ရောက်ကြည့်ရှုခြင်း
1. Admin Panel သို့ ဝင်ရောက်ပါ (`http://localhost:8000/admin/`)။
2. ဘယ်ဘက် Sidebar ရှိ **設定 > システム設定 > ユーザー履歴ログ** ကို နှိပ်ပါ။
3. အကယ်၍ Log မရှိသေးပါက စာမျက်နှာပေါ်ရှိ **「サンプルログを生成」 (Generate Sample Logs)** ခလုတ်ကို နှိပ်၍ ချက်ချင်း စမ်းသပ်နိုင်ပါသည်။
4. Front Page (`/`, `/products/detail/2`, `/mypage/login`) စာမျက်နှာများသို့ သွားရောက်ကြည့်ရှုပါက နောက်ကွယ်မှ အလိုအလျောက် Log ရေးသားနေမည် ဖြစ်ပါသည်။

---

## ၅။ Git သတိပြုရန်အချက် (Git Handling Note)

သင် `git checkout main` ပြုလုပ်စဉ် အောက်ပါ Error တက်ရသည့် အကြောင်းရင်းမှာ-
```
error: Your local changes to the following files would be overwritten by checkout:
        app/config/eccube/services.yaml
```
ကျွန်ုပ်တို့သည် `app/config/eccube/services.yaml` နှင့် Twig Extension အချို့တွင် ပြင်ဆင်မှုများ ပြုလုပ်ထားသောကြောင့် ဖြစ်ပါသည်။ အဆိုပါ ပြင်ဆင်မှုများကို မပျောက်ပျက်စေရန် အောက်ပါအတိုင်း Commit သို့မဟုတ် Stash ပြုလုပ်နိုင်ပါသည်-

```bash
# နည်းလမ်း (၁) - လက်ရှိအပြောင်းအလဲများကို Commit ပြုလုပ်ပြီးမှ Branch ပြောင်းရန် (အကြံပြုသည်)
git add .
git commit -m "feat: Add User History EventListener, Subscriber, and Admin Log Page"
git checkout main

# နည်းလမ်း (၂) - ခေတ္တ Stash သိမ်းထားလိုပါက
git stash
git checkout main
# ပြန်လည်ယူလိုပါက
git stash pop
```
