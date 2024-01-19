import {LazyValue} from './../svue/lib/lazyvalue.js'

if (!('MS' in window)) {
    window.MS = {}
}
window.MS.lazyValue = LazyValue
