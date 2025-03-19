define(['jquery'], function($) {
    console.log("navbar.js loaded.");
    return {
        init: function(params) {
            console.log("Navbar injection module loaded.");
            // Standardwerte – können über params überschrieben werden.
            var linkUrl = params.linkUrl || '/moodle/local/learningplan/index.php';
            var linkLabel = params.linkLabel || 'Lernplan';

            // Funktion, die versucht, den Link in die Navbar einzufügen.
            /**
             *
             */
            function injectNavbarLink() {
                // In Boost befindet sich die Primary Navigation typischerweise in einem <nav> mit Klassen wie "moremenu" und
                // "navbar-nav".
                var $navContainer = $("div.primary-navigation nav.moremenu ul.nav");
                console.log($("div.primary-navigation nav.moremenu ul.nav").length);
                if ($navContainer.length && $navContainer.find("#learningplan-nav-item").length === 0) {
                    var $li = $('<li>', {
                        id: 'learningplan-nav-item',
                        class: 'nav-item'
                    });
                    var $a = $('<a>', {
                        href: linkUrl,
                        class: 'nav-link',
                        text: linkLabel
                    });
                    $li.append($a);
                    $navContainer.append($li);
                    console.log("Navbar link injected.");
                    return true;
                }
                return false;
            }

            // Versuch, den Link sofort einzufügen:
            if (!injectNavbarLink()) {
                // Falls die Navigation noch nicht gerendert ist, einen MutationObserver einsetzen.
                var observer = new MutationObserver(function(mutations, observerInstance) {
                    if (injectNavbarLink()) {
                        observerInstance.disconnect();
                    }
                });
                observer.observe(document.body, {childList: true, subtree: true});
            }
        }
    };
});
