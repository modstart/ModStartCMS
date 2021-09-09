export const DialogMixin = {
    data() {
        return {
            visible: false,
        }
    },
    methods: {
        show() {
            this.visible = true
        },
        close() {
            this.visible = false
        }
    }
}