(function($){
    $(document).ready(function() {
        // on form submission
        $(document).on('submit', 'form[name="courses"]', function(event) {
            event.preventDefault();
            submitForm();
        });

        // on typing and pause
        $(document).on('keyup', 'form[name="courses"] input[name="s"]', function() {
            delay(function(){
                submitForm();
            }, 1000);
        });

        // on category selection
        $(document).on('click', '.cat-filter', function(event) {
            event.preventDefault();
            var courseContainer = '.courses-container',
                clearFilter = '.clear-filters',
                thisCategory = $(this).data('course-category');

            if (thisCategory == 'clear') {
                $('.cat-filter.active:not(' + clearFilter + ')').trigger('click');
                $(clearFilter).removeClass('active');
            } else {
                $('.' + thisCategory).toggleClass('active');
                $(courseContainer).toggleClass(thisCategory + '-active');
                $(clearFilter).addClass('active');
            }
        });

        // handle input
        var submitForm = function(query) {
             var $form = $('form[name="courses"]'),
                $input = $('form[name="courses"] input[name="s"]'),
                query = typeof query !== 'undefined' ? query : $input.val(),
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
                    $container.slideUp();
                },
                success: function(response) {
                    $input.prop('disable', false);
                    $container.html(response).slideDown();
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
