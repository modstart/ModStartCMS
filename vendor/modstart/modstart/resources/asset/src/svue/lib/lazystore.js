const LazyStore = function (store, option) {

    const me = this
    const opt = Object.assign({
        commitName: 'SET_LAZY',
        sourcePool: store.state.base,
        sourcePoolName: 'lazy',
    }, option)

    let nameCallbacks = {}
    let nameLocking = {}
    let nameLoaded = {}

    this.register = (name, defaultValue, callback) => {
        nameCallbacks[name] = callback
        store.commit(opt.commitName, [name, defaultValue])
    }

    this.load = (name) => {
        if (name in nameLoaded) {
            return
        }
        if (!(name in nameLocking)) {
            nameLocking[name] = true
            // console.log('LazyStore.load', name)
            if (nameCallbacks[name] === undefined) {
                throw Error('LazyStore.name=' + name + ' not registered')
            }
            nameCallbacks[name](data => {
                // console.log('LazyStore.success', name)
                nameLoaded[name] = true
                store.commit(opt.commitName, [name, data])
                store.commit(opt.commitName, [name + '_init', true])
                delete nameLocking[name]
            })
        }
    }

    this.update = function (/*names*/) {
        for (let i = 0; i < arguments.length; i++) {
            if (arguments[i] in nameLoaded) {
                const name = arguments[i]
                delete nameLoaded[name]
                // store.commit(opt.commitName, [name + '_init', false])
                me.load(name)
            }
        }
    }

    this.clear = function (/*names*/) {
        // console.log(JSON.stringify(arguments))
        // console.log(JSON.stringify(nameLoaded))
        for (let i = 0; i < arguments.length; i++) {
            if (arguments[i] in nameLoaded) {
                const name = arguments[i]
                delete nameLoaded[name]
                store.commit(opt.commitName, [name + '_init', false])
            }
        }
    }

    this.prepareAndGet = function (/*names*/) {
        for (let i = 0; i < arguments.length; i++) {
            const name = arguments[i]
            if (name in nameLoaded) {
                store.commit(opt.commitName, [name + '_init', true])
            } else {
                store.commit(opt.commitName, [name + '_init', false])
            }
            me.load(arguments[i])
        }
        return opt.sourcePool[opt.sourcePoolName]
    }
}


export {
    LazyStore
}
