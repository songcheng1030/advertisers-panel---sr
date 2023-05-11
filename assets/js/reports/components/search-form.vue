<template>
  <!-- Your HTML tags-->
  <div class="panel-header block no-padding editor-panels">
    <div class="search-form-container" style="border-bottom: none;">
      <div style class="dropdown-wrapper" @click="toggleDropdown">
        <b-dropdown
          :text="trans('reports.generate.' + reportType)"
          class
          size="sm"
          menu-class="vue-dropdown-menu"
          toggle-class="report-type-button"
          ref="dropdown-report-type"
          :no-flip="true"
        >
          <b-dropdown-item
            @click="
              reportType = 'overall';
              setMinDateDaterangepicker(false);
            "
            >{{ trans("reports.generate.overall") }}</b-dropdown-item
          >
          <b-dropdown-item
            @click="
              reportType = 'monthly';
              setMinDateDaterangepicker(false);
            "
            >{{ trans("reports.generate.monthly") }}</b-dropdown-item
          >
          <b-dropdown-item
            @click="
              reportType = 'daily';
              setMinDateDaterangepicker(false);
            "
            >{{ trans("reports.generate.daily") }}</b-dropdown-item
          >
          <b-dropdown-item
            @click="
              reportType = 'hourly';
              setMinDateDaterangepicker(true);
            "
            >{{ trans("reports.generate.hourly") }}</b-dropdown-item
          >
        </b-dropdown>
      </div>
      <div class="date-wrapper reports dark">
        <reports-daterange-picker
          :startDefaultDate="startDefaultDate"
          :endDefaultDate="endDefaultDate"
          :minDate="minDate"
          :maxDate="maxDate"
          @range-selected="selectRange"
        ></reports-daterange-picker>
      </div>
      <div class="added-dimensions-wrapper">
        <span v-for="(dim, idx) in dimensionsAddedList" v-bind:key="dim">
          <addedDimensionDropdown
            v-bind:default-value="dim"
            v-bind:index="idx"
            dropdown-class="form th-s-panel just-info2 dimension-dropdown"
            button-class="inner-text blue"
            wide="true"
            v-on:selected-dimension="selectedDimension"
            v-on:deleted-dimension="deletedDimension"
            v-bind:added-dimensions="dimensionsAddedList"
            v-bind:elements-filtered="getFilteredDimensionsSelectable"
            v-bind:element-selected="dimensionsList.find(d => d.value === dim)"
            :elements="dimensionsList"
          ></addedDimensionDropdown>
        </span>
      </div>
      <dimensionDropdown
        v-if="dimensionsAddedList.length < dimensionsList.length"
        default-value
        :default-text="'+' + trans('reports.generate.dimension')"
        dropdown-class="form th-s-panel just-info2 dimension-dropdown"
        button-class="inner-text blue"
        wide="true"
        v-on:selected-dimension="selectedDimension"
        v-bind:added-dimensions="dimensionsAddedList"
        v-bind:elements-filtered="getFilteredDimensionsSelectable"
        :elements="dimensionsList"
      ></dimensionDropdown>

      <div class="dropdown-wrapper">
        <a
          href="javascript:void(0)"
          v-b-toggle.collapse-metrics
          class="metric-dropdown-trigger"
        >
          {{ trans("reports.generate.metrics") }}
          <span class="number">({{ selectedMetrics.length }})</span>
        </a>
      </div>
    </div>
    <div>
      <b-collapse id="collapse-metrics">
        <div class="metrics-wrapper" :style="'display: block'">
          <a
            href="javascript:void(0)"
            class="close-mw"
            v-b-toggle.collapse-metrics
          ></a>
          <div class="mw-title">
            <span class="mwt-main">
              <span class="number">{{ selectedMetrics.length }}</span>
              {{ trans("reports.generate.metrics_selected") }}
            </span>
            <a
              href="javascript:void(0)"
              class="unselect-all-metrics-trigger"
              @click="selectedMetrics = []"
              >{{ trans("reports.generate.unselect_all") }}</a
            >
            <span class="info">
              {{ trans("reports.generate.drag_to_reorder") }}
            </span>
          </div>
          <div class="selected-metrics-wrapper">
            <p class="sm-empty">
              {{ trans("reports.generate.at_least_one_metric") }}
            </p>
            <draggable
              group="metrics"
              style="display: inline; width: 800px;"
              class="sm-container"
              @end="onMovedMetric($event)"
              id="sm-container"
            >
              <a
                @click="unselectMetric(dim)"
                href="javascript:void(0)"
                class="metric-item"
                :rel="dim"
                v-for="dim in selectedMetrics"
                v-bind:key="dim"
              >
                {{
                  userIsAdvertiser && dim == "revenue"
                    ? trans("reports." + dim + "_advertiser")
                    : trans("reports." + dim)
                }}
                <span class="close"></span>
              </a>
            </draggable>
          </div>
          <div class="mw-title">
            <span class="mwt-main">
              {{ trans("reports.generate.unselected_metrics") }}
            </span>
            <a
              href="javascript:void(0)"
              class="select-all-metrics-trigger"
              @click="selectedMetrics = metrics"
              >{{ trans("reports.generate.select_all") }}</a
            >
          </div>
          <div class="unselected-metrics-wrapper">
            <a
              @click="selectMetric(dim)"
              href="javascript:void(0)"
              class="metric-item"
              :rel="dim"
              v-for="dim in metrics.filter(
                m => selectedMetrics.indexOf(m) === -1
              )"
              v-bind:key="dim"
            >
              {{
                userIsAdvertiser && dim == "revenue"
                  ? trans("reports." + dim + "_advertiser")
                  : trans("reports." + dim)
              }}
              <span class="close"></span>
            </a>
          </div>
        </div>
      </b-collapse>
    </div>
    <div class="search-form-second-container">
      <div>
        <div class="dropdown-wrapper">
          <a
            href="javascript:void(0)"
            class="fd-btn filterdimensions-toggle"
            v-b-toggle.collapse-filters
            >{{ trans("reports.generate.filters") }} ({{ filtersReady }})</a
          >
        </div>
      </div>
      <div class="filter-dropdown">
        <b-collapse id="collapse-filters">
          <div class="fd-menu" :style="'display: block;'">
            <a
              href="javascript:void(0)"
              class="close-fdm"
              v-b-toggle.collapse-filters
            ></a>
            <ul class="fdm-body">
              <filterDimension
                v-for="(dim, idx) in dimensionsAddedListForFilters"
                v-bind:key="dim"
                v-bind:dim="dim"
                v-bind:dimensionsList="dimensionsList"
                v-bind:searchXtraValues="xtraSearchFilter[dim]"
                v-bind:defaultSelected="[...filtersInfo[dim]]"
                v-on:deletedFilter="deleteFilter"
                v-on:fullfilled="onFullfillFilter"
                v-on:deletedFullfiled="onDeletedFullfiledFilter"
                @filter-dimension-change="onFilterDimensionChange"
                v-bind:style="
                  idx === 0
                    ? 'padding-top: 20px!important;'
                    : idx === dimensionsAddedListForFilters.length - 1
                    ? 'padding-bottom: 20px!important'
                    : ''
                "
              ></filterDimension>
            </ul>
            <div
              class="fdm-footer"
              :class="userIsAdvertiser ? 'advertiser' : ''"
            >
              <span>{{ trans("reports.generate.add") }}</span>
              <span class="fdm-wrapper">
                <template
                  v-for="dimensionFilter in dimensionsList.filter(
                    d => !d.filterOther
                  )"
                >
                  <a
                    :key="dimensionFilter.value"
                    href="javascript:void(0)"
                    v-on:click="addFilterFromFooter(dimensionFilter.value)"
                    v-if="
                      dimensionsAddedListForFilters.indexOf(
                        dimensionFilter.value
                      ) === -1
                    "
                    class="new-filter"
                    :data-filter="dimensionFilter.value"
                    selectable="true"
                    >{{ trans("reports.generate." + dimensionFilter.value) }}</a
                  >
                </template>
                <span
                  class="more-filters"
                  v-on-clickaway="hideMoreFilters"
                  v-on:click="showMoreFilters = !showMoreFilters"
                  v-if="
                    dimensionsList
                      .filter(
                        d =>
                          dimensionsAddedListForFilters.indexOf(d.value) === -1
                      )
                      .some(d => d.filterOther)
                  "
                >
                  <span class="main-mf">
                    {{ trans("reports.generate.other") }}
                  </span>
                  <span
                    class="mf-inner"
                    v-bind:style="
                      showMoreFilters ? 'display: block;' : 'display: none;'
                    "
                  >
                    <template
                      v-for="dimensionFilter in dimensionsList.filter(
                        d => d.filterOther
                      )"
                    >
                      <a
                        :key="dimensionFilter.value"
                        href="javascript:void(0)"
                        v-on:click="addFilterFromFooter(dimensionFilter.value)"
                        v-if="
                          dimensionsAddedListForFilters.indexOf(
                            dimensionFilter.value
                          ) === -1
                        "
                        data-filter="domain"
                        class="new-filter other"
                        selectable="true"
                      >
                        {{ trans("reports.generate." + dimensionFilter.value) }}
                      </a>
                    </template>
                  </span>
                </span>
              </span>
            </div>
          </div>
        </b-collapse>
      </div>
    </div>
    <div class="buttons-container">
      <b-button
        size="sm"
        variant="primary"
        class="btn-run-report"
        @click="generateReport(false)"
        :class="[
          loading && !csv ? 'loading' : '',
          loading ? 'disabled-btn' : ''
        ]"
      >
        <span class="icon"></span>
        {{
          loading && !csv
            ? trans("common.cancel")
            : trans("reports.generate.run")
        }}
      </b-button>
      <b-button
        v-if="userIsAdmin || userIsAdvertiser || userIsCampaignViewer"
        size="sm"
        variant="outline-primary"
        class="btn-run-csv"
        @click="generateReport(true)"
        :class="[
          loading && csv ? 'loading' : '',
          loading ? 'disabled-btn' : ''
        ]"
      >
        <span class="icon"></span>
        {{
          loading && csv
            ? trans("common.cancel")
            : trans("reports.generate.runcsv")
        }}
      </b-button>
    </div>
  </div>
