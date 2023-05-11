import Vue from "vue";
import { format as dateFormatter } from "date-fns";
import { es, enGB, pt } from "date-fns/locale";
import { BootstrapVue } from "bootstrap-vue";
const inBrowser = typeof window !== "undefined";

let locale = "en";
const localeObj = inBrowser
  ? document.getElementById("locale")
  : {
      value: locale
    };
if (localeObj) {
  locale = localeObj.value;
}


const localesDateFn = {
  en: enGB,
  es,
  pt
};

const formatWithLocale = (date, formatStr) => {
  if (formatStr === "common.date_format") {
    formatStr = "dd/MM/yyyy";
  }
  return dateFormatter(date, formatStr, {
    locale: localesDateFn[locale]
  });
};

Vue.use(BootstrapVue);

if (!inBrowser) {
  global.document = {
    getElementById: function() {
      return {
        value: "-"
      };
    }
  };
  global.navigator = {
    platform: "node",
    maxTouchPoints: 0,
    userAgent: "node"
  };
}

Vue.filter("formatDate", function(value) {
  if (value) {
    return formatWithLocale(value, Translator.trans("common.date_format"));
  }
});
Vue.filter("formatISO", function(value) {
  if (value) {
    return formatWithLocale(value, "yyyy-MM-dd HH:mm:ss"); // default='YYYY-MM-DDTHH:mm:ss.SSSZ'
  }
});
Vue.filter("formatNumber", function(value, minFractionDigits) {
  if (!value) {
    value = 0;
  }
  var formatter = new Intl.NumberFormat("en", {
    minimumFractionDigits: minFractionDigits || 0,
    maximumFractionDigits: 2
  });
  return formatter.format(Math.floor(Number(value) * 100) / 100);
});
Vue.filter("formatPercent", function(value) {
  if (!value) {
    value = 0;
  }
  var formatter = new Intl.NumberFormat("en", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
  return formatter.format(Math.floor(Number(value) * 100) / 100) + " %";
});
Vue.filter("formatInteger", function(value) {
  if (value) {
    var formatter = new Intl.NumberFormat("en", {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    });
    return formatter.format(value);
  } else {
    return "0";
  }
});
Vue.filter("formatGroupedDate", function(value) {
  if (value) {
    return formatWithLocale(
      value,
      Translator.trans("common.date_format") + " ddd"
    );
  }
});
Vue.filter("formatFullDateCard", function(value) {
  if (value) {
    return formatWithLocale(value, "PP");
  }
});
Vue.filter("formatMonthDateCard", function(value) {
  if (value) {
    return formatWithLocale(value, "MMM yyyy");
  }
});

Vue.filter("capitalizeAllWords", function(value) {
  if (!value) return "";
  value = value.toString();
  value = value
    .split(" ")
    .map(w => w.charAt(0).toUpperCase() + w.slice(1))
    .join(" ");

  return value;
});

export default Vue;
