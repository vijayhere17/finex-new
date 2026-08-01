<?php

/**
 * Finex compensation plan configuration (new user panel).
 * Old 200-level / booster / reward ladders replaced by Direct ROI + 12-level ROI share.
 */

return [

    // Registration is FREE.
    'registration_fee' => 0,

    // Company receive wallet (slot / deposit USDT goes here)
    'deposit_wallet' => env('DEPOSIT_WALLET', '0x4d02Eda4EE50E55D97974D0C7b8647Ea9853B0aE'),

    // Company payout wallet (admin / auto withdrawal sends FROM this address)
    'withdrawal_wallet' => env('WITHDRAWAL_WALLET', '0xE263603Cd83fa6c125D96B007933b70a667759e7'),

    // Optional: set only on server .env — never commit a private key to git
    'withdrawal_private_key' => env('WITHDRAWAL_PRIVATE_KEY', ''),

    'usdt_contract' => '0x55d398326f99059fF775485246999027B3197955',

    // Fixed sequential slots (Slot 1 .. Slot 12)
    'slot_amounts' => [10, 20, 40, 80, 160, 320, 640, 1280, 2560, 5120, 10240, 20480],

    // Direct ROI % = min(qualified_active_directs * percent_per_direct, max_percent)
    'direct_roi' => [
        'percent_per_direct' => 1,
        'max_percent' => 12,
        'active_status' => 'active',
        'registered_status' => 'registered',
    ],

    // Daily ROI on each active slot
    'daily_roi' => [
        'max_days' => 300,
        'earning_type' => 2,
    ],

    // TEMP testing: run ROI every N minutes using a new fake calendar date each run.
    // Production: set enabled => false (normal cron is once/day at 00:05).
    'test_roi' => [
        'enabled' => env('TEST_ROI_ENABLED', true),
        'interval_minutes' => (int) env('TEST_ROI_INTERVAL_MINUTES', 10),
        // Fake dates start here and advance +1 day per successful test run
        'date_seed' => env('TEST_ROI_DATE_SEED', '2026-01-01'),
    ],

    // Level Income on ROI — % of downline Daily ROI, unlock by active directs
    // 1 direct → Level 1, …, 12 directs → Level 12
    'level_roi' => [
        'max_depth' => 12,
        'earning_type' => 4,
        'ladder' => [
            1 => 12,
            2 => 11,
            3 => 10,
            4 => 9,
            5 => 8,
            6 => 7,
            7 => 6,
            8 => 5,
            9 => 4,
            10 => 3,
            11 => 2,
            12 => 1,
        ],
    ],

    // Legacy keys kept so old code paths that still reference them do not crash.
    // New Daily/Level services use level_roi / direct_roi above.
    'level_income_ladder' => [
        1 => 12, 2 => 11, 3 => 10, 4 => 9, 5 => 8, 6 => 7,
        7 => 6, 8 => 5, 9 => 4, 10 => 3, 11 => 2, 12 => 1,
    ],
    'level_income_max_depth' => 12,
    'level_income_direct_rules' => [
        ['from' => 1, 'to' => 12, 'mode' => 'equal'],
    ],

    // Direct sponsor % on slot buy (OLD plan). Finex uses Direct ROI % + Level ROI only.
    // Keep ladder for reference; set enabled false so no "Direct Sponsor Income" wallet credit.
    'direct_sponsor_enabled' => false,
    'direct_sponsor_levels' => [
        1 => 3,
        2 => 2,
        3 => 1,
    ],

    // Auto Slot Upgrade — accumulate income from 2nd & 3rd directs toward next slot
    // When direct_sponsor_enabled is false, AutoUpgradeService uses slot amount * these % instead.
    'auto_upgrade' => [
        'source_direct_positions' => [2, 3], // 2nd and 3rd activated directs
        'income_earning_type' => 11,         // Auto Upgrade Income to uplines
        'divert_percent_of_slot' => [        // % of activated slot amount → auto-upgrade wallet
            2 => 2,
            3 => 1,
        ],
        'upline_ladder' => [                 // % of upgraded slot amount
            1 => 5,
            2 => 3,
            3 => 2,
        ],
    ],

    // Flat withdrawal fee (new plan)
    'withdrawal_fee_percent' => 10,

    // Legacy withdrawal tiers (unused by new flat fee; kept for reference)
    'withdrawal_charge_tiers' => [
        0 => 10,
    ],

    'capital_withdrawal_charge_percent' => 30,
    'capital_withdrawal_window_months' => 8,

    // Disable legacy salary / reward crons in ProcessDaily
    'legacy_salary_enabled' => false,
    'legacy_reward_cron_enabled' => false,
    'legacy_booster_enabled' => false,
    'legacy_locked_reward_enabled' => false,

    // Cap multipliers (package earning limit = amount * cap)
    'working_cap_multiplier' => 3,
    'non_working_cap_multiplier' => 2,

    // earning_type map
    // 1  = Direct Sponsor Income
    // 2  = Daily ROI
    // 4  = Level ROI Income
    // 11 = Auto Upgrade Income
];
