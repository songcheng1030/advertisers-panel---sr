<template>
  <div id="reportrange-wrapper" v-on-clickaway="clickedOutside">
    <div
      id="reportrange-vue"
      v-on:click="toggleDatepicker()"
      :class="active ? 'active' : ''"
      :data-min-date="minDate | formatISO"
      :data-max-date="maxDate | formatISO"
    >
      <span class="date">
        <span class="icon"></span>
        {{ trans("reports.generate.date") }}
      </span>
      <span class="from">
        {{ trans("reports.generate.from") }}
        <span>{{ startDate | formatDate }}</span>
        <input type="hidden" id="from" :value="startDate | formatISO" />
      </span>
      <span class="to">
        {{ trans("reports.generate.to") }}
        <span>{{ endDate | formatDate }}</span>
        <input type="hidden" id="to" :value="endDate | formatISO" />
      </span>
      <span class="caret"></span>
    </div>
    <transition name="fade">
      <div id="calendars-wrapper" v-show="active">
        <div id="first-row">
          <div id="default-ranges-wrapper">
            <button
              v-for="defaultRange in defaultRanges"
              v-bind:key="defaultRange.txt"
              :class="checkDefaultRange(defaultRange) ? 'selected' : ''"
              class="default-range"
              @click="selectDefaultRange(defaultRange)"
            >{{ defaultRange.txt }}</button>
            <button
              class="default-range"
              :class="checkNoDefaultRange() ? 'selected' : ''"
              @click="selectCustomRange()"
            >{{ trans("common.custom_date") }}</button>
          </div>
          <div id="left-calendar-wrapper" :class="isCustomRangeActive ? 'custom-range' : ''">
            <v-calendar
              ref="calendar-from"
              :first-day-of-week="2"
              :attributes="attrs"
              @dayclick="selectStart"
              :min-date="minDate"
              :max-date="maxDate"
              @daymouseenter="mouseoverOnDay"
              @daymouseleave="mouseoutOnDay"
              :locale="globalLocale"
            ></v-calendar>
          </div>
          <div id="right-calendar-wrapper" :class="isCustomRangeActive ? 'custom-range' : ''">
            <v-calendar
              ref="calendar-to"
              :first-day-of-week="2"
              :attributes="attrs"
              @dayclick="selectEnd"
              :min-date="minDate"
              :max-date="maxDate"
              @daymouseenter="mouseoverOnDay"
              @daymouseleave="mouseoutOnDay"
              :locale="globalLocale"
            ></v-calendar>
          </div>
        </div>
        <div id="second-row">
          <div id="buttons">
            <button id="ok" @click="apply()">{{ trans("common.apply") }}</button>
            <button id="ko" @click="cancel()">{{ trans("common.cancel") }}</button>
          </div>
          <div id="inputs">
            <span id="from">
              {{ trans("common.from") }}
              <span>{{ startDate | formatDate }}</span>
            </span>
            <span id="to">
              {{ trans("common.to") }}
              <span>{{ endDate | formatDate }}</span>
            </span>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>
<script>
import { directive as onClickaway } from "vue-clickaway";
import {
  isSameDay,
  isAfter,
  isBefore,
  differenceInDays,
  format
} from "date-fns";

let active = false;

let startDate = new Date();
let endDate = new Date();

const inBrowser = typeof window !== "undefined";

let isCustomRangeActive = false;

