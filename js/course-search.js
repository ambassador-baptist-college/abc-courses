(function($){
    $(document).ready(function() {
        // on form submission
        $(document).on('submit', 'form[name="courses"]', function() {
            event.preventDefault();
            submitForm();
        });

        // on typing and pause
        $(document).on('keyup', 'form[name="courses"] input[name="s"]', function() {
            delay(function(){
                submitForm();
            }, 1000);
        });

        // handle input
        var submitForm = function() {
             var $form = $('form[name="courses"]'),
                $input = $('form[name="courses"] input[name="s"]'),
                query = $input.val(),
                $container = $('section.courses-container');

            $.ajax({
                type: 'post',
                url: myAjax.ajaxurl,
                data: {
                    action: 'load_course_search_results',
                    query: query
                },
                beforeSend: function() {
                    $input.prop('disable', true);
                    $container.addClass('loading');
                },
                success: function(response) {
                    $input.prop('disable', false);
                    $container.removeClass('loading').html(response);
                }
            });
        }

        // debounce on keyup
        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            }
        })();

    });
})(jQuery);