</template>

<script>
import dimensionDropdown from "./dimension-dropdown";
import addedDimensionDropdown from "./added-dimension-dropdown";
import draggable from "vuedraggable";
import filterDimension from "./filter-dimension";

import { parseISO, parse, endOfMonth } from "date-fns";

import reportsDaterangePicker from "./reports-daterange-picker";

import { directive as onClickaway } from "vue-clickaway";
let xtraSearchFilter = {
  sales_manager: [],
  campaign_name: [],
  type: [],
  advertiser: [],
  agency: [],
  country: [],
  ssp: [],
  dsp: [],
  deal_id: []
};

let dimensionsList = [
  // sales manager lo deben poder ver los admins, completos, y los sales manager head, que solo deberían ver los propios
  {
    value: "sales_manager",
    txt: Translator.trans("reports.generate.sales_manager"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: true
  },
  {
    value: "campaign_name",
    txt: Translator.trans("reports.generate.campaign_name_no_adv"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: false
  },
  {
    value: "type",
    txt: Translator.trans("reports.generate.type"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: true
  },
  {
    value: "deal_id",
    txt: Translator.trans("reports.generate.deal_id"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: false
  },
  {
    value: "advertiser",
    txt: Translator.trans("reports.generate.advertiser"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: false
  },
  {
    value: "agency",
    txt: Translator.trans("reports.generate.agency"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: false
  },
  {
    value: "country",
    txt: Translator.trans("reports.generate.country"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: false,
    searchOnlyExact: true
  },
  {
    value: "ssp",
    txt: Translator.trans("reports.generate.ssp"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: true,
    searchOnlyExact: true
  },
  {
    value: "dsp",
    txt: Translator.trans("reports.generate.dsp"),
    xtraClass: "list-item-wicon",
    icon: true,
    filterOther: true,
    searchOnlyExact: true
  }
];

let showMoreFilters = false;
let dimensionsAddedList = ["campaign_name"];
let dimensionsAddedListForFilters = ["campaign_name"];

let elementsFiltered = [];

let filtersReady = 0;

let reportType = "overall";

let metrics = [
  "request",
  "bids",
  "impressions",
  "cpm",
  "revenue",
  "rebate_percent",
  "rebate_cost",
  "net_revenue",
  "viewable_impressions",
  "viewability_percent",
  "clicks",
  "ctr",
  "complete_views",
  "vtr",
  "viewed25",
  "viewed50",
  "viewed75",
  "25perc",
  "50perc",
  "75perc"
];

let selectedMetrics = [
  "request",
  "bids",
  "impressions",
  "revenue",
  "vtr",
  "viewability_percent",
  "ctr"
];

let startDefaultDate = new Date();
let endDefaultDate = new Date();

let selectedRange = { start: startDefaultDate, end: endDefaultDate };

let filtersInfo = {};

let filterDimensionsData = {};

let minDate = parseISO("2018-01-01 00:00:00");
let maxDate = endOfMonth(new Date());

let showMetrics = false;
let showFilters = false;

const openedDropdown = false;

export default {
  components: {
    dimensionDropdown,
    addedDimensionDropdown,
    draggable,
    filterDimension,
    reportsDaterangePicker
  },
  data: function() {
    return {
      dimensionsList,
      dimensionsAddedList,
      dimensionsAddedListForFilters,
      elementsFiltered,
      xtraSearchFilter,
      filtersReady,
      showMoreFilters,
      reportType,
      metrics,
      selectedMetrics,
      startDefaultDate,
      endDefaultDate,
      filtersInfo,
      minDate,
      maxDate,
      showMetrics,
      showFilters,
      selectedRange,
      filterDimensionsData,
      openedDropdown
    };
  },
  props: {
    loading: Boolean,
    csv: Boolean
  },
  computed: {
    getFilteredDimensionsSelectable: function() {
      return this.elementsFiltered;
    }
  },
  methods: {
    trans: function(txt, params) {
      return Translator.trans(txt, params);
    },
    selectMetric: function(metric) {
      if (this.selectedMetrics.indexOf(metric) === -1) {
        this.selectedMetrics.push(metric);
      }
    },
    toggleDropdown: function() {
      if (this.openedDropdown) {
        this.$refs["dropdown-report-type"].hide();
      } else {
        this.$refs["dropdown-report-type"].show();
      }
      this.openedDropdown = !this.openedDropdown;
    },
    unselectMetric: function(metric) {
      if (this.selectedMetrics.indexOf(metric) > -1) {
        this.selectedMetrics = this.selectedMetrics.filter(sm => sm !== metric);
      }
    },
    hideMoreFilters: function() {
      this.showMoreFilters = false;
    },
    trans: function(txt, params) {
      return Translator.trans(txt, params);
    },
    selectedDimension: function(val) {
      if (val && val.idx !== null && val.val) {
        if (val.idx === -1) {
          this.$set(
            this.dimensionsAddedList,
            this.dimensionsAddedList.length,
            val.val
          );
        } else {
          this.$set(this.dimensionsAddedList, val.idx, val.val);
        }

        this.dimensionsAddedList.map(d =>
          this.dimensionsAddedListForFilters.indexOf(d) === -1 &&
          !this.dimensionsList.find(dim => dim.value === d).notFilter
            ? this.dimensionsAddedListForFilters.push(d)
            : 0
        );

        this.dimensionsList.map(el => {
          if (this.dimensionsAddedList.indexOf(el.value) > -1) {
            el.hidden = true;
          } else {
            el.hidden = false;
          }
        });

        /* this.dimensionsAddedList.push(val);

                    this.dimensionsAddedList = Object.assign([], this.dimensionsAddedList);*/
        this.elementsFiltered = this.dimensionsList.filter(
          el => this.dimensionsAddedList.indexOf(el.value) === -1
        );
      }
    },
    deletedDimension: function(idx) {
      if (idx > -1) {
        this.dimensionsAddedList.splice(idx, 1);
        this.dimensionsAddedList.map(d =>
          this.dimensionsAddedListForFilters.indexOf(d) === -1
            ? this.dimensionsAddedListForFilters.push(d)
            : 0
        );
        //this.dimensionsAddedListForFilters = this.dimensionsAddedList.slice(0);
        this.dimensionsList.map(el => {
          if (this.dimensionsAddedList.indexOf(el.value) > -1) {
            el.hidden = true;
          } else {
            el.hidden = false;
          }
        });
        this.elementsFiltered = this.dimensionsList.filter(
          el => this.dimensionsAddedList.indexOf(el.value) === -1
        );

        this.dimensionsAddedListForFilters = this.dimensionsAddedListForFilters.filter(
          el => {
            const existSomeFilterWithData =
              document.querySelectorAll("[data-dimension='" + el + "']")
                .length > 0;
            const isInDimensionsList = this.dimensionsAddedList.some(
              d => d === el
            );
            return existSomeFilterWithData || isInDimensionsList;
          }
        );
      }
    },
    deleteFilter: function(ev) {
      if (this.dimensionsAddedListForFilters.indexOf(ev) > -1) {
        if (
          document.querySelector('[data-dimension="' + ev + '"].rm-box') !==
          null
        ) {
          //this.filtersReady--;
        }
        delete this.filterDimensionsData[ev];
        this.dimensionsAddedListForFilters.splice(
          this.dimensionsAddedListForFilters.indexOf(ev),
          1
        );
        // filtersReady--;
      }
    },
    onFullfillFilter: function(ev) {
      //this.filtersReady++;
    },
    onDeletedFullfiledFilter: function(ev) {
      /*if (this.filtersReady > 0) {
        this.filtersReady--;
      }*/
    },
    onFilterDimensionChange: function(ev) {
      if (ev.checkboxSelected) {
        this.filterDimensionsData[ev.dim] = {
          addedFilters: ev.addedFilters,
          include: ev.include
        };
      } else {
        //this.$delete(this.filterDimensionsData, ev.dim);
      }
      this.filtersReady = Object.keys(this.filterDimensionsData)
        .map(k =>
          this.filterDimensionsData[k].addedFilters.length > 0 ? 1 : 0
        )
        .reduce((acc, elem) => acc + elem, 0);
    },
    addFilterFromFooter: function(filter) {
      if (this.dimensionsAddedListForFilters.indexOf(filter) === -1) {
        this.dimensionsAddedListForFilters.push(filter);
      }
    },
    onMovedDimension: function(ev) {
      Array.from(document.querySelectorAll("div.added-dimension")).map(d => {
        d.style.transform = "";
      });
    },
    onMovedMetric: function(ev) {
      this.selectedMetrics.splice(
        ev.newIndex,
        0,
        this.selectedMetrics.splice(ev.oldIndex, 1)[0]
      );
    },
    setMinDateDaterangepicker: function(isHourly) {
      if (isHourly) {
        this.minDate = parseISO("2019-06-01 00:00:00");
      } else {
        this.minDate = parseISO("2018-01-01 00:00:00");
      }

      this.maxDate = endOfMonth(new Date());
    },
    generateReport: function(csv) {
      if (this.loading) {
        if (this.csv === csv) {
          this.$emit("cancel-report");
        }
      } else {
        const symfonyEnv = document
          ? document.getElementById("current-symfony-environment").value
          : "pro";
        const reportDataDto = {
          metrics: this.selectedMetrics,
          reportType: this.reportType,
          dimensions: this.dimensionsAddedList,
          dateRange: this.selectedRange,
          filters: this.filterDimensionsData,
          env: symfonyEnv,
          onlyOwnStats: this.shouldShowOnlyOwnStats,
          csv: csv
        };

        this.$emit("launched-report", reportDataDto);
      }
    },
    selectRange: function(ev) {
      this.selectedRange = ev;
    }
  },
  mounted: function() {
    this.setMinDateDaterangepicker(false);

    if (this.userIsAdvertiser) {
      this.dimensionsList = [
        {
          value: "campaign_name",
          txt: this.trans("reports.generate.campaign_name"),
          xtraClass: "list-item-wicon",
          icon: true,
          filterOther: false,
          searchOnlyExact: true
        }
      ];
      this.metrics = [
        "impressions",
        "cpm",
        "vtr",
        "revenue",
        "viewability_percent",
        "ctr",
        "complete_views",
        "clicks",
        "viewable_impressions",
        "viewed25",
        "viewed50",
        "viewed75",
        "25perc",
        "50perc",
        "75perc"
      ];
      this.selectedMetrics = [
        "impressions",
        "cpm",
        "revenue",
        "vtr",
        "viewability_percent"
      ];
    }

    if (!this.userIsSalesManagerHead && !this.userIsAdmin) {
      this.dimensionsList = this.dimensionsList.filter(
        d => d.value !== "sales_manager"
      );
    }
    if (!this.userIsAdmin) {
      this.dimensionsList = this.dimensionsList.filter(
        d =>
          d.value !== "advertiser" && d.value !== "dsp" && d.value !== "agency"
      );
      this.metrics = this.metrics.filter(
        m =>
          m !== "rebate_percent" && m !== "rebate_cost" && m !== "net_revenue"
      );
    }

    if (this.userIsCampaignViewer) {
      this.dimensionsList = this.dimensionsList.filter(
        d =>
          d.value !== "ssp" &&
          d.value !== "country" &&
          d.value !== "type" &&
          d.value !== "deal_id"
      );
    }

    this.generateReport(false);

    const self = this;

    this.dimensionsList.map(d => {
      let req = new XMLHttpRequest();
      req.open("GET", "/predictive_content/" + d.value);
      req.onreadystatechange = function() {
        if (this.readyState === 4) {
          if (this.status === 200) {
            self.xtraSearchFilter[d.value] = JSON.parse(this.responseText);
          } else {
            self.xtraSearchFilter[d.value] = [];
          }
        }
      };
      req.send();
    });
    this.elementsFiltered = this.dimensionsList.filter(
      el => this.dimensionsAddedList.indexOf(el.value) === -1
    );
  },
  directives: {
    onClickaway
  }
};
</script>

<style scoped lang="scss">
@import "../../../scss/_variables";
/* Your styles */
.panel-header {
  /* padding: 20px;*/
  border-bottom: none;
  user-select: none;
}
.search-form-container,
.buttons-container {
  border-bottom: 1px solid #f8f8f8;
  padding: 20px;
  &.search-form-container {
    padding-bottom: 0px;
  }
  @media #{$tablet} {
    padding-bottom: 0px;
  }
}
.center {
  text-align: center;
}
.date-wrapper {
  display: inline-block;
  @media #{$tablet} {
    width: 100%;
    ::v-deep {
      #reportrange-vue .from,
      #reportrange-vue .to {
        margin: 0 40px 0 0;
        display: inline-block;
      }
    }
  }
}

::v-deep {
  .dropdown-wrapper {
    display: inline-block;
    cursor: pointer;
    @media #{$tablet} {
      display: block;
      width: 100%;
      .b-dropdown,
      .metric-dropdown-trigger {
        width: 100%;
      }
    }

    button {
      padding: 10px 14px 9px !important;
      width: 170px;
      &::after {
        position: absolute;
        top: 8px;
        right: 15px;
        content: "";
        width: 12px;
        height: 100%;
      }
    }
  }

  #reportrange-wrapper.datepicker_reports {
    #reportrange-vue {
      padding: 10px 35px 8px 10px !important;
      .from,
      .to {
        font-family: quatro, sans-serif;
        margin-left: 42px;
        font-size: 12px;
      }
      .to {
        margin-left: 38px;
      }
    }
  }
}

.search-form-container {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  flex-wrap: wrap;

  > div {
    margin-right: 10px;
    margin-bottom: 10px;
  }

  @media #{$tablet} {
    display: block;
    /*align-items: center;
    justify-content: flex-start;
    flex-direction: column;*/
    > div {
      margin-right: 0px;
      margin-bottom: 8px;
    }
  }
}

.search-form-second-container {
  display: block;
  padding: 20px;
  padding-top: 0;
  border-bottom: 1px solid #f8f8f8;
}

.added-dimensions-wrapper {
  display: inline;
  span + span {
    margin-left: 8px;
  }
  @media #{$tablet} {
    display: inline-block;
    width: 100%;
  }
}

