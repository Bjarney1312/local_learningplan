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
        }
    };
});
