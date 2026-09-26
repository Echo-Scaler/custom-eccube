<?php

/*
 * User History Navigation Provider
 * Adds the "User History Logs" menu item into the Admin sidebar under Settings > System Settings.
 */

namespace Customize\Nav;

use Eccube\Common\EccubeNav;

class UserHistoryNav implements EccubeNav
{
    public static function getNav(): array
    {
        return [
            // Top-level "Activity & Logs" sidebar menu
            'log' => [
                'name' => 'admin.log.log_management',
                'icon' => 'fa-history',
                'children' => [
                    'user_history' => [
                        'name' => 'admin.setting.system.user_history_log',
                        'url' => 'admin_setting_system_user_history',
                    ],
                    'system_log' => [
                        'name' => 'admin.setting.system.log_display',
                        'url' => 'admin_setting_system_log',
                    ],
                    'login_history' => [
                        'name' => 'admin.setting.system.login_history',
                        'url' => 'admin_setting_system_login_history',
                    ],
                ],
            ],
            // Also keep under Settings > System Settings for users browsing through System Settings
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
