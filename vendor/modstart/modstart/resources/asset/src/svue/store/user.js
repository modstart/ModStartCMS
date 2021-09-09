const user = {

    state: {
        init: false,
        user: {
            id: 0
        },
    },

    mutations: {
        SET_USER: (state, user) => {
            state.user = user
            state.init = true
        },
    }
}

export default user