export default {
  data: function() {
    return {
      active,
      startDate,
      endDate,
      isCustomRangeActive,
      attrs: [
        {
          key: "range",
          highlight: true,
          dates: [{ start: startDate, end: endDate }]
        }
      ]
    };
  },
  props: {
    startDefaultDate: undefined,
    endDefaultDate: undefined,
    minDate: undefined,
    maxDate: undefined,
    defaultRanges: Array
  },
  methods: {
    trans: function(txt, params) {
      return Translator.trans(txt, params);
    },
    toggleDatepicker: function() {
      this.active = !this.active;
      if (this.active) {
        this.setBorderRadius();
      }
    },
    checkDefaultRange: function(defaultRange) {
      let startDateIsDefaultStart = isSameDay(
        defaultRange.range[0],
        this.startDate
      );
      let endDateIsDefaultStart = isSameDay(
        defaultRange.range[1],
        this.endDate
      );

      return startDateIsDefaultStart && endDateIsDefaultStart;
    },
    checkNoDefaultRange: function() {
      let noDefaultRange = true;

      this.defaultRanges.map(dr => {
        noDefaultRange = noDefaultRange && !this.checkDefaultRange(dr);
      });

      this.isCustomRangeActive = noDefaultRange;

      return noDefaultRange;
    },
    selectDefaultRange: function(defaultRange) {
      this.startDate = new Date(defaultRange.range[0]);
      this.endDate = new Date(defaultRange.range[1]);
      const rangeObj = { start: this.startDate, end: this.endDate };

      this.setParametersAndCalendarPage(rangeObj);

      this.active = false;
      this.isCustomRangeActive = false;

      this.$emit("range-selected", {
        start: this.startDate,
        end: this.endDate
      });
    },
    selectCustomRange: function() {
      this.startDate = new Date();
      this.endDate = new Date();
      this.endDate.setDate(this.endDate.getDate() - 1);
      const rangeObj = { start: this.startDate, end: this.endDate };

      this.setParametersAndCalendarPage(rangeObj);

      this.isCustomRangeActive = true;
    },
    selectStart: function(day) {
      if (day.date > this.endDate) {
        // si hemos elegido un final anterior al inicio, guardamos el numero de días que hay entrambos
        const difference = differenceInDays(this.endDate, this.startDate);

        // entonces, a la fecha final recien elegida le restamos esa misma diferencia
        this.startDate = new Date(day.date.getTime());

        this.endDate = new Date(
          new Date(day.date.getTime()).setDate(
            new Date(day.date.getTime()).getDate() + difference
          )
        );

        if (isAfter(this.endDate, this.maxDate)) {
          // si es posterior a la maxima fecha posible, seteamos el end a esa maxdate
          this.endDate = this.maxDate;
        }
      } else {
        this.startDate = new Date(day.date.getTime());
      }

      const rangeObj = { start: this.startDate, end: this.endDate };

      this.setParametersAndCalendarPage(rangeObj);
    },
    selectEnd: function(day) {
      if (day.date < this.startDate) {
        // si hemos elegido un final anterior al inicio, guardamos el numero de días que hay entrambos
        const difference = differenceInDays(this.startDate, this.endDate);

        // entonces, a la fecha final recien elegida le restamos esa misma diferencia
        this.endDate = new Date(day.date.getTime());

        this.startDate = new Date(
          new Date(day.date.getTime()).setDate(
            new Date(day.date.getTime()).getDate() + difference
          )
        );

        if (isBefore(this.startDate, this.minDate)) {
          // si es anterior a la minima fecha posible, seteamos el start a esa mindate
          this.startDate = this.minDate;
        }
      } else {
        this.endDate = new Date(day.date.getTime());
      }

      const rangeObj = { start: this.startDate, end: this.endDate };

      this.setParametersAndCalendarPage(rangeObj);
    },
    setParametersAndCalendarPage: function(rangeObj) {
      this.attrs = [
        {
          key: "range",
          highlight: true,
          dates: [rangeObj]
        }
      ];

      this.$emit("range-selected", {
        start: this.startDate,
        end: this.endDate
      });

      this.setCalendarPage();
    },
    setCalendarPage: function() {
      const calendarFrom = this.$refs["calendar-from"];
      const calendarTo = this.$refs["calendar-to"];

      calendarFrom.move(this.startDate).then(() => this.setBorderRadius());
      calendarTo.move(this.endDate).then(() => this.setBorderRadius());

      this.setBorderRadius();
    },
    setBorderRadius: function() {
      const sameDate = isSameDay(this.startDate, this.endDate);
      const elsStart = Array.from(
        document.querySelectorAll(".id-" + format(this.startDate, "yyyy-MM-dd"))
      );
      Array.from(document.querySelectorAll("[start-date]")).map(el => {
        el.parentElement.style.cssText = "";
        el.removeAttribute("start-date");
      });

      const elsEnd = Array.from(
        document.querySelectorAll(".id-" + format(this.endDate, "yyyy-MM-dd"))
      );
      Array.from(document.querySelectorAll("[end-date]")).map(el => {
        el.parentElement.style.cssText = "";
        el.removeAttribute("end-date");
      });
      if (!sameDate) {
        elsStart.map(function(el) {
          el.setAttribute("start-date", "");
          el.parentElement.style.cssText =
            "border-bottom-left-radius: 5px!important; border-top-left-radius: 5px!important; border: 1px solid rgb(65, 163, 255)";
        });

        elsEnd.map(function(el) {
          el.setAttribute("end-date", "");
          el.parentElement.style.cssText =
            "border-bottom-right-radius: 5px!important; border-top-right-radius: 5px!important; border: 1px solid rgb(65, 163, 255)";
        });
      } else {
        // aqui ambos grupos de elementos deberían ser el mismo, así que seteamos el borde redondeado completo sólo a los start
        elsStart.map(function(el) {
          el.setAttribute("start-date", "");
          el.parentElement.style.cssText =
            "border-bottom-right-radius: 5px!important; border-top-right-radius: 5px!important; border-bottom-left-radius: 5px!important; border-top-left-radius: 5px!important; border: 1px solid rgb(65, 163, 255)";
        });
      }
    },
    apply: function() {
      this.active = false;

      this.$emit("range-selected", {
        start: this.startDate,
        end: this.endDate
      });
    },
    cancel: function() {
      this.startDate = new Date();
      this.endDate = new Date();
      this.active = false;
    },
    clickedOutside: function() {
      this.active = false;
    },
    mouseoverOnDay: function(args) {
      Array.from(document.querySelectorAll(".overdiv")).map(el => el.remove());
      const overdiv = document.createElement("div");
      overdiv.style.cssText = `background-color: rgba(65,163,255,.2);border-radius: 3px; position: absolute;z-index: -5;height: 31px;width: 34px;margin-left: -1px;margin-top: -28px;`;
      overdiv.classList.add("overdiv");
      args.el.parentElement.appendChild(overdiv);
    },
    mouseoutOnDay: function(args) {
      Array.from(document.querySelectorAll(".overdiv")).map(el => el.remove());
    }
  },
  directives: { onClickaway },
  watch: {
    startDefaultDate: function(newVal, oldVal) {
      this.selectStart({ date: newVal });
    },
    endDefaultDate: function(newVal, oldVal) {
      this.selectEnd({ date: newVal });
    }
  },
  mounted: function() {
    if (this.startDefaultDate) {
      this.selectStart({ date: this.startDefaultDate });
    }
    if (this.endDefaultDate) {
      this.selectEnd({ date: this.endDefaultDate });
    }
  }
};
</script>
<style lang="scss">
@import "../../scss/_variables";

