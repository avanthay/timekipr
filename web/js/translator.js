var Translator = Translator || {};

Translator.fallbackLocale = 'de';

Translator.trans = function(key) {
    return this.messages[window.locale || this.fallbackLocale][key];
};