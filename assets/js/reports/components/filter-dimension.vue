<template>
  <li class="filter-wrapper from-metric without-padding">
    <div class="left">
      <p class="filter-name" v-bind:data-value="dim">
        {{
          dimensionsList.find(d => d.value === dim)
            ? dimensionsList.find(d => d.value === dim).txt
            : dim
        }}
      </p>
    </div>
    <div class="right">
      <div
        class="flex-top"
        v-if="addedFilters.length > 0"
        v-bind:class="selectedAtLeastOne ? 'active' : ''"
      >
        <b-dropdown
          :text="trans('reports.generate.' + includeExclude)"
          menu-class="vue-dropdown-menu"
          class="include-exclude-select"
          :toggle-class="includeExclude"
        >
          <b-dropdown-item @click="onIncludeExclude('include')">
            {{ trans("reports.generate.include") }}</b-dropdown-item
          >
          <b-dropdown-item @click="onIncludeExclude('exclude')">
            {{ trans("reports.generate.exclude") }}</b-dropdown-item
          >
        </b-dropdown>
        <div class="rm-box-wrapper">
          <p
            :title="addedFilter"
            class="rm-box"
            v-for="(addedFilter, idx) in addedFilters"
            v-bind:data-dimension="dim"
            v-bind:key="addedFilter + addedFiltersValues[idx] || addedFilter"
            v-bind:class="includeExclude"
          >
            {{ addedFilter }}
            <input
              type="hidden"
              class="added-filter-value"
              v-if="addedFiltersValues[idx]"
              :value="addedFiltersValues[idx]"
            />
            <input
              type="hidden"
              class="added-filter-value"
              v-else
              :value="addedFilter"
            />
            <span
              class="rm-box-btn"
              v-on:click="removeSettedFilter(addedFilter, idx)"
            ></span>
          </p>
        </div>
        <div class="form-field check-option">
          <input
            type="checkbox"
            v-bind:name="
              'fc-' +
                dim
                  .split('_')
                  .map(e => e.substring(0, 1).toUpperCase() + e.substring(1))
                  .join('_')
            "
            v-bind:id="
              'fc-' +
                dim
                  .split('_')
                  .map(e => e.substring(0, 1).toUpperCase() + e.substring(1))
                  .join('_')
            "
            @change="checkboxChanged"
            v-bind:checked="selectedAtLeastOne"
            v-model="checkboxSelected"
            style="opacity: 0;"
          />
          <label
            class="show-checkbox"
            style="opacity: 0;"
            v-bind:for="
              'fc-' +
                dim
                  .split('_')
                  .map(e => e.substring(0, 1).toUpperCase() + e.substring(1))
                  .join('_')
            "
          ></label>
        </div>
      </div>
      <div class="form-field without-margin">
        <div class="search-input">
          <input
            type="text"
            v-on:focus="showResults = true"
            v-on:blur="showResults = false"
            placeholder="Buscar"
            v-on:keyup.enter="selectSelectedElement()"
            v-on:keydown.down="nextFilter()"
            v-on:keydown.up="previousFilter()"
            v-on:keyup="updateFilters()"
            v-bind:data-filter-id="dim"
            v-model="searchTerm"
          />
          <a
            href="javascript:void(0)"
            class="clear-search"
            v-on:click="searchTerm = ''"
            v-bind:style="searchTerm ? 'display: block;' : 'display: none;'"
          ></a>
        </div>
        <a
          href="javascript:void(0)"
          class="rm-btn"
          v-on:click="deleteFilter(dim)"
        ></a>
        <div
          v-on:mouseover="overResults = true"
          v-on:mouseout="overResults = false"
          class="search-res-wrapper"
          v-if="searchTerm !== '' && (showResults || overResults)"
          v-bind:class="searchTerm !== '' ? 'active' : ''"
        >
          <div
            v-if="!dimensionsList.find(d => d.value === dim).searchOnlyExact"
            v-on:click="
              selectFilterRes(
                trans('reports.filters.contains', { filter: searchTerm })
              )
            "
            class="single-res"
          >
            {{ trans("reports.filters.contains", { filter: searchTerm }) }}
          </div>
          <div
            v-if="!dimensionsList.find(d => d.value === dim).searchOnlyExact"
            v-on:click="
              selectFilterRes(
                trans('reports.filters.begins', { filter: searchTerm })
              )
            "
            class="single-res"
          >
            {{ trans("reports.filters.begins", { filter: searchTerm }) }}
          </div>
          <div
            v-if="!dimensionsList.find(d => d.value === dim).searchOnlyExact"
            v-on:click="
              selectFilterRes(
                trans('reports.filters.ends', { filter: searchTerm })
              )
            "
            class="single-res"
          >
            {{ trans("reports.filters.ends", { filter: searchTerm }) }}
          </div>
          <div
            v-if="!dimensionsList.find(d => d.value === dim).searchOnlyExact"
            v-on:click="
              selectFilterRes(
                trans('reports.filters.exact', { filter: searchTerm })
              )
            "
            class="single-res"
          >
            {{ trans("reports.filters.exact", { filter: searchTerm }) }}
          </div>
          <div
            v-for="(xtra, idx) in searchXtraValuesFiltered"
            v-on:click="selectFilterRes(xtra)"
            class="single-res"
            v-bind:key="xtra + idx"
            v-bind:style="
              xtra.toLowerCase().indexOf(searchTerm.toLowerCase()) > -1
                ? 'display: block'
                : 'display: none;'
            "
          >
            {{ xtra }}
          </div>
        </div>
      </div>
    </div>
  </li>
