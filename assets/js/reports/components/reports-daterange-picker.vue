<template>
  <daterangepicker
    class="datepicker_reports"
    :startDefaultDate="startDefaultDate"
    :endDefaultDate="endDefaultDate"
    :minDate="minDate"
    :maxDate="maxDate"
    :defaultRanges="defaultRanges"
    @range-selected="selectedRange"
  ></daterangepicker>
</template>

<script>
import daterangepicker from "../../vue-common/daterange-picker";
import { subDays, subMonths, startOfMonth, endOfMonth } from "date-fns";

const today = Translator.trans("common.today");
const yesterday = Translator.trans("common.yesterday");
const lastWeek = Translator.trans("common.last_week");
const currentMonth = Translator.trans("common.current_month");
const pastMonth = Translator.trans("common.last_month");
const last30Days = Translator.trans("common.last_30_days");
let defaultRanges = [
  { txt: today, range: [new Date(), new Date()] },
  { txt: yesterday, range: [subDays(new Date(), 1), subDays(new Date(), 1)] },
  { txt: lastWeek, range: [subDays(new Date(), 6), new Date()] },
  { txt: last30Days, range: [subDays(new Date(), 30), new Date()] },
  {
    txt: currentMonth,
    range: [startOfMonth(new Date()), endOfMonth(new Date())]
  },
  {
    txt: pastMonth,
    range: [
      startOfMonth(subMonths(new Date(), 1)),
      endOfMonth(subMonths(new Date(), 1))
    ]
  }
];

export default {
  components: { daterangepicker },
  data: function() {
    return { defaultRanges };
  },
  props: {
    startDefaultDate: undefined,
    endDefaultDate: undefined,
    minDate: undefined,
    maxDate: undefined
  },
  methods: {
    selectedRange: function(ev) {
      this.$emit("range-selected", ev);
    }
  }
};
</script>
<style lang="scss" scoped>
@import "../../../scss/_variables";

.datepicker_reports {
  --dropdown-border-color: #ebeff6;
  --dropdown-background-color: #ebeff6;

  --dropdown-active-border-color: #ebeff6;

  --font-size-date: 15px;
  --font-color-date: #{$black};

  --font-size-input: 15px;
  --font-color-input: #{$black};

  --dropdown-padding: 8px 38px 9px 14px;

  margin-left: 0px;

  --display-date-image: none;

  --weight-display-date: 400;

  ::v-deep {
    .caret {
      opacity: 1 !important;
    }
  }
}
</style>
