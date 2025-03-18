define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            console.log("Lernplan-Menüerweiterung geladen.");

            $(document).on('click', '.section-actions .dropdown-toggle', function() {
                let menu = $(this).closest('.dropdown').find('.dropdown-menu');

                if (!menu.length || menu.find('.learningplan-menu-item').length) {
                    return;
                }

                let sectionId = $(this).closest('.section').attr('id')?.replace('section-', '');

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
                                (response == 1 ? 'Lernplan deaktivieren' : 'Lernplan aktivieren'))
                            .on('click', function(e) {
                                e.preventDefault();
                                toggleLearningPlanSetting(sectionId, courseId, menuItem);
                            });

                        menu.append(menuItem);
                        console.log("Menüeintrag für Abschnitt " + sectionId + " hinzugefügt.");

                        // **Section-Button ein-/ausblenden**
                        let sectionButton = $('.section-' + sectionId + ' .learningplan-save-button');
                        if (response === 0) {
                            sectionButton.hide();

                            // **Lernplan-Eintrag entfernen, falls vorhanden**
                            ajax.call([{
                                methodname: 'local_learningplan_delete_section_data_for_all',
                                args: {
                                    courseid: courseId,
                                    sectionid: sectionId
                                },
                                done: function() {
                                    console.log("Lernplan-Eintrag für Abschnitt " + sectionId + " wurde für alle User entfernt.");
                                    sectionButton.find('i').removeClass('fa-trash').addClass('fa-save');
                                    sectionButton.removeClass('btn-danger').addClass('btn-primary');
                                },
                                fail: function(error) {
                                    console.error('Fehler beim Entfernen des Lernplan-Eintrags:', error);
                                    console.log('Section id=' + sectionId + ' Courseid=' + courseId);
                                    console.log('Nachricht aus der Basis Funktion');
                                }
                            }]);
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
                        let newText = (newvalue == 1 ? 'Lernplan deaktivieren' : 'Lernplan aktivieren');
                        menuItem.html('<i class="icon fa fa-list-alt fa-fw"></i> ' + newText);
                        console.log("Lernplan-Einstellung für Abschnitt " + sectionId + " geändert!");

                        // **Section-Button aktualisieren**
                        let sectionButton = $('[data-section-id="' + sectionId + '"].learningplan-save-button');
                        console.log("Suche Button für sectionId:", sectionId);
                        console.log("Gefundene Elemente:", sectionButton.length);

                        if (newvalue === 0) {
                            sectionButton.hide();

                            // **Lernplan-Eintrag entfernen, falls vorhanden**
                            ajax.call([{
                                methodname: 'local_learningplan_delete_section_data_for_all',
                                args: {
                                    courseid: courseId,
                                    sectionid: sectionId
                                },
                                done: function() {
                                    console.log("Lernplan-Eintrag für Abschnitt " + sectionId + " wurde für alle User entfernt.");
                                    sectionButton.find('i').removeClass('fa-trash').addClass('fa-save');
                                    sectionButton.removeClass('btn-danger').addClass('btn-primary');
                                },
                                fail: function(error) {
                                    console.error('Fehler beim Entfernen des Lernplan-Eintrags:', error);
                                    console.log('Section id=' + sectionId + ' Courseid=' + courseId);
                                    console.log('Nachricht aus der Toggle Funktion');
                                }
                            }]);
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
