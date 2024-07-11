<script src="@asset('vendor/DataQcloudCosFe/script/cos-js-sdk-v5.min.js')"></script>
<script>
    window.__uploadCustomUpload = function (file, callback) {
        const initData = file.cuted.file._initData;
        console.log('initData', initData);
        const cos = new COS({
            getAuthorization: (options, callback) => {
                callback({
                    TmpSecretId: initData.uploadConfig.TmpSecretId,
                    TmpSecretKey: initData.uploadConfig.TmpSecretKey,
                    SecurityToken: initData.uploadConfig.SecurityToken,
                    StartTime: initData.uploadConfig.StartTime,
                    ExpiredTime: initData.uploadConfig.ExpiredTime

                })
            }
        });
        cos.uploadFile({
            Bucket: initData.uploadConfig.bucket,
            Region: initData.uploadConfig.region,
            Key: initData.fullPath,
            Body: file.blob.source,
            // 触发分块上传的阈值，超过5MB 使用分块上传，小于5MB使用简单上传。可自行设置，非必须
            SliceSize: 100 * 1024,
            // onTaskReady: function (taskId) {
            //     console.log(taskId);
            // },
            onProgress: function (progressData) {
                callback.onProgress(file, progressData.percent);
            },
            // onFileFinish: function (err, data, options) {
            //     console.log(options.Key + '上传' + (err ? '失败' : '完成'));
            // },
        }).then(function (res) {
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
