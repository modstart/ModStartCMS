<script src="https://gosspublic.alicdn.com/aliyun-oss-sdk-6.17.1.min.js"></script>
<script>
    window.__uploadCustomUpload = function (file, callback) {
        const initData = file.cuted.file._initData;
        const client = new OSS({
            region: initData.uploadConfig.region,
            accessKeyId: initData.uploadConfig.accessKeyId,
            accessKeySecret: initData.uploadConfig.accessKeySecret,
            stsToken: initData.uploadConfig.securityToken,
            refreshSTSToken: async () => {
                //TODO
                const info = await fetch('your_sts_server');
                return {
                    accessKeyId: info.accessKeyId,
                    accessKeySecret: info.accessKeySecret,
                    stsToken: info.stsToken
                }
            },
            refreshSTSTokenInterval: 300000,
            bucket: initData.uploadConfig.bucket
        });
        client.multipartUpload(initData.fullPath, file.blob.source, {
            // 获取分片上传进度、断点和返回值。
            progress: (p, cpt, res) => {
                callback.onProgress(file, p);
            },
            // 设置并发上传的分片数量。
            parallel: 1,
            // 设置分片大小。默认值为1 MB，最小值为100 KB。
            partSize: 1024 * 1024,
        }).then(function (result) {
            MS.api.postSuccess("{{$param['server']}}", {
                action: "uploadEnd",
                name: initData.name,
                type: initData.type,
                lastModifiedDate: initData.lastModifiedDate,
                size: initData.size,
                md5: initData.md5,
            }, function (res) {
                callback.onSuccess(file, res);
            });
        }).catch(function (err) {
            callback.onError(file, err);
        });
    };
</script>
