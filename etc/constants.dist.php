<?php
/**
 * Constants
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

define('ROOT_PATH', dirname(__DIR__));

// The site secret to be passed to be allowed to access the page
define('SITE_SECRET', '');
define('ADMIN_SECRET', '');

// The site title to be shown on the page
define('SITE_TITLE_BOOKINGS', '');
define('SITE_TITLE_BOOKINGS_HEADING', '');
define('SITE_TITLE_PUBLIC', '');
define('SITE_TITLE_CHILDS', '');

// Change the robots options
define('SITE_ROBOTS', 'noindex, nofollow');

// The Icons Suggestion
define('ICON_ARRAY_SUGGESTION', [
    'star-of-life' => 'Initial Wert',
    'egg' => 'Frühstück',
    'cookie' => 'Snacks',
    'carrot' => 'Mittagessen',
    'bottle-water' => 'Getränke',
    'bread-slice' => 'Abendessen',
    'business-time' => 'Arbeit',
    'calendar-days' => 'Anwesenheit',
]);

