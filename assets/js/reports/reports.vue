<template>
  <div id="container-reports" :class="isIos ? 'ios' : ''">
    <a id="target" style="display: none"></a>
    <div id="cards-row" v-if="!userIsAdvertiser && !userIsCampaignViewer">
      <InformativeCard
        :metric="
          trans('reports.revenue' + (userIsAdmin ? '_admin' : '_advertiser'))
        "
        :value="'$' + cardRevenue + (userIsAdmin ? '' : ' / $' + cardObjective)"
        :help="
          trans('reports.objective.help' + (userIsAdmin ? '' : '_advertiser'))
        "
        color="blue"
        :date="new Date() | formatMonthDateCard"
      ></InformativeCard>
      <InformativeCard
        v-if="!userIsAdmin"
        :metric="trans('reports.completion.card')"
        :value="percentCompletionObjective"
        :help="trans('reports.completion.help')"
        color="orange"
        :date="new Date() | formatFullDateCard"
      ></InformativeCard>
      <InformativeCard
        v-else
        :metric="trans('reports.impressions.card')"
        :value="cardImpressions"
        :help="trans('reports.impressions.help')"
        color="orange"
        :date="new Date() | formatFullDateCard"
      ></InformativeCard>
      <InformativeCard
        :metric="trans('reports.active_deals')"
        :value="cardActiveDeals"
        :help="trans('reports.active_deals.help')"
        color="red"
        :date="new Date() | formatFullDateCard"
      ></InformativeCard>
      <InformativeCard
        :metric="trans('reports.active_direct_campaigns')"
        last="last-item"
        :value="cardActiveDirectCampaigns"
        :help="trans('reports.active_direct_campaigns.help')"
        color="purple"
        :date="new Date() | formatFullDateCard"
      ></InformativeCard>
    </div>
    <div id="container-form">
      <div id="container-title">
        <h2>{{ trans("reports.generate.title") }}</h2>
        <b-button v-b-toggle.collapse-searcher class="btn-toggle-search-form">
          <span class="icon edit"></span>
          <span class="text hide">{{ trans("common.hide") }}</span>
          <span class="text show">{{ trans("common.edit") }}</span>
        </b-button>
      </div>

      <b-collapse visible id="collapse-searcher">
        <SearchForm
          @launched-report="launchReport"
          @cancel-report="cancelReport"
          :loading="loading"
          :csv="csv"
        ></SearchForm>
      </b-collapse>
      <div id="table-container">
        <b-table
          hover
          :items="items"
          :fields="fields"
          :per-page="pageSize"
          :current-page="currentPage"
          :responsive="true"
          :busy="false"
          :selectable="true"
          select-mode="multi"
          :sort-by.sync="sortBy"
          :sort-desc.sync="sortDesc"
        >
          <template v-slot:table-busy>
            <div class="text-center my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </template>
          <template v-slot:top-row="data">
            <b-td
              v-for="(totalCell, idx) in totalRow[0]"
              :key="totalCell + idx"
              :class="data.fields[idx].tdClass"
              class="top-row"
            >
              <table>
                <tr>
                  <td
                    v-if="
                      totalRow[0][idx] === '' &&
                        totalRow[0][idx + 1] !== null &&
                        totalRow[0][idx + 1] !== '' &&
                        idx !== totalRow[0].length - 1
                    "
                    style="text-align: right;"
                  >
                    <template v-if="totalRow[1]">{{
                      trans("reports.total_row_without_filters")
                    }}</template>
                    <template v-else>Total:</template>
                  </td>
                  <td v-else-if="totalRow[0][idx] === ''">&nbsp;</td>
                  <td v-else>
                    <template
                      v-if="
                        data.fields[idx].displayTipe === 'number' &&
                          totalRow[0][idx] !== '-'
                      "
                      >{{ totalRow[0][idx] | formatNumber(0) }}</template
                    >
                    <template
                      v-else-if="
                        data.fields[idx].displayTipe === 'money' &&
                          totalRow[0][idx] !== '-'
                      "
                      >${{ totalRow[0][idx] | formatNumber(2) }}</template
                    >
                    <template
                      v-else-if="
                        data.fields[idx].displayTipe === 'percent' &&
                          totalRow[0][idx] !== '-'
                      "
                      >{{ totalRow[0][idx] | formatNumber(2) }} %</template
                    >
                    <template v-else>{{ totalRow[0][idx] }}</template>
                  </td>
                </tr>
                <tr v-if="totalRow[1]">
                  <td
                    v-if="
                      totalRow[1][idx] === '' &&
                        (totalRow[1][idx + 1] === null ||
                          (totalRow[1][idx + 1] !== '' &&
                            idx !== totalRow[1].length - 1))
                    "
                    style="text-align: right;"
                  >
                    {{ trans("reports.total_row_with_filters") }}
                  </td>
                  <td v-else>
                    <template
                      v-if="
                        data.fields[idx].displayTipe === 'number' &&
                          totalRow[1][idx] !== '-'
                      "
                      >{{ totalRow[1][idx] | formatNumber(0) }}</template
                    >
                    <template
                      v-else-if="
                        data.fields[idx].displayTipe === 'money' &&
                          totalRow[1][idx] !== '-'
                      "
                      >${{ totalRow[1][idx] | formatNumber(2) }}</template
                    >
                    <template
                      v-else-if="
                        data.fields[idx].displayTipe === 'percent' &&
                          totalRow[1][idx] !== '-'
                      "
                      >{{ totalRow[1][idx] | formatNumber(2) }} %</template
                    >
                    <template v-else>{{ totalRow[1][idx] }}</template>
                  </td>
                </tr>
              </table>
            </b-td>
          </template>

          <template v-slot:cell()="data">
            <template
              v-if="data.field.displayTipe === 'number' && data.value !== '-'"
              >{{ data.value | formatNumber }}</template
            >
            <template
              v-else-if="
                data.field.displayTipe === 'money' && data.value !== '-'
              "
              >${{ data.value | formatNumber(2) }}</template
            >
            <template
              v-else-if="
                data.field.displayTipe === 'percent' && data.value !== '-'
              "
              >{{ data.value | formatNumber(2) }} %</template
            >
            <template v-else>{{ data.value }}</template>
          </template>
        </b-table>
        <div
          class="table-empty-results"
          v-if="calledFirstTime && (!items || !items.length)"
        >
          {{ trans("common.empty_table") }}
        </div>
        <div class="table-bottom-paginator" v-if="items.length">
          <span>
            Mostrando {{ ((currentPage - 1) * pageSize + 1) | formatNumber }} al
            {{
              (currentPage * pageSize > items.length
                ? items.length
                : currentPage * pageSize) | formatNumber
            }}
            de {{ items.length | formatNumber }} Registros
          </span>

          <div class="pagesizes">
            <dropdown
              v-bind:default-value="pageSize"
              v-bind:button-class="'btn-dropdown-page-size'"
              v-on:selected-element="alert(1)"
              @changed-pagesize="changePagesize"
              name="pageSizeOwned"
              id="pageSizeOwned"
              :default-selected="pageSizes[1]"
              dropdown-class="form th-s-panel page-size-select"
              :elements="pageSizes"
              :elements-filtered="pageSizes"
              v-bind:all-elements="pageSizes"
            ></dropdown>
            <b-pagination
              v-model="currentPage"
              :total-rows="items.length"
              :per-page="pageSize"
              aria-controls="my-table"
              style="float: right;"
              first-number
              last-number
              :prev-text="trans('common.previous')"
              :next-text="trans('common.next')"
            ></b-pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SearchForm from "./components/search-form";
