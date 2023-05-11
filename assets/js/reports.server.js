import Translator from "./translator.server";
import Vue from "./vue-common/vue-common";
import reports from "./reports/reports";

import "../scss/vue.scss";

global.Translator = Translator;

let isIos =
  /iPad|iPhone|iPod/.test(navigator.platform) ||
  (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);

if (isIos) {
  //document.body.classList.add("ios");
}

export default context => {
  global.Translator.setLocale(context.user.locale);
  Vue.mixin({
    computed: {
      userIsAdmin() {
        return context.user.roles.indexOf("ROLE_ADMIN") > -1;
      },
      userIsAdvertiser() {
        return context.user.roles.indexOf("ROLE_ADVERTISER") > -1;
      },
      userIsSalesManagerHead() {
        return context.user.roles.indexOf("ROLE_SALES_MANAGER_HEAD") > -1;
      },
      userIsCampaignViewer() {
        return context.user.roles.indexOf("ROLE_CAMPAIGN_VIEWER") > -1;
      },
      globalLocale() {
        return context.user.locale;
      },
      reportsCards() {
        return context.user.reports_cards;
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
    render: h => h(reports),
    user: context.user
  });

  return app;
};
