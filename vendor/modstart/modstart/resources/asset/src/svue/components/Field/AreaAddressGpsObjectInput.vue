<template>
    <div>
        <el-row :gutter="10">
            <el-col :span="12">
                <AreaObjectInput v-model="areav"></AreaObjectInput>
            </el-col>
            <el-col :span="12">
                <el-input v-model="addressv"></el-input>
            </el-col>
            <el-col :span="24" style="padding-top:10px;">
                <GpsObjectInput v-model="gpsv" ref="gps" :show-text-info="false"/>
            </el-col>
        </el-row>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";
    import AreaObjectInput from "./AreaObjectInput";
    import GpsObjectInput from "./GpsObjectInput";

    export default {
        name: "AreaAddressGpsObjectInput",
        components: {GpsObjectInput, AreaObjectInput},
        mixins: [FieldFilterMixin],
        props: {
            provinceKey: {
                type: String,
                default: 'province',
            },
            cityKey: {
                type: String,
                default: 'city',
            },
            districtKey: {
                type: String,
                default: 'district',
            },
            addressKey: {
                type: String,
                default: 'address',
            },
            lngKey: {
                type: String,
                default: 'addressLng',
            },
            latKey: {
                type: String,
                default: 'addressLat',
            },
        },
        data() {
            return {
                options: [],
                areav: {},
                addressv: '',
                gpsv: {},
                datav: [],
            }
        },
        watch: {
            areav: {
                handler(newValue, oldValue) {
                    this.$set(this.datav, 0, this.areav[this.provinceKey])
                    this.$set(this.datav, 1, this.areav[this.cityKey])
                    this.$set(this.datav, 2, this.areav[this.districtKey])
                    this.$refs.gps && this.$refs.gps.search([this.datav[0], this.datav[1], this.datav[2], this.datav[3]].join(''))
                    // console.log('areav.update',[this.datav[0], this.datav[1], this.datav[2], this.datav[3]].join(''))
                },
                deep: true,
            },
            addressv(newValue, oldValue) {
                this.$set(this.datav, 3, this.addressv)
                this.$refs.gps && this.$refs.gps.search([this.datav[0], this.datav[1], this.datav[2], this.datav[3]].join(''))
            },
            gpsv: {
                handler(newValue, oldValue) {
                    this.$set(this.datav, 4, this.gpsv[this.lngKey])
                    this.$set(this.datav, 5, this.gpsv[this.latKey])
                },
                deep: true,
            },
            datav: {
                handler(newValue, oldValue) {
                    // console.log('datav.change', JSON.stringify(newValue))
                    if (null === this.datav) {
                        this.datav = ['', '', '', '', '', '']
                        return
                    }
                    const v = [
                        this.data[this.provinceKey] || '',
                        this.data[this.cityKey] || '',
                        this.data[this.districtKey] || '',
                        this.data[this.addressKey] || '',
                        this.data[this.lngKey] || '',
                        this.data[this.latKey] || '',
                    ]
                    if (JSON.stringify(newValue) !== JSON.stringify(v)) {
                        // console.log('update', JSON.stringify(newValue))
                        this.data[this.provinceKey] = newValue[0]
                        this.data[this.cityKey] = newValue[1]
                        this.data[this.districtKey] = newValue[2]
                        this.data[this.addressKey] = newValue[3]
                        this.data[this.lngKey] = newValue[4]
                        this.data[this.latKey] = newValue[5]
                        this.$emit('update', this.data)
                    }
                },
                deep: true,
            },
            data: {
                handler(newValue, oldValue) {
                    const v = [
                        this.data[this.provinceKey] || '',
                        this.data[this.cityKey] || '',
                        this.data[this.districtKey] || '',
                        this.data[this.addressKey] || '',
                        this.data[this.lngKey] || '',
                        this.data[this.latKey] || '',
                    ]
                    if (JSON.stringify(v) !== JSON.stringify(this.datav)) {
                        this.$set(this.areav, this.provinceKey, v[0])
                        this.$set(this.areav, this.cityKey, v[1])
                        this.$set(this.areav, this.districtKey, v[2])
                        this.addressv = v[3]
                        this.$set(this.gpsv, this.lngKey, v[4])
                        this.$set(this.gpsv, this.latKey, v[5])
                        this.datav = v
                    }
                },
                deep: true,
                immediate: true
            },
        }
    }
</script>
