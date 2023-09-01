export const ImageUtil = {
    /**
     * @Util 获取图片尺寸
     * @method MS.image.getSize
     * @param imageOrImageFile Image|File 图片或者图片文件
     * @param cb Function 回调函数
     */
    getSize(imageOrImageFile, cb) {
        let img = new Image()
        img.onload = function () {
            cb({
                width: img.width,
                height: img.height,
            })
        }
        img.onerror = function () {
            cb({
                width: 0,
                height: 0
            })
        }
        if (imageOrImageFile instanceof File) {
            img.src = URL.createObjectURL(imageOrImageFile)
        } else {
            img.src = imageOrImageFile
        }
    }
}
