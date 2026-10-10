// Parallel minify worker for gulpfile.js `build:js`.
// Runs inside worker-farm child processes, so multiple files are minified across CPU cores.
// One job = { src: absoluteInputPath, relative: pathRelativeToBase, out: [absoluteOutputPaths] }.
const fs = require('fs');
const path = require('path');
const terser = require('terser');

module.exports = function (job, callback) {
    fs.readFile(job.src, 'utf8', function (err, content) {
        if (err) {
            return callback(err);
        }

        const handleResult = function (result) {
            if (result && result.error) {
                return callback(result.error);
            }
            const code = result && result.code;
            if (typeof code !== 'string') {
                return callback(new Error('minify produced no output: ' + job.src));
            }
            try {
                job.out.forEach(function (out) {
                    fs.mkdirSync(path.dirname(out), { recursive: true });
                    fs.writeFileSync(out, code);
                });
            } catch (writeErr) {
                return callback(writeErr);
            }
            callback(null, { relative: job.relative, size: code.length });
        };

        let output;
        try {
            output = terser.minify(content);
        } catch (minifyErr) {
            return callback(minifyErr);
        }

        if (output && typeof output.then === 'function') {
            output.then(handleResult).catch(callback);
        } else {
            handleResult(output);
        }
    });
};
