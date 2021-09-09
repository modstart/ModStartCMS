<template>
    <div>
        <el-date-picker v-model="datav"
                        type="datetimerange"
                        range-separator="至"
                        start-placeholder="开始时间"
                        end-placeholder="结束时间"
                        value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "DatetimeRangeObjectInput",
        mixins: [FieldFilterMixin],
        props: {
            startKey: {
                type: String,
                default: 'min',
            },
            endKey: {
                type: String,
                default: 'max',
            },
        },
        data() {
            return {
                datav: [],
            }
        },
        watch: {
            datav(newValue, oldValue) {
                if (null === this.datav) {
                    this.datav = ['', '']
                    return
                }
                const v = [this.data[this.startKey] || '', this.data[this.endKey] || '']
                if (JSON.stringify(newValue) !== JSON.stringify(v)) {
                    this.data[this.startKey] = newValue[0]
                    this.data[this.endKey] = newValue[1]
                    this.$emit('update', this.data)
                }
            },
            data: {
                handler(newValue, oldValue) {
                    const v = [this.data[this.startKey] || '', this.data[this.endKey] || '']
                    if (JSON.stringify(v) !== JSON.stringify(this.datav)) {
                        this.datav = v
                    }
                },
                deep: true,
                immediate: true
            },
        }
    }
</script>
