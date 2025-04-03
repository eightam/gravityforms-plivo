<?php
/**
 * Plugin Name: Gravity Forms Plivo Add-On
 * Description: Integrate Gravity Forms with Plivo for SMS notifications
 * Version: 3.0
 * Author: 8am GmbH
 * Text Domain: gravityformsplivo
 * GitHub Plugin URI: https://github.com/eightam/gravityforms-plivo
 * Primary Branch: main
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * Update URI: https://github.com/eightam/gravityforms-plivo
 */

defined('ABSPATH') || die();

include 'class-gf-plivo-dashboard.php';

// Include the update checker
if (!class_exists('YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
    require_once dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
}

// Set up the update checker
if (class_exists('YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory')) {
    $myUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
        'https://github.com/eightam/gravityforms-plivo/',
        __FILE__,
        'gravityforms-plivo'
    );
    
    // Set the branch that contains the stable release
    $myUpdateChecker->setBranch('main');
    
    // Enable release assets
    $myUpdateChecker->getVcsApi()->enableReleaseAssets();
}

// Enable WordPress built-in auto-updates
add_filter('auto_update_plugin', function ($update, $item) {
    // Enable auto-updates for this plugin
    if (isset($item->slug) && $item->slug === 'gravityforms-plivo') {
        return true;
    }
    return $update;
}, 10, 2);

// Add custom update message
add_action('in_plugin_update_message-gravityforms-plivo/gravityforms-plivo.php', function ($plugin_data, $response) {
    if (!empty($response->upgrade_notice)) {
        echo '<br /><span style="color:#900;">' . wp_kses_post($response->upgrade_notice) . '</span>';
    }
}, 10, 2);

GFForms::include_feed_addon_framework();

class GF_Plivo_AddOn extends GFFeedAddOn {
    protected $_version = '3.0';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'gravityformsplivo';
    protected $_path = 'gravityformsplivo/plivo.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Forms Plivo Add-On';
    protected $_short_title = 'Plivo SMS';
    protected $_capabilities_settings_page = 'gravityforms_plivo';
    protected $_capabilities_form_settings = 'gravityforms_plivo';
    protected $_capabilities_uninstall = 'gravityforms_plivo_uninstall';

    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return GF_Plivo_AddOn
     */
    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Handles hooks and loading of language files.
     */
    public function init() {
        parent::init();
        
        // Add metabox to entry detail page
        add_action('gform_entry_detail_sidebar_middle', array($this, 'add_sms_preview_metabox'), 10, 2);
        
        // Handle SMS resend requests
        add_action('admin_init', array($this, 'maybe_resend_sms'));
    }
    
    /**
     * Define feed settings fields.
     *
     * @return array
     */
    public function feed_settings_fields() {
        return array(
            array(
                'title'  => esc_html__('SMS Notification Settings', 'gravityformsplivo'),
                'fields' => array(
                    array(
                        'name'     => 'feedName',
                        'label'    => esc_html__('Name', 'gravityformsplivo'),
                        'type'     => 'text',
                        'required' => true,
                        'class'    => 'medium',
                        'tooltip'  => esc_html__('Enter a name to identify this SMS notification.', 'gravityformsplivo'),
                    ),
                    array(
                        'name'     => 'receiverNumber',
                        'label'    => esc_html__('Receiver Number', 'gravityformsplivo'),
                        'type'     => 'text',
                        'required' => true,
                        'class'    => 'medium',
                        'tooltip'  => esc_html__('Enter the receiving phone number in E.164 format.', 'gravityformsplivo'),
                    ),
                    array(
                        'name'     => 'messageTemplate',
                        'label'    => esc_html__('Message Template', 'gravityformsplivo'),
                        'type'     => 'textarea',
                        'required' => true,
                        'class'    => 'medium merge-tag-support mt-position-right',
                        'tooltip'  => esc_html__('Enter the message template. Use merge tags to include form field values.', 'gravityformsplivo'),
                    ),
                    array(
                        'name'           => 'condition',
                        'label'          => esc_html__('Condition', 'gravityformsplivo'),
                        'type'           => 'feed_condition',
                        'checkbox_label' => esc_html__('Enable Condition', 'gravityformsplivo'),
                        'instructions'   => esc_html__('Send SMS if', 'gravityformsplivo'),
                    ),
                ),
            ),
        );
    }
    
