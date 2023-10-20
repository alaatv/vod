<?php


use App\Models\Activity;

return [

    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => 365,

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the default Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should be implements the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => 'activity_log',

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),


    'log_names' => [
        'batch_transfer' => 'batch_transfer',
        'add_product' => 'add_product_to_order',
        'add_gift' => 'add_gift_to_order',
        'add_coupon' => 'coupon_created',
        'block_content_update' => 'block_content_updated',
        'add_order_by_admin' => 'order_created',
        'pinned' => 'pinned_liveDescription',
        'un_pinned' => 'un_pinned_liveDescription',
        'report' => 'Report',
        'bonyad_user' => 'add_new_user_from_bonyad_ehsan',
        'referral_code' => 'referral_codes_generated',
    ],

    'description' => [
        'edit' => 'updated',
        'create' => 'created',
        'pinned' => 'add_to_pined',
        'un_pinned' => 'remove_from_pinned',
        'created_report' => 'created',
    ]
];
