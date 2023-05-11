<template>
  <div class="dropdown-wrapper" @click="toggleMe">
    <b-dropdown
      :text="elementSelected.txt"
      class="selected-dropdown"
      :class="finalDropdownClass"
      menu-class="vue-dropdown-menu"
      toggle-class="btn-added-dimension-dropdown"
      size="sm"
      ref="dropdown"
      :no-flip="true"
    >
      <template v-slot:button-content>
        <span class="black-button-text">{{ elementSelected.txt }}</span>
      </template>
      <b-dropdown-item
        v-if="elementsFiltered.length == 0"
        :key="'delete'"
        @click="selectedElement('delete')"
      >
        <div class="element-container">
          <span class="noic">{{ "- Dimensión" }}</span>
        </div>
      </b-dropdown-item>
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
    dimension: String,
    index: Number,
    "elements-filtered": Array,
    "element-selected": Object
  },
  methods: {
    selectedElement: function(val) {
      if (val === "delete") {
        this.$emit("deleted-dimension", this.index);
      } else {
        this.finalDropdownClass =
          "dropdown form th-s-panel dark added-dimension";
        this.finalButtonClass = "";
        this.$emit("selected-dimension", { idx: this.index, val });
        //this.elementSelected = val;
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
  beforeUpdate: function() {
    /*this.elementsFiltered.unshift({value: 'delete', txt: '- Dimensión'});

            this.finalDropdownClass = 'dropdown form th-s-panel dark added-dimension';
            this.finalButtonClass = '';*/
  },
  beforeMount: function() {
    // this.elementsFiltered = this.elements.filter(el => this.addedDimensions.indexOf(el.value) === -1);

    this.elementsFiltered.unshift({ value: "delete", txt: "- Dimensión" });

    //this.elementSelected = this.elements.find(el => this.defaultValue === el.value);

    this.finalDropdownClass = "dropdown form th-s-panel dark added-dimension";
    this.finalButtonClass = "";
  },
  updated: function() {
    if (this.elementsFiltered[0].value !== "delete") {
      this.elementsFiltered.unshift({ value: "delete", txt: "- Dimensión" });
    }

    this.finalDropdownClass = "dropdown form th-s-panel dark added-dimension";
    this.finalButtonClass = "";
  },
  created: function() {
    this.opened = false;
  }
};
</script>
<style lang="scss" scoped>
.selected-dropdown {
  background-color: #e6f2fe;
  .black-button-text {
    color: #424853 !important;
    display: block;
    background-color: #e6f2fe;
  }
}
::v-deep {
  .btn-added-dimension-dropdown {
    width: auto;
    min-width: 150px;
    height: 37px;
    pointer-events: none;
    background-color: #e6f2fe !important;
    border-color: #e6f2fe !important;

    text-align: left !important;

    &:hover {
      background-color: #e6f2fe !important;
    }
    &:focus {
      background-color: #e6f2fe !important;
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
