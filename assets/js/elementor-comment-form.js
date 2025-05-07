/**
 * Elementor Comment Form - Frontend JavaScript
 * 
 * Dieses Skript verarbeitet die AJAX-Anfragen des Kommentarformulars.
 */

(function($) {
    'use strict';

    // Allgemeine Formularfunktionen
    var ECF = {
        /**
         * Initialisiert alle Kommentarformulare auf der Seite
         */
        init: function() {
            // Wenn reCAPTCHA aktiviert ist, prüfen, ob die API geladen ist
            if (typeof ecfSettings !== 'undefined' && ecfSettings.recaptcha_enabled) {
                // Wir müssen nicht auf grecaptcha warten, da das Skript bereits im Footer eingebunden ist,
                // aber wir könnten hier zusätzliche Initialisierungen vornehmen, wenn nötig
            }

            // Event-Listener für Reply-Links im Standard-Kommentarbereich
            $(document).on('click', '.comment-reply-link', function(e) {
                e.preventDefault();
                
                var commentId = $(this).data('commentid') || 0;
                var $form = $('.ecf-comment-form');
                
                if ($form.length) {
                    // Kommentar-ID in das Formular übertragen
                    $form.find('input[name="comment_parent"]').val(commentId);
                    
                    // Zum Formular scrollen
                    $('html, body').animate({
                        scrollTop: $form.offset().top - 50
                    }, 500);
                    
                    // Visuelles Feedback, dass es ein Antwort-Kommentar ist
                    $form.addClass('is-reply');
                    
                    // Optional: Hinzufügen eines "Antwort abbrechen"-Links
                    if (!$form.find('.ecf-cancel-reply').length) {
                        var $cancelButton = $('<button>', {
                            type: 'button',
                            class: 'ecf-cancel-reply',
                            text: 'Antwort abbrechen'
                        });
                        
                        $form.find('.ecf-submit-wrapper').prepend($cancelButton);
                        
                        // Event-Handler für den Abbrechen-Button
                        $cancelButton.on('click', function() {
                            $form.removeClass('is-reply');
                            $form.find('input[name="comment_parent"]').val(0);
                            $(this).remove();
                        });
                    }
                }
            });
        },
        
        /**
         * reCAPTCHA-Token generieren
         * @param {function} callback - Funktion, die mit dem Token aufgerufen wird
         */
        getReCAPTCHAToken: function(callback) {
            if (typeof grecaptcha !== 'undefined' && ecfSettings.recaptcha_site_key) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(ecfSettings.recaptcha_site_key, {action: 'comment_submit'})
                    .then(function(token) {
                        if (typeof callback === 'function') {
                            callback(token);
                        }
                    });
                });
            } else {
                // Wenn reCAPTCHA nicht verfügbar ist, trotzdem fortfahren, der Server wird es prüfen
                if (typeof callback === 'function') {
                    callback('');
                }
            }
        },
        
        /**
         * Formular absenden
         * @param {jQuery} $form - jQuery-Objekt des Formulars
         */
        submitForm: function($form) {
            var self = this;
            var $submitButton = $form.find('.ecf-submit-button');
            var $buttonText = $submitButton.find('.ecf-button-text');
            var $loadingIcon = $submitButton.find('.ecf-loading-icon');
            var $messageContainer = $form.closest('.ecf-form-container').find('.ecf-message-container');
            var $successMessage = $messageContainer.find('.ecf-success-message');
            var $errorMessage = $messageContainer.find('.ecf-error-message');
            
            // Formular sperren und Ladezustand anzeigen
            $submitButton.prop('disabled', true).addClass('loading');
            $buttonText.css('opacity', '0.5');
            $loadingIcon.show();
            
            // Meldungen zurücksetzen
            $messageContainer.hide();
            $successMessage.hide().text('');
            $errorMessage.hide().text('');
            
            // Wenn reCAPTCHA aktiviert ist, Token generieren
            if (typeof ecfSettings !== 'undefined' && ecfSettings.recaptcha_enabled) {
                self.getReCAPTCHAToken(function(token) {
                    // Token ins Formular einfügen
                    $form.find('input[name="recaptcha_token"]').val(token);
                    
                    // Formular per AJAX absenden
                    self.sendAjaxRequest($form);
                });
            } else {
                // Ohne reCAPTCHA direkt absenden
                self.sendAjaxRequest($form);
            }
        },
        
        /**
         * AJAX-Anfrage senden
         * @param {jQuery} $form - jQuery-Objekt des Formulars
         */
        sendAjaxRequest: function($form) {
            var $submitButton = $form.find('.ecf-submit-button');
            var $buttonText = $submitButton.find('.ecf-button-text');
            var $loadingIcon = $submitButton.find('.ecf-loading-icon');
            var $messageContainer = $form.closest('.ecf-form-container').find('.ecf-message-container');
            var $successMessage = $messageContainer.find('.ecf-success-message');
            var $errorMessage = $messageContainer.find('.ecf-error-message');
            
            // FormData-Objekt erstellen
            var formData = new FormData($form[0]);
            formData.append('action', 'submit_comment_form');
            
            // AJAX-Anfrage
            $.ajax({
                url: ecfSettings.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Erfolg
                        $successMessage.text(response.data.message);
                        $messageContainer.show();
                        $successMessage.show();
                        
                        // Formular zurücksetzen
                        $form[0].reset();
                        
                        // Wenn es eine Antwort war, zurücksetzen
                        if ($form.hasClass('is-reply')) {
                            $form.removeClass('is-reply');
                            $form.find('input[name="comment_parent"]').val(0);
                            $form.find('.ecf-cancel-reply').remove();
                        }
                        
                        // Optional: Kommentar in die Kommentarliste einfügen
                        if (!response.data.moderation && response.data.comment_html) {
                            var $commentList = $('.comment-list');
                            if ($commentList.length) {
                                if (response.data.comment_parent == 0) {
                                    // Als neuen Hauptkommentar anfügen
                                    $commentList.append(response.data.comment_html);
                                } else {
                                    // Als Antwort auf einen Kommentar einfügen
                                    var $parentLi = $commentList.find('#comment-' + response.data.comment_parent);
                                    if ($parentLi.length) {
                                        var $childrenOl = $parentLi.find('> .children');
                                        if (!$childrenOl.length) {
                                            $childrenOl = $('<ol class="children"></ol>').appendTo($parentLi);
                                        }
                                        $childrenOl.append(response.data.comment_html);
                                    }
                                }
                            }
                        }
                    } else {
                        // Fehler
                        $errorMessage.text(response.data.message);
                        $messageContainer.show();
                        $errorMessage.show();
                    }
                },
                error: function() {
                    // AJAX-Fehler
                    $errorMessage.text('Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
                    $messageContainer.show();
                    $errorMessage.show();
                },
                complete: function() {
                    // Formular entsperren und Ladezustand ausblenden
                    $submitButton.prop('disabled', false).removeClass('loading');
                    $buttonText.css('opacity', '1');
                    $loadingIcon.hide();
                }
            });
        }
    };
    
    // Nach dem Laden des DOM initialisieren
    $(document).ready(function() {
        ECF.init();
        
        // Allgemeiner Event-Handler für alle Kommentarformulare
        $(document).on('submit', '.ecf-comment-form', function(e) {
            e.preventDefault();
            ECF.submitForm($(this));
        });
    });
    
})(jQuery);
