<?php
/**
 * Plivo Dashboard Widget Module
 */

if (!defined('ABSPATH')) {
    exit;
}

class GF_Plivo_Dashboard_Widget {
    private static $_instance = null;

    public static function get_instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init() {
        if (current_user_can('manage_options')) {
            add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
        }
    }

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'gf_plivo_credits_widget',
            __('SMS Messages Dashboard', 'gravityformsplivo'),
            array($this, 'display_widget')
        );
    }

    public function display_widget() {
        $usage = $this->get_usage_data();
        if (is_wp_error($usage)) {
            echo '<div class="error"><p>' . esc_html($usage->get_error_message()) . '</p></div>';
            return;
        }
        
        // Calculate cost (assuming 0.03 per message)
        $cost_per_sms = 0.03;
        $monthly_cost = $usage['month'] * $cost_per_sms;
        $yearly_cost = $usage['year'] * $cost_per_sms;
        $total_cost = $usage['total'] * $cost_per_sms;
        
        // Get package information
        $perPackage = floor(20 / $cost_per_sms);
        $packages = round($usage['total'] / $perPackage, 2);
        ?>
        <div class="gf-plivo-dashboard">
            <!-- Stats Cards -->
            <div class="gf-plivo-stats">
                <div class="stat-card">
                    <h4><?php esc_html_e('This Month', 'gravityformsplivo'); ?></h4>
                    <div class="stat-number"><?php echo esc_html($usage['month']); ?></div>
                    <div class="stat-meta"><?php echo sprintf(esc_html__('$%.2f', 'gravityformsplivo'), $monthly_cost); ?></div>
                </div>
                
                <div class="stat-card">
                    <h4><?php esc_html_e('This Year', 'gravityformsplivo'); ?></h4>
                    <div class="stat-number"><?php echo esc_html($usage['year']); ?></div>
                    <div class="stat-meta"><?php echo sprintf(esc_html__('$%.2f', 'gravityformsplivo'), $yearly_cost); ?></div>
                </div>
                
                <div class="stat-card">
                    <h4><?php esc_html_e('All Time', 'gravityformsplivo'); ?></h4>
                    <div class="stat-number"><?php echo esc_html($usage['total']); ?></div>
                    <div class="stat-meta"><?php echo sprintf(esc_html__('$%.2f', 'gravityformsplivo'), $total_cost); ?></div>
                </div>
            </div>
            
            <!-- Top Forms -->
            <?php if (!empty($usage['top_forms'])) : ?>
            <div class="gf-plivo-section">
                <h3><?php esc_html_e('Top Forms', 'gravityformsplivo'); ?></h3>
                <ul class="gf-plivo-top-forms">
                    <?php foreach ($usage['top_forms'] as $form) : ?>
                    <li>
                        <span class="form-name"><?php echo esc_html($form['title']); ?></span>
                        <span class="form-count"><?php echo esc_html($form['count']); ?></span>
                        <div class="form-bar">
                            <div class="form-bar-fill" style="width: <?php echo esc_attr($form['percentage']); ?>%"></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Recent Messages -->
            <?php if (!empty($usage['recent_messages'])) : ?>
            <div class="gf-plivo-section">
                <h3><?php esc_html_e('Recent Messages', 'gravityformsplivo'); ?></h3>
                <ul class="gf-plivo-recent-messages">
                    <?php foreach ($usage['recent_messages'] as $message) : ?>
                    <li class="<?php echo esc_attr($message['status']); ?>">
                        <div class="message-meta">
                            <span class="message-phone"><?php echo esc_html($message['phone_number']); ?></span>
                            <span class="message-date"><?php echo esc_html(human_time_diff(strtotime($message['date_created']), current_time('timestamp'))); ?> <?php esc_html_e('ago', 'gravityformsplivo'); ?></span>
                        </div>
                        <div class="message-preview"><?php echo esc_html(wp_trim_words($message['message'], 10)); ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <style type="text/css">
            .gf-plivo-dashboard {
                margin: -12px;
            }
            .gf-plivo-stats {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
                background: #f0f0f1;
                padding: 15px;
            }
            .stat-card {
                text-align: center;
                padding: 15px;
                background: #fff;
                border-radius: 5px;
                flex: 1;
                margin: 0 5px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .stat-card h4 {
                margin: 0 0 10px;
                color: #50575e;
            }
            .stat-number {
                font-size: 24px;
                font-weight: bold;
                color: #2271b1;
                margin-bottom: 5px;
            }
            .stat-meta {
                color: #50575e;
                font-size: 12px;
            }
            .gf-plivo-section {
                padding: 15px;
                border-top: 1px solid #f0f0f1;
            }
            .gf-plivo-section h3 {
                margin-top: 0;
                margin-bottom: 15px;
                color: #1d2327;
                font-size: 14px;
            }
            .gf-plivo-top-forms,
            .gf-plivo-recent-messages {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .gf-plivo-top-forms li {
                margin-bottom: 10px;
                display: flex;
                flex-wrap: wrap;
            }
            .form-name {
                flex: 1;
                font-weight: 500;
            }
            .form-count {
                margin-left: 10px;
                color: #50575e;
            }
            .form-bar {
                flex-basis: 100%;
                height: 6px;
                background: #f0f0f1;
                border-radius: 3px;
                margin-top: 5px;
            }
            .form-bar-fill {
                height: 100%;
                background: #2271b1;
                border-radius: 3px;
            }
            .gf-plivo-recent-messages li {
                margin-bottom: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #f0f0f1;
            }
            .gf-plivo-recent-messages li:last-child {
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }
            .message-meta {
                display: flex;
                justify-content: space-between;
                margin-bottom: 5px;
            }
            .message-phone {
                font-weight: 500;
            }
            .message-date {
                color: #50575e;
                font-size: 12px;
            }
            .message-preview {
                color: #50575e;
                font-size: 13px;
            }
            li.failed .message-phone {
                color: #d63638;
            }
        </style>
        <?php
    }

    private function get_usage_data() {
        // Check for a force refresh parameter
        $force_refresh = isset($_GET['refresh_plivo_stats']) || (defined('DOING_AJAX') && DOING_AJAX);
        
        // Only use cache if not forcing a refresh
        if (!$force_refresh) {
            $cached = get_transient('gf_plivo_usage_data');
            if ($cached !== false) return $cached;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'gf_plivo_messages';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        if (!$table_exists) {
            return new WP_Error('missing_table', __('SMS message tracking table does not exist.', 'gravityformsplivo'));
        }
        
        $usage = array(
            'month' => 0,
            'year' => 0,
            'total' => 0,
            'top_forms' => array(),
            'recent_messages' => array()
        );
        
        // Current month usage
        $month_start = date('Y-m-01 00:00:00');
        $usage['month'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE date_created >= %s",
                $month_start
            )
        );
        
        // Current year usage
        $year_start = date('Y-01-01 00:00:00');
        $usage['year'] = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE date_created >= %s",
                $year_start
            )
        );
        
        // Total usage
        $usage['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Top forms
        $top_forms_data = $wpdb->get_results(
            "SELECT form_id, COUNT(*) as count FROM $table_name GROUP BY form_id ORDER BY count DESC LIMIT 5",
            ARRAY_A
        );
        
        if (!empty($top_forms_data)) {
            // Get form titles
            $form_ids = wp_list_pluck($top_forms_data, 'form_id');
            $forms = GFAPI::get_forms();
            $form_titles = array();
            
            foreach ($forms as $form) {
                $form_titles[$form['id']] = $form['title'];
            }
            
            // Calculate percentages
            $max_count = max(wp_list_pluck($top_forms_data, 'count'));
            
            foreach ($top_forms_data as $form_data) {
                $form_id = $form_data['form_id'];
                $count = $form_data['count'];
                $percentage = ($count / $max_count) * 100;
                
                $usage['top_forms'][] = array(
                    'id' => $form_id,
                    'title' => isset($form_titles[$form_id]) ? $form_titles[$form_id] : sprintf(__('Form #%d', 'gravityformsplivo'), $form_id),
                    'count' => $count,
                    'percentage' => $percentage
                );
            }
        }
        
        // Recent messages
        $usage['recent_messages'] = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY date_created DESC LIMIT 5",
            ARRAY_A
        );
        
        set_transient('gf_plivo_usage_data', $usage, HOUR_IN_SECONDS);
        return $usage;
    }

    public function clear_usage_cache() {
        delete_transient('gf_plivo_usage_data');
    }
    
    /**
     * Get monthly statistics
     * 
     * @return int Number of messages sent in the current month
     */
    public function get_monthly_stats() {
        $data = $this->get_usage_data();
        if (is_wp_error($data)) {
            return 0;
        }
        return $data['month'];
    }
    
    /**
     * Get yearly statistics
     * 
     * @return int Number of messages sent in the current year
     */
    public function get_yearly_stats() {
        $data = $this->get_usage_data();
        if (is_wp_error($data)) {
            return 0;
        }
        return $data['year'];
    }
    
    /**
     * Get total messages count
     * 
     * @return int Total number of messages sent
     */
    public function get_total_messages() {
        $data = $this->get_usage_data();
        if (is_wp_error($data)) {
            return 0;
        }
        return $data['total'];
    }
    
    /**
     * Get top forms by SMS usage
     * 
     * @return array Top forms with their message counts
     */
    public function get_top_forms() {
        $data = $this->get_usage_data();
        if (is_wp_error($data)) {
            return array();
        }
        return $data['top_forms'];
    }
    
    /**
     * Get recent messages
     * 
     * @return array Recent messages
     */
    public function get_recent_messages() {
        $data = $this->get_usage_data();
        if (is_wp_error($data)) {
            return array();
        }
        return $data['recent_messages'];
    }
    
    /**
     * Force refresh the statistics
     * 
     * @return array Fresh usage data
     */
    public function refresh_stats() {
        $this->clear_usage_cache();
        return $this->get_usage_data();
    }
}

function gf_plivo_dashboard() {
    return GF_Plivo_Dashboard_Widget::get_instance();
}

add_action('init', array(gf_plivo_dashboard(), 'init'));