.metric-dropdown-trigger {
  font-size: 14px;
  font-family: Roboto, sans-serif;
  text-align: left;
  padding: 10px 14px 9px;
  border-radius: 2px;
  display: inline-block;
  vertical-align: bottom;
  margin-bottom: 0;
  width: 160px;
  background-color: #feefe6;
  border: 1px solid #feefe6;
  position: relative;
  color: rgb(66, 72, 83);

  &:hover {
    text-decoration: none;
    color: rgb(66, 72, 83);
  }

  &:after {
    position: absolute;
    top: 0;
    right: 15px;
    content: "";
    width: 12px;
    height: 100%;
    background: url(../../../img/icons/caret-gray1.png) no-repeat center/auto
      7px;
  }
}

.filterdimensions-toggle {
  position: relative;
  font-size: 14px;
  font-family: Roboto, sans-serif;
  text-align: left;
  padding: 10px 14px 9px;
  border: 1px solid #d7f3f6;
  background-color: #d7f3f6;
  border-radius: 2px;
  display: inline-block;
  min-width: 190px;
  width: 100%;
  color: rgb(66, 72, 83);
  &:hover {
    color: rgb(66, 72, 83);
    text-decoration: none;
  }
  &:focus {
    background-color: #d7f3f6 !important;
    border-color: none !important;
    box-shadow: none !important;
  }
  &::after {
    position: absolute;
    top: 0;
    right: 15px;
    content: "";
    width: 12px;
    height: 100%;
    background: url(../../../img/icons/caret-gray1.png) no-repeat 50% / auto 7px;
  }
}

