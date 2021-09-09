<template>
    <div>
        <span class="tag" v-for="(item,itemIndex) in data">{{item}}</span>
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'MultiSelectField',
        mixins: [FieldViewMixin],
    }
</script>