    /**
     * Configures the settings which should be rendered on the plugin settings page.
     *
     * @return array
     */
    public function plugin_settings_fields() {
        return array(
            array(
                'title'  => esc_html__('Plivo Account Settings', 'gravityformsplivo'),
                'fields' => array(
                    array(
                        'name'     => 'auth_id',
                        'label'    => esc_html__('Auth ID', 'gravityformsplivo'),
                        'type'     => 'text',
                        'class'    => 'medium',
                        'required' => true,
                    ),
                    array(
                        'name'     => 'auth_token',
                        'label'    => esc_html__('Auth Token', 'gravityformsplivo'),
                        'type'     => 'text',
                        'class'    => 'medium',
                        'required' => true,
                    ),
                    array(
                        'name'     => 'sender_id',
                        'label'    => esc_html__('Default Sender ID', 'gravityformsplivo'),
                        'type'     => 'text',
                        'class'    => 'medium',
                        'required' => true,
                        'tooltip'  => esc_html__('Your registered Plivo Sender ID', 'gravityformsplivo'),
                    ),
                ),
            ),
        );
    }

    /**
     * Configures which columns should be displayed on the feed list page.
     *
     * @return array
     */
    public function feed_list_columns() {
        return array(
            'feedName'       => esc_html__('Name', 'gravityformsplivo'),
            'receiverNumber' => esc_html__('Receiver', 'gravityformsplivo'),
        );
    }

    /**
     * Format the value to be displayed in the receiverNumber column.
     *
     * @param array $feed The feed being included in the feed list.
     *
     * @return string
     */
    public function get_column_value_receiverNumber($feed) {
        return rgars($feed, 'meta/receiverNumber');
    }

    /**
     * Prevent feeds being listed or created if the API settings aren't valid.
     *
     * @return bool
     */
    public function can_create_feed() {
        $settings = $this->get_plugin_settings();
        return !empty($settings['auth_id']) && !empty($settings['auth_token']) && !empty($settings['sender_id']);
    }
    
    /**
     * Add SMS preview metabox to entry detail sidebar
     *
     * @param array $form Current form object
     * @param array $entry Current entry object
     */
    public function add_sms_preview_metabox($form, $entry) {
        // Ensure we have a valid form ID
        if (empty($form['id'])) {
            return;
        }
        
        // Get all feeds for this form only
        $feeds = $this->get_feeds();
        
        // Filter feeds to only include those for the current form
        $form_feeds = array();
        foreach ($feeds as $feed) {
            if ($feed['form_id'] == $form['id']) {
                $form_feeds[] = $feed;
            }
        }
        
        if (empty($form_feeds)) {
            return; // No feeds configured for this form
        }
        
        // Filter to only include active feeds
        $active_feeds = array();
        foreach ($form_feeds as $feed) {
            if ($this->is_feed_condition_met($feed, $form, $entry)) {
                $active_feeds[] = $feed;
            }
        }
        
        if (empty($active_feeds)) {
            return; // No active feeds for this entry
        }
        
        // Add nonce for resend action
        $resend_nonce = wp_create_nonce('gf_plivo_resend_sms');
        
        ?>
        <div class="postbox">
            <h3 class="hndle">
                <span><?php esc_html_e('SMS Preview', 'gravityformsplivo'); ?></span>
            </h3>
            <div class="inside">
                <p>
                    <label for="gf-plivo-feed-selector">
                        <?php esc_html_e('Select SMS Feed:', 'gravityformsplivo'); ?>
                    </label>
                    <select id="gf-plivo-feed-selector" class="widefat">
                        <?php foreach ($active_feeds as $index => $feed) : ?>
                            <option value="<?php echo esc_attr($index); ?>">
                                <?php echo esc_html($feed['meta']['feedName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                
                <?php foreach ($active_feeds as $index => $feed) : 
                    $message_template = rgars($feed, 'meta/messageTemplate');
                    $receiver_number = rgars($feed, 'meta/receiverNumber');
                    $message = GFCommon::replace_variables($message_template, $form, $entry, false, true, false, 'text');
                    $display = ($index === 0) ? 'block' : 'none';
                    
                    // Create resend URL
                    $resend_url = add_query_arg(
                        array(
                            'page' => 'gf_entries',
                            'view' => 'entry',
                            'id' => $form['id'],
                            'lid' => $entry['id'],
                            'plivo_resend' => $feed['id'],
                            'plivo_nonce' => $resend_nonce
                        ),
                        admin_url('admin.php')
                    );
                    ?>
                    <div class="gf-plivo-sms-preview" id="gf-plivo-preview-<?php echo esc_attr($index); ?>" style="display: <?php echo $display; ?>">
                        <p>
                            <strong><?php esc_html_e('To:', 'gravityformsplivo'); ?></strong> 
                            <?php echo esc_html($receiver_number); ?>
                            <a href="<?php echo esc_url($resend_url); ?>" class="gf-plivo-resend-button" title="<?php esc_attr_e('Resend SMS', 'gravityformsplivo'); ?>">
                                <span class="dashicons dashicons-smartphone"></span>
                            </a>
                        </p>
                        <div class="gf-plivo-sms-message">
                            <div class="gf-plivo-sms-bubble">
                                <?php echo nl2br(esc_html($message)); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <style type="text/css">
                    .gf-plivo-sms-preview {
                        margin-top: 10px;
                    }
                    .gf-plivo-sms-message {
                        margin-top: 10px;
                    }
                    .gf-plivo-sms-bubble {
                        background-color: #DCF8C6;
                        border-radius: 10px;
                        padding: 10px;
                        position: relative;
                        max-width: 100%;
                        word-wrap: break-word;
                    }
                    .gf-plivo-resend-button {
                        display: inline-block;
                        vertical-align: middle;
                        margin-left: 5px;
                        color: #2271b1;
                        text-decoration: none;
                    }
                    .gf-plivo-resend-button:hover {
                        color: #135e96;
                    }
                    .gf-plivo-resend-button .dashicons {
                        font-size: 16px;
                        width: 16px;
                        height: 16px;
                    }
                </style>
                
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('#gf-plivo-feed-selector').on('change', function() {
                            var selectedIndex = $(this).val();
                            $('.gf-plivo-sms-preview').hide();
                            $('#gf-plivo-preview-' + selectedIndex).show();
                        });
                    });
                </script>
            </div>
        </div>
        <?php
    }
    
