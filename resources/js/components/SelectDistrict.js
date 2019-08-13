// 中国省市区三级联动
const addressData = require('china-area-data/v3/data');

// 引入 loadsh, 一个实用工具库，提供了很多常用的方法
import _ from 'lodash';

Vue.component('select-district', {
  // 定义组件的属性
  props: {
    // 用来初始化省市区的值，在编辑时会使用到
    initValue: {
      type: Array,          // 格式是数组
      default: () => ([]),  // 默认是空数组
    }
  },
  // 定义了这个组件内的数据
  data() {
    return {
      provinces: addressData['86'], // 省列表
      cities: {},                   // 城市列表
      districts: {},                // 地区列表
      provinceId: '',               // 当前选中的省
      cityId: '',                   // 当前选中的市
      districtId: '',               // 当前选中的区
    };
  },

  // 定义观察器
  watch: {
    provinceId(newVal) {
      if (!newVal) {
        this.cities = {};
        this.cityId = '';
        return ;
      }
      // 将城市列表设置为当前省下的城市
      this.cities = addressData[newVal];
      // 如果当前选中的城市不在当前省，则将选中城市清空
      if (!this.cities[this.cityId]) {
        this.cityId = '';
      }
    },
    // 当选择的市发生改变时触发
    cityId(newVal) {
      if (!newVal) {
        this.districts = {};
        this.districtId = '';
        return;
      }
      // 将地区列表设置为当前城市下的地区
      this.districts = addressData[newVal];
      // 如果当前选中的地区不在当前城市，则将选中地区清空
      if (!this.districts[this.districtId]) {
        this.districtId = '';
      }
    },
    // 当选择的区发生改变时触发
    districtId() {
      // 触发一个名为 change 的 Vue 事件，事件的值就是当前选中的省市区名称，格式为数组
      this.$emit('change', [this.provinces[this.provinceId], this.cities[this.cityId], this.districts[this.districtId]]);
    },
  },

  // 组件初始化时会调用这个方法
  created() {
    this.setFormValue(this.initValue);
  },
  methods: {
    setFormValue(value) {
      // 过滤掉空值
      value = _.filter(value);
      // 如果数组长度为0，则将省清空
      if(value.length === 0) {
        this.provinceId = '';
        return;
      }
      // 从当前省列表中找到与数组第一个元素同名的项的索引
      const provinceId = _.findKey(this.provinces, o => o === value[0]);
      // 没找到清空省的值
      if (!provinceId) {
        this.provinceId = '';
        return;
      }
      // 找到了，将对应的省设置为对应的ID
      this.provinceId = provinceId;

      // 由于观察器的作用，这个时候城市列表已经变成了对应省的城市列表
      // 从当前城市列表找到与数组第二个元素同名的项的索引
      const cityId = _.findKey(addressData[provinceId], o => o === value[1]);
      if (!cityId) {
        this.cityId = '';
        return;
      }
      this.cityId = cityId;

      const districtId = _.findKey(addressData[cityId], o => o === value[2]);
      if (!districtId) {
        this.districtId = '';
        return;
      }
      this.districtId = districtId;
    }
  }
});