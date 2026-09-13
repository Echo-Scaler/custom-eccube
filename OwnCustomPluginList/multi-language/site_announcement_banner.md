# EC-CUBE 4: Site Announcement Banner Plugin တည်ဆောက်ခြင်း လမ်းညွှန် (Hello World / Twig Injection)

ဤလမ်းညွှန်သည် EC-CUBE 4 တွင် Core file များကို မထိခိုက်စေဘဲ **Twig Injection (Template Event)** စနစ်ကို အသုံးပြု၍ Website ၏ ထိပ်ဆုံး (Header) တွင် Announcement Banner (အထူးကြေညာချက် / ပရိုမိုးရှင်း အသိပေးချက်) ပြသပေးသည့် Plugin တစ်ခုကို အစအဆုံး အဆင့်ဆင့် ဖန်တီးနည်း ဖြစ်ပါသည်။

---

## ၁။ ဖန်တီးရမည့် ဖိုင်လမ်းကြောင်းများ စာရင်း (File Path List)

Plugin တစ်ခုလုံးအတွက် ဖန်တီးရမည့် ဖိုင်လမ်းကြောင်းများနှင့် ၎င်းတို့၏ တာဝန်များမှာ အောက်ပါအတိုင်း ဖြစ်ပါသည်-

| No | File Path | အမျိုးအစား | အဓိက တာဝန် |
|:---|:---|:---|:---|
| ၁ | [composer.json](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/composer.json) | Configuration | Plugin ၏ Metadata (Name, Version, Code, Dependencies) သတ်မှတ်ခြင်း |
| ၂ | [PluginManager.php](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/PluginManager.php) | Lifecycle | Plugin Install, Enable, Disable, Uninstall ပြုလုပ်ချိန် Lifecycle စီမံခြင်း |
| ၃ | [SiteAnnouncementEvent.php](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/SiteAnnouncementEvent.php) | Event Subscriber | `default_frame.twig` ကို ဖမ်းယူပြီး Banner UI နှင့် CSS/JS ကို Hook ထိုးထည့်ခြင်း (Twig Injection) |
| ၄ | [banner.twig](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/Resource/template/banner.twig) | Twig Snippet | Banner ၏ HTML တည်ဆောက်ပုံ (စာသား၊ အိုင်ကွန်၊ Close Button) |
| ၅ | [banner_asset.twig](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/Resource/template/assets/banner_asset.twig) | Twig Asset | Banner ၏ CSS Style နှင့် ပိတ်လိုက်ပါက LocalStorage တွင် မှတ်ထားမည့် Vanilla JS |

---

## ၂။ Twig Injection အလုပ်လုပ်ပုံ သဘောတရား (How Twig Injection Works)

EC-CUBE 4 တွင် Frontend သို့မဟုတ် Admin စာမျက်နှာများကို Render မလုပ်မီ `Eccube\Event\TemplateEvent` ကို Dispatch လုပ်ပေးပါသည်။ 
Event Subscriber က ထို Event ကို ဖမ်းယူပြီး အောက်ပါ အဓိက နည်းလမ်း (၄) မျိုးဖြင့် Template ထဲသို့ UI များ ထည့်သွင်းနိုင်ပါသည်-

1. **`addSnippet($templatePath)`**: HTML အပိုင်းအစ (UI component) များကို လက်ရှိ Page ၏ သတ်မှတ်ထားသော နေရာ (ဥပမာ- body အစ သို့မဟုတ် အဆုံး) ထဲသို့ ပေါင်းစပ်ထည့်သွင်းပေးသည်။
2. **`addAsset($templatePath)`**: CSS (`<link>` / `<style>`) နှင့် JavaScript (`<script>`) များကို HTML `<head>` သို့မဟုတ် footer သို့ inject လုပ်ပေးသည်။
3. **`setParameter($key, $value)`**: Twig ထဲသို့ Controller ဘက်မှ PHP Variable များကို ထပ်ဆောင်းပေးပို့ပေးသည်။
4. **`getSource()` / `setSource($source)`**: မူရင်း Twig source code တစ်ခုလုံးကို string အနေဖြင့် ရယူပြီး regex သို့မဟုတ် string replace ဖြင့် အတိအကျ ပြင်ဆင်ပြီး ပြန်အစားထိုးနိုင်သည်။