#calendars-wrapper {
  position: absolute;
  z-index: 50000;

  background: #fff;

  box-sizing: border-box;
  box-shadow: 0 8px 30px -5px rgba(56, 61, 71, 0.2);
  border-radius: 4px;

  /*margin: 10px;*/
  margin-top: 8px;

  right: var(--right-position-calendar);

  @media #{$tablet} {
    width: calc(100% - 40px);
  }

  div {
    &#first-row {
      display: flex;
      justify-content: center;

      @media #{$tablet} {
        flex-flow: column;
      }

      #default-ranges-wrapper {
        padding: 0 20px;
        padding-bottom: 3px;
        /* display: flex;
                flex-direction: column;
                justify-content: space-around;*/
        margin-top: 6px;
        margin-left: auto;
        margin-right: auto;

        button.default-range {
          /*margin: 0px 5px;*/
          display: block;
          padding: 9px 24px;
          margin-bottom: 0;
          margin-top: 5px;
          border-radius: 3px;
          width: 180px;
          border: 1px solid #fff;
          font-size: 14px;
          &.selected {
            border: 1px solid #41a3ff;
          }
          &:hover {
            border-radius: 0;
            background-color: rgba(65, 163, 255, 0.2);
          }
        }

        @media #{$tablet} {
          border-right: none;
        }
      }

      #left-calendar-wrapper {
        padding: 10px;
        padding-left: 8px;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        &.custom-range {
          height: auto;
        }
        @media #{$tablet} {
          padding: 0;
          margin: 0 auto;
          border-right: none;
          border-left: none;
          display: none;
          &.custom-range {
            display: block;
          }
        }

        div {
          border: 0;
        }
      }
      #right-calendar-wrapper {
        padding: 10px;
        &.custom-range {
          height: auto;
        }
        @media #{$tablet} {
          padding: 0;
          margin: 0 auto;
          display: none;
          &.custom-range {
            display: block;
          }
        }
        div {
          border: 0;
        }
      }
    }

    &#second-row {
      padding: 20px 14px 17px;
      border-top: 1px solid #ddd;
      display: flex;
      justify-content: space-between;

      #buttons {
        @media #{$tablet} {
          width: 100%;
        }
        button {
          margin-left: 5px;
          color: #ffffff;
          padding: 8px 20px 10px;
          font-size: 15px;
          font-weight: 400;
          width: 100px;
          border-radius: 3px;
          border: 1px solid #e4e7ed;
          height: 35px;

          @media #{$tablet} {
            width: 40%;
          }
          &#ok {
            border-color: #41a3ff;
            background-color: #41a3ff;
            &:hover {
              opacity: 0.8;
            }
          }
          &#ko {
            background-color: transparent;
            color: #424853;
            &:hover {
              background-color: #e4e7ed;
            }
            @media #{$tablet} {
              float: right;
            }
          }
        }
      }
      #inputs {
        @media #{$tablet} {
          display: none;
        }
        line-height: 38px;
        #from,
        #to {
          position: relative;
          bottom: 1px;
          left: 1px;
          color: #acaeb2;
          font-size: 12px;

          padding-left: 13px;
          padding-right: 6px;
          span {
            border: 1px solid #e4e7ed;

            color: #424853;
            font-size: 15px;
            padding: 7px 15px 9px;
            border-radius: 2px;
            margin-left: 6px;
          }
        }
        #from {
          padding-right: 2px;
        }
      }
    }
  }
}

