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
                 * Filtert die Tabelle basierend auf der Eingabe im Suchfeld und der Filterauswahl.
                 */
                function filterTable() {
                    let searchText = searchInput.val().toLowerCase();
                    let filterValue = filterSelect.val().toLowerCase();

                    $(".learningplan-section").each(function() {
                        let row = $(this);
                        let courseName = row.find("td").eq(0).text().toLowerCase();
                        let sectionName = row.find("td").eq(1).text().toLowerCase();
                        let progress = row.data("progress").toLowerCase();

                        // Extrahiere das Datum aus dem timecreated-Feld (falls als dd.mm.yyyy gespeichert)
                        // let timeCreatedText = row.find("td").eq(2).text().trim();
                        // let timeCreated = formatDateForSearch(timeCreatedText);
                        let timeCreated = row.find("td").eq(2).text().trim();

                        // Extrahiere das Datum aus dem processing_deadline-Feld (falls als <input> gespeichert)
                         let deadlineInput = row.find("td").eq(3).find("input");
                         let processingDeadline = deadlineInput.length ? deadlineInput.val() : "";
                         processingDeadline = formatDateForSearch(processingDeadline);

                        // Suchtext mit Datum vergleichen
                        let matchesSearch =
                            courseName.includes(searchText) ||
                            sectionName.includes(searchText) ||
                            timeCreated.includes(searchText) ||
                            processingDeadline.includes(searchText);

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


                /**
                 * Wandelt ein Datum im Format "dd.mm.yyyy" in "yyyy-mm-dd" um.
                 * @param {string }dateString
                 */
                 function formatDateForSearch(dateString) {
                    //let parts = dateString.split(".");
                    let parts = dateString.split("-");
                    if (parts.length === 3) {
                        // return `${parts[2]}-${parts[1]}-${parts[0]}`;
                        return `${parts[2]}.${parts[1]}.${parts[0]}`;
                    }
                    return dateString; // Falls das Format nicht passt, original zurückgeben
                 }
            });

            /**
             * TABELLENSORTIERUNG
             */
            $(document).ready(function() {
                let sortDirection = {}; // Speichert die Sortierrichtung für jede Spalte

                $(".sortable").on("click", function() {
                    let column = $(this).data("column");
                    let tableBody = $("#learningPlanTable tbody");
                    let rows = tableBody.find("tr").toArray();

                    // Wechselnde Sortierrichtung
                    sortDirection[column] = !sortDirection[column];

                    rows.sort(function(rowA, rowB) {
                        let cellA = getCellValue($(rowA), column);
                        let cellB = getCellValue($(rowB), column);

                        return (cellA < cellB ? -1 : 1) * (sortDirection[column] ? 1 : -1);
                    });

                    // Sortierte Zeilen in <tbody> einfügen (nicht <thead>!)
                    $.each(rows, function(index, row) {
                        tableBody.append(row);
                    });

                    // Aktualisiere die Sortierpfeile
                    updateSortIndicators();
                });

                /**
                 * Gibt den Index einer bestimmten Tabellenspalte zurück.
                 *
                 * @param {string} column - Der Name der Spalte, nach der sortiert werden soll.
                 * @returns {number} Der Index der Spalte.
                 */
                function getColumnIndex(column) {
                    let columns = ["coursename", "sectionname", "addeddate", "processing_deadline", "progress"];
                    return columns.indexOf(column);
                }

                /**
                 * Gibt den Index einer bestimmten Tabellenspalte zurück.
                 * @param {string} row - Der Name der Spalte, nach der sortiert werden soll.
                 * @param {string} column - Der Name der Spalte, nach der sortiert werden soll.
                 * @returns {number} Der Index der Spalte.
                 */
                function getCellValue(row, column) {
                    let cell = row.find("td").eq(getColumnIndex(column));

                    if (column === "processing_deadline") {
                        return new Date(cell.find("input").val()).getTime() || 0;
                    }

                    if (column === "progress") {
                        return cell.find("select").val();
                    }

                    if (column === "addeddate") {
                        return new Date(cell.text().trim().split(".").reverse().join("-")).getTime() || 0;
                    }

                    return cell.text().trim();
                }


                /**
                 * Aktualisiert die Pfeile in den Spaltenüberschriften, je nach Sortierrichtung.
                 */
                function updateSortIndicators() {
                    $(".sortable").each(function() {
                        let column = $(this).data("column");
                        if (sortDirection[column]) {
                            $(this).addClass("sorted-asc").removeClass("sorted-desc");
                        } else {
                            $(this).addClass("sorted-desc").removeClass("sorted-asc");
                        }
                    });
                }

            });


        }
    };
});
