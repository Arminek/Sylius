/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    'use strict';

    $.fn.extend({
        addToCart: function () {
            var form = $(this);
            var submitButton = $(this).find('button[type="submit"]');
            var validationElement = $(this).find('.sylius-validation-error');
            var redirectUrl = $(this).data('redirect');

            validationElement.hide();

            submitButton.api({
                method: 'POST',
                beforeSend: function (settings) {
                    settings.data = form.serialize();

                    return settings;
                },
                onSuccess: function (response) {
                    console.log(response);
                    location.replace(redirectUrl);
                },
                onFailure: function (response) {
                    validationElement.show();
                    console.log(response);
                    validationElement.html('elo z responsa z messagem od walidacji hop hop');
                }
            });
        }
    });
})( jQuery );