#reportrange-vue {
  background: #fff;
  cursor: pointer;
  /*padding: 17px 20px;*/
  box-shadow: 0 2px 5px 0 rgba(#383d47, 0.03);
  width: 526px;
  display: flex;
  align-items: center;
  position: relative;
  /*  padding: var(--dropdown-padding);*/
  padding: 8px 38px 9px 14px;
  border: 1px solid var(--dropdown-border-color);
  &.active {
    border: 1px solid var(--dropdown-active-border-color);
  }
  background-color: var(--dropdown-background-color);
  border-radius: 2px;
  width: auto;
  @media #{$tablet} {
    padding: 4px 38px 9px 14px;
    width: 100%;
  }
  &.active {
    /*border-color: $bright-blue;*/

    .caret {
      transform: translateY(-50%) rotate(180deg);
    }
  }

  .date {
    font-family: "Roboto", sans-serif;
    color: var(--font-color-date);
    font-size: var(--font-size-date);
    font-weight: var(--weight-display-date);

    @media #{$tablet} {
      display: none;
    }
    .icon {
      background: url(../../img/icons/icon-calendar.png) no-repeat center /
        contain;
      height: 26px;
      width: 27px;
      display: var(--display-date-image);
      vertical-align: middle;
      margin-right: 10px;
    }
  }

  .from {
    margin-left: 22px;
    @media #{$tablet} {
      margin-left: 0;
      margin-right: 20px;
    }
    @media #{$small-phone} {
      margin-right: 5px;
      font-size: 10px;
    }
  }

  .from,
  .to {
    margin-left: 42px;
    font-size: 12px;
    @media #{$tablet} {
      margin: 0 50px 0 0;
      display: inline-block;
    }
    span {
      /*font-size: 21px;
            color: $bright-blue;*/
      margin-left: 6px;
      font-family: "Roboto", sans-serif;
      @media #{$tablet} {
        margin: 6px 0 0px;
        display: inline-block;
      }
      color: var(--font-color-input);
      font-size: var(--font-size-input);
    }
  }

  .to {
    margin-left: 18px;
    @media #{$small-phone} {
      font-size: 10px;
      margin-left: 0;
    }
  }

  .caret {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.6;
    transition: 0.2s;
    right: 15px;
    width: 12px;
    height: 100%;
    background: url(../../img/icons/caret-gray1.png) no-repeat center/auto 7px;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.vc-border-gray-400 {
  border: none;
}

#calendars-wrapper * {
  font-family: arial;
}
#default-ranges-wrapper {
  width: auto;
}
</style>
