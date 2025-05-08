/**
* Plugin Name: Elementor Comment Form Widget
* Description: Ein vollständig stylebares Kommentarformular für Elementor Pro
* Version: 1.0.0
* Author: Entwickelt mit Claude
* Text Domain: elementor-comment-form
*/

// Wenn diese Datei direkt aufgerufen wird, abbrechen
if (!defined('ABSPATH')) {
exit;
}

/**
* Hauptklasse des Plugins
*/
class Elementor_Comment_Form {

/**
* Instance der Plugin-Klasse
* @var Elementor_Comment_Form
*/
private static $instance = null;

/**
* Plugin-Version
* @var string
*/
const VERSION = '1.0.0';

/**
* Konstruktor
*/
public function __construct() {
// Plugin-Konstanten definieren
$this->define_constants();

// Sprachdateien laden
add_action('plugins_loaded', [$this, 'load_textdomain']);

// Admin-Einstellungsseite
add_action('admin_menu', [$this, 'add_admin_menu']);
add_action('admin_init', [$this, 'register_settings']);

// Elementor-Widget registrieren
add_action('elementor/widgets/register', [$this, 'register_widgets']); // Korrigiert für neuere Elementor-Versionen

// Elementor-Abhängigkeit prüfen
add_action('admin_notices', [$this, 'check_elementor_dependency']);

// Frontend-Assets registrieren
add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);

// AJAX-Actionen registrieren
add_action('wp_ajax_submit_comment_form', [$this, 'process_comment_form']);
add_action('wp_ajax_nopriv_submit_comment_form', [$this, 'process_comment_form']);
}

