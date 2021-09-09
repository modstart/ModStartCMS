const matchRules = (rules, rule) => {
    if (typeof rule == 'object') {
        let match = false
        rule.forEach(o => {
            if (rules.filter(reg => reg.test(o)).length > 0) {
                match = true
            }
        })
        return match
    } else if (rules.filter(reg => reg.test(rule)).length > 0) {
        return true
    }
    return false
}
const processMenu = (menus, rules) => {
    menus.forEach(menu => {
        if (!('children' in menu)) {
            menu.children = []
        }
        if (!menu.rule) {
            menu.rule = menu.url
        }
        if (menu.children.length > 0) {
            processMenu(menu.children, rules)
        }
        let hasChildrenMenu = (menu.children.filter(o => o.show).length > 0)
        menu.show = (matchRules(rules, menu.rule) || hasChildrenMenu)
    })
}

const panel = {
    state: {
        style: {
            top: '0px',
            mobileTop: '0px',
            showLogo: true,
            showHeadRightTool: true,
            alwaysShowMenu: false,
        },
        menus: [],
        rules: [],
    },
    mutations: {
        SET_PANEL_STYLE: (state, data) => {
            Object.assign(state.style, data)
        },
        SET_PANEL_MENU: (state, menus) => {
            processMenu(menus, state.rules)
            state.menus = menus
        },
        SET_PANEL_RULES: (state, rules) => {
            rules = rules.map(rule => {
                let reg = rule.replace(/\*/g, '.+').replace(/\//g, '\\/')
                return new RegExp("^" + reg + "$")
            })
            processMenu(state.menus, rules)
            state.rules = rules
        },
    }
}

export default panel