import InformativeCard from "./components/informative-card";
import { format, parse } from "date-fns";
import Vue from "vue";
import dropdown from "./components/pagesize-dropdown";

const items = [];
const fields = [];

const totalRow = [];

const currentPage = 1;

const calledFirstTime = false;

let sortBy = "";
let sortDesc = true;

let loading = false;

const pageSizes = [
  {
    value: 25,
    label: Translator.trans("reports.pagination.25"),
    txt: Translator.trans("reports.pagination.25")
  },
  {
    value: 50,
    label: Translator.trans("reports.pagination.50"),
    txt: Translator.trans("reports.pagination.50")
  },
  {
    value: 100,
    label: Translator.trans("reports.pagination.100"),
    txt: Translator.trans("reports.pagination.100")
  },
  {
    value: 200,
    label: Translator.trans("reports.pagination.200"),
    txt: Translator.trans("reports.pagination.200")
  },
  {
    value: 500,
    label: Translator.trans("reports.pagination.500"),
    txt: Translator.trans("reports.pagination.500")
  }
];

const pageSize = 50;

let isIos =
  /iPad|iPhone|iPod/.test(navigator.platform) ||
  (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1);

let csv = true;

const securerReq = {};
const dataReq = {};

export default {
  components: { SearchForm, InformativeCard, dropdown },
  computed: {
    cardRevenue: function() {
      return Vue.filter("formatInteger")(this.reportsCards.revenue);
    },
    cardImpressions: function() {
      return Vue.filter("formatInteger")(this.reportsCards.impressions);
    },
    cardActiveDeals: function() {
      return Vue.filter("formatInteger")(this.reportsCards.active_deals);
    },
    cardActiveDirectCampaigns: function() {
      return Vue.filter("formatInteger")(
        this.reportsCards.active_direct_campaigns
      );
    },
    percentCompletionObjective: function() {
      let percentCompletionObjective = "NA";
      if (this.reportsCards.objective != 0) {
        percentCompletionObjective = Vue.filter("formatPercent")(
          (100 * this.reportsCards.revenue) / this.reportsCards.objective
        );
      }
      return percentCompletionObjective;
    },
    cardObjective: function() {
      return Vue.filter("formatInteger")(this.reportsCards.objective);
    }
  },
  data: function() {
    return {
      items,
      fields,
      currentPage,
      totalRow,
      calledFirstTime,
      sortBy,
      sortDesc,
      loading,
      csv,
      isIos,
      pageSizes,
      pageSize,
      securerReq,
      dataReq
    };
  },
  methods: {
    trans: function(txt, params) {
      return Translator.trans(txt, params);
    },
    changePagesize: function(ev) {
      this.pageSize = ev.val;
    },
    cancelReport: function() {
      if (this.securerReq && this.securerReq.abort) {
        this.securerReq.abort();
      }
      if (this.dataReq && this.dataReq.abort) {
        this.dataReq.abort();
      }
      this.loading = false;
    },
    launchReport: function(ev) {
      const userIsAdvertiser = this.userIsAdvertiser;
      const userIsAdmin = this.userIsAdmin;
      const userIsCampaignViewer = this.userIsCampaignViewer;
      this.csv = ev.csv;
      this.loading = true;
      function guid(a) {
        return a
          ? (a ^ ((Math.random() * 16) >> (a / 4))).toString(16)
          : ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, guid);
      }

      function parseFilters(filters) {
        const retFilters = [];

        const filterLabels = Object.keys(filters);
        for (let i = 0; i < filterLabels.length; i++) {
          const label = filterLabels[i];
          for (let j = 0; j < filters[label].addedFilters.length; j++) {
            retFilters.push({
              label,
              value: filters[label].addedFilters[j],
              include: filters[label].include
            });
          }
        }
        return retFilters;
      }

      const uuid = guid();
      this.securerReq = new XMLHttpRequest();
      this.securerReq.open("GET", "/reports/genrepo/" + uuid /*, false*/);

      this.securerReq.onreadystatechange = function() {
        if (
          this.securerReq.readyState === 4 &&
          this.securerReq.status === 200
        ) {
          const payload = {
            Dimensions: ev.dimensions,
            Metrics: ev.metrics,
            PDate: [
              format(ev.dateRange.start, "dd/MM/yyyy"),
              format(ev.dateRange.end, "dd/MM/yyyy")
            ],
            Filters: parseFilters(ev.filters),
            env: ev.env,
            onlyOwnStats: ev.onlyOwnStats,
            csv: ev.csv,
            uuid: uuid,
            reportType: ev.reportType
          };

          this.dataReq = new XMLHttpRequest();
          this.dataReq.open(
            "POST",
            "https://reports.vidoomy.com/reports_/adv/repo.php" /*,
            false*/
          );
          var FD = new FormData();

          // Push our data into our FormData object
          for (name in payload) {
            if (Array.isArray(payload[name])) {
              //payload[name].map(d => {
              if (name === "Filters") {
                payload[name].map((d, idx) => {
                  const keys = Object.keys(d);
                  for (let i = 0; i < keys.length; i++) {
                    FD.append(
                      name + "[" + idx + "][" + keys[i] + "]",
                      d[keys[i]]
                    );
                  }
                });
              } else {
                payload[name].map(d => {
                  FD.append(name + "[]", d);
                });
              }
              //});
            } else {
              FD.append(name, payload[name]);
            }
          }

          this.dataReq.onreadystatechange = function() {
            if (this.dataReq.status === 200 && this.dataReq.readyState === 4) {
              function getColumnTypeByMetric(m) {
                if (
                  m === "revenue" ||
                  m === "net_revenue" ||
                  m === "rebate_cost" ||
                  m === "cpm"
                ) {
                  return "money";
                } else if (
                  m === "viewability_percent" ||
                  m === "rebate_percent" ||
                  m === "ctr" ||
                  m === "vtr" ||
                  m === "25perc" ||
                  m === "50perc" ||
                  m === "75perc"
                ) {
                  return "percent";
                } else {
                  return "number";
                }
              }

              const columns = [
                ...ev.dimensions.map(d => {
                  return {
                    key: d,
                    label:
                      d === "deal_id" && userIsAdvertiser
                        ? this.trans("reports.generate.campaign_name")
                        : this.trans("reports." + d),
                    sortable: true,
                    tdClass: "dimension-field"
                  };
                }),
                ...ev.metrics.map(m => {
                  return {
                    key: m,
                    label:
                      userIsAdvertiser && m == "revenue"
                        ? Translator.trans("reports." + m + "_advertiser")
                        : Translator.trans("reports." + m),
                    sortable: true,
                    tdClass: "metric-field",
                    thClass: "header-metric-field",
                    displayTipe: getColumnTypeByMetric(m)
                  };
                })
              ];

              if (ev.reportType !== "overall") {
                columns.unshift({
                  key: ev.reportType,
                  label: this.trans("reports.generate." + ev.reportType),
                  sortable: true,
                  tdClass: "dimension-field",
                  thClass: "header-dimension-field",
                  sortByFormatted: function formatter(value, key, item) {
                    if (key === "hourly") {
                      return parse(value, "yyyy-MM-dd, hhaa", new Date()); // la fecha viene como 2020-02-26, 12AM
                    } else {
                      if (key === "monthly") {
                        return parse(value, "MMMM yyyy", new Date()); // la fecha viene como February 2020
                      } else if (key === "daily") {
                        return parse(value, "yyyy-MM-dd", new Date()); // la fecha viene como 2020-02-26
                      } else {
                        return value;
                      }
                    }
                  }
                });

                this.sortBy = ev.reportType;
                this.sortDesc = false;
              } else {
                if (!ev.dimensions.length) {
                  columns.unshift({
                    key: ev.reportType,
                    label: this.trans("reports.generate." + ev.reportType),
                    sortable: true,
                    thClass: "header-dimension-field",
                    tdClass: "dimension-field"
                  });
                }

                if (ev.metrics.find(o => o === "revenue")) {
                  this.sortBy = "revenue";
                } else if (ev.metrics.find(o => o === "impressions")) {
                  this.sortBy = "impressions";
                } else if (ev.metrics && ev.metrics.length) {
                  this.sortBy = ev.metrics[0];
                }
                this.sortDesc = true;
              }

              if (ev.csv) {
                var blob = this.dataReq.response;
                var fileName = null;
                var contentType = this.dataReq.getResponseHeader(
                  "content-type"
                );

                fileName = `VidoomyReport_${format(
                  new Date(),
                  "yyyy-MM-dd_hhmmss"
                )}.csv`;

                if (window.navigator.msSaveOrOpenBlob) {
                  // Internet Explorer
                  window.navigator.msSaveOrOpenBlob(
                    new Blob([blob], { type: contentType }),
                    fileName
                  );
                } else {
                  var el = document.getElementById("target");
                  el.href = window.URL.createObjectURL(
                    new Blob([blob], { type: contentType })
                  );
                  el.download = fileName;
                  el.click();
                }
                this.loading = false;
              } else {
                this.calledFirstTime = true;
                const rawData = JSON.parse(this.dataReq.responseText);

                const dataAsObj = rawData.data.map(row => {
                  let rowAsObject = {};
                  for (let i = 0; i < row.length; i++) {
                    if (columns[i]) {
                      rowAsObject[columns[i].key] = row[i];
                    }
                  }
                  return rowAsObject;
                });

                this.fields = columns;
                this.items = dataAsObj;

                this.totalRow = rawData.dataT;
                this.loading = false;
              }
            }
          }.bind(this);
          this.dataReq.send(FD);
        }
      }.bind(this);

      this.securerReq.send();
    }
  }
};
</script>
<style lang="scss"></style>
<style lang="scss" scoped>
@import "../../scss/_variables";
::v-deep {
  @import "node_modules/bootstrap/scss/bootstrap";
  @import "node_modules/bootstrap-vue/src/index.scss";

  .dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
  }

  .dimension-field {
    background-color: #ebeff6;
  }

  .top-row {
    font-weight: 700;
    background-color: #ebeff6;
    padding: 0 !important;
  }
  tr:hover {
    background-color: #e1f0fd;
    td {
      background-color: #e1f0fd;
    }
  }
  tr {
    &.b-table-row-selected.table-active {
      background-color: #fff1c7 !important;
    }
    &:focus {
      outline: none !important;
    }
  }

  td,
  th {
    white-space: nowrap;
    padding: 1rem 0.75rem !important;
    border-top: 1px solid #f8f8f8;
  }

  td.metric-field {
    text-align: right;
  }
  th.header-metric-field {
    text-align: right;
  }

  th {
    border-bottom: none !important;
    border-top: none !important;

    div {
      display: inline;
    }
  }

  thead {
    user-select: none;
    tr {
      th {
        padding: 28px 19px !important;
        background-image: none !important;
        &[aria-sort] {
          &[aria-sort="none"]:before {
            background-image: url(../../img/icon-sort@2x.png);
          }
          &[aria-sort="ascending"]:before {
            background-image: url(../../img/icon-sort-asc@2x.png);
          }

          &[aria-sort="descending"]:before {
            background-image: url(../../img/icon-sort-desc@2x.png);
          }
          &:before {
            display: inline-block;
            content: "";
            width: 8px;
            height: 11px;
            cursor: pointer;
            padding-right: 18px;
            background-repeat: no-repeat;
            background-size: 8px 11px;
          }
        }
      }
    }
  }
}

