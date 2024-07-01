<script>
export default {
    name: "VisitStatisticReport",
    data() {
        return {
            searchData: {
                time: ['', ''],
            },
            pv: [],
            uv: [],
            time: [],
            loading: true,
            ignoreLoad: false,
            chartEle: null,
            chart: null,
        }
    },
    watch: {
        searchData: {
            handler: function (val) {
                this.doLoad()
            },
            deep: true,
        }
    },
    mounted() {
        this.doLoad()
    },
    methods: {
        doUpdate() {
            let isInit = false
            if (!this.chart) {
                this.chartEle = document.getElementById("chart");
                this.chart = echarts.init(this.chartEle);
                isInit = true
            }
            this.chart.setOption({
                "grid": {"right": "1%", "left": "1%", "bottom": "10%", "containLabel": true},
                "toolbox": {
                    "feature": {
                        "dataView": {"show": true, "readOnly": false},
                        "restore": {"show": true},
                        "saveAsImage": {"show": true}
                    }
                },
                "tooltip": {
                    "trigger": "axis",
                    "axisPointer": {"type": "shadow", "snap": true, "crossStyle": {"color": "#999"}}
                },
                "legend": {"data": ["访问量", "访客数"]},
                "xAxis": {
                    "type": "category",
                    "data": this.time
                },
                "yAxis": {"type": "value", "minInterval": 1},
                "series": [{
                    "name": "访问量",
                    "data": this.pv,
                    "type": "line",
                    "smooth": true,
                    "areaStyle": {
                        opacity: 0.1
                    },
                    "itemStyle": {"normal": {"color": "#4F7FF3", "lineStyle": {"color": "#4F7FF3"}}}
                }, {
                    "name": "访客数",
                    "data": this.uv,
                    "type": "line",
                    "smooth": true,
                    "areaStyle": {
                        opacity: 0.1
                    },
                    "itemStyle": {"normal": {"color": "#6A46BD", "lineStyle": {"color": "#6A46BD"}}}
                }]
            });
            if (isInit) {
                MS.ui.onResize(this.chartEle, this.chart.resize);
            }
        },
        doLoad() {
            if (this.ignoreLoad) {
                this.ignoreLoad = false;
                return;
            }
            this.loading = true
            MS.widget.requestInContainer(this, {
                scope: 'admin',
                data: {
                    type: this.searchData.type,
                    start: this.searchData.time[0],
                    end: this.searchData.time[1],
                },
                success: (res) => {
                    this.loading = false;
                    this.records = res.data.records;
                    this.ignoreLoad = true;
                    this.searchData.time = [
                        res.data.start,
                        res.data.end
                    ];
                    this.pv = res.data.pv;
                    this.uv = res.data.uv;
                    this.time = res.data.time;
                    this.doUpdate()
                }
            })
        },
    }
}
</script>

<template>
    <div>
        <div class="ub-content-box margin-bottom tw-flex">
            <div class="tw-flex-grow">
                时间
                <el-date-picker type="daterange"
                                v-model="searchData.time"
                                value-format="yyyy-MM-dd"
                                clearable
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"></el-date-picker>
            </div>
            <div class="tw-flex">
                <a href="javascript:;" class="btn" data-dialog-request="config">
                    <i class="iconfont icon-cog"></i>
                    设置
                </a>
                <a href="javascript:;" class="btn" data-dialog-request="item"
                   data-dialog-width="95%"
                   data-dialog-height="95%">
                    <i class="iconfont icon-list"></i>
                    明细
                </a>
            </div>
        </div>
        <div class="ub-content-box margin-bottom" style="min-height:calc(100vh - 4.5rem);">
            <div>
                <i class="fa fa-bar-chart"></i> 访问统计
            </div>
            <div>
                <div id="chart" style="height:400px;"></div>
            </div>
        </div>
    </div>
</template>