    /**
     * Check if we need to resend an SMS and process it
     */
    public function maybe_resend_sms() {
        // Check if this is a resend request
        if (!isset($_GET['plivo_resend']) || !isset($_GET['plivo_nonce']) || !isset($_GET['lid']) || !isset($_GET['id'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_GET['plivo_nonce'], 'gf_plivo_resend_sms')) {
            wp_die(__('Security check failed.', 'gravityformsplivo'));
        }
        
        // Get feed, entry and form
        $feed_id = intval($_GET['plivo_resend']);
        $entry_id = intval($_GET['lid']);
        $form_id = intval($_GET['id']);
        
        $feed = $this->get_feed($feed_id);
        $entry = GFAPI::get_entry($entry_id);
        $form = GFAPI::get_form($form_id);
        
        if (!$feed || !$entry || !$form) {
            return;
        }
        
        // Process the feed to resend the SMS
        $this->process_feed($feed, $entry, $form, true);
        
        // Redirect back to the entry detail page
        $redirect_url = add_query_arg(
            array(
                'page' => 'gf_entries',
                'view' => 'entry',
                'id' => $form_id,
                'lid' => $entry_id,
                'plivo_resent' => '1'
            ),
            admin_url('admin.php')
        );
        
        wp_redirect($redirect_url);
        exit;
    }

    /**
     * Get the add-on icon for the add-on/feed settings page.
     *
     * @return string
     */
    public function get_menu_icon() {
        return 'gform-icon--phone';
    }
    
    /**
     * Override the feed list actions to add a duplicate option.
     *
     * @param array $feed The feed being included in the feed list.
     *
     * @return array An array of actions for the current feed.
     */
    public function get_column_value_actions($feed) {
        $feed_id = $feed['id'];
        $form_id = $this->get_current_form_id();
        
        // Get the base actions from the parent
        $actions = parent::get_column_value_actions($feed);
        
        return $actions;
    }
    
    /**
     * Plugin activation handler
     * 
     * Creates the database table for storing SMS messages
     */
    public static function activate() {
        global $wpdb;
        
        // Create the SMS messages table if it doesn't exist
        $table_name = $wpdb->prefix . 'gf_plivo_messages';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            form_id bigint(20) NOT NULL,
            entry_id bigint(20) NOT NULL,
            feed_id bigint(20) NOT NULL,
            to_number varchar(20) NOT NULL,
            message text NOT NULL,
            message_uuid varchar(100) NOT NULL,
            status varchar(20) NOT NULL,
            date_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_entry (form_id, entry_id),
            KEY message_uuid (message_uuid)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Schedule a one-time event to import messages
        if (!wp_next_scheduled('gf_plivo_import_messages')) {
            wp_schedule_single_event(time() + 60, 'gf_plivo_import_messages');
        }
    }
    
    /**
     * Import historical messages from Plivo API
     * 
     * This method fetches messages from Plivo API and stores them in the local database.
     * Due to Plivo API limitations, only messages from the last 90 days can be imported.
     */
    public function import_messages_from_api() {
        global $wpdb;
        
        // Check if we already have messages in the database
        $table_name = $wpdb->prefix . 'gf_plivo_messages';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // If we already have messages, don't import again
        if ($count > 0) {
            return;
        }
        
        // Get Plivo API credentials
        $auth_id = $this->get_plugin_setting('auth_id');
        $auth_token = $this->get_plugin_setting('auth_token');
        
        if (empty($auth_id) || empty($auth_token)) {
            // Log error and exit if credentials are not set
            error_log('Plivo API credentials not set. Cannot import messages.');
            return;
        }
        
        // Initialize Plivo client
        if (!class_exists('RestClient')) {
            require_once('vendor/plivo/plivo-php/src/Plivo/RestClient.php');
        }
        
        try {
            $client = new \Plivo\RestClient($auth_id, $auth_token);
            
            // Get messages from the last 90 days (Plivo API limitation)
            $end_time = new DateTime();
            $start_time = new DateTime();
            $start_time->modify('-90 days');
            
            // Format dates for Plivo API
            $start_time_str = $start_time->format('Y-m-d H:i:s');
            $end_time_str = $end_time->format('Y-m-d H:i:s');
            
            // Get message records from Plivo
            $response = $client->messages->getList(
                [
                    'limit' => 100,
                    'start_time' => $start_time_str,
                    'end_time' => $end_time_str
                ]
            );
            
            $messages = $response->getContent();
            
            // Process each message
            foreach ($messages as $message) {
                // Try to find the form and entry IDs from the message
                $form_id = 0;
                $entry_id = 0;
                $feed_id = 0;
                
                // Look for entries that might match this message
                // This is a best-effort approach since we don't have direct mapping
                $entries = GFAPI::get_entries(
                    0, // All forms
                    [
                        'status' => 'active',
                        'field_filters' => [
                            [
                                'key' => 'date_created',
                                'operator' => '>=',
                                'value' => date('Y-m-d H:i:s', strtotime($message->created_time) - 3600) // 1 hour before message
                            ],
                            [
                                'key' => 'date_created',
                                'operator' => '<=',
                                'value' => date('Y-m-d H:i:s', strtotime($message->created_time) + 3600) // 1 hour after message
                            ]
                        ]
                    ]
                );
                
                // If we found matching entries, use the first one
                if (!empty($entries)) {
                    $entry = $entries[0];
                    $form_id = $entry['form_id'];
                    $entry_id = $entry['id'];
                    
                    // Try to find a matching feed
                    $feeds = $this->get_feeds(['form_id' => $form_id]);
                    if (!empty($feeds)) {
                        $feed_id = $feeds[0]['id'];
                    }
                }
                
                // Insert the message into our database
                $wpdb->insert(
                    $table_name,
                    [
                        'form_id' => $form_id,
                        'entry_id' => $entry_id,
                        'feed_id' => $feed_id,
                        'to_number' => $message->to_number,
                        'message' => $message->message,
                        'message_uuid' => $message->message_uuid,
                        'status' => $message->status,
                        'date_created' => date('Y-m-d H:i:s', strtotime($message->created_time))
                    ]
                );
            }
            
            // Add admin notice about imported messages
            add_action('admin_notices', function() use ($messages) {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p>' . sprintf(
                    __('Successfully imported %d messages from Plivo API.', 'gravityformsplivo'),
                    count($messages)
                ) . '</p>';
                echo '</div>';
            });
            
        } catch (Exception $e) {
            // Log error
            error_log('Error importing messages from Plivo API: ' . $e->getMessage());
            
            // Add admin notice about error
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p>' . sprintf(
                    __('Error importing messages from Plivo API: %s', 'gravityformsplivo'),
                    $e->getMessage()
                ) . '</p>';
                echo '</div>';
            });
        }
    }
    
    /**
     * Track SMS message in the database
     *
     * @param int $form_id Form ID
     * @param int $entry_id Entry ID
     * @param int $feed_id Feed ID
     * @param string $to_number Recipient number
     * @param string $message Message content
     * @param string $message_uuid Plivo message UUID
     * @param string $status Message status
     */
    public function track_sms_message($form_id, $entry_id, $feed_id, $to_number, $message, $message_uuid, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_plivo_messages';
        
        $wpdb->insert(
            $table_name,
            array(
                'form_id' => $form_id,
                'entry_id' => $entry_id,
                'feed_id' => $feed_id,
                'to_number' => $to_number,
                'message' => $message,
                'message_uuid' => $message_uuid,
                'status' => $status,
                'date_created' => current_time('mysql')
            )
        );
    }
    
    /**
     * Update message status in the database
     *
     * @param string $message_uuid Plivo message UUID
     * @param string $status New status
     */
    public function update_message_status($message_uuid, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gf_plivo_messages';
        
        $wpdb->update(
            $table_name,
            array('status' => $status),
            array('message_uuid' => $message_uuid)
        );
    }
}

function gf_plivo() {
    return GF_Plivo_AddOn::get_instance();
}

// Register activation hook
register_activation_hook(__FILE__, array('GF_Plivo_AddOn', 'activate'));

// Register action for importing messages
add_action('gf_plivo_import_messages', array(gf_plivo(), 'import_messages_from_api'));

// Register action for updating message status
add_action('gf_plivo_update_message_status', array(gf_plivo(), 'update_message_status'), 10, 2);

GFAddOn::register('GF_Plivo_AddOn');