#container-form {
  margin: 28px 0;
  background-color: #fff;
  font-size: 14px;
  font-family: Roboto, sans-serif;
  text-align: left;
  #container-title {
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    display: flex;
    border-bottom: 1px solid #f8f8f8;
    h2 {
      font-family: quatro, sans-serif;
      font-size: 21px;
      font-weight: 500;
      display: inline-block;
      margin: 0 !important;
    }
    .btn-toggle-search-form {
      &:before {
        content: unset;
      }
      @media #{$tablet} {
        float: none;
        width: 85px;
      }
      background-color: #e6f2fe;
      color: #41a3ff;
      font-size: 14px;
      border: 1px solid #e6f2fe;
      /*padding: 5px 11px 9px;*/
      padding: 7px 11px 9px;
      border-radius: 3px;
      line-height: 1;

      &:hover {
        border-color: #41a3ff;
        opacity: 1;
      }
      &:focus {
        box-shadow: none;
      }
      span {
        font-family: quatro, sans-serif;
      }

      .hide {
        display: inline;
      }
      .show {
        display: none;
      }

      span.icon.edit {
        &:before {
          margin-right: 2px !important;
          width: 16px;
          background-image: url(../../img/icon-edit-b.png);
        }
      }
      &.collapsed {
        color: #fff;
        background-color: #41a3ff;
        border-color: #41a3ff;

        span.icon.edit {
          &:before {
            margin-right: 2px !important;
            width: 16px;
            background-image: url(../../img/icon-edit-w.png);
          }
        }

        .hide {
          display: none;
        }
        .show {
          display: inline;
        }
      }
    }
  }
}

