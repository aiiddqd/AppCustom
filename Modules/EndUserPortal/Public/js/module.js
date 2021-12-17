/**
 * Module's JavaScript.
 */

function eupInitSettings()
{
	$(document).ready(function(){

        summernoteInit('#eup-settings-footer', {
            insertVar: false,
            disableDragAndDrop: true
        });

		$('#eup-show-preview').click(function(e) {
			$('body:first').append($('#eup-widget-code').val());

			e.preventDefault();

			$(this).fadeOut();
		});

		$('#eup-widget-form input:visible,#eup-widget-form select:visible').on('change keyup', function(e) {
			$('#eup-widget-code-wrapper').addClass('hidden');
			$('#eup-widget-save-wrapper').removeClass('hidden');
		});

		$(".eup-colorpicker").colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        }).on('changeColor.colorpicker', function(event) {
            $('#eup-widget-code-wrapper').addClass('hidden');
			$('#eup-widget-save-wrapper').removeClass('hidden');
			 return true;
        }).trigger("change");
	});
}