.fd-menu {
  position: relative;
  margin: 10px 0;
  background: #fff;
  color: #78889a;
  border-radius: 2px;
  box-shadow: 0 0 39px -3px rgba(56, 61, 71, 0.1);
  .close-fdm {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 20px;
    height: 20px;
    background: url(../../../img/icons/icon-close2.png) no-repeat 50%/11px;
    z-index: 1;
  }

  .fdm-body {
    margin: 10px;
    margin-bottom: 0;
    font-family: quatro, sans-serif;
  }
  .fdm-footer {
    &.advertiser {
      display: none; // no tiene sentido, sólo hay una dimensión
    }
    width: 100%;
    background-color: #ebeff6;
    padding: 17px 18px;
    border-top: 1px solid #f0f2f6;
    span {
      margin-right: 10px;
      font-size: 14px;

      a {
        color: #41a3ff;
        margin-right: 10px;
        font-size: 14px;
        text-decoration: none;
      }

      span.more-filters {
        position: relative;

        .main-mf {
          color: #41a3ff;
          cursor: pointer;
          position: relative;
          padding-right: 27px;
          display: inline-block;
          &::after {
            position: absolute;
            top: 0;
            right: 10px;
            content: "";
            width: 12px;
            height: 100%;
            background: url(../../../img/icons/caret-blue.png) no-repeat 50% /
              auto 7px;
          }
        }

        .mf-inner {
          position: absolute;
          z-index: 3;
          padding: 5px 0;
          background: #fff;
          color: #78889a;
          border-radius: 2px;
          z-index: 5;
          box-shadow: 0 0 39px -3px rgba(56, 61, 71, 0.1);
          width: 200px;
          max-width: 300px;
          left: 5px;
          top: 38px;
          margin: 0;
          display: none;
          a {
            padding: 7px 15px;
            display: block;
            margin: 0;
            color: #78889a;
          }
        }
      }
    }
  }
}

