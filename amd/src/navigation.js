define(['jquery'], function($) {
    return {
        init: function(params) {
            let linkUrl = params.linkUrl || '/moodle/local/learningplan/index.php';
            let linkLabel = params.linkLabel || 'Lernplan';

            /**
             * Try to insert the link into the navbar.
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

            // Try to insert the link immediately
            if (!injectNavbarLink()) {
                // If the navigation has not yet been rendered, use a MutationObserver.
                let observer = new MutationObserver(function(mutations, observerInstance) {
                    if (injectNavbarLink()) {
                        observerInstance.disconnect();
                    }
                });
                observer.observe(document.body, {childList: true, subtree: true});
            }
        }
    };
});
