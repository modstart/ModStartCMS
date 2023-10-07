var defaultOption = {
    onError: function (image, successImages, errorImages) {

    },
    onLoaded: function (image, successImages, errorImages) {

    },
    onCompleted: function (successImages, errorImages) {

    }
};
var ImageLoaded = function (images, option) {
    if (typeof option == 'function') {
        option = {onCompleted: option};
    }
    var opt = Object.assign({}, defaultOption, option);
    var successLoadedImages = [];
    var errorLoadedImages = [];
    var imagesObj = [];
    for (var i = 0; i < images.length; i++) {
        if (!images[i]) {
            errorLoadedImages.push(images[i]);
            imagesObj.push(null);
            opt.onError(images[i], successLoadedImages, errorLoadedImages);
            continue;
        }
        var img = new Image();
        img.onload = function () {
            successLoadedImages.push(this.src);
            opt.onLoaded(this.src, successLoadedImages, errorLoadedImages);
        };
        img.onerror = function () {
            errorLoadedImages.push(this.src);
            opt.onError(this.src, successLoadedImages, errorLoadedImages);
        };
        img.src = images[i];
        imagesObj.push(img);
    }
    var waitImageLoaded = function () {
        if (successLoadedImages.length + errorLoadedImages.length >= imagesObj.length) {
            opt.onCompleted(successLoadedImages, errorLoadedImages);
        } else {
            setTimeout(waitImageLoaded, 10);
        }
    };
    waitImageLoaded();
};
module.exports = ImageLoaded;