/**
* Plugin-Konstanten definieren
*/
private function define_constants() {
define('ECF_VERSION', self::VERSION);
define('ECF_PLUGIN_FILE', __FILE__);
define('ECF_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ECF_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/**
* Sprachdateien laden
*/
public function load_textdomain() {
load_plugin_textdomain('elementor-comment-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

/**
* Admin-Menü hinzufügen
*/
public function add_admin_menu() {
add_options_page(
__('Elementor Comment Form', 'elementor-comment-form'),
__('Elementor Comment Form', 'elementor-comment-form'),
'manage_options',
'elementor-comment-form',
[$this, 'render_admin_page']
);
}

/**
* Admin-Seite rendern
*/
public function render_admin_page() {
require_once ECF_PLUGIN_PATH . 'includes/admin/settings-page.php';
}

/**
* Einstellungen registrieren
*/
public function register_settings() {
register_setting('ecf_settings', 'ecf_settings');

add_settings_section(
'ecf_settings_section',
__('Kommentarformular-Einstellungen', 'elementor-comment-form'),
[$this, 'settings_section_callback'],
'elementor-comment-form'
);

add_settings_field(
'recaptcha_site_key',
__('reCAPTCHA v3 Site Key', 'elementor-comment-form'),
[$this, 'recaptcha_site_key_callback'],
'elementor-comment-form',
'ecf_settings_section'
);

add_settings_field(
'recaptcha_secret_key',
__('reCAPTCHA v3 Secret Key', 'elementor-comment-form'),
[$this, 'recaptcha_secret_key_callback'],
'elementor-comment-form',
'ecf_settings_section'
);

add_settings_field(
'use_honeypot',
__('Honeypot-Schutz aktivieren', 'elementor-comment-form'),
[$this, 'use_honeypot_callback'],
'elementor-comment-form',
'ecf_settings_section'
);

add_settings_field(
'default_placeholders',
__('Standard-Platzhaltertexte', 'elementor-comment-form'),
[$this, 'default_placeholders_callback'],
'elementor-comment-form',
'ecf_settings_section'
);
}

/**
* Settings Callback-Funktionen
*/
public function settings_section_callback() {
echo '<p>' . __('Konfigurieren Sie die Einstellungen für das Elementor-Kommentarformular-Widget.', 'elementor-comment-form') . '</p>';
}

public function recaptcha_site_key_callback() {
$options = get_option('ecf_settings');
$value = isset($options['recaptcha_site_key']) ? $options['recaptcha_site_key'] : '';
echo '<input type="text" name="ecf_settings[recaptcha_site_key]" value="' . esc_attr($value) . '" class="regular-text">';
echo '<p class="description">' . __('Geben Sie Ihren Google reCAPTCHA v3 Site-Key ein.', 'elementor-comment-form') . '</p>';
}

public function recaptcha_secret_key_callback() {
$options = get_option('ecf_settings');
$value = isset($options['recaptcha_secret_key']) ? $options['recaptcha_secret_key'] : '';
echo '<input type="password" name="ecf_settings[recaptcha_secret_key]" value="' . esc_attr($value) . '" class="regular-text">';
echo '<p class="description">' . __('Geben Sie Ihren Google reCAPTCHA v3 Secret-Key ein.', 'elementor-comment-form') . '</p>';
}

public function use_honeypot_callback() {
$options = get_option('ecf_settings');
$checked = isset($options['use_honeypot']) ? checked($options['use_honeypot'], 1, false) : '';
echo '<input type="checkbox" name="ecf_settings[use_honeypot]" value="1" ' . $checked . '>';
echo '<p class="description">' . __('Aktivieren, um ein Honeypot-Feld zum Spam-Schutz hinzuzufügen.', 'elementor-comment-form') . '</p>';
}

public function default_placeholders_callback() {
$options = get_option('ecf_settings');
$name = isset($options['placeholder_name']) ? $options['placeholder_name'] : __('Name', 'elementor-comment-form');
$email = isset($options['placeholder_email']) ? $options['placeholder_email'] : __('E-Mail', 'elementor-comment-form');
$website = isset($options['placeholder_website']) ? $options['placeholder_website'] : __('Website', 'elementor-comment-form');
$comment = isset($options['placeholder_comment']) ? $options['placeholder_comment'] : __('Kommentar', 'elementor-comment-form');

echo '<p><label>' . __('Name:', 'elementor-comment-form') . '<br>';
                echo '<input type="text" name="ecf_settings[placeholder_name]" value="' . esc_attr($name) . '" class="regular-text"></label></p>';

echo '<p><label>' . __('E-Mail:', 'elementor-comment-form') . '<br>';
                echo '<input type="text" name="ecf_settings[placeholder_email]" value="' . esc_attr($email) . '" class="regular-text"></label></p>';

echo '<p><label>' . __('Website:', 'elementor-comment-form') . '<br>';
                echo '<input type="text" name="ecf_settings[placeholder_website]" value="' . esc_attr($website) . '" class="regular-text"></label></p>';

echo '<p><label>' . __('Kommentar:', 'elementor-comment-form') . '<br>';
                echo '<input type="text" name="ecf_settings[placeholder_comment]" value="' . esc_attr($comment) . '" class="regular-text"></label></p>';
}

/**
* Elementor-Abhängigkeit prüfen
*/
public function check_elementor_dependency() {
if (!did_action('elementor/loaded')) {
$message = sprintf(
__('Das Plugin "Elementor Comment Form Widget" benötigt Elementor um zu funktionieren. Bitte installieren und aktivieren Sie %1$sElementor%2$s zuerst.', 'elementor-comment-form'),
'<a href="' . admin_url('plugin-install.php?s=elementor&tab=search&type=term') . '">',
        '</a>'
);
echo '<div class="notice notice-error">
        <p>' . $message . '</p>
</div>';
} else if (!class_exists('ElementorPro\Plugin')) {
$message = __('Das Plugin "Elementor Comment Form Widget" funktioniert am besten mit Elementor Pro. Es wird empfohlen, Elementor Pro zu installieren.', 'elementor-comment-form');
echo '<div class="notice notice-warning">
        <p>' . $message . '</p>
</div>';
}
}

/**
* Elementor-Widgets registrieren
*/
public function register_widgets() {
// Widget-Datei einbinden
require_once ECF_PLUGIN_PATH . 'includes/widgets/comment-form-widget.php';

// Widget registrieren
\Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_Comment_Form_Widget());
}

/**
* Frontend-Assets registrieren
*/
public function register_frontend_assets() {
// CSS
wp_register_style(
'elementor-comment-form',
ECF_PLUGIN_URL . 'assets/css/elementor-comment-form.css',
[],
ECF_VERSION
);

// JavaScript
wp_register_script(
'elementor-comment-form',
ECF_PLUGIN_URL . 'assets/js/elementor-comment-form.js',
['jquery'],
ECF_VERSION,
true
);

// reCAPTCHA einbinden, wenn konfiguriert
$options = get_option('ecf_settings');
if (!empty($options['recaptcha_site_key'])) {
wp_register_script(
'google-recaptcha',
'https://www.google.com/recaptcha/api.js?render=' . esc_attr($options['recaptcha_site_key']),
[],
null,
true
);
}

// Lokalisierungsscript
wp_localize_script('elementor-comment-form', 'ecfSettings', [
'ajaxurl' => admin_url('admin-ajax.php'),
'nonce' => wp_create_nonce('ecf_ajax_nonce'),
'recaptcha_enabled' => !empty($options['recaptcha_site_key']),
'recaptcha_site_key' => !empty($options['recaptcha_site_key']) ? $options['recaptcha_site_key'] : '',
]);
}

/**
* AJAX-Kommentarformular verarbeiten
*/
public function process_comment_form() {
// Nonce überprüfen
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ecf_ajax_nonce')) {
wp_send_json_error(['message' => __('Sicherheitsüberprüfung fehlgeschlagen. Bitte laden Sie die Seite neu.', 'elementor-comment-form')]);
wp_die();
}

// Honeypot-Schutz überprüfen
$options = get_option('ecf_settings');
if (!empty($options['use_honeypot']) && !empty($_POST['website_hp'])) {
wp_send_json_error(['message' => __('Spam-Verdacht. Bitte versuchen Sie es erneut.', 'elementor-comment-form')]);
wp_die();
}

// reCAPTCHA überprüfen, wenn aktiviert
if (!empty($options['recaptcha_site_key']) && !empty($options['recaptcha_secret_key'])) {
if (empty($_POST['recaptcha_token'])) {
wp_send_json_error(['message' => __('reCAPTCHA-Überprüfung fehlgeschlagen. Bitte laden Sie die Seite neu.', 'elementor-comment-form')]);
wp_die();
}

// reCAPTCHA-Token verifizieren
$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
$response = wp_remote_post($verify_url, [
'body' => [
'secret' => $options['recaptcha_secret_key'],
'response' => $_POST['recaptcha_token'],
'remoteip' => $_SERVER['REMOTE_ADDR']
]
]);

if (is_wp_error($response)) {
wp_send_json_error(['message' => __('reCAPTCHA-Überprüfung fehlgeschlagen. Bitte versuchen Sie es später erneut.', 'elementor-comment-form')]);
wp_die();
}

$body = json_decode(wp_remote_retrieve_body($response), true);
if (empty($body['success']) || $body['success'] !== true || $body['score'] < 0.5) {
        wp_send_json_error(['message'=> __('Spam-Verdacht. Bitte versuchen Sie es erneut.', 'elementor-comment-form')]);
        wp_die();
        }
        }

        // Pflichtfelder überprüfen
        $required_fields = ['author', 'email', 'comment', 'post_id'];
        foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
        wp_send_json_error(['message' => __('Bitte füllen Sie alle Pflichtfelder aus.', 'elementor-comment-form')]);
        wp_die();
        }
        }

        // E-Mail-Format überprüfen
        if (!is_email($_POST['email'])) {
        wp_send_json_error(['message' => __('Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'elementor-comment-form')]);
        wp_die();
        }

        // Datenschutzerklärung überprüfen, falls erforderlich
        if (isset($_POST['privacy_required']) && $_POST['privacy_required'] === 'yes' && empty($_POST['privacy_accepted'])) {
        wp_send_json_error(['message' => __('Bitte stimmen Sie der Datenschutzerklärung zu.', 'elementor-comment-form')]);
        wp_die();
        }

        // Kommentar-Daten vorbereiten
        $comment_data = [
        'comment_post_ID' => intval($_POST['post_id']),
        'comment_author' => sanitize_text_field($_POST['author']),
        'comment_author_email' => sanitize_email($_POST['email']),
        'comment_content' => sanitize_textarea_field($_POST['comment']),
        'comment_author_url' => isset($_POST['url']) ? esc_url_raw($_POST['url']) : '',
        'comment_parent' => isset($_POST['comment_parent']) ? intval($_POST['comment_parent']) : 0,
        'comment_type' => 'comment',
        ];

        // Kommentar einfügen
        $comment_id = wp_insert_comment($comment_data);

        if (!$comment_id || is_wp_error($comment_id)) {
        wp_send_json_error(['message' => __('Beim Speichern des Kommentars ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.', 'elementor-comment-form')]);
        wp_die();
        }

        // Bei erfolgreicher Einfügung
        $comment = get_comment($comment_id);
        $response = [
        'message' => __('Vielen Dank! Ihr Kommentar wurde erfolgreich gespeichert.', 'elementor-comment-form'),
        'comment_id' => $comment_id,
        'comment_parent' => $comment->comment_parent,
        'comment_html' => wp_list_comments([
        'style' => 'ol',
        'short_ping' => true,
        'echo' => false,
        ], [$comment])
        ];

        // Wenn der Kommentar moderiert werden muss
        if ($comment->comment_approved != 1) {
        $response['message'] = __('Vielen Dank! Ihr Kommentar wartet auf Moderation.', 'elementor-comment-form');
        $response['moderation'] = true;
        }

        wp_send_json_success($response);
        wp_die();
        }

        /**
        * Singleton-Instanz erstellen
        */
        public static function get_instance() {
        if (null === self::$instance) {
        self::$instance = new self();
        }
        return self::$instance;
        }
        }

        // Plugin initialisieren
        function elementor_comment_form() {
        return Elementor_Comment_Form::get_instance();
        }

        // Plugin starten
        add_action('plugins_loaded', function() {
        // Elementor geladen?
        if (did_action('elementor/loaded')) {
        elementor_comment_form();
        }
        });