---

## ၃။ အဆင့်ဆင့် ရေးသားတည်ဆောက်ပုံ (Step-by-Step Implementation)

### အဆင့် (၁) - Plugin Metadata သတ်မှတ်ခြင်း

ဖိုင်လမ်းကြောင်း: [composer.json](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/composer.json)

EC-CUBE က plugin အဖြစ် အသိအမှတ်ပြုရန် `type: "eccube-plugin"` နှင့် `extra.code` သတ်မှတ်ပေးရပါမည်။

```json
{
  "name": "ec-cube/SiteAnnouncement",
  "version": "1.0.0",
  "description": "Site Announcement Banner Plugin for EC-CUBE 4 (Hello World / Twig Injection Demo)",
  "type": "eccube-plugin",
  "require": {
    "ec-cube/plugin-installer": "^2.0"
  },
  "extra": {
    "code": "SiteAnnouncement"
  }
}
```

---

### အဆင့် (၂) - Plugin Lifecycle စီမံခြင်း

ဖိုင်လမ်းကြောင်း: [PluginManager.php](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/PluginManager.php)

Plugin ကို စတင် install သို့မဟုတ် enable လုပ်သည့်အခါ လိုအပ်ပါက လုပ်ဆောင်ရမည့် Logic များကို ရေးသားသည့် Class ဖြစ်ပါသည်။

```php
<?php

namespace Plugin\SiteAnnouncement;

use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginManager
 * Plugin Lifecycle (install, uninstall, enable, disable) ကို စီမံသော Class
 */
class PluginManager extends AbstractPluginManager
{
    public function install(array $meta, ContainerInterface $container)
    {
        // Plugin install လုပ်ချိန်တွင် လုပ်ဆောင်လိုသည့် အချက်များ (ဥပမာ - default data ထည့်သွင်းခြင်း)
    }

    public function uninstall(array $meta, ContainerInterface $container)
    {
        // Plugin uninstall လုပ်ချိန်တွင် cleanup ပြုလုပ်ခြင်း
    }

    public function enable(array $meta, ContainerInterface $container)
    {
        // Plugin ကို enable ပြုလုပ်ချိန်
    }

    public function disable(array $meta, ContainerInterface $container)
    {
        // Plugin ကို disable ပြုလုပ်ချိန်
    }
}
```

---

### အဆင့် (၃) - Twig Injection Event Subscriber ရေးသားခြင်း

ဖိုင်လမ်းကြောင်း: [SiteAnnouncementEvent.php](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/SiteAnnouncementEvent.php)

ဤဖိုင်သည် Plugin ၏ နှလုံးသည်းပွတ် ဖြစ်ပါသည်။ `default_frame.twig` (Frontend layout frame) ကို render လုပ်ချိန်တွင် Banner HTML နှင့် CSS/JS Asset များကို Hook ထိုးထည့်ပေးပါသည်။

