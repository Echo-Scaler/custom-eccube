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
