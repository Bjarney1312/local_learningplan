define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            console.log("Lernplan-Menüerweiterung geladen.");

            $(document).on('click', '.section-actions .dropdown-toggle', function() {
                let menu = $(this).closest('.dropdown').find('.dropdown-menu');

                if (!menu.length || menu.find('.learningplan-menu-item').length) {
                    return;
                }

                let sectionId = $(this).closest('.section-actions').data('sectionid');
                let courseId = $('body').attr('class').match(/course-(\d+)/);
                courseId = courseId ? courseId[1] : null;

                if (!sectionId || !courseId) {
                    return;
                }

                // API Call: Prüfen, ob der Abschnitt bereits in der DB ist
                ajax.call([{
                    methodname: 'local_learningplan_get_section_option',
                    args: {
                        sectionid: sectionId,
                        courseid: courseId
                    },
                    done: function(response) {
                        // Button-Titel basierend auf dem Wert setzen
                        let menuItem = $('<a href="#" class="dropdown-item learningplan-menu-item">')
                            .html('<i class="icon fa fa-list-alt fa-fw"></i> ' +
                                (response == 1 ? 'Nicht zum Lernplan hinzufügbar' : 'Zum Lernplan hinzufügbar'))
                            .on('click', function(e) {
                                e.preventDefault();
                                toggleLearningPlanSetting(sectionId, courseId, menuItem);
                            });

                        menu.append(menuItem);
                        console.log("Menüeintrag für Abschnitt " + sectionId + " hinzugefügt.");

                        // **Section-Button ein-/ausblenden**
                        let sectionButton = $('.section-' + sectionId + ' .learningplan-save-button');
                        if (response == 0) {
                            sectionButton.hide();
                        } else {
                            sectionButton.show();
                        }
                    },
                    fail: function(error) {
                        console.error('Fehler beim Abrufen der Einstellung:', error);
                    }
                }]);
            });

            /**
             * @param {number} sectionId
             * @param {number} courseId
             * @param {number} menuItem
             */
            function toggleLearningPlanSetting(sectionId, courseId, menuItem) {
                ajax.call([{
                    methodname: 'local_learningplan_toggle_section_option',
                    args: {
                        sectionid: sectionId,
                        courseid: courseId
                    },
                    done: function(newvalue) {
                        let newText = (newvalue == 1 ? 'Nicht zum Lernplan hinzufügbar' : 'Zum Lernplan hinzufügbar');
                        menuItem.html('<i class="icon fa fa-list-alt fa-fw"></i> ' + newText);
                        console.log("Lernplan-Einstellung für Abschnitt " + sectionId + " geändert!");

                        // **Section-Button aktualisieren**
                        let sectionButton = $('.section-' + sectionId + ' .learningplan-save-button');
                        if (newvalue == 0) {
                            sectionButton.hide();
                        } else {
                            sectionButton.show();
                        }
                    },
                    fail: function(error) {
                        console.error('Fehler beim Umschalten:', error);
                    }
                }]);
            }
        }
    };
});
