<template>
    <div>
        <el-image
                v-if="!!data"
                style="width:60px;height:60px;"
                :src="data">
        </el-image>
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'ImageField',
        mixins: [FieldViewMixin],
    }
</script>
