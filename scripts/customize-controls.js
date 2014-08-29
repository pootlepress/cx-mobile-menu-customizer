
(function( exports, $ ){
    var api = wp.customize;

    api.FontControl = api.Control.extend({
        ready: function() {
            var control = this,
                picker = this.container.find('.color-picker-hex');

            picker.val( control.settings['font_color']()).wpColorPicker({
                change: function() {
                    control.settings['font_color'].set( picker.wpColorPicker('color') );
                },
                clear: function() {
                    control.settings['font_color'].set( false );
                }
            });
        }
    });

    api.controlConstructor['font'] = api.FontControl;

    api.BorderControl = api.Control.extend({
        ready: function() {
            var control = this,
                picker = this.container.find('.color-picker-hex');

            picker.val( control.settings['border_color']()).wpColorPicker({
                change: function() {
                    control.settings['border_color'].set( picker.wpColorPicker('color') );
                },
                clear: function() {
                    control.settings['border_color'].set( false );
                }
            });
        }
    });

    api.controlConstructor['border'] = api.BorderControl;

    api.ShadowControl = api.Control.extend({
        ready: function() {
            var control = this,
                picker = this.container.find('.color-picker-hex');

            picker.val( control.settings['shadow_color']()).wpColorPicker({
                change: function() {
                    control.settings['shadow_color'].set( picker.wpColorPicker('color') );
                },
                clear: function() {
                    control.settings['shadow_color'].set( false );
                }
            });
        }
    });

    api.controlConstructor['shadow'] = api.ShadowControl;

    $(document).ready(function () {
        $('#customize-controls .customize-control-slider').each(function () {
            var $control = $(this);
            var $currentValueText = $control.find('.current-value-text');
            $control.find('.slider').change(function () {
                $currentValueText.text($(this).val() + $currentValueText.attr('unit'));
            });
            $control.find('.slider').bind('input', function () {
                $currentValueText.text($(this).val() + $currentValueText.attr('unit'));
            });
        });

        $("#customize-controls .customize-control-font").each(function () {

            $(this).find('.sc-font-size-unit').change(function () {
                var $fontControl = $(this).closest('.customize-control-font');
                var $fontSizeInput = $fontControl.find('.sc-font-size-number');

                if ($(this).val() == 'px') {
                    $fontSizeInput.attr('min', '10');
                    $fontSizeInput.attr('max', '100');
                    $fontSizeInput.attr('step', '1');

                    var number = parseFloat($fontSizeInput.val());
                    if (number < 10) {
                        number = 10;
                        $fontSizeInput.val(number).change();
                    } else if (number > 100) {
                        number = 100;
                        $fontSizeInput.val(number).change();
                    }

                } else if ($(this).val() == 'em') {
                    $fontSizeInput.attr('min', '0.1');
                    $fontSizeInput.attr('max', '10');
                    $fontSizeInput.attr('step', '0.1');

                    var number = parseFloat($fontSizeInput.val());
                    if (number < 0.1) {
                        number = 0.1;
                        $fontSizeInput.val(number).change();
                    } else if (number > 10) {
                        number = 10;
                        $fontSizeInput.val(number).change();
                    }
                }
            }).change();
        });

        var secondLayers = [];
        $('#customize-theme-controls .accordion-section[id^=accordion-section-mmm_]').each(function () {

            secondLayers.push($(this));
        });

        var $secondLayerContainer = $('<div class="accordion-container"><div><ul></ul></div></div>')

        var $firstLayerSection = $('<li id="accordion-section-mmm" class="control-section accordion-section">' +
            '<h3 class="accordion-section-title">Mobile Menu</h3>' +
            '<ul class="accordion-section-content"></ul>' +
            '</li>');

        for (var i in secondLayers) {
            $secondLayerContainer.find('> div > ul').append(secondLayers[i]);
        }

        $firstLayerSection.find('> .accordion-section-content').append($secondLayerContainer);

        $('#customize-theme-controls > ul').prepend($firstLayerSection);

        $secondLayerContainer.find('.accordion-section').removeClass('open');
        $secondLayerContainer.find('.accordion-section-content').hide();
    });

})( wp, jQuery );
