const Collection = {
    sort: {
        change(direct, list, index) {
            switch (direct) {
                case 'up':
                case 'down':
                case 'top':
                case 'bottom':
                    Collection.sort[direct](list, index)
                    break
            }
        },
        up(list, index) {
            if (index > 0 && index < list.length) {
                let item = list.splice(index, 1)
                list.splice(index - 1, 0, item[0])
            }
        },
        down(list, index) {
            if (index >= 0 && index < list.length) {
                let item = list.splice(index, 1)
                list.splice(index + 1, 0, item[0])
            }
        },
        top(list, index) {
            if (index > 0 && index < list.length) {
                let item = list.splice(index, 1)
                list.splice(0, 0, item[0])
            }
        },
        bottom(list, index) {
            if (index >= 0 && index < list.length) {
                let item = list.splice(index, 1)
                list.push(item[0])
            }
        }
    }
}

export {
    Collection
}
