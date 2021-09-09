<template>
    <div class="pb-gps-object-input">
        <div style="padding-bottom:10px;" v-if="showTextInfo">
            <el-row :gutter="10">
                <el-col :span="12">
                    <el-input v-model="keywords" @enter.native="search(keywords)"
                              placeholder="输入关键词搜索，或拖动标记调整位置"></el-input>
                </el-col>
                <el-col :span="6">
                    <el-button style="display:block;width:100%;" @click="search(keywords)"><i
                            class="iconfont icon-search"></i> 搜索
                    </el-button>
                </el-col>
                <el-col :span="6" style="line-height:24px;">
                    坐标：
                    {{datav.join('')?datav.join(','):'未选择'}}
                </el-col>
            </el-row>
        </div>
        <baidu-map class="map" :center="{lng:116.404,lat: 39.915}" :zoom="14" @ready="onReady" :map-click="true"
                   @click="doClick">
            <bm-navigation anchor="BMAP_ANCHOR_TOP_RIGHT"></bm-navigation>
            <bm-scale anchor="BMAP_ANCHOR_TOP_RIGHT"></bm-scale>
            <bm-marker :position="position" :dragging="true" @dragend="onChange"></bm-marker>
        </baidu-map>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";
    import BaiduMap from 'vue-baidu-map'
    import Vue from 'vue'

    Vue.use(BaiduMap, {
        ak: window.__baiduMapAK
    })

    export default {
        name: "GpsObjectInput",
        mixins: [FieldInputMixin],
        props: {
            lngKey: {
                type: String,
                default: 'addressLng',
            },
            latKey: {
                type: String,
                default: 'addressLat',
            },
            showTextInfo: {
                type: Boolean,
                default: true,
            },
        },
        data() {
            return {
                $map: null,
                processSearch: false,
                keywords: '',
                options: [],
                datav: [],
            }
        },
        computed: {
            position() {
                let pos
                if (this.datav.join('')) {
                    pos = {
                        lng: this.datav[0],
                        lat: this.datav[1]
                    }
                } else {
                    pos = {lng: 116.404, lat: 39.915}
                }
                return pos
            }
        },
        watch: {
            datav(newValue, oldValue) {
                if (null === this.datav) {
                    this.datav = ['', '']
                    return
                }
                const v = [
                    this.data[this.lngKey] || '',
                    this.data[this.latKey] || '',
                ]
                if (JSON.stringify(newValue) !== JSON.stringify(v)) {
                    this.data[this.lngKey] = newValue[0]
                    this.data[this.latKey] = newValue[1]
                    this.$emit('update', this.data)
                }
            },
            data: {
                handler(newValue, oldValue) {
                    const v = [
                        this.data[this.lngKey] || '',
                        this.data[this.latKey] || '',
                    ]
                    if (JSON.stringify(v) !== JSON.stringify(this.datav)) {
                        this.datav = v
                    }
                },
                deep: true,
                immediate: true
            },
        },
        methods: {
            onReady(map) {
                this.$map = map
                this.$map.map.setViewport([this.position])
            },
            onChange(e) {
                this.datav = [
                    e.point.lng,
                    e.point.lat
                ]
            },
            doClick(e) {
                this.datav = [
                    e.point.lng,
                    e.point.lat
                ]
            },
            search(keywords) {
                this.processSearch = true
                this.keywords = keywords
                this.doSearch()
            },
            doSearch() {
                if (!this.processSearch) {
                    return
                }
                this.processSearch = false
                if (!this.$map) {
                    return
                }
                let ls = new this.$map.BMap.LocalSearch(this.$map.map)
                ls.search(this.keywords)
                ls.setSearchCompleteCallback(rs => {
                    if (ls.getStatus() == BMAP_STATUS_SUCCESS) {
                        if (rs.getCurrentNumPois() > 0) {
                            let poi = rs.getPoi(0)
                            this.datav = [
                                poi.point.lng,
                                poi.point.lat
                            ]
                            this.$map.map.setViewport([poi.point])
                        } else {
                            this.$dialog.tipError('没有搜搜索到任何地点')
                        }
                    } else {
                        this.$dialog.tipError('没有搜搜索到任何地点')
                    }
                });
            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-gps-object-input {
        .map {
            width: 100%;
            height: 300px;
            border: 1px solid #CCC;
        }
    }
</style>


