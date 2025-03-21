define(['jquery', 'core/str'], function($, str) {
    return {
        init: function(params) {
            let linkUrl = params.linkUrl || '/moodle/local/learningplan/index.php';
            str.get_string('pluginname', 'local_learningplan').done(function(linkLabel) {

                /**
                 * Inserts a navigation link for the learning plan into the Moodle navbar.
                 *
                 * This function searches for the primary navigation container and checks if the
                 * learning plan link already exists. If not, it creates and appends a new
                 * navigation item linking to the specified learning plan URL.
                 *
                 * @returns {boolean} Returns true if the link was successfully inserted, otherwise false.
                 */
                function injectNavbarLink() {
                    let $navContainer = $("div.primary-navigation nav.moremenu ul.nav");

                    if ($navContainer.length && $navContainer.find("#learningplan-nav-item").length === 0) {
                        let $li = $('<li>', {
                            id: 'learningplan-nav-item',
                            class: 'nav-item'
                        });
                        let $a = $('<a>', {
                            href: linkUrl,
                            class: 'nav-link',
                            text: linkLabel
                        });
                        $li.append($a);
                        $navContainer.append($li);
                        return true;
                    }
                    return false;
                }

                // Try to insert the link directly
                if (!injectNavbarLink()) {
                    // If the navigation is not yet rendered, use MutationObserver
                    let observer = new MutationObserver(function(mutations, observerInstance) {
                        if (injectNavbarLink()) {
                            observerInstance.disconnect();
                        }
                    });
                    observer.observe(document.body, {childList: true, subtree: true});
                }
            }).fail(function() {
                console.error('Error loading the language string for the navbar link');
            });
        }
    };
});
