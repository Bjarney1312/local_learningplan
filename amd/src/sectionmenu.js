define(['jquery', 'core/ajax', 'core/str'], function($, ajax, str) {
    let langStrings = {};

    /**
     * Loading language strings for chosen language.
     */
    function loadLangStrings() {
        return str.get_strings([
            {key: 'learningplan_activate', component: 'local_learningplan'},
            {key: 'learningplan_deactivate', component: 'local_learningplan'}
        ]).done(function(strings) {
            langStrings.activate = strings[0];
            langStrings.deactivate = strings[1];
        }).fail(function() {
            console.error('Error loading the language strings');
        });
    }
    return {
        init: function() {
            loadLangStrings().done(function() {
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

                    // API Call: Check whether the section is already in the DB
                    ajax.call([{
                        methodname: 'local_learningplan_get_section_option',
                        args: {
                            sectionid: sectionId,
                            courseid: courseId
                        },
                        done: function(response) {
                            let menuItem = $('<a href="#" class="dropdown-item learningplan-menu-item">')
                                .html('<i class="icon fa fa-list-alt fa-fw"></i> ' +
                                    (response === 1 ? langStrings.deactivate : langStrings.activate))
                                .on('click', function(e) {
                                    e.preventDefault();
                                    toggleLearningPlanSetting(sectionId, courseId, menuItem);
                                });

                            menu.append(menuItem);

                            let sectionButton = $('.section-' + sectionId + ' .learningplan-save-button');
                            if (response === 0) {
                                sectionButton.hide();
                                ajax.call([{
                                    methodname: 'local_learningplan_delete_section_data_for_all',
                                    args: {
                                        courseid: courseId,
                                        sectionid: sectionId
                                    },
                                    done: function() {
                                        sectionButton.find('i').removeClass('fa-solid').addClass('fa-regular');
                                    },
                                    fail: function(error) {
                                        console.error('Error when removing the entries', error);
                                    }
                                }]);
                            } else {
                                sectionButton.show();
                            }
                        },
                        fail: function(error) {
                            console.error('Error when retrieving the setting', error);
                        }
                    }]);
                });

                /**
                 * Updates the item menu to enable or disable the option to add the section to the learning plan.
                 * @param {number} sectionId Number of the section in the course
                 * @param {number} courseId ID of the course
                 * @param {number} menuItem Menu Item
                 */
                function toggleLearningPlanSetting(sectionId, courseId, menuItem) {
                    ajax.call([{
                        methodname: 'local_learningplan_toggle_section_option',
                        args: {
                            sectionid: sectionId,
                            courseid: courseId
                        },
                        done: function(newvalue) {
                            let newText = (newvalue === 1 ? langStrings.deactivate : langStrings.activate);
                            menuItem.html('<i class="icon fa fa-list-alt fa-fw"></i> ' + newText);

                            let sectionButton = $('[data-section-id="' + sectionId + '"].learningplan-save-button');
                            if (newvalue === 0) {
                                sectionButton.hide();
                                ajax.call([{
                                    methodname: 'local_learningplan_delete_section_data_for_all',
                                    args: {
                                        courseid: courseId,
                                        sectionid: sectionId
                                    },
                                    done: function() {
                                        sectionButton.find('i').removeClass('fa-solid').addClass('fa-regular');
                                    },
                                    fail: function(error) {
                                        console.error('Error when removing the entries', error);
                                    }
                                }]);
                            } else {
                                sectionButton.show();
                            }
                        },
                        fail: function(error) {
                            console.error('Error when switching the menu item', error);
                        }
                    }]);
                }
            });
        }
    };
});
