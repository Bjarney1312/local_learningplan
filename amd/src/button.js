define(['jquery', 'core/ajax'], function($, ajax) {
    return {
        init: function(params) {
            let userId = params;
            let courseId = $('body').attr('class').match(/course-(\d+)/);
            courseId = courseId ? courseId[1] : null;

            if (!userId || !courseId) {
                return;
            }

            // Add a button to each section title if not already present
            $('.sectionname').each(function() {
                let sectionTitle = $(this);
                let sectionId = sectionTitle.closest('[id^="section-"]').attr('id').replace('section-', '');

                // API Call: Check whether the learning plan function is active for the section
                ajax.call([{
                    methodname: 'local_learningplan_get_section_option',
                    args: {
                        sectionid: sectionId,
                        courseid: courseId
                    },
                    done: function(response) {

                        if (!sectionTitle.find('.learningplan-save-button').length) {
                            let button = $('<button>')
                                .addClass('btn learningplan-save-button')
                                .attr('data-section-id', sectionId)
                                .attr('data-course-id', courseId)
                                .attr('data-user-id', userId)
                                .html('<i class="icon fa-regular fa-bookmark"></i>')
                                .css({
                                    'border-color': 'transparent',
                                    'margin-right': '5px',
                                    'height': '40px',
                                    'width': '40px',
                                });

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
                                        icon.removeClass('fa-regular').addClass('fa-solid');
                                    }
                                },
                                fail: function(error) {
                                    console.error('Error:', error);
                                }
                            }]);

                            sectionTitle.prepend(button);

                            if (response === 0) {
                                button.hide();
                            }
                        }
                    },
                    fail: function(error) {
                        console.error('Error when retrieving the learning plan option:', error);
                    }
                }]);
            });

            // Aktion für den Klick auf den Learningplan-Button
            $(document).on('click', '.learningplan-save-button', function() {
                let button = $(this);
                let courseId = button.data('course-id');
                let sectionId = button.data('section-id');
                let userId = button.data('user-id');
                let currentIcon = button.find('i');

                if (currentIcon.hasClass('fa-regular')) {
                    ajax.call([{
                        methodname: 'local_learningplan_save_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            currentIcon.removeClass('fa-regular').addClass('fa-solid');
                        },
                        fail: function(error) {
                            console.error('Error:', error);
                        }
                    }]);
                } else if (currentIcon.hasClass('fa-solid')) {
                    ajax.call([{
                        methodname: 'local_learningplan_delete_section_data',
                        args: {
                            courseid: courseId,
                            sectionid: sectionId,
                            userid: userId
                        },
                        done: function() {
                            currentIcon.removeClass('fa-solid').addClass('fa-regular');
                        },
                        fail: function(error) {
                            console.error('Error:', error);
                        }
                    }]);
                }
            });
        }
    };
});
