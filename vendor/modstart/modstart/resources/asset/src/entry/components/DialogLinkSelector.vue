<template>
    <div class="pb-dialog-link-selector">
        <el-button-group v-if="types.length>1" class="tw-mb-2">
            <el-button v-for="(t,tIndex) in types"
                       @click="doSelectType(t.name)"
                       size="small"
                       :key="tIndex"
                       :type="t.name===type?'primary':''">
                <i :class="t.icon"></i>
                {{ t.title }}
            </el-button>
        </el-button-group>
        <div class="ub-border tw-px-2 tw-rounded tw-bg-white">
            <el-tabs v-if="filterLinks.length>1" v-model="linkTitle">
                <el-tab-pane v-for="(linkGroup,linkGroupIndex) in filterLinks"
                             :label="linkGroup.title"
                             :name="linkGroup.title"
                             :key="linkGroup.title">
                    <div class="pb-dialog-link-selector-item" v-for="(link,linkIndex) in linkGroup.list"
                         :key="linkIndex"
                         @click="doSelect(link)">
                        <span class="title">{{ link.title }}</span>
                        <span class="link">({{ link.link }})</span>
                    </div>
                </el-tab-pane>
            </el-tabs>
            <div v-else-if="filterLinks.length>0"
                 class="tw-py-2">
                <div class="pb-dialog-link-selector-item" v-for="(link,linkIndex) in filterLinks[0].list"
                     :key="linkIndex"
                     @click="doSelect(link)">
                    <span class="title">{{ link.title }}</span>
                    <span class="link">({{ link.link }})</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DialogLinkSelector",
    data() {
        return {
            type: 'web',
            types: window.__data.types,
            links: window.__data.links,
            linkTitle: null,
        }
    },
    computed: {
        filterLinks() {
            return this.links.filter(link => link.type === this.type)
        }
    },
    mounted() {
        this.selectFirstLinkTab()
    },
    methods: {
        selectFirstLinkTab() {
            this.linkTitle = this.filterLinks[0].title
        },
        doSelectType(type) {
            this.type = type
            this.selectFirstLinkTab()
        },
        doSelect(link) {
            // console.log('link', link)
            parent.__selectorDialogItems = [JSON.parse(JSON.stringify(link))]
            parent.__selectorDialog.close()
        },
    }
}
</script>

<style lang="less" scoped>
.pb-dialog-link-selector {
    position: relative;
}

.pb-dialog-link-selector-item {
    display: block;
    background: #FFF;
    margin: 0.2rem 0;
    line-height: 1.3rem;
    padding: 0 0.5rem;
    border-radius: 0.2rem;
    cursor: pointer;
    border: 1px solid #EEE;

    .link {
        color: #999;
        font-family: "Source Code Pro", monospace;
    }

    &:hover {
        border: 1px solid var(--color-warning);
    }
}
</style>
