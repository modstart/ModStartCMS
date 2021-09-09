import Vue from 'vue'
import {Cookie} from "../lib/cookie";
import {Device} from "../lib/device";
import MemberProfilePasswordDialog from "../pages/Components/MemberProfilePasswordDialog";
import $ from 'jquery'

export default {
    metaInfo() {
        return {
            title: '空页面',
            titleTemplate: '%s - ' + (this.$store.state.base.config.siteName ? this.$store.state.base.config.siteName : '')
        }
    },
    components: {
        MemberProfilePasswordDialog,
        PanelMenu: () => import('../layouts/Panel/PanelMenu'),
    },
    computed: {
        isPC() {
            return Device.isPC()
        },
        headTop() {
            if (Device.isPC()) {
                return this.$store.state.panel.style.top
            }
            return this.$store.state.panel.style.mobileTop;
        },
        headMenuTop() {
            if (Device.isPC()) {
                return this.$store.state.panel.style.top
            }
            return `calc( ${this.$store.state.panel.style.mobileTop} + 50px)`
        }
    },
    data() {
        return {
            menuCollapse: false,
            showRight: false,
            pageMenuLayout: null,
        }
    },
    watch: {
        menuCollapse: {
            handler(n, o) {
                if (n) {
                    $('body').addClass('pb-menu-is-active')
                } else {
                    $('body').removeClass('pb-menu-is-active')
                }
            },
            immediate: true,
        },
        showRight: {
            handler(n, o) {
                if (n) {
                    $('body').addClass('pb-right-is-active')
                } else {
                    $('body').removeClass('pb-right-is-active')
                }
            },
            immediate: true,
        }
    },
    mounted() {
        this.menuCollapse = (Cookie.get('layout-panelMenuCollapse') === '1')
    },
    methods: {
        menuToggle() {
            this.menuCollapse = !this.menuCollapse
            Cookie.set('layout-panelMenuCollapse', this.menuCollapse ? '1' : '0')
        },
        handleUpdatePageMenu(pageMenu) {
            this.pageMenuLayout = null
            Vue.component('PanelPageMenuLayout', {
                render() {
                    if (pageMenu && pageMenu[0]) {
                        return pageMenu[0]
                    } else {
                        if (this.$parent.$refs.mainView.$options.metaInfo && this.$parent.$refs.mainView.$options.metaInfo.title) {
                            return this.$parent.$createElement('smart-link', {'class': 'active'}, this.$parent.$refs.mainView.$options.metaInfo.title)
                        }
                    }
                }
            })
            this.pageMenuLayout = 'PanelPageMenuLayout'
        },
        onPanelMenuHide() {
            this.menuCollapse = false
            Cookie.set('layout-panelMenuCollapse', this.menuCollapse ? '1' : '0')
        }
    }
}
