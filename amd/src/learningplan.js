define(['jquery', 'core/ajax'], function($, ajax) {
 return {
        init: function() {
            $(document).on('click', '.remove-section-btn', function() {
                let button = $(this);
                let courseId = button.data('courseid');
                let sectionId = button.data('sectionid');
                let userId = button.data('userid');

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
                        // Die gesamte Tabellenzeile entfernen
                        $('tr[data-sectionid="' + sectionId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });
                    },
                    fail: function(error) {
                        console.error('Fehler beim Entfernen der Section:', error);
                    }
                }]);
            });

            console.log('Datepicker wird initialisiert');

            /**
             * DATEPICKER INITIALISIEREN
             */
// Event Listener für das Ändern des Datums
            $(document).ready(function() {
                // Event Listener für das Ändern des Datums
                $('.datepicker').on('change', function() {
                    let input = $(this);
                    let courseId = input.data('courseid');
                    let sectionId = input.data('sectionid');
                    let userId = input.data('userid');
                    let newDeadline = input.val(); // Holt das Datum im YYYY-MM-DD Format

                    console.log('Neues Datum gewählt:', newDeadline);

                    // AJAX-Request zum Speichern des neuen Datums
                    ajax.call([{
                        methodname: 'local_learningplan_update_deadline',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId,
                            deadline: newDeadline
                        },
                        done: function() {
                            console.log('Deadline erfolgreich gespeichert!');
                        },
                        fail: function(error) {
                            console.error('Fehler beim Speichern der Deadline:', error);
                        }
                    }]);
                });
            });


        }
    };
});