```php
<?php

namespace Plugin\SiteAnnouncement;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SiteAnnouncementEvent
 * Twig Template ထဲသို့ Announcement Banner ကို Inject လုပ်ပေးသော Event Subscriber
 */
class SiteAnnouncementEvent implements EventSubscriberInterface
{
    /**
     * မည်သည့် Template Event များကို နားထောင်မည်ကို သတ်မှတ်ခြင်း
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // Frontend frame template ဖြစ်သည့် default_frame.twig ကို Hook လုပ်ခြင်း
            'default_frame.twig' => 'onDefaultFrameRender',
        ];
    }

    /**
     * default_frame.twig ကို render လုပ်ခါနီးတွင် Banner HTML နှင့် Assets များ ထည့်သွင်းခြင်း
     *
     * @param TemplateEvent $event
     */
    public function onDefaultFrameRender(TemplateEvent $event)
    {
        // ၁။ CSS / JS Assets များကို inject လုပ်ခြင်း
        $event->addAsset('@SiteAnnouncement/assets/banner_asset.twig');

        // ၂။ Banner HTML Snippet ကို inject လုပ်ခြင်း
        $event->addSnippet('@SiteAnnouncement/banner.twig');

        // လိုအပ်ပါက Template ထဲသို့ dynamic variable ပေးပို့နိုင်သည်
        $event->setParameter('announcement_message', '🎉 အထူးပရိုမိုးရှင်း! ယခုအပတ်အတွင်း ဝယ်ယူမှုတိုင်းကို ပို့ဆောင်ခ အခမဲ့ (Free Delivery) ဖြင့် ပို့ဆောင်ပေးနေပါပြီ။');
    }
}
```

---

### အဆင့် (၄) - Banner UI HTML Snippet ရေးသားခြင်း

ဖိုင်လမ်းကြောင်း: [banner.twig](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/Resource/template/banner.twig)

User မျက်စိရှေ့တွင် ပေါ်လာမည့် Banner HTML component ဖြစ်ပါသည်။ ပိတ်နိုင်သော Close Button (`&times;`) ပါဝင်ပါသည်။

```twig
{# Plugin Announcement Banner HTML Snippet #}
<div id="site-announcement-banner" class="site-announcement-bar" role="alert" style="display: none;">
    <div class="site-announcement-container">
        <div class="site-announcement-content">
            <span class="announcement-badge">NOTICE</span>
            <span class="announcement-text">
                {{ announcement_message|default('📢 Welcome to our store! Check out our latest products and deals.') }}
            </span>
        </div>
        <button type="button" class="announcement-close-btn" id="close-announcement-btn" aria-label="Close Announcement">
            &times;
        </button>
    </div>
</div>
```

---

### အဆင့် (၅) - Styling (CSS) နှင့် Dismiss Logic (JS) Assets ရေးသားခြင်း

ဖိုင်လမ်းကြောင်း: [banner_asset.twig](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/Resource/template/assets/banner_asset.twig)

Banner ၏ အမြင်ဒီဇိုင်း လှပစေရန်နှင့် User က Close button နှိပ်လိုက်ပါက Browser ၏ `localStorage` ထဲတွင် မှတ်ထားပြီး ထပ်မပေါ်အောင် ထိန်းချုပ်ပေးသော Logic ဖြစ်ပါသည်။

```twig
{# Announcement Banner CSS Styling & JavaScript #}
<style>
    .site-announcement-bar {
        position: relative;
        width: 100%;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #ffffff;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .site-announcement-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .site-announcement-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-grow: 1;
        justify-content: center;
        text-align: center;
    }

    .announcement-badge {
        background-color: #ff4757;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .announcement-text {
        font-weight: 500;
        line-height: 1.4;
    }

    .announcement-close-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        padding: 0 5px;
        opacity: 0.8;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .announcement-close-btn:hover {
        opacity: 1;
        transform: scale(1.15);
    }

    @media (max-width: 768px) {
        .site-announcement-container {
            padding: 8px 12px;
        }
        .site-announcement-content {
            font-size: 12px;
            flex-direction: column;
            gap: 4px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const banner = document.getElementById('site-announcement-banner');
        const closeBtn = document.getElementById('close-announcement-btn');
        const storageKey = 'ec_site_announcement_closed';

        // အရင်က Close လုပ်ထားခြင်း မရှိမှသာ Banner ကို ပြသမည်
        if (!localStorage.getItem(storageKey)) {
            if (banner) {
                banner.style.display = 'block';
            }
        }

        if (closeBtn && banner) {
            closeBtn.addEventListener('click', function () {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-100%)';
                setTimeout(function () {
                    banner.style.display = 'none';
                    // User ပိတ်လိုက်ကြောင်း localStorage တွင် မှတ်သားခြင်း
                    localStorage.setItem(storageKey, 'true');
                }, 300);
            });
        }
    });
</script>
```

