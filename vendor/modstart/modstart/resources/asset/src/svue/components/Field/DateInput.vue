<template>
    <div>
        <el-date-picker :value="data" @change="onDataChange" type="date" value-format="yyyy-MM-dd" :placeholder="placeholder" style="width:12em;"></el-date-picker>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";

    export default {
        name: "DateInput",
        mixins: [FieldInputMixin],
    }
</script>
