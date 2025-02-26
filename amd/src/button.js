define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            console.log("JavaScript wird geladen!");


            $('.learningplan-save-button').each(function() {
                var button = $(this);
                var courseId = button.data('course-id');
                var sectionId = button.data('section-id');
                var userId = button.data('user-id');

                // Überprüfen, ob die Section gespeichert ist
                ajax.call([{
                    methodname: 'local_learningplan_check_section_data',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId
                    },
                    done: function(response) {
                        console.log('Server-Antwort learningplan_check: ' + response);
                        if (response) {
                            button.text('Entfernen');
                        } else {
                            button.text('Speichern');
                        }
                    },
                    fail: function(error) {
                        console.error('Fehler:', error);
                    }
                }]);
            });


            $(document).on('click', '.learningplan-save-button', function() {
                var button = $(this);
                var courseId = button.data('course-id');
                var sectionId = button.data('section-id');
                var userId = button.data('user-id');

                var currentText = button.text().trim();
                console.log('currentText:' + currentText);

                if (currentText === 'Speichern') {
                    ajax.call([{
                        methodname: 'local_learningplan_save_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            button.text('Entfernen').prop('disabled', false);
                        },
                        fail: function(error) {
                            console.log('Fehler:', error);
                            alert('Fehler beim Speichern der Daten.');
                        }
                    }]);
                } else if (currentText === 'Entfernen') {
                    ajax.call([{
                        methodname: 'local_learningplan_delete_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            button.text('Speichern').prop('disabled', false);
                        },
                        fail: function(error) {
                            console.log('Fehler:', error);
                            alert('Fehler beim Entfernen der Daten.');
                        }
                    }]);
                }
            });
        }
    };
});
