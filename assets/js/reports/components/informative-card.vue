<template>
  <div class="item" :class="color + ' ' + last">
    <div class="img-wrapper">
      <img :src="imgPath" alt="" v-b-tooltip.hover />
      <div class="img-tooltip">
        <p>{{ value }}</p>
        <p>{{ date }}</p>
      </div>
    </div>
    <h2>{{ value }}</h2>
    <p class="subtitle">{{ metric }}</p>
    <div class="help">
      <p class="help-inner">{{ help }}</p>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    color: String,
    metric: String,
    last: String,
    value: String,
    help: String,
    date: String
  },
  data: function() {
    return {
      imgPath: require("../../../img/feats/feat-" + this.color + ".png")
    };
  }
};
</script>
<style lang="scss" scoped>
@import "../../../scss/_variables";
.item {
  background-color: #fff;
  &:not(.last-item) {
    margin-right: 0.75vw;
  }
  width: 100%;
  padding: 0.8vw 0.3vw 0.8vw 1.1vw;
  border-radius: 2px;
  border-bottom: 8px solid;
  position: relative;

  @media #{$tablet} {
    margin-bottom: 15px;
    padding: 14px;
  }

  &.blue {
    border-color: #6335d2;
  }
  &.orange {
    border-color: #ffac35;
  }
  &.red {
    border-color: #ff166b;
  }
  &.green {
    border-color: #35d29c;
  }
  &.purple {
    border-color: #9900b9;
  }

  .img-wrapper {
    position: relative;
    display: inline-block;
    img {
      display: inline-block;
      vertical-align: top;
      max-height: 30px;
      height: 30px;
      margin: 3px 3px 0;
      cursor: pointer;
    }
    .img-tooltip {
      width: 180px;
      font-family: Roboto, sans-serif;
      font-size: 18px;
      font-weight: 600;
      position: absolute;
      color: #fff;
      background-color: rgba(0, 0, 0, 0.9);
      padding: 10px;
      border-radius: 3px;
      bottom: 25px;
      transform: translateY(0);
      left: 45px;
      visibility: hidden;
      opacity: 0;
      transition: 0.2s;
      z-index: 1000;

      p {
        margin-bottom: 0;
      }

      p:last-of-type {
        color: #8793a7;
        font-size: 10px;
        font-weight: 400;
        margin-top: 3px;
      }
    }

    &:hover {
      .img-tooltip {
        opacity: 1;
        visibility: visible;
      }
    }
  }

  h2 {
    font-weight: 600;
    font-family: Roboto, sans-serif;
    font-size: 2.2vw;
    margin: 0.45vw 0 0.3vw;
    @media #{$tablet} {
      font-size: 12.2vw;
      margin: 8px 0;
    }
  }

  p.subtitle {
    font-size: 0.78vw;
    margin-bottom: 0;
    font-family: Roboto, sans-serif;
    @media #{$tablet} {
      font-size: 4.78vw;
    }
  }
  .help {
    position: absolute;
    width: 20px;
    height: 20px;
    background: url(../../../img/icon-help.svg) no-repeat 50% / contain;
    top: 10px;
    right: 10px;
    cursor: pointer;

    .help-inner {
      position: absolute;
      width: auto;
      white-space: nowrap;
      z-index: 100000;
      background-color: #fff;
      right: -10px;
      bottom: 25px;
      transform: translateY(-10px);
      padding: 15px;
      transition: 0.2s;
      color: #626e84;
      font-family: Roboto, sans-serif;
      font-size: 12px;
      border-radius: 4px;
      line-height: 1.2;
      box-shadow: 2px 3px 30px -3px rgba(56, 61, 71, 0.1);
      visibility: hidden;
      opacity: 0;
      transform: translateY(0);
      display: inline-table;
      left: unset;
    }

    &:hover .help-inner {
      visibility: visible;
      opacity: 1;
      transform: translateY(0);
    }
  }
}
</style>
