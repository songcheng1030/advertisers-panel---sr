<template>
  <div class="dropdown-wrapper margintopOnMobile" @click="toggleMe">
    <b-dropdown
      :text="defaultText"
      class="selected-dropdown"
      :class="finalDropdownClass"
      menu-class="vue-dropdown-menu"
      size="sm"
      toggle-class="btn-dimension-dropdown"
      variant="outline-secondary"
      ref="dropdown"
      :no-flip="true"
    >
      <template v-slot:button-content>
        <span class="blue-button-text">{{ defaultText }}</span>
      </template>
      <template v-for="elem in elementsFiltered">
        <b-dropdown-item
          v-if="elem.value !== ''"
          :key="elem.value"
          @click="selectedElement(elem.value)"
        >
          <div class="element-container">
            <span v-if="elem.value !== 'delete'" class="icon"></span>
            <span class="noic">{{ elem.txt }}</span>
          </div>
        </b-dropdown-item>
        <b-dropdown-header v-else tag="div" :key="elem.txt">
          {{ elem.txt }}
        </b-dropdown-header>
      </template>
    </b-dropdown>
  </div>
</template>

<script>
const opened = false;
export default {
  components: {},
  data: function() {
    return {
      // elementsFiltered: this.elementsFiltered,
      finalDropdownClass: this.finalDropdownClass,
      finalButtonClass: this.finalButtonClass,
      opened
    };
  },
  props: {
    elements: Array,
    name: String,
    id: String,
    defaultValue: String,
    defaultText: String,
    wide: String,
    "dropdown-class": String,
    "button-class": String,
    "added-dimensions": Array,
    "elements-filtered": Array
  },
  methods: {
    selectedElement: function(val) {
      if (val === "delete") {
        this.$emit("deleted-dimension", this.index);
      } else {
        this.finalDropdownClass = "dropdown form th-s-panel dark";
        this.finalButtonClass = "";
        this.$emit("selected-dimension", { idx: -1, val });
      }
    },
    toggleMe: function() {
      if (this.$refs.dropdown) {
        if (this.opened) {
          this.$refs.dropdown.hide();
        } else {
          this.$refs.dropdown.show();
        }
      }
      this.opened = !this.opened;
    }
  },
  beforeMount: function() {
    this.elementsFiltered = this.elements.filter(
      el => this.addedDimensions.indexOf(el.value) === -1
    );
    this.finalDropdownClass = this.dropdownClass;
    this.finalButtonClass = this.buttonClass;
  },
  created: function() {},
  watch: {
    // addedDimensions: function(val, oldVal) {
    //   this.elementsFiltered = this.elements.filter(
    //     el => this.addedDimensions.indexOf(el.value) === -1
    //   );
    // }
  }
};
</script>
<style lang="scss" scoped>
@import "../../../scss/_variables";
.selected-dropdown .blue-button-text {
  color: #41a3ff !important;
  display: block;
}

.margintopOnMobile {
  @media #{$tablet} {
    margin-top: 8px;
  }
}
::v-deep {
  .btn-dimension-dropdown {
    border-radius: 0 !important;
    width: 140px;
    height: 37px;
    border: 1px solid #e0e3e6 !important;
    pointer-events: none;
    text-align: left !important;
    display: block;
    &:hover {
      color: #6c757d !important;
      background: transparent !important;
    }
    &:focus {
      background-color: transparent !important;
      border-color: none !important;
      box-shadow: none !important;
    }
    &::after {
      margin-left: 0 !important;
      border: none !important;
      width: 12px;
      background: url(../../../img/icons/caret-gray1.png) no-repeat center 7px;
      vertical-align: inherit !important;
      position: absolute;
      right: 10px;
      height: 15px;
      top: 8px;
    }
  }

  .dropdown-menu {
    border: none !important;
    box-shadow: 0 0 39px -3px rgba(56, 61, 71, 0.1);
    transition: opacity 0.2s ease-in, visibility 0.2s ease-in;
  }

  .dropdown-item {
    padding: 0 !important;
    width: 238px !important;
    .element-container {
      display: block;
      overflow: hidden;
      text-overflow: ellipsis;
      .icon {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-bottom: 1px dotted #78889a;
        border-left: 1px dotted #78889a;
        margin: 0 5px 0 10px;
        transform: translateY(-4px);
      }
      .noic {
        color: rgb(120, 136, 154);
        font-family: quatro, sans-serif;
        font-size: 14px;
      }
    }
  }
}
</style>
