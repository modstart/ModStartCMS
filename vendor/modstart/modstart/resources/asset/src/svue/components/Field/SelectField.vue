<template>
    <div>
        <span class="tag" v-if="data">{{data}}</span>
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'SelectField',
        mixins: [FieldViewMixin],
    }
</script>