---

## ၄။ Plugin ကို Install ပြုလုပ်ခြင်းနှင့် စမ်းသပ်ခြင်း (Installation & Testing)

ဖိုင်များ အားလုံး ဖန်တီးပြီးပါက EC-CUBE တွင် Plugin အသက်ဝင်စေရန် အောက်ပါ Terminal command များကို Terminal (သို့မဟုတ် Docker Container) ထဲတွင် အစဉ်လိုက် run ပေးရပါမည်-

### အဆင့် (၁) - Plugin ကို Install လုပ်ခြင်း
```bash
bin/console eccube:plugin:install --code=SiteAnnouncement
```

### အဆင့် (၂) - Plugin ကို Enable ပြုလုပ်ခြင်း
```bash
bin/console eccube:plugin:enable --code=SiteAnnouncement
```

### အဆင့် (၃) - Cache ရှင်းထုတ်ခြင်း
```bash
bin/console cache:clear --no-warmup
```

### အဆင့် (၄) - စမ်းသပ်စစ်ဆေးခြင်း
1. Browser တွင် Store Front URL (ဥပမာ- `http://localhost/` သို့မဟုတ် သက်ဆိုင်ရာ Domain) သို့ ဝင်ရောက်ကြည့်ရှုပါ။
2. စာမျက်နှာ၏ ထိပ်ဆုံးတွင် Notice Badge ပါဝင်သော အပြာရောင် Announcement Banner လှပစွာ ပေါ်လာမည် ဖြစ်ပါသည်။
3. ညာဘက်ခြမ်းရှိ `&times;` (Close button) ကို နှိပ်ကြည့်ပါက Banner သည် ချောမွေ့စွာ ပျောက်ကွယ်သွားပြီး Refresh ပြန်လုပ်လျှင်ပင် LocalStorage ကြောင့် ထပ်မံမပေါ်တော့ကြောင်း တွေ့မြင်ရမည် ဖြစ်ပါသည်။
4. ပြန်လည် စမ်းသပ်လိုပါက Browser Console (`F12`) တွင် `localStorage.removeItem('ec_site_announcement_closed')` ဟု ရိုက်ထည့်ပြီး refresh ပြုလုပ်နိုင်ပါသည်။

---

## ၅။ Twig Injection ၏ အဓိက အချက်များ အနှစ်ချုပ် (Key Takeaways)

1. **Core Independent**: Core theme file များ ဖြစ်သော `src/Eccube/Resource/template/default/default_frame.twig` ကို တစ်စက်မှ ပြင်စရာမလိုဘဲ UI component များကို လွတ်လပ်စွာ inject ပြုလုပ်နိုင်ခြင်း။
2. **`@PluginCode` Namespace**: Twig snippet များကို ခေါ်ယူရာတွင် `@SiteAnnouncement/...` ဟု Plugin code ကို `@` ခံ၍ ခေါ်ရပြီး ၎င်းသည် plugin ၏ `Resource/template/` directory သို့ အလိုအလျောက် ညွှန်းဆိုပေးခြင်း။
3. **Target Specific Pages**: `default_frame.twig` အပြင် `Product/detail.twig` (ပစ္စည်းအသေးစိတ်စာမျက်နှာ), `Cart/index.twig` (ဈေးဝယ်ခြင်းတောင်း စာမျက်နှာ) စသည်ဖြင့် မိမိပြသလိုသော စာမျက်နှာ သီးသန့် template များကိုလည်း အလားတူ Event ဖြင့် Hook ထိုးနိုင်ခြင်း။






***********

အဖြေမှာ **User က Close ခလုတ်ကို နှိပ်ထားခြင်း ရှိ/မရှိ** အပေါ် မူတည်ပါသည်-