.buttons-container {
  .btn-run-report {
    padding: 7px 31px 5px;
    background: #41a3ff;

    font-family: quatro, sans-serif;

    line-height: 1;

    width: 201.31px;
    text-align: left;

    white-space: nowrap;

    &:focus,
    &:hover {
      background: #41a3ff !important;
      box-shadow: none !important;
      border-color: #41a3ff !important;
    }
    span.icon:before {
      background: url(../../../img/icons/icon-play.png) no-repeat 50% / contain;
      margin: 0 10px 0px 0;
      display: inline-block;
      content: "";
      width: 21px;
      height: 21px;
      vertical-align: middle;
      position: relative;
      top: -1px;
    }

    &.loading {
      span.icon:before {
        background: url(../../../img/loading.svg) no-repeat 50% / contain;
      }
    }

    &.disabled-btn {
      border-color: #aaa !important;
      background-color: #aaa !important;
      &.loading {
        background-color: #f33 !important;
        border-color: #f33 !important;
      }
    }

    @media #{$tablet} {
      width: 100%;
      margin-bottom: 8px;
    }
  }
  .btn-run-csv {
    border: 1px solid #41a3ff !important;
    color: #41a3ff;
    padding: 7px 32px;
    height: 35px;
    font-family: quatro, sans-serif;
    margin-left: 6px;
    line-height: 1;
    width: 186.68px;
    text-align: left;
    white-space: nowrap;
    @media #{$tablet} {
      margin-bottom: 8px;
    }

    &:hover {
      background-color: #e6f2fe;
      color: #41a3ff;
    }
    span.icon:before {
      background: url(../../../img/icons/icon-csv.png) no-repeat 50% / contain;
      display: inline-block;
      content: "";
      width: 17px;
      height: 17px;
      margin: 0 10px -3px 0;
    }
    &:focus,
    &:active {
      box-shadow: none !important;
      border: 1px solid #41a3ff !important;
      background-color: #e6f2fe !important;
      color: #41a3ff !important;
    }

    &.loading {
      span.icon:before {
        background: url(../../../img/loading.svg) no-repeat 50% / contain;
      }
    }

    &.disabled-btn {
      border-color: #aaa !important;
      background-color: #aaa !important;
      color: #fff !important;
      span.icon {
        filter: grayscale(1);
      }
      &.loading {
        background-color: #f33 !important;
        border-color: #f33 !important;
      }
    }

    @media #{$tablet} {
      width: 100%;
      margin-left: 0;
    }
  }
}

