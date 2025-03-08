define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            $(document).on('click', '.remove-section-btn', function() {
                let button = $(this);
                let courseId = button.data('course-id');
                let sectionId = button.data('section-id');
                let userId = button.data('user-id');

                console.log('Neue Javascript wurde geladen');

                ajax.call([{
                    methodname: 'local_learningplan_delete_section_data',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId
                    },
                    done: function() {
                        console.log('Section aus Datenbank gelöscht');
                        button.closest('.learningplan-section').fadeOut(300, function() {
                            $(this).remove();
                        });
                    },
                    fail: function(error) {
                        console.error('Fehler beim Entfernen der Section:', error);
                    }
                }]);
            });
        }
    };
});
