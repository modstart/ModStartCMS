<template>
    <div class="pb-data-file-manager">
        <DataSelector :url="url"
                      :category="category"
                      mode="flat"
                      :min="min"
                      :max="max"
                      :permission="permission"
                      @on-select="onSelect"></DataSelector>
    </div>
</template>

<script>
    import DataSelector from "../../svue/components/DataSelector";

    export default {
        name: "DataFileManager",
        components: {DataSelector},
        data() {
            return Object.assign({
                url: '',
                category: '',
                permission: {
                    'View': true,
                    'Upload': true,
                    'Delete': true,
                    'Add/Edit': true,
                }
            }, window.__fileManager)
        },
        computed: {
            min() {
                if (parent.__selectorDialogOption) {
                    return parent.__selectorDialogOption.limitMin
                }
                return 1
            },
            max() {
                if (parent.__selectorDialogOption) {
                    return parent.__selectorDialogOption.limitMax
                }
                return 1
            }
        },
        methods: {
            onSelect(items) {
                parent.__selectorDialogItems = items
                parent.__selectorDialog.close()
            }
        }
    }
</script>

<style lang="less" scoped>
    .pb-data-file-manager {
        background: #FFF;
    }
</style>