#collapse-metrics {
  padding-left: 20px;
  padding-right: 20px;
}

::v-deep {
  .report-type-button {
    border-radius: 0 !important;
    background-color: #ebeff6 !important;
    color: #424853 !important;
    border: none !important;
    width: 170px;
    height: 37px;

    text-align: left !important;
    pointer-events: none;

    &:focus {
      box-shadow: none;
    }

    &.dropdown-toggle {
      font-size: 14px;

      padding: 10px 14px 9px;
      line-height: 1.2;

      &::after {
        margin-left: 0 !important;
        position: absolute;
        right: 15px;
        border: none !important;
        border-top: none !important;
        border-bottom: none !important;
        border-left: none !important;
        border-right: none !important;
        width: 12px;
        height: 100%;
        background: url(../../../img/icons/caret-gray1.png) no-repeat
          center/auto 7px;
        vertical-align: inherit;
        bottom: 0px;
        top: 0px;
      }
    }
  }
  .vue-dropdown-menu {
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    box-shadow: 0 0 39px -3px rgba(56, 61, 71, 0.1);
    transition: opacity 0.2s ease-in, visibility 0.2s ease-in;
    li {
      a {
        font-size: 14px;
        padding: 10px 16px !important;
        font-family: Quatro, sans-serif;
        color: rgb(120, 136, 154) !important;
      }
    }
  }
}

