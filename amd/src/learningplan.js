define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function() {
            $(document).on('click', '.remove-section-btn', function() {
                let button = $(this);
                let courseId = button.data('courseid');
                let sectionId = button.data('sectionid');
                let userId = button.data('userid');

                // Removes the table row and the database entry when a section is removed from the learning plan
                ajax.call([{
                    methodname: 'local_learningplan_delete_section_data',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId
                    },
                    done: function() {
                        $('tr[data-sectionid="' + sectionId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });
                    },
                    fail: function(error) {
                        console.error('Error when removing the section:', error);
                    }
                }]);
            });

            // Datepicker-Element
            $('.datepicker').on('change', function() {
                let input = $(this);
                let courseId = input.data('courseid');
                let sectionId = input.data('sectionid');
                let userId = input.data('userid');
                let newDeadline = input.val();

                ajax.call([{
                    methodname: 'local_learningplan_update_deadline',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId,
                        deadline: newDeadline
                    },
                    done: function() {
                        // Nothing to do
                    },
                    fail: function(error) {
                        console.error('Error when saving the deadline:', error);
                    }
                }]);
            });

            // Dropdown for progress
            $('.progress-dropdown').on('change', function() {
                let input = $(this);
                let row = input.closest('.learningplan-section');
                let courseId = input.data('courseid');
                let sectionId = input.data('sectionid');
                let userId = input.data('userid');
                let newProgress = input.val();

                ajax.call([{
                    methodname: 'local_learningplan_update_progress',
                    args: {
                        courseid: courseId,
                        sectionid: sectionId,
                        userid: userId,
                        progress: newProgress
                    },
                    done: function() {
                        row.attr('data-progress', newProgress);
                        filterTable();
                    },
                    fail: function(error) {
                        console.error('Error when saving the processing status:', error);
                    }
                }]);
            });

            // Search elements and filter options
            const searchInput = $("#searchInput");
            const filterSelect = $("#filterSelect");

            searchInput.on("keyup", filterTable);
            filterSelect.on("change", filterTable);

            /**
             * Filters the table based on the input in the search field and the filter selection.
             */
            function filterTable() {
                let searchText = searchInput.val().toLowerCase();
                let filterValue = filterSelect.val().toLowerCase();

                $(".learningplan-section").each(function() {
                    let row = $(this);
                    let courseName = row.find("td").eq(0).text().toLowerCase();
                    let sectionName = row.find("td").eq(1).text().toLowerCase();
                    let progress = row.attr("data-progress").toLowerCase();
                    let timeCreated = row.find("td").eq(2).text().trim();

                    let deadlineInput = row.find("td").eq(3).find("input");
                    let processingDeadline = deadlineInput.length ? deadlineInput.val() : "";
                    processingDeadline = formatDateForSearch(processingDeadline);

                    let matchesSearch =
                        courseName.includes(searchText) ||
                        sectionName.includes(searchText) ||
                        timeCreated.includes(searchText) ||
                        processingDeadline.includes(searchText);

                    let matchesFilter = filterValue === "" || progress === filterValue;

                    row.toggle(matchesSearch && matchesFilter);
                });
            }

            /**
             * Converts a date in the format ‘yyyy-mm-dd’ to ‘dd.mm.yyyy’.
             * @param {string} dateString - Date to be formatted
             */
            function formatDateForSearch(dateString) {
                let parts = dateString.split("-");
                if (parts.length === 3) {
                    return `${parts[2]}.${parts[1]}.${parts[0]}`;
                }
                return dateString;
            }

            // Table sorting
            let sortDirection = {};

            $(".sortable").on("click", function() {
                let column = $(this).data("column");
                let tableBody = $("#learningPlanTable tbody");
                let rows = tableBody.find("tr").toArray();

                sortDirection[column] = !sortDirection[column];

                rows.sort(function(rowA, rowB) {
                    let cellA = getCellValue($(rowA), column);
                    let cellB = getCellValue($(rowB), column);

                    return (cellA < cellB ? -1 : 1) * (sortDirection[column] ? 1 : -1);
                });

                $.each(rows, function(index, row) {
                    tableBody.append(row);
                });

                updateSortIndicators();
            });

            /**
             * Returns the index of a specific table column.
             * @param {string} column - The name of the column to be sorted by.
             * @returns {number} - The index of the column.
             */
            function getColumnIndex(column) {
                let columns = ["coursename", "sectionname", "addeddate", "processing_deadline", "progress"];
                return columns.indexOf(column);
            }

            /**
             * Returns the content of a specific table column.
             * @param {string} row - The name of the row to be sorted by.
             * @param {string} column - The name of the column to be sorted by.
             * @returns {number} The index of the column.
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
             * Updates the arrows in the column headings, depending on the sorting direction.
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
        }
    };
});
