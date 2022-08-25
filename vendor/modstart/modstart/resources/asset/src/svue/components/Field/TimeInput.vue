<template>
    <div>
        <el-time-picker
                :value="data"
                @change="onDataChange"
                style="width:10em;"
                :picker-options="{selectableRange: '00:00:00 - 23:59:00'}"
                value-format="HH:mm:ss"
                :placeholder="placeholder"></el-time-picker>
    </div>
</template>

<script>
    import {FieldInputMixin} from "../../lib/fields-config";
    export default {
        name: "TimeInput",
        mixins: [FieldInputMixin],
    }
</script>
