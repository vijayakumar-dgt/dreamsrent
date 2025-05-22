(function () {
    "use strict";
    window.loadTranslationFile = async function(file, module) {
        try {
            const response = await fetch(`/user/translations/${file}/${module}`);
            if (!response.ok) throw new Error(`Failed to load ${file} translations`);

            const data = await response.json();

            window.translations = window.translations || {};
            window.translations[file] = window.translations[file] || {};

            window.translations[file] = {
                ...window.translations[file],
                ...data[file]
            };

        } catch (error) {
            window.translations = window.translations || {};
            window.translations[file] = window.translations[file] || {};
        }
    }

    window._l = function(key, replacements = {}) {
        const [file, ...keys] = key.split('.');
        let translation = keys.reduce((obj, i) => obj?.[i] ?? key, window.translations[file] || {});

        Object.keys(replacements).forEach((placeholder) => {
            const regex = new RegExp(`:${placeholder}`, 'g');
            translation = translation.replace(regex, replacements[placeholder]);
        });

        return translation;
    }
})();
    
    