</template>

<script>
let searchTerm = "";
let addedFilters = [];
let addedFiltersValues = [];
let includeExclude = "include";
let selectedAtLeastOne = false;
let showResults = false;
let overResults = false;
let selectedElement = 0;
let searchXtraValuesFiltered = [];
let searchXtraIds = [];
let searchXtraValuesComponent = [];
let currentVal = "";
let checkboxSelected = true;
export default {
  components: {},
  data: function() {
    return {
      currentVal,
      searchTerm,
      addedFilters,
      addedFiltersValues,
      includeExclude,
      selectedAtLeastOne,
      showResults,
      overResults,
      selectedElement,
      searchXtraValuesFiltered,
      searchXtraIds,
      searchXtraValuesComponent,
      checkboxSelected
    };
  },
  props: {
    dim: String,
    dimensionsList: Array,
    searchXtraValues: Array,
    defaultSelected: Array
  },
  methods: {
    trans: function(txt, params) {
      return Translator.trans(txt, params);
    },
    deleteFilter: function(dim) {
      this.$emit("deletedFilter", dim);
      this.addedFilters = [];
      this.$emit("filter-dimension-change", {
        dim: this.dim,
        addedFilters: this.addedFiltersValues.length
          ? this.addedFiltersValues
          : this.addedFilters,
        include: this.includeExclude,
        checkboxSelected: false
      });
    },
    selectFilterRes: function(val) {
      if (!this.selectedAtLeastOne) {
        this.$emit("fullfilled");
      }
      if (this.dim === "publisher_manager") {
      }
      if (this.addedFilters.indexOf(val) === -1 || !isNaN(val)) {
        this.selectedAtLeastOne = true;
        // si nos viene un valor numérico (aunque como string), buscamos si existe entre los xtraIds
        if (!isNaN(val)) {
          // es un número, buscamos en los searchXtraIds
          if (this.searchXtraIds.indexOf(val) > -1) {
            val = this.searchXtraValuesComponent[
              this.searchXtraIds.indexOf(val)
            ];
          }
        }
        // si tenemos searchXtraIds, debemos usar su valor en vez de la string
        if (this.searchXtraIds.length) {
          this.addedFiltersValues.push(
            this.searchXtraIds[this.searchXtraValuesComponent.indexOf(val)]
          );
        } else {
          this.addedFiltersValues = [];
        }
        this.addedFilters.push(val);
        this.searchTerm = "";
        this.$emit("filter-dimension-change", {
          dim: this.dim,
          addedFilters: this.addedFiltersValues.length
            ? this.addedFiltersValues
            : this.addedFilters,
          include: this.includeExclude,
          checkboxSelected: true
        });
      }
    },
    checkboxChanged: function() {
      this.$emit("filter-dimension-change", {
        dim: this.dim,
        addedFilters: this.addedFiltersValues.length
          ? this.addedFiltersValues
          : this.addedFilters,
        include: this.includeExclude,
        checkboxSelected: true
      });
    },
    onIncludeExclude: function(val) {
      this.includeExclude = val;
      this.$emit("filter-dimension-change", {
        dim: this.dim,
        addedFilters: this.addedFiltersValues.length
          ? this.addedFiltersValues
          : this.addedFilters,
        include: this.includeExclude,
        checkboxSelected: true
      });
    },
    removeSettedFilter: function(filter, idx) {
      this.addedFilters.splice(idx, 1);
      if (this.addedFiltersValues.length) {
        this.addedFiltersValues.splice(idx, 1);
      }
      //let idx = this.addedFilters.indexOf(filter);
      //if (idx > -1) {
      //    this.addedFilters.splice(idx, 1);
      if (this.addedFilters.length === 0) {
        this.selectedAtLeastOne = false;
        this.$emit("deletedFullfiled");
      }
      this.$emit("filter-dimension-change", {
        dim: this.dim,
        addedFilters: this.addedFiltersValues.length
          ? this.addedFiltersValues
          : this.addedFilters,
        include: this.includeExclude,
        checkboxSelected: false
      });
      //}
    },
    selectSelectedElement: function() {
      const allResults = Array.from(
        document
          .querySelector(".search-res-wrapper")
          .querySelectorAll("div.single-res")
      );
      allResults[this.selectedElement].click();
      this.selectedElement = 0;
    },
    closeResultsWithTimeout: function() {
      setTimeout(() => {
        this.showResults = false;
      }, 500);
    },
    updateFilters: function() {
      if (
        this.searchXtraValuesComponent &&
        this.searchXtraValuesComponent.length > 0
      ) {
        this.searchXtraValuesFiltered = this.searchXtraValuesComponent
          .filter(
            xtra =>
              xtra.toLowerCase().indexOf(this.searchTerm.toLowerCase()) > -1 &&
              this.addedFilters.indexOf(xtra) === -1
          )
          .sort(
            (a, b) =>
              a.toLowerCase().indexOf(this.searchTerm.toLowerCase()) -
              b.toLowerCase().indexOf(this.searchTerm.toLowerCase())
          )
          .slice(0, 10);
      }
    },
    nextFilter: function() {
      const allResults = Array.from(
        document
          .querySelector(".search-res-wrapper")
          .querySelectorAll("div.single-res")
      );
      if (this.selectedElement !== null) {
        this.selectedElement++;
        if (this.selectedElement > allResults.length - 1) {
          this.selectedElement = 0;
        }
      } else {
        this.selectedElement = 0;
      }
      allResults.map(
        function(r, idx, arr) {
          if (this.selectedElement % arr.length === idx) {
            r.classList.add("selected");
            document.querySelector(".search-res-wrapper").scrollTop =
              r.offsetTop;
          } else {
            r.classList.remove("selected");
          }
        }.bind(this)
      );
    },
    previousFilter: function() {
      const allResults = Array.from(
        document
          .querySelector(".search-res-wrapper")
          .querySelectorAll("div.single-res")
      );
      if (this.selectedElement !== null) {
        this.selectedElement--;
        if (this.selectedElement < 0) {
          this.selectedElement = allResults.length - 1;
        }
      } else {
        this.selectedElement = allResults.length - 1;
      }
      allResults.map(
        function(r, idx, arr) {
          if (this.selectedElement % arr.length === idx) {
            r.classList.add("selected");
            document.querySelector(".search-res-wrapper").scrollTop =
              r.offsetTop;
          } else {
            r.classList.remove("selected");
          }
        }.bind(this)
      );
    }
  },
  created: function() {
    addedFilters = [];
    this.addedFiltersValues = [];
    this.searchXtraIds = [];
    this.currentVal = "";
    if (
      this.searchXtraValues &&
      this.searchXtraValues.length &&
      typeof this.searchXtraValues[0] === "object" &&
      this.searchXtraValues[0] !== null
    ) {
      this.searchXtraIds = this.searchXtraValues.map(o => o.id);
      this.searchXtraValuesComponent = this.searchXtraValues.map(
        o => o.nicename
      );
    } else {
      this.searchXtraValuesComponent = this.searchXtraValues;
    }
  },
  watch: {
    defaultSelected: function(newVal, oldVal) {
      if (newVal.length > 0 && newVal.find(v => v !== undefined)) {
        newVal.map(v => v && v.value && this.selectFilterRes(v.value));
        this.includeExclude =
          newVal && newVal[0] ? newVal[0].include : "include";
        if (newVal.filter(o => o && o.value && !isNaN(o.value))) {
          //this.addedFilters = [];
          newVal.map(o => o && o.value && this.selectFilterRes(o.value));
        }
      }
    },
    searchXtraValues: function(newVal, oldVal) {
      // si es un objeto, es que tenemos el id, y debe ser el que usemos para enviar
      if (
        newVal.length &&
        typeof newVal[0] === "object" &&
        newVal[0] !== null
      ) {
        this.searchXtraIds = newVal.map(o => o.id);
        this.searchXtraValuesComponent = newVal.map(o => o.nicename);
      } else {
        this.searchXtraValuesComponent = newVal;
      }
      if (this.defaultSelected.filter(o => o && o.value && !isNaN(o.value))) {
        this.addedFilters = [];
        this.defaultSelected.map(
          o => o && o.value && this.selectFilterRes(o.value)
        );
      }
    }
  },
  mounted: function() {
    this.currentVal = "";
    this.addedFiltersValues = [];
    if (this.defaultSelected && this.defaultSelected.length > 0) {
      this.defaultSelected.map(
        v => v && v.value && this.selectFilterRes(v.value)
      );
      this.includeExclude =
        this.defaultSelected && this.defaultSelected[0]
          ? this.defaultSelected[0].include
          : "include";
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../scss/_variables";
.search-res-wrapper {
  position: absolute;
  z-index: 3;
  padding: 5px 0;
  background: #fff;
  color: #78889a;
  border-radius: 2px;
  z-index: 5;
  box-shadow: 0px 0px 39px -3px rgba(56, 61, 71, 0.1);
  width: 100%;
  max-width: 300px;
  left: 5px;
  top: 38px;
  visibility: hidden;
  opacity: 0;
  max-height: 250px;
  overflow: auto;
  &.active {
    visibility: visible;
    opacity: 1;
  }
  .single-res {
    font-family: quatro, sans-serif;
    font-size: 14px;
    padding: 7px 15px;
    cursor: pointer;
    &:hover,
    &.selected {
      background-color: #f5f7fa;
    }
  }
}
.form-field.without-margin {
  margin: 0;
}
li.without-padding {
  padding: 10px !important;
}
li.filter-wrapper {
  display: flex;
  border-bottom: 1px solid #f8f8f8;
  position: relative;
  padding-top: 20px !important;
  @media #{$tablet} {
    flex-wrap: wrap;
  }
}
.left {
  display: flex;
  align-items: baseline;
  width: 166px;
  font-size: 14px;
  @media #{$tablet} {
    width: 100%;
  }
}
.right {
  width: calc(100% - 166px);
  text-align: left;
  @media #{$tablet} {
    width: 100%;
  }
  .search-input {
    display: inline-block;
    width: 300px;
    position: relative;
    @media #{$tablet} {
      width: calc(100% - 45px);
    }
    input {
      width: 100%;
      padding-right: 30px;
    }
    a.clear-search {
      position: absolute;
      width: 10px;
      height: 10px;
      background: url(../../../img/icons/icon-close2.png) no-repeat 50% /
        contain;
      right: 10px;
      top: 50%;
      -webkit-transform: translateY(-50%);
      transform: translateY(-50%);
      cursor: pointer;
      opacity: 0.6;
      display: none;
    }
  }
  .rm-btn {
    width: 36px;
    height: 36px;
    background: rgba(255, 64, 64, 0.1) url(../../../img/icons/icon-trash.png)
      no-repeat 50%/40%;
    display: inline-block;
    vertical-align: middle;
    border-radius: 50%;
    margin-left: 5px;
    text-align: center;
    line-height: 35px;
  }
  .rm-box {
    padding: 9px 30px 7px 14px;
    border-radius: 3px;
    font-size: 14px;
    font-family: Roboto, sans-serif;
    height: 36px;
    display: inline-block;
    margin: 5px 5px;
    position: relative;
    color: #78889a;
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    &.include {
      background-color: rgba(100, 216, 212, 0.2);
      .rm-box-btn:hover {
        background-color: rgba(92, 214, 210, 0.2);
      }
    }
    &.exclude {
      background-color: hsla(0, 100%, 71%, 0.1);
      .rm-box-btn:hover {
        background-color: rgba(255, 97, 97, 0.2);
      }
    }
    .rm-box-btn {
      position: absolute;
      width: 25px;
      height: 100%;
      background: url(../../../img/icons/icon-close2.png) no-repeat 50%;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      opacity: 0.6;
      &:hover {
        opacity: 1;
      }
    }
  }
  .flex-top {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    label {
      margin-bottom: 0;
    }
    @media #{$tablet} {
      height: auto;
      p {
        height: auto;
        margin-bottom: 5px;
        font-size: 12px;
        margin-right: 0;
      }
    }
    ::v-deep .include-exclude-select {
      button {
        width: 100% !important;
        font-size: 14px !important;
        font-family: Roboto, sans-serif !important;
        text-align: left !important;
        /*padding: 10px 14px 12px !important;*/
        padding-right: 35px !important;
        border: 1px solid #e0e3e6 !important;
        border-radius: 2px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        height: 37px !important;
        &.exclude {
          background-color: #ff6b6b !important;
          border-color: #ff6b6b !important;
          color: #fff !important;
        }
        &.include {
          background-color: #64d8d4 !important;
          border-color: #64d8d4 !important;
          color: #fff !important;
        }
        &::after {
          margin-left: 0 !important;
          border: none !important;
          width: 12px;
          background: url(../../../img/icons/caret-white.png) no-repeat center
            7px;
          vertical-align: inherit !important;
          position: absolute;
          right: 10px;
          height: 15px;
          top: 8px;
        }
      }
    }
  }
}
.filter-name {
  padding-top: 8px;
  padding-left: 5px;
  margin-bottom: 10px;
}
.rm-box-wrapper {
  display: flex;
  flex-wrap: wrap;
}
</style>
