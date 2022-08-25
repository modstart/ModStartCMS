<template>
    <div>
        <el-select :value="data" @change="onDataChange" :placeholder="placeholder">
            <el-option
                    v-for="(item,itemIndex) in option"
                    :key="itemIndex"
                    :label="item"
                    :value="item">
            </el-option>
        </el-select>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "SelectInput",
        mixins: [FieldInputMixin],
    }
</script>
