
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
//
//        var secondLayers = [];
//        $('#customize-theme-controls .accordion-section[id^=accordion-section-mmm_]').each(function () {
////            var secondLayer = {};
////            var secondLayerId = $(this).attr('id').replace('accordion-section-', '');
////            var secondLayerTitle = $(this).find('> .accordion-section-title').text();
////            var secondLayerControls = $(this).find('> .accordion-section-content > .customize-control');
////
////            secondLayer.id = secondLayerId;
////            secondLayer.title = secondLayerTitle;
////            secondLayer.controls = secondLayerControls;
//
//            secondLayers.push($(this));
//        });
//
//        var $secondLayerContainer = $('<div class="accordion-container"><div><ul></ul></div></div>')
//
//        var $firstLayerSection = $('<li id="accordion-section-mmm" class="control-section accordion-section">' +
//            '<h3 class="accordion-section-title">Mobile Menu</h3>' +
//            '<ul class="accordion-section-content"></ul>' +
//            '</li>');
//
//        for (var i in secondLayers) {
//            $secondLayerContainer.find('> div > ul').append(secondLayers[i]);
//        }
//
//        $firstLayerSection.find('> .accordion-section-content').append($secondLayerContainer);
//
//        $('#customize-theme-controls > ul').prepend($firstLayerSection);
//
//        $secondLayerContainer.find('.accordion-section').removeClass('open');
//        $secondLayerContainer.find('.accordion-section-content').hide();
//

        // reset
        var $resetRow = $("<div class='reset-row'></div>");
        var $resetButton = $('<input class="reset-button button" type="button" value="Reset to Default" />');

        $resetButton.click(function () {

            $('#accordion-panel-mmm_panel').find('.customize-control').each(function () {

                if ($(this).hasClass('customize-control-checkbox')) {
                    resetCheckBoxControl($(this));
                } else if ($(this).hasClass('customize-control-select')) {
                    resetDropDownControl($(this));
                } else if ($(this).hasClass('customize-control-font')) {
                    resetFontControl($(this));
                } else if ($(this).hasClass('customize-control-color')) {
                    resetColorControl($(this));
                } else if ($(this).hasClass('customize-control-border')) {
                    resetBorderControl($(this));
                } else if ($(this).hasClass('customize-control-padding')) {
                    resetPaddingControl($(this));
                } else if ($(this).hasClass('customize-control-shadow')) {
                    resetShadowControl($(this));
                } else if ($(this).hasClass('customize-control-text')) {
                    resetTextControl($(this));
                } else if ($(this).hasClass('customize-control-image')) {
                    resetImageControl($(this));
                }
            });

            // trigger change for an arbitrary control, to make customizer reload preview
            $('#accordion-panel-mmm_panel').find('.customize-control select, .customize-control input').change();
        });

        function resetImageControl($imageControl) {
            $imageControl.find('.remove-button').click();
        }

        function resetTextControl($textControl) {
            var defaultValue = $textControl.find('input').attr('data-default-value');
            if (typeof defaultValue == 'undefined' || defaultValue == null) {
                defaultValue = '';
            }
            $textControl.find('input').val(defaultValue);
        }

        function resetCheckBoxControl($checkBoxControl) {
            var defaultValue = $checkBoxControl.find('input').attr('data-default-value');
            if (defaultValue == '1') {
                if (!$checkBoxControl.find('input').prop('checked')) {
                    $checkBoxControl.find('input').click();
                }
            } else {
                if ($checkBoxControl.find('input').prop('checked')) {
                    $checkBoxControl.find('input').click();
                }
            }
        }

        function resetDropDownControl($dropDownControl) {
            var defaultValue = $dropDownControl.find('select').attr('data-default-value');
            $dropDownControl.find('select').val(defaultValue);
        }

        function resetFontControl($fontControl) {
            $fontControl.find('.sc-font-family-list').val();


//            var defaultFamily = $fontControl.find('.sc-font-family-list').attr('data-default-value');
//            defaultFamily = defaultFamily.split(', ')[0].replace('*', '').trim();
//            $fontControl.find('.sc-font-family-list option').each(function () {
//                var optionFamily = $(this).attr('value');
//                var optionFamilyArr = optionFamily.split(', ');
//
//                for (var i in optionFamilyArr) {
//                    var opt = optionFamilyArr[i];
//                    var f = opt.replace('"', '');
//
////                    if (f == 'sans-serif' || f == 'serif') {
////                        continue;
////                    }
//
//                    if (f == defaultFamily) {
//                        $fontControl.find('.sc-font-family-list').val(optionFamily);
//                        break;
//                    }
//                }
//            });

            // just set it to empty value, server will convert empty value to default value
            $fontControl.find('.sc-font-family-list').val('');

            $fontControl.find('.sc-font-weight-style-list').val($fontControl.find('.sc-font-weight-style-list').attr('data-default-value')); // default font weight and style

            if ($fontControl.find('.wp-picker-default').length > 0) {
                $fontControl.find('.wp-picker-default').click(); // reset to default color
            } else if ($fontControl.find('.wp-picker-clear').length > 0) {
                $fontControl.find('.wp-picker-clear').click(); // reset to no color
            } else {

            }

            $fontControl.find(".sc-font-size-number").val(parseInt($fontControl.find(".sc-font-size-number").attr('default')));

            var defaultUnit = $fontControl.find('.sc-font-size-unit').attr('data-default-value');
            $fontControl.find('.sc-font-size-unit').val(defaultUnit);
        }

        function resetColorControl($colorControl) {
            if ($colorControl.find('.wp-picker-default').length > 0) {
                $colorControl.find('.wp-picker-default').click(); // reset to default color
            } else if ($colorControl.find('.wp-picker-clear').length > 0) {
                $colorControl.find('.wp-picker-clear').click(); // reset to no color
            } else {

            }

        }

        function resetBorderControl($borderControl) {
            var defaultValue = $borderControl.find('.border-width-number').attr('default');
            $borderControl.find('.border-width-number').val(defaultValue);

            var defaultStyle = $borderControl.find('.border-style-list').attr('data-default-value');
            $borderControl.find('.border-style-list').val(defaultStyle);

            if ($borderControl.find('.wp-picker-default').length > 0) {
                $borderControl.find('.wp-picker-default').click(); // reset to default color
            } else if ($borderControl.find('.wp-picker-clear').length > 0) {
                $borderControl.find('.wp-picker-clear').click(); // reset to no color
            } else {

            }
        }

        function resetPaddingControl($paddingControl) {
            var width1Default = $paddingControl.find('.width-1-number').attr('default');
            $paddingControl.find('.width-1-number').val(width1Default);

            var width2Default = $paddingControl.find('.width-2-number').attr('default');
            $paddingControl.find('.width-2-number').val(width2Default);
        }

        function resetShadowControl($shadowControl) {
            var defaultWidth = $shadowControl.find('.pp-shadow-width-number').attr('default');
            $shadowControl.find('.pp-shadow-width-number').val(defaultWidth);

            if ($shadowControl.find('.wp-picker-default').length > 0) {
                $shadowControl.find('.wp-picker-default').click(); // reset to default color
            } else if ($shadowControl.find('.wp-picker-clear').length > 0) {
                $shadowControl.find('.wp-picker-clear').click(); // reset to no color
            } else {

            }

            var defaultBlur = $shadowControl.find('.pp-shadow-blur-number').attr('default');
            $shadowControl.find('.pp-shadow-blur-number').val(defaultBlur);
        }

//        function resetSliderControl(controlName) {
//            var $slider = $('#customize-control-' + controlName + ' .slider');
//            $slider.val(parseInt($slider.attr('default'))).change();
//        }

        $resetRow.append($resetButton);
        $('#customize-control-mmm_reset').append($resetRow);
    });

})( wp, jQuery );
