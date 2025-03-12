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


            /**
             * STATUS DROPDOWN
             */

            $('.progress-dropdown').on('change', function() {
                let input = $(this);
                let courseId = input.data('courseid');
                let sectionId = input.data('sectionid');
                let userId = input.data('userid');
                let newProgress = input.val();

                console.log('Neuer Bearbeitungsstand:', newProgress);

                // AJAX-Request zum Speichern des neuen Bearbeitungsstands
                ajax.call([{
                    methodname: 'local_learningplan_update_progress',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId,
                        progress: newProgress
                    },
                    done: function() {
                        console.log('Bearbeitungsstand erfolgreich gespeichert!');
                    },
                    fail: function(error) {
                        console.error('Fehler beim Speichern des Bearbeitungsstands:', error);
                    }
                }]);
            });



            /**
             * SUCHE UND FILTER
             */
            $(document).ready(function() {
                const searchInput = $("#searchInput");
                const filterSelect = $("#filterSelect");

                /**
                 *
                 */
                function filterTable() {
                    let searchText = searchInput.val().toLowerCase();
                    let filterValue = filterSelect.val().toLowerCase();

                    $(".learningplan-section").each(function() {
                        let row = $(this);
                        let courseName = row.find("td").eq(0).text().toLowerCase();
                        let sectionName = row.find("td").eq(1).text().toLowerCase();
                        let progress = row.data("progress").toLowerCase();

                        let matchesSearch = courseName.includes(searchText) || sectionName.includes(searchText);
                        let matchesFilter = filterValue === "" || progress === filterValue;

                        if (matchesSearch && matchesFilter) {
                            row.show();
                        } else {
                            row.hide();
                        }
                    });
                }

                // Event Listener hinzufügen
                searchInput.on("keyup", filterTable);
                filterSelect.on("change", filterTable);
            });


        }
    };
});
