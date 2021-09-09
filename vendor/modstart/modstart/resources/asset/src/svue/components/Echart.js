import Vue from 'vue'

import ECharts from 'vue-echarts'
import 'echarts/lib/chart/bar'
import 'echarts/lib/chart/pie'
import 'echarts/lib/chart/line'
import 'echarts/lib/component/tooltip'

export default (Vue) => {
    Vue.component("e-charts", ECharts)
}
