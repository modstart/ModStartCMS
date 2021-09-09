export const UnitUtil = {
    px2rem(px, remBase) {
        let pxV = parseFloat(px)
        if (isNaN(pxV)) {
            return null
        }
        return pxV / remBase
    },
    px2remWithUnit(px, remBase) {
        const v = UnitUtil.px2rem(px, remBase)
        if (null===v) {
            return null
        }
        return v + 'rem'
    },
    px2remContent(pxContent, remBase) {
        let results = []
        for (let line of pxContent.split("\n")) {
            results.push(line.replace(/([^\d]?)([\d\.]+)px/g, (val, s0, s1) => {
                return s0 + UnitUtil.px2remWithUnit(s1, remBase)
            }))
        }
        return results.join("\n")
    },
    rem2px(rem, remBase) {
        let remV = parseFloat(rem)
        if (isNaN(remV)) {
            return null
        }
        return remV * remBase
    },
    rem2pxWithUnit(rem, remBase) {
        const v = UnitUtil.rem2px(rem, remBase)
        if (null===v) {
            return null
        }
        return v + 'px'
    },
    rem2pxContent(remContent, remBase) {
        let results = []
        for (let line of remContent.split("\n")) {
            results.push(line.replace(/([^\d]?)([\d\.]+)rem/g, (val, s0, s1) => {
                return s0 + UnitUtil.rem2pxWithUnit(s1, remBase)
            }))
        }
        return results.join("\n")
    }
}