#container-reports {
  #cards-row {
    display: flex;
    @media #{$tablet} {
      flex-direction: column;
    }
  }
}

#table-container {
  padding-bottom: 40px;
  color: #424853 !important;
  ::v-deep {
    table {
      border-bottom: 5px solid #f3f6fb;
      color: #424853 !important;
      tr.b-table-top-row {
        > td {
          border-top: 6px solid #f3f6fb;
          color: #424853 !important;
          table {
            border-bottom: none;
            tr {
              td {
                border-top: none;
                color: #424853 !important;
              }
            }
          }
        }
        /*td, th {
          border-top: none;
        }*/
      }
    }
  }

  .table-empty-results {
    margin-top: -1rem;
    padding: 15px 10px 15px 18px;
    text-align: center;
    border-bottom: 5px solid #f6f8fc;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    font-size: 14px;
    font-family: Roboto, sans-serif;
  }

  .table-bottom-paginator {
    padding-left: 1%;
    display: flex;
    justify-content: space-between;
    @media #{$tablet} {
      margin-right: 0;
    }
    /*padding-right: 1%;*/
    height: 35px;
    margin-top: 1em;
    margin-right: 2em;
    span {
      line-height: 35px;
      font-family: quatro;
      font-size: 12px;
    }
    ::v-deep {
      div.pagesizes {
        display: flex;
        div.dropdown-wrapper {
          margin-right: 10px;
          .dropdown {
            background-color: #fff !important;
            .dropdown-toggle {
              width: 120px;
              max-width: 120px;
              min-width: 120px;
              font-family: quatro, sans-serif;
              height: 32px;
              background-color: #fff !important;
              span.black-button-text {
                background-color: #fff !important;
              }
            }
            ul {
              width: 119px !important;
              max-width: 119px !important;
              min-width: 119px !important;
              .dropdown-item {
                width: 119px !important;
                max-width: 119px !important;
                min-width: 119px !important;
                .element-container {
                  padding: 10px 16px;
                  font-size: 12px;
                  font-family: quatro, sans-serif;
                  .icon {
                    display: none;
                  }
                  .noic {
                    font-size: 12px;
                    font-family: quatro, sans-serif;
                  }
                }
              }
            }
          }
        }
      }
      ul.b-pagination {
        font-family: quatro, sans-serif;
        color: rgb(120, 136, 154);
        height: 32px;

        li {
          &.active {
            a.page-link {
              user-select: none;
              color: #78889a !important;
              border-left: 1px solid #f3f3f3 !important;
              border-right: 1px solid #f3f3f3 !important;
              border-top-color: #e1f0fd !important;
              border-bottom-color: #e1f0fd !important;
              background-color: #e1f0fd !important;
            }
          }
          &:not(.active) {
            span,
            a {
              user-select: none;
              color: #78889a !important;
              border-color: #f3f3f3;
              border-left: 1px solid transparent !important;
              border-right: 1px solid transparent !important;
            }

            &:first-of-type {
              span,
              a {
                border-left: 1px solid #f3f3f3 !important;
              }
            }
            &:last-of-type {
              span,
              a {
                border-right: 1px solid #f3f3f3 !important;
              }
            }
          }

          &.disabled {
            span,
            a {
              color: #666 !important;
            }
          }

          .page-link {
            padding: 6px 13px !important;
          }
          .page-link:focus {
            box-shadow: none;
          }
          .page-link:hover {
            background-color: #e1f0fd !important;
          }
        }
      }
    }
  }
}
</style>
<style lang="scss">
.main {
  padding: 70px 24px 70px;
}
</style>
