<template>
    <div>
        <el-date-picker v-model="data" @change="onDataChange" type="datetime"
                        value-format="yyyy-MM-dd HH:mm:ss" :placeholder="placeholder"></el-date-picker>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";

    export default {
        name: "DatetimeInput",
        mixins: [FieldInputMixin],
    }
</script>
