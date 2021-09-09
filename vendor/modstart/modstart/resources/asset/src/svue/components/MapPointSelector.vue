<template>
    <div>
        <div :id="id" style="width:100%;height:300px;border:1px solid #CCC;"></div>
    </div>
</template>

<script>
    import {DomUtil, StrUtil} from './../lib/util'

    export default {
        name: "MapPointSelector",
        props: {
            ak: {
                type: String,
                default: null,
            },
        },
        data() {
            return {
                id: 'mapPointSelector',
                isReady: false,
                point: {lng: 10, lat: 10},
                map: null,
                marker: null,
            }
        },
        mounted() {
            this.id = 'mapPointSelector_' + StrUtil.randomString(10)
            this.$nextTick(() => {
                this.doInit()
            })
        },
        methods: {
            doSetPointByAddress(address, level) {
                if (!this.map) {
                    setTimeout(() => {
                        this.doSetPointByAddress(address, level)
                    }, 100)
                    return
                }
                level = level || 15
                this.map.centerAndZoom(address, level)
            },
            doSetPoint(point, level) {
                if (!this.map) {
                    setTimeout(() => {
                        this.doSetPoint(point, level)
                    }, 100)
                    return
                }
                level = level || 15
                this.map.centerAndZoom(new BMap.Point(point.lng, point.lat), level)
                this.marker.setPosition(pt)
            },
            doInit() {
                const funcName = 'MapPointSelector_' + StrUtil.randomString(10)
                window[funcName] = () => {

                    const marker = new BMap.Marker(this.point.lng, this.point.lat);
                    marker.enableDragging();
                    const map = new BMap.Map(this.id);
                    map.addOverlay(marker);
                    map.enableScrollWheelZoom();

                    const setPosition = (pt) => {
                        marker.setPosition(pt)
                        if (JSON.stringify(this.point) !== JSON.stringify({lng: pt.lng, lat: pt.lat})) {
                            this.point.lng = pt.lng
                            this.point.lat = pt.lat
                            this.$emit('on-point-select', this.point)
                        }
                    }
                    map.addEventListener('click', function (e) {
                        setPosition(e.point);
                    })
                    map.addEventListener('load', function () {
                        setPosition(map.getCenter());
                    })
                    marker.addEventListener('dragend', function (e) {
                        setPosition(e.point);
                    })
                    this.map = map
                    this.marker = marker
                    this.isReady = true
                    this.$emit('ready', map)
                }
                DomUtil.loadScript(`https://api.map.baidu.com/api?v=2.0&ak=${this.ak}&callback=${funcName}`)
            }
        }
    }
</script>

<style scoped>

</style>
