import enTranslations from "../../translations/messages.en.yml";
import esTranslations from "../../translations/messages.es.yml";

var translations = { en: enTranslations, es: esTranslations };

var Translator = global.Translator || undefined;

if (typeof Translator === "undefined") {
  Translator = {
    trans: function(key) {
      return translations[this.locale][key];
    },
    setLocale: function(locale) {
      this.locale = locale;
    },
    locale: "en"
  };
}

global.Translator = Translator;

export default Translator;
