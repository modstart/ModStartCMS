<template>
    <div>
        <div :id="id" style="width:100%;height:100px;">{{ currentData }}</div>
    </div>
</template>

<script>
import {FieldVModel} from "../lib/fields-config";

export default {
    name: "JsonEditor",
    mixins: [FieldVModel],
    data() {
        return {
            id: 'JsonEditor' + Math.random().toString(36).substr(2),
        }
    },
    mounted() {
        MS.util.loadScript(this.$url.cdn('asset/vendor/ace/ace.js'), () => {
            this.init()
        })
    },
    methods: {
        init() {
            const editor = ace.edit(this.id);
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/json");
            editor.session.on('change', () => {
                // console.log('JsonEditor.changed', editor.session.getValue());
                this.currentData = editor.session.getValue();
            })
        }
    }
}
</script>

<style scoped>

</style>
