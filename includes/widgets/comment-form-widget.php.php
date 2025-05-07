<?php
/**
 * Elementor Comment Form Widget
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor-Widget-Klasse für das Kommentarformular
 */
class Elementor_Comment_Form_Widget extends \Elementor\Widget_Base {

    /**
     * Widget-Name zurückgeben
     * @return string
     */
    public function get_name() {
        return 'elementor_comment_form';
    }

    /**
     * Widget-Titel zurückgeben
     * @return string
     */
    public function get_title() {
        return __('Kommentarformular', 'elementor-comment-form');
    }

    /**
     * Widget-Symbol zurückgeben
     * @return string
     */
    public function get_icon() {
        return 'eicon-comments';
    }

    /**
     * Widget-Kategorien zurückgeben
     * @return array
     */
    public function get_categories() {
        return ['general'];
    }

    /**
     * Widget-Schlüsselwörter zurückgeben
     * @return array
     */
    public function get_keywords() {
        return ['kommentar', 'formular', 'comment', 'form'];
    }

    /**
     * Widget-Skripte registrieren
     */
    public function get_script_depends() {
        $scripts = ['elementor-comment-form'];
        
        // reCAPTCHA hinzufügen, wenn konfiguriert
        $options = get_option('ecf_settings');
        if (!empty($options['recaptcha_site_key'])) {
            $scripts[] = 'google-recaptcha';
        }
        
        return $scripts;
    }

    /**
     * Widget-Stile registrieren
     */
    public function get_style_depends() {
        return ['elementor-comment-form'];
    }

