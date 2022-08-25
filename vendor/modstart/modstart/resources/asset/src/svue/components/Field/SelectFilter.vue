<template>
    <div>
        <el-select :value="data" @change="onDataChange">
            <el-option
                    v-for="(item,itemIndex) in field.option"
                    :key="itemIndex"
                    :label="item"
                    :value="item">
            </el-option>
        </el-select>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "SelectFilter",
        mixins: [FieldFilterMixin],
    }
</script>