---

### ၁။ လက်ရှိ ရေးဆွဲထားသော ပုံစံ (Current Behavior)

1. **Close ခလုတ် (`&times;`) ကို မနှိပ်မချင်း:**
   * **ဟုတ်ကဲ့၊ အကြိမ်ကြိမ် Refresh လုပ်တိုင်း အမြဲတမ်း ပေါ်နေပါမည် (YES)**။
   * Page ကို refresh လုပ်လုပ်၊ တခြားစာမျက်နှာများ (Products, Cart, About) သို့ ကူးသွားသွား အပေါ်ဆုံးတွင် အမြဲ မြင်တွေ့နေရပါမည်။

2. **Close ခလုတ် (`&times;`) ကို နှိပ်ပြီး ပိတ်လိုက်ပြီးနောက်:**
   * **Refresh ပြန်လုပ်လျှင် ထပ်မပေါ်တော့ပါ (NO)**။
   * အဘယ်ကြောင့်ဆိုသော် အသုံးပြုသူ (User) က ပိတ်လိုက်ကြောင်း Browser ၏ `localStorage` ထဲတွင် အမြဲတမ်း မှတ်သားသွားသောကြောင့် ဖြစ်ပါသည်။ (Ecommerce ဝဘ်ဆိုဒ်များတွင် ပရိုမိုးရှင်း Banner များကို User စိတ်အနှောင့်အယှက် မဖြစ်စေရန် အသုံးပြုလေ့ရှိသော Standard UX ပုံစံ ဖြစ်ပါသည်)။

---

### ၂။ အကယ်၍ Close နှိပ်ပြီး ပိတ်လိုက်ရင်တောင် Refresh လုပ်တိုင်း အမြဲတမ်း ပြန်ပေါ်စေချင်ပါသလား?

အကယ်၍ စမ်းသပ်မှု ပိုမို လွယ်ကူစေရန် သို့မဟုတ် Close နှိပ်ပြီး ပိတ်လိုက်သော်လည်း **Page refresh လုပ်တိုင်း အမြဲ ပြန်ပေါ်နေစေချင်ပါက** အောက်ပါ နည်းလမ်း (၂) မျိုးထဲမှ ကြိုက်နှစ်သက်ရာကို ရွေးချယ်နိုင်ပါသည်-

#### နည်းလမ်း (A) - Refresh လုပ်တိုင်း အမြဲတမ်း ပြန်ပေါ်စေခြင်း (No Storage)
Close နှိပ်လျှင် ခေတ္တ ပျောက်သွားမည်ဖြစ်သော်လည်း Page ကို Refresh လုပ်လိုက်သည်နှင့် အမြဲတမ်း ချက်ချင်း ပြန်ပေါ်လာမည့် ပုံစံ ဖြစ်ပါသည်။

#### နည်းလမ်း (B) - Session သုံးခြင်း (SessionStorage)
User က လက်ရှိ Browser Tab ကို ဖွင့်ထားစဉ်အတွင်း Close နှိပ်ထားပါက မပေါ်တော့ဘဲ၊ Browser Tab ပိတ်ပြီး အသစ် ပြန်ဖွင့်မှသာ ပြန်ပေါ်လာမည့် ပုံစံ ဖြစ်ပါသည်။

---

👉 **အကယ်၍ Refresh လုပ်တိုင်း အမြဲတမ်း (Always) ပြန်ပေါ်နေစေချင်ပါက** [banner_asset.twig](file:///Users/kyawwaiyan/Documents/my-unique-tech/custom-ec-cube/app/Plugin/SiteAnnouncement/Resource/template/assets/banner_asset.twig) ထဲမှ `localStorage` စစ်ဆေးချက်ကို ဖြုတ်ပေးနိုင်ပါသည်။ ထိုသို့ အမြဲတမ်း ပေါ်နေစေရန် ပြင်ဆင်ပေးစေလိုပါသလားခင်ဗျာ?