    /**
     * Register Widget Controls - hier definieren wir alle Anpassungsmöglichkeiten
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Formularinhalt', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Standard-Einstellungen aus den Plugin-Optionen laden
        $options = get_option('ecf_settings');
        $default_name = isset($options['placeholder_name']) ? $options['placeholder_name'] : __('Name', 'elementor-comment-form');
        $default_email = isset($options['placeholder_email']) ? $options['placeholder_email'] : __('E-Mail', 'elementor-comment-form');
        $default_website = isset($options['placeholder_website']) ? $options['placeholder_website'] : __('Website', 'elementor-comment-form');
        $default_comment = isset($options['placeholder_comment']) ? $options['placeholder_comment'] : __('Kommentar', 'elementor-comment-form');

        // Feldlabels
        $this->add_control(
            'label_name',
            [
                'label' => __('Label: Name', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Name', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'label_email',
            [
                'label' => __('Label: E-Mail', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('E-Mail', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'label_website',
            [
                'label' => __('Label: Website', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Website', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'label_comment',
            [
                'label' => __('Label: Kommentar', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Kommentar', 'elementor-comment-form'),
            ]
        );

        // Platzhalter
        $this->add_control(
            'placeholder_name',
            [
                'label' => __('Platzhalter: Name', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $default_name,
            ]
        );

        $this->add_control(
            'placeholder_email',
            [
                'label' => __('Platzhalter: E-Mail', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $default_email,
            ]
        );

        $this->add_control(
            'placeholder_website',
            [
                'label' => __('Platzhalter: Website', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $default_website,
            ]
        );

        $this->add_control(
            'placeholder_comment',
            [
                'label' => __('Platzhalter: Kommentar', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => $default_comment,
            ]
        );

        // Submit-Button-Text
        $this->add_control(
            'submit_button_text',
            [
                'label' => __('Button-Text', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Kommentar absenden', 'elementor-comment-form'),
            ]
        );

        // Erfolgsmeldung
        $this->add_control(
            'success_message',
            [
                'label' => __('Erfolgsmeldung', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Vielen Dank! Ihr Kommentar wurde erfolgreich gespeichert.', 'elementor-comment-form'),
            ]
        );

        // Erfolgsmeldung (Moderation)
        $this->add_control(
            'moderation_message',
            [
                'label' => __('Moderationsmeldung', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Vielen Dank! Ihr Kommentar wartet auf Moderation.', 'elementor-comment-form'),
            ]
        );

        // Fehlermeldung
        $this->add_control(
            'error_message',
            [
                'label' => __('Allgemeine Fehlermeldung', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 'elementor-comment-form'),
            ]
        );

        // Datenschutz-Option
        $this->add_control(
            'show_privacy',
            [
                'label' => __('Datenschutz-Checkbox anzeigen', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Ja', 'elementor-comment-form'),
                'label_off' => __('Nein', 'elementor-comment-form'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'privacy_text',
            [
                'label' => __('Datenschutz-Text', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Ich stimme zu, dass meine Daten gespeichert werden. Weitere Informationen finden Sie in unserer Datenschutzerklärung.', 'elementor-comment-form'),
                'condition' => [
                    'show_privacy' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'privacy_link',
            [
                'label' => __('Datenschutz-Seite URL', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://ihre-website.ch/datenschutz/', 'elementor-comment-form'),
                'condition' => [
                    'show_privacy' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Styling-Controls für das Formular
        $this->start_controls_section(
            'section_form_style',
            [
                'label' => __('Formular-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_background_color',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'label' => __('Border', 'elementor-comment-form'),
                'selector' => '{{WRAPPER}} .ecf-form-container',
            ]
        );

        $this->add_responsive_control(
            'form_border_radius',
            [
                'label' => __('Border Radius', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_padding',
            [
                'label' => __('Padding', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Label-Stil
        $this->start_controls_section(
            'section_label_style',
            [
                'label' => __('Label-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .ecf-form-label',
            ]
        );

        $this->add_responsive_control(
            'label_spacing',
            [
                'label' => __('Abstand zum Feld', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Eingabefeld-Stil
        $this->start_controls_section(
            'section_input_style',
            [
                'label' => __('Eingabefeld-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_input_style');

        // Normal-Zustand
        $this->start_controls_tab(
            'tab_input_normal',
            [
                'label' => __('Normal', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'input_background_color',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_text_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'input_typography',
                'selector' => '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'input_border',
                'label' => __('Border', 'elementor-comment-form'),
                'selector' => '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea',
            ]
        );

        $this->add_responsive_control(
            'input_border_radius',
            [
                'label' => __('Border Radius', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label' => __('Padding', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input, {{WRAPPER}} .ecf-form-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover-Zustand
        $this->start_controls_tab(
            'tab_input_hover',
            [
                'label' => __('Hover', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'input_background_color_hover',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:hover, {{WRAPPER}} .ecf-form-textarea:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_text_color_hover',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:hover, {{WRAPPER}} .ecf-form-textarea:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color_hover',
            [
                'label' => __('Border-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:hover, {{WRAPPER}} .ecf-form-textarea:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Fokus-Zustand
        $this->start_controls_tab(
            'tab_input_focus',
            [
                'label' => __('Fokus', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'input_background_color_focus',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:focus, {{WRAPPER}} .ecf-form-textarea:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_text_color_focus',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:focus, {{WRAPPER}} .ecf-form-textarea:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color_focus',
            [
                'label' => __('Border-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-form-input:focus, {{WRAPPER}} .ecf-form-textarea:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'input_box_shadow_focus',
                'label' => __('Box Shadow', 'elementor-comment-form'),
                'selector' => '{{WRAPPER}} .ecf-form-input:focus, {{WRAPPER}} .ecf-form-textarea:focus',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Button-Stil
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Button-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        // Normal-Zustand
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .ecf-submit-button',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'elementor-comment-form'),
                'selector' => '{{WRAPPER}} .ecf-submit-button',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Hover-Zustand
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'button_background_color_hover',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __('Border-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow_hover',
                'label' => __('Box Shadow', 'elementor-comment-form'),
                'selector' => '{{WRAPPER}} .ecf-submit-button:hover',
            ]
        );

        $this->add_control(
            'button_hover_animation',
            [
                'label' => __('Hover-Animation', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        // Aktiv-Zustand (beim Absenden)
        $this->start_controls_tab(
            'tab_button_active',
            [
                'label' => __('Aktiv', 'elementor-comment-form'),
            ]
        );

        $this->add_control(
            'button_background_color_active',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button.loading' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_active',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button.loading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_active',
            [
                'label' => __('Border-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-submit-button.loading' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Meldungen-Stil
        $this->start_controls_section(
            'section_messages_style',
            [
                'label' => __('Meldungen-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Erfolgs-Stil
        $this->add_control(
            'success_message_heading',
            [
                'label' => __('Erfolgsmeldung', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'success_message_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-success-message' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'success_message_background',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-success-message' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'success_message_typography',
                'selector' => '{{WRAPPER}} .ecf-success-message',
            ]
        );

        $this->add_responsive_control(
            'success_message_border_radius',
            [
                'label' => __('Border Radius', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-success-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'success_message_padding',
            [
                'label' => __('Padding', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-success-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Fehler-Stil
        $this->add_control(
            'error_message_heading',
            [
                'label' => __('Fehlermeldung', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'error_message_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-error-message' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'error_message_background',
            [
                'label' => __('Hintergrundfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-error-message' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'error_message_typography',
                'selector' => '{{WRAPPER}} .ecf-error-message',
            ]
        );

        $this->add_responsive_control(
            'error_message_border_radius',
            [
                'label' => __('Border Radius', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-error-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'error_message_padding',
            [
                'label' => __('Padding', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .ecf-error-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Datenschutz-Checkbox-Stil
        $this->start_controls_section(
            'section_privacy_style',
            [
                'label' => __('Datenschutz-Checkbox-Stil', 'elementor-comment-form'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_privacy' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'privacy_text_color',
            [
                'label' => __('Textfarbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-privacy-checkbox-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'privacy_link_color',
            [
                'label' => __('Link-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-privacy-checkbox-label a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'privacy_link_hover_color',
            [
                'label' => __('Link-Hover-Farbe', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecf-privacy-checkbox-label a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'privacy_typography',
                'selector' => '{{WRAPPER}} .ecf-privacy-checkbox-label',
            ]
        );

        $this->add_responsive_control(
            'privacy_spacing',
            [
                'label' => __('Abstand nach oben', 'elementor-comment-form'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ecf-privacy-checkbox-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Widget rendern
     */
    protected function render() {
        // Widget-Einstellungen abrufen
        $settings = $this->get_settings_for_display();
        
        // Post-ID für die Kommentare
        $post_id = get_the_ID();
        
        // Eindeutige Form-ID erstellen
        $form_id = 'ecf-form-' . $this->get_id();
        
        // Start des Ausgabe-Containers
        ?>
        <div class="ecf-form-container">
            <div class="ecf-message-container" style="display: none;">
                <div class="ecf-success-message" style="display: none;"></div>
                <div class="ecf-error-message" style="display: none;"></div>
            </div>
            
            <form id="<?php echo esc_attr($form_id); ?>" class="ecf-comment-form" method="post">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                <input type="hidden" name="comment_parent" value="0">
                
                <?php
                // Honeypot-Feld hinzufügen, wenn aktiviert
                $options = get_option('ecf_settings');
                if (!empty($options['use_honeypot'])) {
                    ?>
                    <div class="ecf-honeypot-field" style="position: absolute; left: -9999px; visibility: hidden; opacity: 0;">
                        <input type="text" name="website_hp" value="">
                    </div>
                    <?php
                }
                ?>
                
                <div class="ecf-form-row">
                    <div class="ecf-form-group">
                        <label for="<?php echo esc_attr($form_id); ?>-author" class="ecf-form-label">
                            <?php echo esc_html($settings['label_name']); ?> <span class="required">*</span>
                        </label>
                        <input type="text" id="<?php echo esc_attr($form_id); ?>-author" name="author" class="ecf-form-input" placeholder="<?php echo esc_attr($settings['placeholder_name']); ?>" required>
                    </div>
                </div>
                
                <div class="ecf-form-row">
                    <div class="ecf-form-group">
                        <label for="<?php echo esc_attr($form_id); ?>-email" class="ecf-form-label">
                            <?php echo esc_html($settings['label_email']); ?> <span class="required">*</span>
                        </label>
                        <input type="email" id="<?php echo esc_attr($form_id); ?>-email" name="email" class="ecf-form-input" placeholder="<?php echo esc_attr($settings['placeholder_email']); ?>" required>
                    </div>
                </div>
                
                <div class="ecf-form-row">
                    <div class="ecf-form-group">
                        <label for="<?php echo esc_attr($form_id); ?>-url" class="ecf-form-label">
                            <?php echo esc_html($settings['label_website']); ?>
                        </label>
                        <input type="url" id="<?php echo esc_attr($form_id); ?>-url" name="url" class="ecf-form-input" placeholder="<?php echo esc_attr($settings['placeholder_website']); ?>">
                    </div>
                </div>
                
                <div class="ecf-form-row">
                    <div class="ecf-form-group">
                        <label for="<?php echo esc_attr($form_id); ?>-comment" class="ecf-form-label">
                            <?php echo esc_html($settings['label_comment']); ?> <span class="required">*</span>
                        </label>
                        <textarea id="<?php echo esc_attr($form_id); ?>-comment" name="comment" class="ecf-form-textarea" placeholder="<?php echo esc_attr($settings['placeholder_comment']); ?>" rows="6" required></textarea>
                    </div>
                </div>
                
                <?php
                // Datenschutz-Checkbox, wenn aktiviert
                if ($settings['show_privacy'] === 'yes') {
                    ?>
                    <div class="ecf-form-row">
                        <div class="ecf-privacy-checkbox-wrapper">
                            <input type="checkbox" id="<?php echo esc_attr($form_id); ?>-privacy" name="privacy_accepted" class="ecf-privacy-checkbox" required>
                            <label for="<?php echo esc_attr($form_id); ?>-privacy" class="ecf-privacy-checkbox-label">
                                <?php
                                // Text mit Link zur Datenschutzerklärung
                                $privacy_text = $settings['privacy_text'];
                                
                                if (!empty($settings['privacy_link']['url'])) {
                                    $privacy_link = $settings['privacy_link']['url'];
                                    $privacy_link_target = $settings['privacy_link']['is_external'] ? ' target="_blank"' : '';
                                    $privacy_link_nofollow = $settings['privacy_link']['nofollow'] ? ' rel="nofollow"' : '';
                                    
                                    // Text mit Link zur Datenschutzerklärung ersetzen
                                    $privacy_text = str_replace(__('Datenschutzerklärung', 'elementor-comment-form'), '<a href="' . esc_url($privacy_link) . '"' . $privacy_link_target . $privacy_link_nofollow . '>' . __('Datenschutzerklärung', 'elementor-comment-form') . '</a>', $privacy_text);
                                }
                                
                                echo $privacy_text;
                                ?>
                            </label>
                            <input type="hidden" name="privacy_required" value="yes">
                        </div>
                    </div>
                    <?php
                }
                ?>
                
                <div class="ecf-form-row">
                    <div class="ecf-submit-wrapper">
                        <button type="submit" class="ecf-submit-button">
                            <span class="ecf-button-text"><?php echo esc_html($settings['submit_button_text']); ?></span>
                            <span class="ecf-loading-icon" style="display: none;">
                                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="4" opacity="0.3"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" stroke-linecap="round">
                                        <animateTransform
                                            attributeName="transform"
                                            attributeType="XML"
                                            type="rotate"
                                            from="0 12 12"
                                            to="360 12 12"
                                            dur="1s"
                                            repeatCount="indefinite"
                                        />
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                
                <?php
                // reCAPTCHA hinzufügen, wenn konfiguriert
                $options = get_option('ecf_settings');
                if (!empty($options['recaptcha_site_key'])) {
                    ?>
                    <input type="hidden" name="recaptcha_token" class="ecf-recaptcha-token" value="">
                    <?php
                }
                ?>
                
                <?php wp_nonce_field('ecf_ajax_nonce', 'nonce'); ?>
            </form>
        </div>
        
        <script>
            (function($) {
                $(document).ready(function() {
                    // Formular-Variablen
                    var $form = $('#<?php echo esc_attr($form_id); ?>');
                    var $submitButton = $form.find('.ecf-submit-button');
                    var $buttonText = $submitButton.find('.ecf-button-text');
                    var $loadingIcon = $submitButton.find('.ecf-loading-icon');
                    var $messageContainer = $form.closest('.ecf-form-container').find('.ecf-message-container');
                    var $successMessage = $messageContainer.find('.ecf-success-message');
                    var $errorMessage = $messageContainer.find('.ecf-error-message');
                    
                    // Formular-Verarbeitung
                    $form.on('submit', function(e) {
                        e.preventDefault();
                        
                        // Formular sperren und Ladezustand anzeigen
                        $submitButton.prop('disabled', true);
                        $buttonText.css('opacity', '0.5');
                        $loadingIcon.show();
                        
                        // Meldungen zurücksetzen
                        $messageContainer.hide();
                        $successMessage.hide().text('');
                        $errorMessage.hide().text('');
                        
                        <?php if (!empty($options['recaptcha_site_key'])) { ?>
                        // reCAPTCHA-Token holen
                        grecaptcha.ready(function() {
                            grecaptcha.execute('<?php echo esc_attr($options['recaptcha_site_key']); ?>', {action: 'comment_submit'})
                            .then(function(token) {
                                $form.find('.ecf-recaptcha-token').val(token);
                                submitForm();
                            })
                            .catch(function(error) {
                                handleError('<?php echo esc_js($settings['error_message']); ?>');
                            });
                        });
                        <?php } else { ?>
                        // Formular direkt absenden
                        submitForm();
                        <?php } ?>
                        
                        // Formular per AJAX absenden
                        function submitForm() {
                            var formData = $form.serialize();
                            
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'submit_comment_form',
                                    ...Object.fromEntries(new FormData($form[0]))
                                },
                                success: function(response) {
                                    if (response.success) {
                                        // Erfolg
                                        if (response.data.moderation) {
                                            $successMessage.text('<?php echo esc_js($settings['moderation_message']); ?>');
                                        } else {
                                            $successMessage.text('<?php echo esc_js($settings['success_message']); ?>');
                                        }
                                        
                                        $messageContainer.show();
                                        $successMessage.show();
                                        
                                        // Formular zurücksetzen
                                        $form[0].reset();
                                    } else {
                                        // Fehler
                                        handleError(response.data.message || '<?php echo esc_js($settings['error_message']); ?>');
                                    }
                                },
                                error: function() {
                                    // AJAX-Fehler
                                    handleError('<?php echo esc_js($settings['error_message']); ?>');
                                },
                                complete: function() {
                                    // Formular entsperren und Ladezustand ausblenden
                                    $submitButton.prop('disabled', false);
                                    $buttonText.css('opacity', '1');
                                    $loadingIcon.hide();
                                }
                            });
                        }
                        
                        // Fehlermeldung anzeigen
                        function handleError(message) {
                            $errorMessage.text(message);
                            $messageContainer.show();
                            $errorMessage.show();
                            
                            // Formular entsperren und Ladezustand ausblenden
                            $submitButton.prop('disabled', false);
                            $buttonText.css('opacity', '1');
                            $loadingIcon.hide();
                        }
                    });
                });
            })(jQuery);
        </script>
        <?php
    }
}