.metrics-wrapper {
  box-shadow: 0px 0px 39px -3px rgba(56, 61, 71, 0.1);
  background-color: #ffffff;
  border-radius: 4px;
  padding: 20px;
  margin-bottom: 20px;
  font-family: "Roboto", sans-serif;
  position: relative;
  .close-mw {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 20px;
    height: 20px;
    background: url(../../../img/icons/icon-close2.png) no-repeat center/11px;
  }
  .mw-title {
    margin-bottom: 20px;
    .mwt-main {
      color: #2f3848;
      font-size: 16px;
      font-weight: 600;
      margin-right: 10px;
    }
    .unselect-all-metrics-trigger {
      color: #41a3ff;
      font-size: 14px;
      margin-right: 10px;
    }
    .info {
      color: #a2aab8;
      font-size: 14px;
    }
  }

  .sm-empty {
    background-color: #ebeff6;
    border-radius: 2px;
    max-width: 993px;
    text-align: center;
    margin: 0 0 30px !important;
    padding: 15px 20px;
    display: none;
  }

  .sm-container {
    display: inline-block;
    width: 800px;
    margin-bottom: 20px;
    .metric-item {
      text-decoration: none !important;
      color: #000;
      display: inline-block;
      border: 1px solid #c6cdd7;
      padding: 6px 40px 6px 10px;
      font-size: 14px;
      margin-bottom: 10px;
      margin-right: 10px;
      border-radius: 2px;
      position: relative;
      cursor: move;
      @media #{$tablet} {
        width: 100%;
      }
      .close {
        background: url(../../../img/icons/icon-close2.png) no-repeat right 10px
          center/10px;
        position: absolute;
        cursor: pointer;
        right: 0;
        top: 0;
        height: 100%;
        width: 30px;
      }
    }
  }

  .unselected-metrics-wrapper {
    a {
      display: inline-block;
      background-color: #ebeff6;
      padding: 6px 10px;
      font-size: 14px;
      margin-bottom: 10px;
      margin-right: 10px;
      border-radius: 2px;
      text-decoration: none !important;
      color: #000;
      @media #{$tablet} {
        width: 100%;
      }
    }
  }
}
</style>
