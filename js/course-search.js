(function($){
    $(document).ready(function() {
        // make :contains case-insensitive
        $.expr[":"].contains = $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });

        // on form submission
        $(document).on('submit', 'form[name="courses"]', function(event) {
            event.preventDefault();
            submitForm();
        });

        // on typing and pause
        $(document).on('keyup', 'form[name="courses"] input[name="s"]', function() {
            delay(function(){
                filterCourses();
            }, 300);
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
        var filterCourses = function() {
            var searchString = $('input[name="s"]').val(),
                validCourses = $('.course:contains("' + searchString + '")');

            // add container class
            if (searchString.length > -1) {
                $('.courses-container').addClass('search-active');
            }

            // add course classes
            $('.course.search-active').removeClass('search-active');
            validCourses.addClass('search-active');
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
