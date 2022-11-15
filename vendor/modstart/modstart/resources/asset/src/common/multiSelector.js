var MultiSelector = require('./../lib/multi-selector/multi-selector.js');

if (!('api' in window)) {
    window.api = {}
}
window.api.multiSelector = MultiSelector
if (!('MS' in window)) {
    window.MS = {}
}
window.MS.multiSelector = MultiSelector
