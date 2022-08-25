<template>
    <div>
        <el-date-picker @onPick="onDataChange"
                        :value="data"
                        type="datetimerange"
                        range-separator="至"
                        start-placeholder="开始时间"
                        end-placeholder="结束时间"
                        value-format="yyyy-MM-dd HH:mm:ss"
                        format="yyyy-MM-dd HH:mm:ss"
                        :picker-options="pickerOptions"></el-date-picker>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";
    import {DateUtil} from "../../lib/date-util";

    export default {
        name: "DatetimeRangeFilter",
        mixins: [FieldFilterMixin],
        data(){
            return {
                pickerOptions:{
                    onPick:(v)=>{
                        if(v.minDate && v.maxDate){
                            this.onDataChange([
                                DateUtil.formatDatetime(v.minDate),
                                DateUtil.formatDatetime(v.maxDate)
                            ])
                        }
                    }
                },
            }
        },
    }
</script>
