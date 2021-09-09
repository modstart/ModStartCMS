import {Base64} from 'js-base64';

export const Base64Util = {
    encode(data) {
        return Base64.encode(data)
    },
    decode(data) {
        return Base64.decode(data)
    }
}