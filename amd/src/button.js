define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            console.log("JavaScript wird geladen!");

            $(document).on('click', '.learningplan-save-button', function() {
                var button = $(this);
                var courseId = button.data('course-id');
                var sectionId = button.data('section-id');
                var userId = button.data('user-id');

                // AJAX-Request an Moodle senden
                ajax.call([{
                    methodname: 'local_learningplan_save_section_data',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId
                    },
                    done: function() {
                        button.text('Gespeichert! ✅').prop('disabled', true);
                    },
                    fail: function(error) {
                        console.log('Fehler:', error);
                        alert('Fehler beim Speichern der Daten.');
                    }
                }]);
            });
        }
    };
});
