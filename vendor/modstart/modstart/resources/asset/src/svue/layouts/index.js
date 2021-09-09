export default (Vue) => {
    Vue.component("panel-box", () => import('./Panel/PanelBox'))
    Vue.component("panel-box-body", () => import('./Panel/PanelBoxBody'))
    Vue.component("panel-content", () => import('./Panel/PanelContent'))
    Vue.component("panel-bread-crumb", () => import('./Panel/PanelBreadCrumb'))
}
