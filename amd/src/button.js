define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function(params) {
            console.log("JavaScript wird geladen!");
            var userId = params;
            var courseId = $('body').attr('class').match(/course-(\d+)/);
            courseId = courseId ? courseId[1] : null;

            if (!userId || !courseId) {
                console.error("Fehler: userId oder courseId fehlen!");
                return;
            }

            // Füge Buttons zu jedem Section-Titel hinzu, falls noch nicht vorhanden
            $('.sectionname').each(function() {
                var sectionTitle = $(this);
                var sectionId = sectionTitle.closest('[id^="section-"]').attr('id').replace('section-', '');

                if (!sectionTitle.find('.learningplan-save-button').length) {
                    let button = $('<button>')
                        .addClass('btn btn-icon btn-primary learningplan-save-button')
                        .attr('data-section-id', sectionId)
                        .attr('data-course-id', courseId)
                        .attr('data-user-id', userId)
                        .html('<i class="icon fa fa-save"></i>');

                    ajax.call([{
                        methodname: 'local_learningplan_check_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function(response) {
                            let icon = button.find('i');
                            if (response) {
                                if (icon.hasClass('fa-save')) {
                                    icon.removeClass('fa-save').addClass('fa-trash');
                                    button.removeClass('btn-primary').addClass('btn-danger');
                                }
                            } else {
                                if (icon.hasClass('fa-trash')) {
                                    icon.removeClass('fa-trash').addClass('fa-save');
                                    button.removeClass('btn-danger').addClass('btn-primary');
                                }
                            }
                        },
                        fail: function(error) {
                            console.error('Fehler:', error);
                        }
                    }]);

                    sectionTitle.prepend(button);
                }
            });

            $(document).on('click', '.learningplan-save-button', function() {
                var button = $(this);
                var courseId = button.data('course-id');
                var sectionId = button.data('section-id');
                var userId = button.data('user-id');
                var currentIcon = button.find('i');

                if (currentIcon.hasClass('fa-save')) {
                    ajax.call([{
                        methodname: 'local_learningplan_save_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            currentIcon.removeClass('fa-save').addClass('fa-trash');
                            button.removeClass('btn-primary').addClass('btn-danger');
                        },
                        fail: function(error) {
                            console.log('Fehler:', error);
                            alert('Fehler beim Speichern der Daten.');
                        }
                    }]);
                } else if (currentIcon.hasClass('fa-trash')) {
                    ajax.call([{
                        methodname: 'local_learningplan_delete_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            currentIcon.removeClass('fa-trash').addClass('fa-save');
                            button.removeClass('btn-danger').addClass('btn-primary');
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
