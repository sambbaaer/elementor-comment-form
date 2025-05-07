<?php
/**
 * Elementor Comment Form - Admin-Einstellungsseite
 */

// Direkten Zugriff verhindern
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="ecf-admin-container">
        <div class="ecf-admin-main">
            <form method="post" action="options.php">
                <?php
                settings_fields('ecf_settings');
                do_settings_sections('elementor-comment-form');
                submit_button(__('Einstellungen speichern', 'elementor-comment-form'));
                ?>
            </form>
        </div>
        
        <div class="ecf-admin-sidebar">
            <div class="ecf-admin-box">
                <h3><?php _e('Verwendung', 'elementor-comment-form'); ?></h3>
                <p><?php _e('So verwenden Sie dieses Plugin:', 'elementor-comment-form'); ?></p>
                <ol>
                    <li><?php _e('Konfigurieren Sie hier die Plugin-Einstellungen.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Bearbeiten Sie Ihre Seite mit Elementor Pro.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Ziehen Sie das "Kommentarformular"-Widget in den gewünschten Bereich.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Passen Sie das Formular nach Ihren Wünschen an.', 'elementor-comment-form'); ?></li>
                </ol>
            </div>
            
            <div class="ecf-admin-box">
                <h3><?php _e('reCAPTCHA v3 Konfiguration', 'elementor-comment-form'); ?></h3>
                <p><?php _e('Um Google reCAPTCHA v3 zu verwenden:', 'elementor-comment-form'); ?></p>
                <ol>
                    <li><?php _e('Besuchen Sie die <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA-Website</a>.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Erstellen Sie ein neues Projekt vom Typ "reCAPTCHA v3".', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Geben Sie Ihre Domain-Namen in die Liste der erlaubten Domains ein.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('Kopieren Sie den Site-Key und Secret-Key in die Felder links.', 'elementor-comment-form'); ?></li>
                </ol>
            </div>
            
            <div class="ecf-admin-box">
                <h3><?php _e('Honeypot vs. reCAPTCHA', 'elementor-comment-form'); ?></h3>
                <p><?php _e('Sie können eine oder beide Spam-Schutzmaßnahmen aktivieren:', 'elementor-comment-form'); ?></p>
                <ul>
                    <li><?php _e('<strong>Honeypot:</strong> Fügt ein verstecktes Feld hinzu, das nur von Bots ausgefüllt wird. Einfach, aber weniger effektiv bei ausgeklügelten Bots.', 'elementor-comment-form'); ?></li>
                    <li><?php _e('<strong>reCAPTCHA v3:</strong> Eine fortschrittliche Lösung von Google, die Benutzerverhalten analysiert, ohne ein CAPTCHA anzuzeigen. Bietet besseren Schutz, erfordert jedoch ein Google-Konto und kann Bedenken hinsichtlich des Datenschutzes aufwerfen.', 'elementor-comment-form'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .ecf-admin-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }
    
    .ecf-admin-main {
        flex: 1;
        min-width: 500px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 4px;
    }
    
    .ecf-admin-sidebar {
        width: 300px;
    }
    
    .ecf-admin-box {
        background: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .ecf-admin-box h3 {
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    @media (max-width: 960px) {
        .ecf-admin-container {
            flex-direction: column;
        }
        
        .ecf-admin-sidebar {
            width: 100%;
        }
    }
</style>
