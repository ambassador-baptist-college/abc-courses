(function($){
    $(document).ready(function() {
        $(document).on('submit', 'form[name="courses"]', function() {
            event.preventDefault();
            var $form = $(this),
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
        });
    });
})(jQuery);
