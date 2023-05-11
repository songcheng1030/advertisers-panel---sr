import Vue from "./vue-common/vue-common";
import reports from "./reports/reports";

import "../scss/vue.scss";
import VCalendar from "v-calendar";

const user = JSON.parse(document.getElementById("vue-user").value);
Vue.use(VCalendar, {
  locales: {
    es: {
      masks: {
        weekdays: "WWW"
      }
    },
    en: {
      masks: {
        weekdays: "WWW"
      }
    },
    pt: {
      masks: {
        weekdays: "WWW"
      }
    }
  },
  locale: user.locale
});
let isIos =
  /iPad|iPhone|iPod/.test(navigator.platform) ||
  (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);

if (isIos) {
  document.body.classList.add("ios");
}

/*
Vue.mixin({
  data: function () {
    return {
      userIsAdmin: user.roles.indexOf('ROLE_ADMIN') > -1,
      locale: user.locale,
      reportsCards: user.reports_cards
    }
  }
})*/
Vue.mixin({
  computed: {
    userIsAdmin() {
      return user.roles.indexOf("ROLE_ADMIN") > -1;
    },
    userIsAdvertiser() {
      return user.roles.indexOf("ROLE_ADVERTISER") > -1;
    },
    userIsSalesManagerHead() {
      return user.roles.indexOf("ROLE_SALES_MANAGER_HEAD") > -1;
    },
    userIsCampaignViewer() {
      return user.roles.indexOf("ROLE_CAMPAIGN_VIEWER") > -1;
    },
    globalLocale() {
      return user.locale;
    },
    reportsCards() {
      return user.reports_cards;
    },
    shouldShowOnlyOwnStats() {
      return 0;
    }
  }
});

const app = new Vue({
  el: "#vue-container",
  components: {
    reports
  },
  user
});

export default app;
